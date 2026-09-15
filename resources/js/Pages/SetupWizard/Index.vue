<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Check, Building2, Clock, Users, UserPlus, Wallet } from 'lucide-vue-next';

const props = defineProps({
    tenant: {
        type: Object,
        default: () => ({}),
    },
    steps: {
        type: Array,
        default: () => [],
    },
    currentStep: {
        type: Number,
        default: 1,
    },
    departments: {
        type: Array,
        default: () => [],
    },
    positions: {
        type: Array,
        default: () => [],
    },
});

const step = ref(props.currentStep);

const stepIcons = [Building2, Clock, Users, UserPlus, Wallet];

const step1Form = useForm({
    address: props.tenant.settings?.address || '',
    phone: props.tenant.settings?.phone || '',
    npwp: props.tenant.settings?.npwp || '',
});

const step2Form = useForm({
    work_start: props.tenant.settings?.work_hours?.start || '08:00',
    work_end: props.tenant.settings?.work_hours?.end || '17:00',
    late_tolerance: props.tenant.settings?.late_tolerance_minutes || 15,
    geofence_radius: props.tenant.settings?.geofence_radius || 100,
});

const step3Form = useForm({
    departments: [{ name: '' }],
    positions: [{ name: '', level: 1 }],
});

const step4Form = useForm({
    employees: [{ name: '', email: '', department_id: '', position_id: '', join_date: '' }],
});

const addDepartment = () => {
    step3Form.departments.push({ name: '' });
};

const removeDepartment = (index) => {
    step3Form.departments.splice(index, 1);
};

const addPosition = () => {
    step3Form.positions.push({ name: '', level: 1 });
};

const removePosition = (index) => {
    step3Form.positions.splice(index, 1);
};

const addEmployee = () => {
    step4Form.employees.push({ name: '', email: '', department_id: '', position_id: '', join_date: '' });
};

const removeEmployee = (index) => {
    step4Form.employees.splice(index, 1);
};

const submitStep1 = () => step1Form.post('/setup-wizard/step-1');
const submitStep2 = () => step2Form.post('/setup-wizard/step-2');
const submitStep3 = () => step3Form.post('/setup-wizard/step-3');
const submitStep4 = () => step4Form.post('/setup-wizard/step-4');
const submitStep5 = () => step5Form.post('/setup-wizard/step-5');

const step5Form = useForm({});
</script>

