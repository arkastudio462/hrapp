<script setup>
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { Camera, Check, X, Loader2, ScanFace } from 'lucide-vue-next';
import * as faceapi from 'face-api.js';

const props = defineProps({
    employee: Object,
    hasFaceData: Boolean,
});

const videoRef = ref(null);
const canvasRef = ref(null);
const mode = ref(props.hasFaceData ? 'attendance' : 'register');
const isProcessing = ref(false);
const message = ref('');
const messageType = ref('');
const registeredPhotos = ref([]);
const stream = ref(null);
const modelsLoaded = ref(false);
const faceDetected = ref(false);
const detecting = ref(false);

const loadModels = async () => {
    try {
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
            faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
            faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
            faceapi.nets.faceExpressionNet.loadFromUri('/models'),
        ]);
        modelsLoaded.value = true;
        message.value = '';
    } catch (error) {
        console.error('Gagal memuat model deteksi wajah:', error);
        message.value = 'Gagal memuat model deteksi wajah. Periksa koneksi internet dan muat ulang halaman.';
        messageType.value = 'error';
        modelsLoaded.value = false;
    }
};

const retryLoadModels = async () => {
    message.value = '';
    await loadModels();
    if (modelsLoaded.value) {
        startCamera();
    }
};

const startCamera = async () => {
    if (!modelsLoaded.value) return;
    try {
        stream.value = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: 640, height: 480 },
        });
        if (videoRef.value) {
            videoRef.value.srcObject = stream.value;
            startFaceDetection();
        }
    } catch (error) {
        message.value = 'Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.';
        messageType.value = 'error';
    }
};

const startFaceDetection = () => {
    if (!modelsLoaded.value) return;
    detecting.value = true;
    const detect = async () => {
        if (!videoRef.value || !detecting.value) return;

        try {
            const detection = await faceapi
                .detectSingleFace(videoRef.value, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (detection) {
                faceDetected.value = true;
                const canvas = canvasRef.value;
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    canvas.width = videoRef.value.videoWidth;
                    canvas.height = videoRef.value.videoHeight;
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    const box = detection.detection.box;
                    ctx.strokeStyle = '#16a34a';
                    ctx.lineWidth = 3;
                    ctx.strokeRect(box.x, box.y, box.width, box.height);
                }
            } else {
                faceDetected.value = false;
            }
        } catch (error) {
            console.log('Detection error:', error);
        }

        if (detecting.value) {
            requestAnimationFrame(detect);
        }
    };
    detect();
};

const stopCamera = () => {
    detecting.value = false;
    if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop());
    }
};

const capturePhoto = () => {
    const video = videoRef.value;
    const canvas = canvasRef.value;
    if (! video || ! canvas) return null;

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);

    return canvas.toDataURL('image/jpeg', 0.8);
};

const registerFace = async () => {
    if (registeredPhotos.value.length >= 5) {
        message.value = 'Maksimal 5 foto sudah tercapai.';
        messageType.value = 'error';
        return;
    }

    if (!faceDetected.value) {
        message.value = 'Wajah tidak terdeteksi. Posisikan wajah di dalam frame.';
        messageType.value = 'error';
        return;
    }

    const photo = capturePhoto();
    if (photo) {
        registeredPhotos.value.push(photo);
        message.value = `Foto ${registeredPhotos.value.length}/5 berhasil diambil.`;
        messageType.value = 'success';
    }
};

const submitRegistration = async () => {
    if (registeredPhotos.value.length < 3) {
        message.value = 'Minimal 3 foto diperlukan untuk pendaftaran.';
        messageType.value = 'error';
        return;
    }

    isProcessing.value = true;
    router.post('/face-attendance/register', {
        face_data: registeredPhotos.value,
    }, {
        onFinish: () => {
            isProcessing.value = false;
        },
    });
};

const submitAttendance = async () => {
    if (!faceDetected.value) {
        message.value = 'Wajah tidak terdeteksi. Posisikan wajah di dalam frame.';
        messageType.value = 'error';
        return;
    }

    isProcessing.value = true;
    message.value = '';

    try {
        const position = await new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0,
            });
        });

        const photo = capturePhoto();
        if (! photo) {
            message.value = 'Gagal mengambil foto.';
            messageType.value = 'error';
            isProcessing.value = false;
            return;
        }

        router.post('/face-attendance/verify', {
            photo: photo,
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
        }, {
            onFinish: () => {
                isProcessing.value = false;
            },
        });
    } catch (error) {
        message.value = 'Gagal mendapatkan lokasi. Pastikan izin lokasi diberikan.';
        messageType.value = 'error';
        isProcessing.value = false;
    }
};

onMounted(async () => {
    await loadModels();
    if (modelsLoaded.value) {
        startCamera();
    }
});

onUnmounted(() => {
    stopCamera();
});
</script>

