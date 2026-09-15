<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Settings, CheckCircle, AlertCircle, Loader2 } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    tenant: Object,
});

const mapContainer = ref(null);
let map = null;
let marker = null;
let circle = null;

const form = useForm({
    company_name: props.tenant.name,
    address: props.tenant.settings?.address || '',
    phone: props.tenant.settings?.phone || '',
    npwp: props.tenant.settings?.npwp || '',
    work_start: props.tenant.settings?.work_hours?.start || '08:00',
    work_end: props.tenant.settings?.work_hours?.end || '17:00',
    late_tolerance: props.tenant.settings?.late_tolerance_minutes || 15,
    geofence_radius: props.tenant.settings?.geofence_radius || 100,
    annual_leave: props.tenant.settings?.annual_leave || 12,
    sick_leave: props.tenant.settings?.sick_leave || 0,
    office_latitude: props.tenant.settings?.office_latitude || '',
    office_longitude: props.tenant.settings?.office_longitude || '',
    attendance_method: props.tenant.settings?.attendance_method || 'face_location',
});

const submit = () => {
    form.put('/settings', {
        preserveScroll: true,
        onSuccess: () => {
            form.recentlySuccessful = true;
        },
    });
};

const getFieldError = (field) => {
    if (form.errors[field]) {
        return form.errors[field];
    }
    return null;
};

const initMap = () => {
    if (! mapContainer.value) return;

    const lat = form.office_latitude || -6.2088;
    const lng = form.office_longitude || 106.8456;

    map = L.map(mapContainer.value).setView(
        [lat, lng],
        form.office_latitude ? 15 : 5
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    updateMarker();
    updateCircle();

    map.on('click', (e) => {
        form.office_latitude = e.latlng.lat;
        form.office_longitude = e.latlng.lng;
        updateMarker();
        updateCircle();
    });
};

const updateMarker = () => {
    if (marker) {
        marker.setLatLng([form.office_latitude, form.office_longitude]);
    } else {
        marker = L.marker([form.office_latitude, form.office_longitude], {
            draggable: true,
        }).addTo(map);

        marker.on('dragend', (e) => {
            const position = e.target.getLatLng();
            form.office_latitude = position.lat;
            form.office_longitude = position.lng;
            updateCircle();
        });
    }
};

const updateCircle = () => {
    if (circle) {
        circle.setLatLng([form.office_latitude, form.office_longitude]);
        circle.setRadius(form.geofence_radius);
    } else {
        circle = L.circle([form.office_latitude, form.office_longitude], {
            radius: form.geofence_radius,
            color: '#E07A5F',
            fillColor: '#E07A5F',
            fillOpacity: 0.2,
        }).addTo(map);
    }
};

const useCurrentLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                form.office_latitude = position.coords.latitude;
                form.office_longitude = position.coords.longitude;
                map.setView([form.office_latitude, form.office_longitude], 15);
                updateMarker();
                updateCircle();
            },
            () => {
                alert('Gagal mendapatkan lokasi saat ini.');
            }
        );
    }
};

onMounted(() => {
    initMap();
});
</script>

