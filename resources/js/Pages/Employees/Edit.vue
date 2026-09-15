<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle, Loader2, User, Briefcase, Camera, X } from 'lucide-vue-next';
import { ref } from 'vue';

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
    photo: null,
});

const getFieldError = (field) => form.errors[field] || null;

const photoPreview = ref(null);

const handlePhoto = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.photo = file;
        photoPreview.value = URL.createObjectURL(file);
    }
};

const removePhoto = () => {
    form.photo = null;
    photoPreview.value = null;
};

const submit = () => {
    form.put(`/employees/${props.employee.id}`, {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit Karyawan" />

    <AppLayout title="Edit Karyawan">
        <div class="w-full py-4 sm:mx-auto sm:max-w-3xl sm:py-8">
            <Link href="/employees" class="group inline-flex items-center gap-1.5 text-sm font-bold text-muted-app transition-colors hover:text-coral">
                <ArrowLeft class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" />
                Kembali
            </Link>

            <h1 class="mt-4 font-display text-xl font-extrabold tracking-tight text-ink-app sm:text-2xl lg:text-3xl">Edit Karyawan</h1>
            <p class="mt-1 text-sm text-muted-app">{{ employee.nik }} - {{ employee.name }}</p>

            <form class="mt-6 space-y-5 sm:mt-8 sm:space-y-6" @submit.prevent="submit">
                <!-- Data Diri -->
                <div class="overflow-hidden rounded-2xl border border-outline-app/40 bg-white shadow-soft">
                    <div class="flex items-center gap-3 border-b border-outline-app/40 bg-cream/60 px-4 py-3 sm:px-6 sm:py-4">
                        <div class="flex size-9 items-center justify-center rounded-xl bg-coral/10 text-coral">
                            <User class="h-4.5 w-4.5" />
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-ink-app sm:text-base">Data Diri</h2>
                            <p class="text-[11px] text-muted-app sm:text-xs">Informasi pribadi karyawan</p>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 gap-x-4 gap-y-3.5 sm:grid-cols-2 sm:gap-x-5 sm:gap-y-4">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wide text-muted-app sm:text-xs">Nama Lengkap <span class="text-coral">*</span></label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    :class="['mt-1.5 block w-full rounded-xl border bg-white px-3 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors sm:px-3.5', getFieldError('name') ? 'border-coral' : 'border-outline-app/50 focus:border-coral focus:ring-2 focus:ring-coral/10']"
                                />
                                <p v-if="getFieldError('name')" class="mt-1.5 text-xs font-medium text-coral-deep">{{ getFieldError('name') }}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wide text-muted-app sm:text-xs">Email Pribadi</label>
                                <input
                                    v-model="form.email_personal"
                                    type="email"
                                    :class="['mt-1.5 block w-full rounded-xl border bg-white px-3 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors sm:px-3.5', getFieldError('email_personal') ? 'border-coral' : 'border-outline-app/50 focus:border-coral focus:ring-2 focus:ring-coral/10']"
                                />
                                <p v-if="getFieldError('email_personal')" class="mt-1.5 text-xs font-medium text-coral-deep">{{ getFieldError('email_personal') }}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wide text-muted-app sm:text-xs">Telepon</label>
                                <input
                                    v-model="form.phone"
                                    type="text"
                                    :class="['mt-1.5 block w-full rounded-xl border bg-white px-3 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors sm:px-3.5', getFieldError('phone') ? 'border-coral' : 'border-outline-app/50 focus:border-coral focus:ring-2 focus:ring-coral/10']"
                                />
                                <p v-if="getFieldError('phone')" class="mt-1.5 text-xs font-medium text-coral-deep">{{ getFieldError('phone') }}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wide text-muted-app sm:text-xs">Tanggal Lahir</label>
                                <input
                                    v-model="form.birth_date"
                                    type="date"
                                    :class="['mt-1.5 block w-full rounded-xl border bg-white px-3 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors sm:px-3.5', getFieldError('birth_date') ? 'border-coral' : 'border-outline-app/50 focus:border-coral focus:ring-2 focus:ring-coral/10']"
                                />
                                <p v-if="getFieldError('birth_date')" class="mt-1.5 text-xs font-medium text-coral-deep">{{ getFieldError('birth_date') }}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wide text-muted-app sm:text-xs">Jenis Kelamin</label>
                                <select
                                    v-model="form.gender"
                                    :class="['mt-1.5 block w-full rounded-xl border bg-white px-3 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors sm:px-3.5', getFieldError('gender') ? 'border-coral' : 'border-outline-app/50 focus:border-coral focus:ring-2 focus:ring-coral/10']"
                                >
                                    <option value="">Pilih</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                                <p v-if="getFieldError('gender')" class="mt-1.5 text-xs font-medium text-coral-deep">{{ getFieldError('gender') }}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wide text-muted-app sm:text-xs">Status Karyawan <span class="text-coral">*</span></label>
                                <select
                                    v-model="form.status"
                                    required
                                    :class="['mt-1.5 block w-full rounded-xl border bg-white px-3 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors sm:px-3.5', getFieldError('status') ? 'border-coral' : 'border-outline-app/50 focus:border-coral focus:ring-2 focus:ring-coral/10']"
                                >
                                    <option value="permanent">Permanent</option>
                                    <option value="contract">Kontrak</option>
                                    <option value="probation">Percobaan</option>
                                </select>
                                <p v-if="getFieldError('status')" class="mt-1.5 text-xs font-medium text-coral-deep">{{ getFieldError('status') }}</p>
                            </div>
                        </div>

                        <div class="mt-4 sm:mt-5">
                            <label class="block text-[11px] font-bold uppercase tracking-wide text-muted-app sm:text-xs">Alamat</label>
                            <textarea
                                v-model="form.address"
                                rows="3"
                                :class="['mt-1.5 block w-full rounded-xl border bg-white px-3 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors sm:px-3.5', getFieldError('address') ? 'border-coral' : 'border-outline-app/50 focus:border-coral focus:ring-2 focus:ring-coral/10']"
                            />
                            <p v-if="getFieldError('address')" class="mt-1.5 text-xs font-medium text-coral-deep">{{ getFieldError('address') }}</p>
                        </div>

                        <div class="mt-4 sm:mt-5">
                            <label class="block text-[11px] font-bold uppercase tracking-wide text-muted-app sm:text-xs">Foto Profil</label>
                            <div class="mt-2 flex items-center gap-4 sm:gap-5">
                                <div v-if="photoPreview || employee.photo" class="relative">
                                    <img :src="photoPreview || `/storage/${employee.photo}`" class="size-20 rounded-2xl object-cover ring-2 ring-outline-app/30 sm:size-24" />
                                    <button
                                        type="button"
                                        class="absolute -top-1.5 -right-1.5 flex size-6 items-center justify-center rounded-full bg-coral text-white shadow-md transition-transform hover:scale-110"
                                        @click="removePhoto"
                                    >
                                        <X class="h-3 w-3" />
                                    </button>
                                </div>
                                <label class="cursor-pointer">
                                    <div class="flex flex-col items-center gap-2 rounded-2xl border-2 border-dashed border-outline-app/50 bg-cream/40 px-5 py-4 transition-colors hover:border-coral/40 hover:bg-coral/5 sm:px-6 sm:py-5">
                                        <div class="flex size-10 items-center justify-center rounded-xl bg-coral/10 text-coral sm:size-12">
                                            <Camera class="h-4 w-4 sm:h-5 sm:w-5" />
                                        </div>
                                        <div class="text-center">
                                            <span class="text-xs font-bold text-ink-app sm:text-sm">Pilih Foto</span>
                                            <p class="mt-0.5 text-[10px] text-muted-app sm:text-[11px]">JPG, PNG. Maks 2MB</p>
                                        </div>
                                    </div>
                                    <input type="file" accept="image/*" class="hidden" @change="handlePhoto" />
                                </label>
                            </div>
                            <p v-if="getFieldError('photo')" class="mt-1.5 text-xs font-medium text-coral-deep">{{ getFieldError('photo') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pekerjaan -->
                <div class="overflow-hidden rounded-2xl border border-outline-app/40 bg-white shadow-soft">
                    <div class="flex items-center gap-3 border-b border-outline-app/40 bg-cream/60 px-4 py-3 sm:px-6 sm:py-4">
                        <div class="flex size-9 items-center justify-center rounded-xl bg-coral/10 text-coral">
                            <Briefcase class="h-4.5 w-4.5" />
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-ink-app sm:text-base">Pekerjaan</h2>
                            <p class="text-[11px] text-muted-app sm:text-xs">Informasi jabatan dan departemen</p>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 gap-x-4 gap-y-3.5 sm:grid-cols-2 sm:gap-x-5 sm:gap-y-4">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wide text-muted-app sm:text-xs">Departemen <span class="text-coral">*</span></label>
                                <select
                                    v-model="form.department_id"
                                    required
                                    :class="['mt-1.5 block w-full rounded-xl border bg-white px-3 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors sm:px-3.5', getFieldError('department_id') ? 'border-coral' : 'border-outline-app/50 focus:border-coral focus:ring-2 focus:ring-coral/10']"
                                >
                                    <option value="">Pilih Departemen</option>
                                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                                </select>
                                <p v-if="getFieldError('department_id')" class="mt-1.5 text-xs font-medium text-coral-deep">{{ getFieldError('department_id') }}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wide text-muted-app sm:text-xs">Jabatan <span class="text-coral">*</span></label>
                                <select
                                    v-model="form.position_id"
                                    required
                                    :class="['mt-1.5 block w-full rounded-xl border bg-white px-3 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors sm:px-3.5', getFieldError('position_id') ? 'border-coral' : 'border-outline-app/50 focus:border-coral focus:ring-2 focus:ring-coral/10']"
                                >
                                    <option value="">Pilih Jabatan</option>
                                    <option v-for="pos in positions" :key="pos.id" :value="pos.id">{{ pos.name }}</option>
                                </select>
                                <p v-if="getFieldError('position_id')" class="mt-1.5 text-xs font-medium text-coral-deep">{{ getFieldError('position_id') }}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wide text-muted-app sm:text-xs">Tanggal Masuk <span class="text-coral">*</span></label>
                                <input
                                    v-model="form.join_date"
                                    type="date"
                                    required
                                    :class="['mt-1.5 block w-full rounded-xl border bg-white px-3 py-2.5 text-sm font-semibold text-ink-app outline-none transition-colors sm:px-3.5', getFieldError('join_date') ? 'border-coral' : 'border-outline-app/50 focus:border-coral focus:ring-2 focus:ring-coral/10']"
                                />
                                <p v-if="getFieldError('join_date')" class="mt-1.5 text-xs font-medium text-coral-deep">{{ getFieldError('join_date') }}</p>
                            </div>
                            <div class="flex items-center gap-3 pt-6">
                                <label class="flex items-center gap-2">
                                    <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-outline-app/50 text-ink-app focus:ring-coral" />
                                    <span class="text-sm font-bold text-ink-app">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <Link
                        href="/employees"
                        class="inline-flex w-full items-center justify-center rounded-xl border border-outline-app/50 bg-white px-5 py-2.5 text-sm font-semibold text-ink-app transition-colors hover:bg-cream/60 sm:w-auto"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-coral px-6 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-coral-deep hover:shadow-lg disabled:opacity-50 sm:w-auto"
                        :disabled="form.processing"
                    >
                        <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                        <CheckCircle v-else-if="form.recentlySuccessful" class="size-4" />
                        <span>{{ form.recentlySuccessful ? 'Tersimpan!' : 'Perbarui Karyawan' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