<template>
    <Head title="Absensi Wajah" />

    <EmployeeLayout title="Absen Wajah">
        <div class="text-center">
            <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-coral text-white shadow-card shadow-coral/30">
                <ScanFace class="size-8" />
            </div>
            <h1 class="mt-3 text-xl font-extrabold tracking-tight text-ink-app">
                {{ hasFaceData ? 'Absensi Wajah' : 'Daftarkan Wajah' }}
            </h1>
            <p class="mt-1 text-sm text-muted-app">
                {{ hasFaceData ? 'Posisikan wajah di dalam frame untuk absensi.' : 'Ambil minimal 3 foto wajah untuk pendaftaran.' }}
            </p>
        </div>

        <!-- Camera -->
        <div class="relative mt-5 overflow-hidden rounded-2xl bg-black shadow-card">
            <video
                ref="videoRef"
                autoplay
                playsinline
                muted
                class="w-full"
            />
            <canvas ref="canvasRef" class="pointer-events-none absolute inset-0 h-full w-full" />
            <!-- Face Detection Status -->
            <div class="absolute right-4 bottom-4 left-4 flex items-center justify-between">
                <div :class="[
                    'flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold',
                    faceDetected ? 'bg-sage text-green-800' : 'bg-coral text-white',
                ]">
                    <div :class="['h-2 w-2 rounded-full', faceDetected ? 'animate-pulse bg-green-700' : 'bg-white']" />
                    {{ faceDetected ? 'Wajah Terdeteksi' : 'Wajah Tidak Terdeteksi' }}
                </div>
                <div v-if="!modelsLoaded" class="flex items-center gap-2 rounded-full bg-amber px-3 py-1.5 text-xs font-bold text-ink-app">
                    <Loader2 class="h-3 w-3 animate-spin" />
                    Loading...
                </div>
            </div>
        </div>

        <!-- Message -->
        <div
            v-if="message"
            :class="[
                'mt-4 rounded-2xl p-4 text-center text-sm font-semibold animate-fade-up',
                messageType === 'success' ? 'bg-sage/50 text-green-800' : 'bg-blush/40 text-coral-deep',
            ]"
        >
            {{ message }}
            <button
                v-if="messageType === 'error' && !modelsLoaded"
                class="mt-2 block w-full rounded-xl bg-coral px-4 py-2 text-xs font-bold text-white"
                @click="retryLoadModels"
            >
                Muat Ulang
            </button>
        </div>

        <!-- Register Mode -->
        <div v-if="!hasFaceData" class="mt-5">
            <button
                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-coral px-4 py-3 text-sm font-extrabold text-white shadow-soft transition-all hover:bg-coral-deep disabled:opacity-50"
                :disabled="isProcessing || registeredPhotos.length >= 5 || !faceDetected"
                @click="registerFace"
            >
                <Camera class="size-4" />
                Ambil Foto ({{ registeredPhotos.length }}/5)
            </button>

            <!-- Photo Preview -->
            <div v-if="registeredPhotos.length" class="mt-4 flex gap-2 overflow-x-auto pb-2">
                <div
                    v-for="(photo, i) in registeredPhotos"
                    :key="i"
                    class="relative h-20 w-20 flex-shrink-0"
                >
                    <img :src="photo" class="h-full w-full rounded-xl object-cover" />
                    <button
                        class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-coral text-white"
                        @click="registeredPhotos.splice(i, 1)"
                    >
                        <X class="h-3 w-3" />
                    </button>
                </div>
            </div>

            <button
                v-if="registeredPhotos.length >= 3"
                class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-coral-deep px-4 py-3 text-sm font-extrabold text-white shadow-soft transition-all hover:opacity-90 disabled:opacity-50"
                :disabled="isProcessing"
                @click="submitRegistration"
            >
                <Loader2 v-if="isProcessing" class="size-4 animate-spin" />
                Daftarkan Wajah
            </button>
        </div>

        <!-- Attendance Mode -->
        <div v-else class="mt-5">
            <button
                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-coral px-4 py-3 text-sm font-extrabold text-white shadow-soft transition-all hover:bg-coral-deep disabled:opacity-50"
                :disabled="isProcessing || !faceDetected"
                @click="submitAttendance"
            >
                <Loader2 v-if="isProcessing" class="size-4 animate-spin" />
                <Check v-else class="size-4" />
                {{ isProcessing ? 'Memproses...' : 'Absensi Sekarang' }}
            </button>
        </div>

        <!-- Info -->
        <div class="mt-5 rounded-2xl bg-white p-4 shadow-soft">
            <h3 class="text-sm font-extrabold text-ink-app">Tips Absensi Berhasil:</h3>
            <ul class="mt-2 space-y-1 text-sm text-muted-app">
                <li>Pastikan wajah terlihat jelas dan tidak tertutup</li>
                <li>Pencahayaan harus cukup</li>
                <li>Posisikan wajah di tengah frame</li>
                <li>Lokasi GPS akan diverifikasi terhadap area kantor</li>
            </ul>
        </div>
    </EmployeeLayout>
</template>
