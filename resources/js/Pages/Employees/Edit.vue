<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    employee: Object,
    departments: Array,
    positions: Array,
});

const form = useForm({
    name: props.employee.name,
    email_personal: props.employee.email_personal || '',
    phone: props.employee.phone || '',
    birth_date: props.employee.birth_date || '',
    gender: props.employee.gender || '',
    address: props.employee.address || '',
    department_id: props.employee.department_id,
    position_id: props.employee.position_id,
    join_date: props.employee.join_date,
    status: props.employee.status,
    is_active: props.employee.is_active,
});

const submit = () => form.put(`/employees/${props.employee.id}`);
</script>

<template>
    <Head title="Edit Karyawan" />

    <div class="min-h-screen bg-stone-50">
        <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                <Link href="/employees" class="inline-flex items-center gap-1 text-sm font-semibold text-inkmuted hover:text-ink">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
            </div>
        </header>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">Edit Karyawan</h1>
                <p class="mt-1 text-sm text-inkmuted">{{ employee.nik }} - {{ employee.name }}</p>

                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <h2 class="font-display text-lg font-bold text-ink">Data Diri</h2>
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-ink">Nama Lengkap *</label>
                                <input v-model="form.name" type="text" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Email Pribadi</label>
                                <input v-model="form.email_personal" type="email" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Telepon</label>
                                <input v-model="form.phone" type="text" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Tanggal Lahir</label>
                                <input v-model="form.birth_date" type="date" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Jenis Kelamin</label>
                                <select v-model="form.gender" class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink">
                                    <option value="">Pilih</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Status Karyawan *</label>
                                <select v-model="form.status" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink">
                                    <option value="permanent">Permanent</option>
                                    <option value="contract">Kontrak</option>
                                    <option value="probation">Percobaan</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-ink">Alamat</label>
                            <textarea v-model="form.address" rows="3" class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                        </div>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <h2 class="font-display text-lg font-bold text-ink">Pekerjaan</h2>
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-ink">Departemen *</label>
                                <select v-model="form.department_id" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink">
                                    <option value="">Pilih Departemen</option>
                                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Jabatan *</label>
                                <select v-model="form.position_id" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink">
                                    <option value="">Pilih Jabatan</option>
                                    <option v-for="pos in positions" :key="pos.id" :value="pos.id">{{ pos.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Tanggal Masuk *</label>
                                <input v-model="form.join_date" type="date" required class="mt-1.5 block w-full rounded-full border border-stone-300 bg-white px-5 py-3 text-ink outline-none focus:border-ink" />
                            </div>
                            <div class="flex items-center gap-3 pt-6">
                                <label class="flex items-center gap-2">
                                    <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-stone-300 text-ink focus:ring-ink" />
                                    <span class="text-sm font-medium text-ink">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-ink px-4 py-3 text-sm font-semibold text-white hover:bg-moss-700 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Perbarui Karyawan
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
