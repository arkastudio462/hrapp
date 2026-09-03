<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { Camera, Check, X, Loader2 } from 'lucide-vue-next';

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

const startCamera = async () => {
    try {
        stream.value = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: 640, height: 480 },
        });
        if (videoRef.value) {
            videoRef.value.srcObject = stream.value;
        }
    } catch (error) {
        message.value = 'Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.';
        messageType.value = 'error';
    }
};

const stopCamera = () => {
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

onMounted(() => {
    startCamera();
});

onUnmounted(() => {
    stopCamera();
});
</script>

<template>
    <Head title="Absensi Wajah" />

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                <Link href="/dashboard" class="inline-flex items-center gap-1 text-sm font-semibold text-inkmuted hover:text-ink">
                    Kembali ke Dashboard
                </Link>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-lg px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="font-display text-2xl font-bold tracking-tight text-ink">
                        {{ hasFaceData ? 'Absensi Wajah' : 'Daftarkan Wajah' }}
                    </h1>
                    <p class="mt-2 text-sm text-inkmuted">
                        {{ hasFaceData ? 'Posisikan wajah di dalam frame untuk absensi.' : 'Ambil minimal 3 foto wajah untuk pendaftaran.' }}
                    </p>
                </div>

                <!-- Camera -->
                <div class="mt-8 overflow-hidden rounded-xl border border-stone-200 bg-black">
                    <video
                        ref="videoRef"
                        autoplay
                        playsinline
                        muted
                        class="w-full"
                    />
                    <canvas ref="canvasRef" class="hidden" />
                </div>

                <!-- Message -->
                <div
                    v-if="message"
                    :class="[
                        'mt-4 rounded-xl p-4 text-center text-sm font-medium',
                        messageType === 'success' ? 'bg-moss-50 text-moss-700' : 'bg-red-50 text-red-700',
                    ]"
                >
                    {{ message }}
                </div>

                <!-- Register Mode -->
                <div v-if="!hasFaceData" class="mt-6">
                    <div class="flex gap-2">
                        <button
                            class="flex-1 rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
                            :disabled="isProcessing || registeredPhotos.length >= 5"
                            @click="registerFace"
                        >
                            <Camera class="mr-2 inline h-4 w-4" />
                            Ambil Foto ({{ registeredPhotos.length }}/5)
                        </button>
                    </div>

                    <!-- Photo Preview -->
                    <div v-if="registeredPhotos.length" class="mt-4 flex gap-2 overflow-x-auto pb-2">
                        <div
                            v-for="(photo, i) in registeredPhotos"
                            :key="i"
                            class="relative h-20 w-20 flex-shrink-0"
                        >
                            <img :src="photo" class="h-full w-full rounded-lg object-cover" />
                            <button
                                class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white"
                                @click="registeredPhotos.splice(i, 1)"
                            >
                                <X class="h-3 w-3" />
                            </button>
                        </div>
                    </div>

                    <button
                        v-if="registeredPhotos.length >= 3"
                        class="mt-4 w-full rounded-full bg-moss-600 px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
                        :disabled="isProcessing"
                        @click="submitRegistration"
                    >
                        <Loader2 v-if="isProcessing" class="mr-2 inline h-4 w-4 animate-spin" />
                        Daftarkan Wajah
                    </button>
                </div>

                <!-- Attendance Mode -->
                <div v-else class="mt-6">
                    <button
                        class="w-full rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
                        :disabled="isProcessing"
                        @click="submitAttendance"
                    >
                        <Loader2 v-if="isProcessing" class="mr-2 inline h-4 w-4 animate-spin" />
                        <Check v-else class="mr-2 inline h-4 w-4" />
                        {{ isProcessing ? 'Memproses...' : 'Absensi Sekarang' }}
                    </button>
                </div>

                <!-- Info -->
                <div class="mt-6 rounded-xl border border-stone-200 bg-white p-4">
                    <h3 class="text-sm font-semibold text-ink">Informasi:</h3>
                    <ul class="mt-2 space-y-1 text-sm text-inkmuted">
                        <li>Pastikan wajah terlihat jelas dan tidak tertutup</li>
                        <li>Pencahayaan harus cukup</li>
                        <li>Lokasi GPS akan diverifikasi terhadap area kantor</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