<template>
    <Head title="Setup Wizard" />

    <div class="min-h-screen bg-cream">
        <!-- Header -->
        <div class="bg-coral">
            <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center gap-2.5 rounded-2xl bg-white/20 px-5 py-2.5 backdrop-blur-sm">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white">
                        <span class="text-sm font-bold text-coral">H</span>
                    </div>
                    <span class="font-display text-lg font-bold text-white tracking-tight">HRHub</span>
                </div>
                <h1 class="mt-5 font-display text-2xl font-bold tracking-tight text-white">Selamat Datang</h1>
                <p class="mt-1.5 text-sm text-white/80">Lengkapi pengaturan perusahaan Anda dalam 5 langkah mudah.</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="mx-auto max-w-3xl px-4 -mt-6 pb-12 sm:px-6 lg:px-8 relative z-10">
            <!-- Progress Steps -->
            <div class="mb-8 rounded-2xl bg-white p-5 shadow-soft">
                <div class="flex items-center justify-between">
                    <template v-for="(s, i) in steps" :key="s.id">
                        <div class="flex flex-col items-center gap-2 relative">
                            <div
                                :class="[
                                    'flex h-11 w-11 items-center justify-center rounded-full text-sm font-bold transition-all duration-300',
                                    s.completed ? 'bg-sage text-coral-deep scale-100' :
                                    step === s.id ? 'bg-coral text-white scale-110 shadow-lg shadow-coral/30' :
                                    'bg-cream text-muted-app',
                                ]"
                            >
                                <Check v-if="s.completed" class="h-5 w-5" />
                                <span v-else>{{ s.id }}</span>
                            </div>
                            <span
                                :class="[
                                    'text-[11px] font-medium whitespace-nowrap',
                                    step === s.id ? 'text-coral font-semibold' : s.completed ? 'text-coral-deep' : 'text-muted-app',
                                ]"
                            >
                                {{ s.label }}
                            </span>
                        </div>
                        <div
                            v-if="i < steps.length - 1"
                            :class="[
                                'h-0.5 flex-1 mx-2 rounded-full transition-colors duration-500',
                                s.completed ? 'bg-sage' : 'bg-outline-app',
                            ]"
                        />
                    </template>
                </div>
            </div>

            <!-- Step Content -->
            <div class="rounded-2xl bg-white p-6 shadow-soft animate-fade-up sm:p-8">
                <!-- Step 1: Company Profile -->
                <div v-if="step === 1">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-coral text-white">
                            <Building2 class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-display text-lg font-bold text-ink-app">Company Profile</h2>
                            <p class="text-sm text-muted-app">Informasi dasar perusahaan Anda.</p>
                        </div>
                    </div>
                    <form class="mt-6 space-y-4" @submit.prevent="submitStep1">
                        <div>
                            <label for="address" class="block text-sm font-medium text-ink-app">Alamat</label>
                            <input
                                id="address"
                                v-model="step1Form.address"
                                type="text"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app bg-cream/50 px-4 py-3 text-sm text-ink-app outline-none transition-colors focus:border-coral focus:bg-white"
                                placeholder="Alamat perusahaan"
                            />
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-ink-app">Telepon</label>
                            <input
                                id="phone"
                                v-model="step1Form.phone"
                                type="text"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app bg-cream/50 px-4 py-3 text-sm text-ink-app outline-none transition-colors focus:border-coral focus:bg-white"
                                placeholder="021-1234567"
                            />
                        </div>
                        <div>
                            <label for="npwp" class="block text-sm font-medium text-ink-app">NPWP (Opsional)</label>
                            <input
                                id="npwp"
                                v-model="step1Form.npwp"
                                type="text"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app bg-cream/50 px-4 py-3 text-sm text-ink-app outline-none transition-colors focus:border-coral focus:bg-white"
                                placeholder="00.000.000.0-000.000"
                            />
                        </div>
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-coral px-4 py-3 text-sm font-semibold text-white transition-all hover:bg-coral-deep hover:shadow-lg hover:shadow-coral/25 active:scale-[0.98] disabled:opacity-50"
                            :disabled="step1Form.processing"
                        >
                            Selanjutnya
                        </button>
                    </form>
                </div>

                <!-- Step 2: Work Settings -->
                <div v-if="step === 2">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-coral text-white">
                            <Clock class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-display text-lg font-bold text-ink-app">Work Settings</h2>
                            <p class="text-sm text-muted-app">Atur jam kerja dan geofence.</p>
                        </div>
                    </div>
                    <form class="mt-6 space-y-4" @submit.prevent="submitStep2">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="work_start" class="block text-sm font-medium text-ink-app">Jam Masuk</label>
                                <input
                                    id="work_start"
                                    v-model="step2Form.work_start"
                                    type="time"
                                    class="mt-1.5 block w-full rounded-xl border border-outline-app bg-cream/50 px-4 py-3 text-sm text-ink-app outline-none transition-colors focus:border-coral focus:bg-white"
                                />
                            </div>
                            <div>
                                <label for="work_end" class="block text-sm font-medium text-ink-app">Jam Pulang</label>
                                <input
                                    id="work_end"
                                    v-model="step2Form.work_end"
                                    type="time"
                                    class="mt-1.5 block w-full rounded-xl border border-outline-app bg-cream/50 px-4 py-3 text-sm text-ink-app outline-none transition-colors focus:border-coral focus:bg-white"
                                />
                            </div>
                        </div>
                        <div>
                            <label for="late_tolerance" class="block text-sm font-medium text-ink-app">Toleransi Keterlambatan (menit)</label>
                            <input
                                id="late_tolerance"
                                v-model="step2Form.late_tolerance"
                                type="number"
                                min="0"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app bg-cream/50 px-4 py-3 text-sm text-ink-app outline-none transition-colors focus:border-coral focus:bg-white"
                            />
                        </div>
                        <div>
                            <label for="geofence_radius" class="block text-sm font-medium text-ink-app">Radius Geofence (meter)</label>
                            <input
                                id="geofence_radius"
                                v-model="step2Form.geofence_radius"
                                type="number"
                                min="50"
                                max="500"
                                class="mt-1.5 block w-full rounded-xl border border-outline-app bg-cream/50 px-4 py-3 text-sm text-ink-app outline-none transition-colors focus:border-coral focus:bg-white"
                            />
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button
                                type="button"
                                class="flex-1 rounded-xl border border-outline-app px-4 py-3 text-sm font-semibold text-ink-app transition-colors hover:bg-cream"
                                @click="step = 1"
                            >
                                Kembali
                            </button>
                            <button
                                type="submit"
                                class="flex-1 rounded-xl bg-coral px-4 py-3 text-sm font-semibold text-white transition-all hover:bg-coral-deep hover:shadow-lg hover:shadow-coral/25 active:scale-[0.98] disabled:opacity-50"
                                :disabled="step2Form.processing"
                            >
                                Selanjutnya
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: Departments & Positions -->
                <div v-if="step === 3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-coral text-white">
                            <Users class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-display text-lg font-bold text-ink-app">Departments & Positions</h2>
                            <p class="text-sm text-muted-app">Buat departemen dan jabatan.</p>
                        </div>
                    </div>
                    <form class="mt-6 space-y-6" @submit.prevent="submitStep3">
                        <!-- Departments -->
                        <div>
                            <h3 class="text-sm font-semibold text-ink-app">Departemen</h3>
                            <div class="mt-2 space-y-2">
                                <div v-for="(dept, i) in step3Form.departments" :key="i" class="flex gap-2 items-center">
                                    <input
                                        v-model="dept.name"
                                        type="text"
                                        class="flex-1 rounded-xl border border-outline-app bg-cream/50 px-4 py-2.5 text-sm text-ink-app outline-none transition-colors focus:border-coral focus:bg-white"
                                        placeholder="Nama departemen"
                                        required
                                    />
                                    <button
                                        v-if="step3Form.departments.length > 1"
                                        type="button"
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-muted-app transition-colors hover:bg-coral/10 hover:text-coral"
                                        @click="removeDepartment(i)"
                                    >
                                        <span class="text-lg leading-none">&times;</span>
                                    </button>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="mt-3 text-sm font-semibold text-coral hover:text-coral-deep transition-colors"
                                @click="addDepartment"
                            >
                                + Tambah Departemen
                            </button>
                        </div>

                        <!-- Positions -->
                        <div>
                            <h3 class="text-sm font-semibold text-ink-app">Jabatan</h3>
                            <div class="mt-2 space-y-2">
                                <div v-for="(pos, i) in step3Form.positions" :key="i" class="flex gap-2 items-center">
                                    <input
                                        v-model="pos.name"
                                        type="text"
                                        class="flex-1 rounded-xl border border-outline-app bg-cream/50 px-4 py-2.5 text-sm text-ink-app outline-none transition-colors focus:border-coral focus:bg-white"
                                        placeholder="Nama jabatan"
                                        required
                                    />
                                    <input
                                        v-model="pos.level"
                                        type="number"
                                        min="1"
                                        class="w-20 rounded-xl border border-outline-app bg-cream/50 px-3 py-2.5 text-sm text-ink-app outline-none transition-colors focus:border-coral focus:bg-white"
                                        placeholder="Level"
                                        required
                                    />
                                    <button
                                        v-if="step3Form.positions.length > 1"
                                        type="button"
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-muted-app transition-colors hover:bg-coral/10 hover:text-coral"
                                        @click="removePosition(i)"
                                    >
                                        <span class="text-lg leading-none">&times;</span>
                                    </button>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="mt-3 text-sm font-semibold text-coral hover:text-coral-deep transition-colors"
                                @click="addPosition"
                            >
                                + Tambah Jabatan
                            </button>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button
                                type="button"
                                class="flex-1 rounded-xl border border-outline-app px-4 py-3 text-sm font-semibold text-ink-app transition-colors hover:bg-cream"
                                @click="step = 2"
                            >
                                Kembali
                            </button>
                            <button
                                type="submit"
                                class="flex-1 rounded-xl bg-coral px-4 py-3 text-sm font-semibold text-white transition-all hover:bg-coral-deep hover:shadow-lg hover:shadow-coral/25 active:scale-[0.98] disabled:opacity-50"
                                :disabled="step3Form.processing"
                            >
                                Selanjutnya
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 4: Add Employees -->
                <div v-if="step === 4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-coral text-white">
                            <UserPlus class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-display text-lg font-bold text-ink-app">Add Employees</h2>
                            <p class="text-sm text-muted-app">Tambahkan karyawan Anda.</p>
                        </div>
                    </div>
                    <form class="mt-6 space-y-5" @submit.prevent="submitStep4">
                        <div v-for="(emp, i) in step4Form.employees" :key="i" class="rounded-xl border border-outline-app bg-cream/30 p-5 transition-all hover:bg-cream/50">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-ink-app">Nama</label>
                                    <input
                                        v-model="emp.name"
                                        type="text"
                                        class="mt-1 block w-full rounded-xl border border-outline-app bg-white px-4 py-2.5 text-sm text-ink-app outline-none transition-colors focus:border-coral"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink-app">Email</label>
                                    <input
                                        v-model="emp.email"
                                        type="email"
                                        class="mt-1 block w-full rounded-xl border border-outline-app bg-white px-4 py-2.5 text-sm text-ink-app outline-none transition-colors focus:border-coral"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink-app">Departemen</label>
                                    <select
                                        v-model="emp.department_id"
                                        class="mt-1 block w-full rounded-xl border border-outline-app bg-white px-4 py-2.5 text-sm text-ink-app outline-none transition-colors focus:border-coral"
                                        required
                                    >
                                        <option value="">Pilih</option>
                                        <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                            {{ dept.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink-app">Jabatan</label>
                                    <select
                                        v-model="emp.position_id"
                                        class="mt-1 block w-full rounded-xl border border-outline-app bg-white px-4 py-2.5 text-sm text-ink-app outline-none transition-colors focus:border-coral"
                                        required
                                    >
                                        <option value="">Pilih</option>
                                        <option v-for="pos in positions" :key="pos.id" :value="pos.id">
                                            {{ pos.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink-app">Tanggal Masuk</label>
                                    <input
                                        v-model="emp.join_date"
                                        type="date"
                                        class="mt-1 block w-full rounded-xl border border-outline-app bg-white px-4 py-2.5 text-sm text-ink-app outline-none transition-colors focus:border-coral"
                                        required
                                    />
                                </div>
                            </div>
                            <button
                                v-if="step4Form.employees.length > 1"
                                type="button"
                                class="mt-3 text-sm font-medium text-muted-app transition-colors hover:text-coral"
                                @click="removeEmployee(i)"
                            >
                                Hapus karyawan ini
                            </button>
                        </div>
                        <button
                            type="button"
                            class="text-sm font-semibold text-coral hover:text-coral-deep transition-colors"
                            @click="addEmployee"
                        >
                            + Tambah Karyawan
                        </button>
                        <div class="flex gap-3 pt-2">
                            <button
                                type="button"
                                class="flex-1 rounded-xl border border-outline-app px-4 py-3 text-sm font-semibold text-ink-app transition-colors hover:bg-cream"
                                @click="step = 3"
                            >
                                Kembali
                            </button>
                            <button
                                type="submit"
                                class="flex-1 rounded-xl bg-coral px-4 py-3 text-sm font-semibold text-white transition-all hover:bg-coral-deep hover:shadow-lg hover:shadow-coral/25 active:scale-[0.98] disabled:opacity-50"
                                :disabled="step4Form.processing"
                            >
                                Selanjutnya
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 5: Setup Payroll -->
                <div v-if="step === 5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-coral text-white">
                            <Wallet class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-display text-lg font-bold text-ink-app">Setup Payroll</h2>
                            <p class="text-sm text-muted-app">Komponen gaji akan dibuatkan secara otomatis.</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-ink-app">Komponen Gaji yang Akan Dibuat:</h3>
                        <div class="mt-3 space-y-2">
                            <div class="flex items-center gap-3 rounded-xl bg-cream/60 px-4 py-2.5">
                                <div class="h-2 w-2 rounded-full bg-amber"></div>
                                <span class="text-sm text-ink-app">Gaji Pokok (BASIC)</span>
                            </div>
                            <div class="flex items-center gap-3 rounded-xl bg-cream/60 px-4 py-2.5">
                                <div class="h-2 w-2 rounded-full bg-amber"></div>
                                <span class="text-sm text-ink-app">Tunjangan Hari Raya (THR)</span>
                            </div>
                            <div class="flex items-center gap-3 rounded-xl bg-cream/60 px-4 py-2.5">
                                <div class="h-2 w-2 rounded-full bg-amber"></div>
                                <span class="text-sm text-ink-app">Tunjangan Transportasi (TRANSPORT)</span>
                            </div>
                            <div class="flex items-center gap-3 rounded-xl bg-cream/60 px-4 py-2.5">
                                <div class="h-2 w-2 rounded-full bg-amber"></div>
                                <span class="text-sm text-ink-app">Tunjangan Makan (MEAL)</span>
                            </div>
                            <div class="flex items-center gap-3 rounded-xl bg-cream/60 px-4 py-2.5">
                                <div class="h-2 w-2 rounded-full bg-amber"></div>
                                <span class="text-sm text-ink-app">Uang Lembur (OVERTIME)</span>
                            </div>
                            <div class="flex items-center gap-3 rounded-xl bg-cream/60 px-4 py-2.5">
                                <div class="h-2 w-2 rounded-full bg-sage"></div>
                                <span class="text-sm text-ink-app">BPJS Kesehatan (1%)</span>
                            </div>
                            <div class="flex items-center gap-3 rounded-xl bg-cream/60 px-4 py-2.5">
                                <div class="h-2 w-2 rounded-full bg-sage"></div>
                                <span class="text-sm text-ink-app">BPJS Ketenagakerjaan (0.5%)</span>
                            </div>
                        </div>
                    </div>
                    <form class="mt-6" @submit.prevent="submitStep5">
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="flex-1 rounded-xl border border-outline-app px-4 py-3 text-sm font-semibold text-ink-app transition-colors hover:bg-cream"
                                @click="step = 4"
                            >
                                Kembali
                            </button>
                            <button
                                type="submit"
                                class="flex-1 rounded-xl bg-coral px-4 py-3 text-sm font-semibold text-white transition-all hover:bg-coral-deep hover:shadow-lg hover:shadow-coral/25 active:scale-[0.98] disabled:opacity-50"
                                :disabled="step5Form.processing"
                            >
                                Selesai
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Skip -->
            <div class="mt-6 text-center">
                <a href="/dashboard" class="text-sm text-muted-app transition-colors hover:text-coral">
                    Lewati dan atur nanti
                </a>
            </div>
        </div>
    </div>
</template>
