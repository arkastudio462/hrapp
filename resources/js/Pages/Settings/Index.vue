<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Settings } from 'lucide-vue-next';
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
    office_latitude: props.tenant.settings?.office_latitude || -6.2088,
    office_longitude: props.tenant.settings?.office_longitude || 106.8456,
});

const submit = () => form.put('/settings');

const initMap = () => {
    if (! mapContainer.value) return;

    map = L.map(mapContainer.value).setView(
        [form.office_latitude, form.office_longitude],
        15
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
            color: '#3C5943',
            fillColor: '#3C5943',
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

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <Link href="/dashboard" class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-[3px] bg-ink">
                            <span class="h-2 w-2 rounded-[1px] bg-white" />
                        </span>
                        <span class="font-display text-lg font-bold tracking-tight">HRapp</span>
                    </Link>
                    <nav class="hidden items-center gap-1 md:flex">
                        <Link href="/dashboard" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Dashboard</Link>
                        <Link href="/employees" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Karyawan</Link>
                        <Link href="/departments" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Departemen</Link>
                        <Link href="/positions" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Jabatan</Link>
                        <Link href="/attendances" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Absensi</Link>
                        <Link href="/leaves" class="rounded-lg px-3 py-2 text-sm font-medium text-inkmuted hover:bg-stone-50 hover:text-ink">Izin/Cuti</Link>
                        <Link href="/settings" class="rounded-lg bg-stone-100 px-3 py-2 text-sm font-medium text-ink">Pengaturan</Link>
                    </nav>
                </div>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <Settings class="h-6 w-6 text-ink" />
                    <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Pengaturan</h1>
                </div>

                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <!-- Company Info -->
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <h2 class="font-display text-lg font-bold text-ink">Informasi Perusahaan</h2>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-ink">Nama Perusahaan</label>
                                <input v-model="form.company_name" type="text" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Alamat</label>
                                <textarea v-model="form.address" rows="2" class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-ink">Telepon</label>
                                    <input v-model="form.phone" type="text" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink">NPWP</label>
                                    <input v-model="form.npwp" type="text" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Work Settings -->
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <h2 class="font-display text-lg font-bold text-ink">Pengaturan Kerja</h2>
                        <div class="mt-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-ink">Jam Masuk</label>
                                    <input v-model="form.work_start" type="time" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink">Jam Pulang</label>
                                    <input v-model="form.work_end" type="time" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-ink">Toleransi Keterlambatan (menit)</label>
                                    <input v-model="form.late_tolerance" type="number" min="0" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink">Radius Geofence (meter)</label>
                                    <input v-model="form.geofence_radius" type="number" min="50" max="500" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" @input="updateCircle" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Office Location -->
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <div class="flex items-center justify-between">
                            <h2 class="font-display text-lg font-bold text-ink">Lokasi Kantor</h2>
                            <button
                                type="button"
                                class="text-sm font-semibold text-ink hover:underline"
                                @click="useCurrentLocation"
                            >
                                Gunakan Lokasi Saat Ini
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-inkmuted">Klik pada peta untuk menetapkan lokasi kantor. Drag marker untuk menyesuaikan.</p>

                        <div ref="mapContainer" class="mt-4 h-[300px] overflow-hidden rounded-xl border border-stone-200" />

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-ink">Latitude</label>
                                <input v-model="form.office_latitude" type="number" step="any" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Longitude</label>
                                <input v-model="form.office_longitude" type="number" step="any" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                        </div>
                    </div>

                    <!-- Leave Settings -->
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <h2 class="font-display text-lg font-bold text-ink">Pengaturan Cuti</h2>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-ink">Cuti Tahunan (hari/tahun)</label>
                                <input v-model="form.annual_leave" type="number" min="0" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Cuti Sakit (hari/tahun)</label>
                                <input v-model="form.sick_leave" type="number" min="0" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