<template>
    <Head title="Pengaturan" />

    <AppLayout title="Pengaturan">
    <div class="w-full py-4 sm:mx-auto sm:max-w-2xl sm:py-8">
        <div class="flex items-center gap-3">
            <Settings class="h-6 w-6 text-ink-app" />
            <h1 class="text-2xl font-extrabold tracking-tight text-ink-app">Pengaturan</h1>
        </div>
        <form class="mt-8 space-y-6" @submit.prevent="submit">
            <!-- General Error -->
            <div v-if="form.hasErrors" class="rounded-2xl border border-blush bg-blush/30 p-4 animate-fade-up">
                <div class="flex items-center gap-2">
                    <AlertCircle class="size-5 text-coral-deep" />
                    <p class="text-sm font-semibold text-coral-deep">Terjadi kesalahan. Silakan periksa isian form Anda.</p>
                </div>
            </div>

            <!-- Company Info -->
            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <h2 class="text-lg font-extrabold text-ink-app">Informasi Perusahaan</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-ink-app">Nama Perusahaan</label>
                        <input v-model="form.company_name" type="text" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('company_name') ? 'border-coral focus:border-coral' : 'border-outline-app/50 focus:border-coral'" />
                        <p v-if="getFieldError('company_name')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('company_name') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink-app">Alamat</label>
                        <textarea v-model="form.address" rows="2" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('address') ? 'border-coral focus:border-coral' : 'border-outline-app/50 focus:border-coral'" />
                        <p v-if="getFieldError('address')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('address') }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-ink-app">Telepon</label>
                            <input v-model="form.phone" type="text" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('phone') ? 'border-coral focus:border-coral' : 'border-outline-app/50 focus:border-coral'" />
                            <p v-if="getFieldError('phone')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('phone') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-ink-app">NPWP</label>
                            <input v-model="form.npwp" type="text" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('npwp') ? 'border-coral focus:border-coral' : 'border-outline-app/50 focus:border-coral'" />
                            <p v-if="getFieldError('npwp')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('npwp') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Work Settings -->
            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <h2 class="text-lg font-extrabold text-ink-app">Pengaturan Kerja</h2>
                <div class="mt-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-ink-app">Jam Masuk</label>
                            <input v-model="form.work_start" type="time" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('work_start') ? 'border-coral' : 'border-outline-app/50 focus:border-coral'" />
                            <p v-if="getFieldError('work_start')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('work_start') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-ink-app">Jam Pulang</label>
                            <input v-model="form.work_end" type="time" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('work_end') ? 'border-coral' : 'border-outline-app/50 focus:border-coral'" />
                            <p v-if="getFieldError('work_end')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('work_end') }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-ink-app">Toleransi Keterlambatan (menit)</label>
                            <input v-model="form.late_tolerance" type="number" min="0" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('late_tolerance') ? 'border-coral' : 'border-outline-app/50 focus:border-coral'" />
                            <p v-if="getFieldError('late_tolerance')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('late_tolerance') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-ink-app">Radius Geofence (meter)</label>
                            <input v-model="form.geofence_radius" type="number" min="50" max="500" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('geofence_radius') ? 'border-coral' : 'border-outline-app/50 focus:border-coral'" @input="updateCircle" />
                            <p v-if="getFieldError('geofence_radius')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('geofence_radius') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Attendance Method -->
            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <h2 class="text-lg font-extrabold text-ink-app">Metode Absensi</h2>
                <p class="mt-1 text-sm text-muted-app">Pilih metode yang digunakan karyawan untuk melakukan presensi harian.</p>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <button
                        type="button"
                        class="flex flex-col items-center gap-2 rounded-2xl border-2 p-5 transition-all"
                        :class="form.attendance_method === 'face_location' ? 'border-coral bg-coral/5 shadow-soft' : 'border-outline-app/50 bg-white hover:border-coral/40'"
                        @click="form.attendance_method = 'face_location'"
                    >
                        <div class="flex size-12 items-center justify-center rounded-xl" :class="form.attendance_method === 'face_location' ? 'bg-coral text-white' : 'bg-cream text-muted-app'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 9h.01"/><path d="M15 9h.01"/><path d="M8 13a4 4 0 0 0 8 0"/><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/></svg>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-extrabold text-ink-app">Wajah + Lokasi</p>
                            <p class="mt-1 text-xs text-muted-app">Face recognition dengan geofence</p>
                        </div>
                    </button>
                    <button
                        type="button"
                        class="flex flex-col items-center gap-2 rounded-2xl border-2 p-5 transition-all"
                        :class="form.attendance_method === 'qr_only' ? 'border-coral bg-coral/5 shadow-soft' : 'border-outline-app/50 bg-white hover:border-coral/40'"
                        @click="form.attendance_method = 'qr_only'"
                    >
                        <div class="flex size-12 items-center justify-center rounded-xl" :class="form.attendance_method === 'qr_only' ? 'bg-coral text-white' : 'bg-cream text-muted-app'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-extrabold text-ink-app">QR Code Saja</p>
                            <p class="mt-1 text-xs text-muted-app">Scan QR code oleh admin</p>
                        </div>
                    </button>
                </div>
            </div>
            <!-- Office Location -->
            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-ink-app">Lokasi Kantor</h2>
                    <button
                        type="button"
                        class="text-sm font-bold text-coral hover:underline"
                        @click="useCurrentLocation"
                    >
                        Gunakan Lokasi Saat Ini
                    </button>
                </div>
                <p class="mt-1 text-sm text-muted-app">Klik pada peta untuk menetapkan lokasi kantor. Drag marker untuk menyesuaikan.</p>
                <div ref="mapContainer" class="mt-4 h-[300px] overflow-hidden rounded-2xl border border-outline-app/30 shadow-soft" />
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-ink-app">Latitude</label>
                        <input v-model="form.office_latitude" type="number" step="any" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('office_latitude') ? 'border-coral' : 'border-outline-app/50 focus:border-coral'" />
                        <p v-if="getFieldError('office_latitude')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('office_latitude') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink-app">Longitude</label>
                        <input v-model="form.office_longitude" type="number" step="any" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('office_longitude') ? 'border-coral' : 'border-outline-app/50 focus:border-coral'" />
                        <p v-if="getFieldError('office_longitude')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('office_longitude') }}</p>
                    </div>
                </div>
            </div>
            <!-- Leave Settings -->
            <div class="rounded-2xl bg-white p-6 shadow-soft">
                <h2 class="text-lg font-extrabold text-ink-app">Pengaturan Cuti</h2>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-ink-app">Cuti Tahunan (hari/tahun)</label>
                        <input v-model="form.annual_leave" type="number" min="0" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('annual_leave') ? 'border-coral' : 'border-outline-app/50 focus:border-coral'" />
                        <p v-if="getFieldError('annual_leave')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('annual_leave') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink-app">Cuti Sakit (hari/tahun)</label>
                        <input v-model="form.sick_leave" type="number" min="0" class="mt-1.5 block w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors" :class="getFieldError('sick_leave') ? 'border-coral' : 'border-outline-app/50 focus:border-coral'" />
                        <p v-if="getFieldError('sick_leave')" class="mt-1 text-xs text-coral-deep">{{ getFieldError('sick_leave') }}</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center gap-4">
                <button
                    type="submit"
                    class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-white shadow-md transition-all disabled:opacity-50"
                    :class="form.recentlySuccessful ? 'bg-sage text-green-800' : 'bg-coral hover:bg-coral-deep'"
                    :disabled="form.processing"
                >
                    <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                    <CheckCircle v-else-if="form.recentlySuccessful" class="size-4" />
                    <span>{{ form.recentlySuccessful ? 'Tersimpan!' : 'Simpan Pengaturan' }}</span>
                </button>
            </div>

            <!-- Validation Errors Summary -->
            <div v-if="form.hasErrors" class="rounded-2xl border border-blush bg-blush/20 p-4">
                <p class="text-sm font-bold text-coral-deep">Detail Kesalahan:</p>
                <ul class="mt-2 space-y-1">
                    <li v-for="(error, field) in form.errors" :key="field" class="text-xs text-coral-deep">
                        <span class="font-semibold">{{ field }}:</span> {{ error }}
                    </li>
                </ul>
            </div>
        </form>
    </div>
    </AppLayout>
</template>
