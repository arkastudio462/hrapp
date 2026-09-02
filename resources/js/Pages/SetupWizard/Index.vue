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

    <div class="min-h-screen bg-white">
        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Setup Wizard</h1>
                <p class="mt-2 text-sm text-inkmuted">Lengkapi pengaturan perusahaan Anda dalam 5 langkah.</p>
            </div>

            <!-- Progress Steps -->
            <div class="mt-8 flex items-center justify-center">
                <div class="flex items-center gap-2">
                    <template v-for="(s, i) in steps" :key="s.id">
                        <div
                            :class="[
                                'flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold transition-colors',
                                s.completed ? 'bg-moss-600 text-white' :
                                step === s.id ? 'bg-ink text-white' :
                                'bg-stone-100 text-inkmuted',
                            ]"
                        >
                            <Check v-if="s.completed" class="h-5 w-5" />
                            <span v-else>{{ s.id }}</span>
                        </div>
                        <div
                            v-if="i < steps.length - 1"
                            :class="[
                                'h-0.5 w-8',
                                s.completed ? 'bg-moss-600' : 'bg-stone-200',
                            ]"
                        />
                    </template>
                </div>
            </div>

            <!-- Step Content -->
            <div class="mt-10 rounded-xl border border-stone-200 bg-white p-6">
                <!-- Step 1: Company Profile -->
                <div v-if="step === 1">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-ink text-white">
                            <Building2 class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-display text-lg font-bold text-ink">Company Profile</h2>
                            <p class="text-sm text-inkmuted">Informasi dasar perusahaan Anda.</p>
                        </div>
                    </div>
                    <form class="mt-6 space-y-4" @submit.prevent="submitStep1">
                        <div>
                            <label for="address" class="block text-sm font-medium text-ink">Alamat</label>
                            <input
                                id="address"
                                v-model="step1Form.address"
                                type="text"
                                class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink"
                                placeholder="Alamat perusahaan"
                            />
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-ink">Telepon</label>
                            <input
                                id="phone"
                                v-model="step1Form.phone"
                                type="text"
                                class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink"
                                placeholder="021-1234567"
                            />
                        </div>
                        <div>
                            <label for="npwp" class="block text-sm font-medium text-ink">NPWP (Opsional)</label>
                            <input
                                id="npwp"
                                v-model="step1Form.npwp"
                                type="text"
                                class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink"
                                placeholder="00.000.000.0-000.000"
                            />
                        </div>
                        <button
                            type="submit"
                            class="w-full rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-moss-700 disabled:opacity-50"
                            :disabled="step1Form.processing"
                        >
                            Selanjutnya
                        </button>
                    </form>
                </div>

                <!-- Step 2: Work Settings -->
                <div v-if="step === 2">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-ink text-white">
                            <Clock class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-display text-lg font-bold text-ink">Work Settings</h2>
                            <p class="text-sm text-inkmuted">Atur jam kerja dan geofence.</p>
                        </div>
                    </div>
                    <form class="mt-6 space-y-4" @submit.prevent="submitStep2">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="work_start" class="block text-sm font-medium text-ink">Jam Masuk</label>
                                <input
                                    id="work_start"
                                    v-model="step2Form.work_start"
                                    type="time"
                                    class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink"
                                />
                            </div>
                            <div>
                                <label for="work_end" class="block text-sm font-medium text-ink">Jam Pulang</label>
                                <input
                                    id="work_end"
                                    v-model="step2Form.work_end"
                                    type="time"
                                    class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink"
                                />
                            </div>
                        </div>
                        <div>
                            <label for="late_tolerance" class="block text-sm font-medium text-ink">Toleransi Keterlambatan (menit)</label>
                            <input
                                id="late_tolerance"
                                v-model="step2Form.late_tolerance"
                                type="number"
                                min="0"
                                class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink"
                            />
                        </div>
                        <div>
                            <label for="geofence_radius" class="block text-sm font-medium text-ink">Radius Geofence (meter)</label>
                            <input
                                id="geofence_radius"
                                v-model="step2Form.geofence_radius"
                                type="number"
                                min="50"
                                max="500"
                                class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink"
                            />
                        </div>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="flex-1 rounded-full border border-stone-300 px-4 py-3 text-sm font-semibold text-ink hover:bg-stone-50"
                                @click="step = 1"
                            >
                                Kembali
                            </button>
                            <button
                                type="submit"
                                class="flex-1 rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
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
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-ink text-white">
                            <Users class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-display text-lg font-bold text-ink">Departments & Positions</h2>
                            <p class="text-sm text-inkmuted">Buat departemen dan jabatan.</p>
                        </div>
                    </div>
                    <form class="mt-6 space-y-6" @submit.prevent="submitStep3">
                        <!-- Departments -->
                        <div>
                            <h3 class="text-sm font-semibold text-ink">Departemen</h3>
                            <div class="mt-2 space-y-2">
                                <div v-for="(dept, i) in step3Form.departments" :key="i" class="flex gap-2">
                                    <input
                                        v-model="dept.name"
                                        type="text"
                                        class="flex-1 rounded-full border border-stone-300 bg-white px-5 py-2.5 text-sm text-ink outline-none focus:border-ink"
                                        placeholder="Nama departemen"
                                        required
                                    />
                                    <button
                                        v-if="step3Form.departments.length > 1"
                                        type="button"
                                        class="text-sm text-inkmuted hover:text-ink"
                                        @click="removeDepartment(i)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="mt-2 text-sm font-semibold text-ink hover:underline"
                                @click="addDepartment"
                            >
                                + Tambah Departemen
                            </button>
                        </div>

                        <!-- Positions -->
                        <div>
                            <h3 class="text-sm font-semibold text-ink">Jabatan</h3>
                            <div class="mt-2 space-y-2">
                                <div v-for="(pos, i) in step3Form.positions" :key="i" class="flex gap-2">
                                    <input
                                        v-model="pos.name"
                                        type="text"
                                        class="flex-1 rounded-full border border-stone-300 bg-white px-5 py-2.5 text-sm text-ink outline-none focus:border-ink"
                                        placeholder="Nama jabatan"
                                        required
                                    />
                                    <input
                                        v-model="pos.level"
                                        type="number"
                                        min="1"
                                        class="w-20 rounded-full border border-stone-300 bg-white px-3 py-2.5 text-sm text-ink outline-none focus:border-ink"
                                        placeholder="Level"
                                        required
                                    />
                                    <button
                                        v-if="step3Form.positions.length > 1"
                                        type="button"
                                        class="text-sm text-inkmuted hover:text-ink"
                                        @click="removePosition(i)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="mt-2 text-sm font-semibold text-ink hover:underline"
                                @click="addPosition"
                            >
                                + Tambah Jabatan
                            </button>
                        </div>

                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="flex-1 rounded-full border border-stone-300 px-4 py-3 text-sm font-semibold text-ink hover:bg-stone-50"
                                @click="step = 2"
                            >
                                Kembali
                            </button>
                            <button
                                type="submit"
                                class="flex-1 rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
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
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-ink text-white">
                            <UserPlus class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-display text-lg font-bold text-ink">Add Employees</h2>
                            <p class="text-sm text-inkmuted">Tambahkan karyawan Anda.</p>
                        </div>
                    </div>
                    <form class="mt-6 space-y-6" @submit.prevent="submitStep4">
                        <div v-for="(emp, i) in step4Form.employees" :key="i" class="rounded-xl border border-stone-200 p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-ink">Nama</label>
                                    <input
                                        v-model="emp.name"
                                        type="text"
                                        class="mt-1 block w-full rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink">Email</label>
                                    <input
                                        v-model="emp.email"
                                        type="email"
                                        class="mt-1 block w-full rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink">Departemen</label>
                                    <select
                                        v-model="emp.department_id"
                                        class="mt-1 block w-full rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                                        required
                                    >
                                        <option value="">Pilih</option>
                                        <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                            {{ dept.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink">Jabatan</label>
                                    <select
                                        v-model="emp.position_id"
                                        class="mt-1 block w-full rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                                        required
                                    >
                                        <option value="">Pilih</option>
                                        <option v-for="pos in positions" :key="pos.id" :value="pos.id">
                                            {{ pos.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink">Tanggal Masuk</label>
                                    <input
                                        v-model="emp.join_date"
                                        type="date"
                                        class="mt-1 block w-full rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm text-ink outline-none focus:border-ink"
                                        required
                                    />
                                </div>
                            </div>
                            <button
                                v-if="step4Form.employees.length > 1"
                                type="button"
                                class="mt-3 text-sm text-inkmuted hover:text-ink"
                                @click="removeEmployee(i)"
                            >
                                Hapus
                            </button>
                        </div>
                        <button
                            type="button"
                            class="text-sm font-semibold text-ink hover:underline"
                            @click="addEmployee"
                        >
                            + Tambah Karyawan
                        </button>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="flex-1 rounded-full border border-stone-300 px-4 py-3 text-sm font-semibold text-ink hover:bg-stone-50"
                                @click="step = 3"
                            >
                                Kembali
                            </button>
                            <button
                                type="submit"
                                class="flex-1 rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
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
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-ink text-white">
                            <Wallet class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="font-display text-lg font-bold text-ink">Setup Payroll</h2>
                            <p class="text-sm text-inkmuted">Komponen gaji akan dibuatkan secara otomatis.</p>
                        </div>
                    </div>
                    <div class="mt-6 space-y-4">
                        <div class="rounded-xl border border-stone-200 p-4">
                            <h3 class="font-semibold text-ink">Komponen Gaji yang Akan Dibuat:</h3>
                            <ul class="mt-2 space-y-1 text-sm text-inkmuted">
                                <li>Gaji Pokok (BASIC)</li>
                                <li>Tunjangan Hari Raya (THR)</li>
                                <li>Tunjangan Transportasi (TRANSPORT)</li>
                                <li>Tunjangan Makan (MEAL)</li>
                                <li>Uang Lembur (OVERTIME)</li>
                                <li>BPJS Kesehatan (1%)</li>
                                <li>BPJS Ketenagakerjaan (0.5%)</li>
                            </ul>
                        </div>
                    </div>
                    <form class="mt-6" @submit.prevent="submitStep5">
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="flex-1 rounded-full border border-stone-300 px-4 py-3 text-sm font-semibold text-ink hover:bg-stone-50"
                                @click="step = 4"
                            >
                                Kembali
                            </button>
                            <button
                                type="submit"
                                class="flex-1 rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
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
                <a href="/dashboard" class="text-sm font-semibold text-inkmuted hover:text-ink">
                    Lewati dan atur nanti
                </a>
            </div>
        </div>
    </div>
</template>
