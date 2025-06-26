<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

// Import DataTable component
import { DataTable } from '@/components/datatable';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps<{
    users: {
        current_page: number;
        data: User[];
        from: number;
        last_page: number;
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
    filters: {
        search: string;
        perPage: number;
        sortField: string;
        sortDirection: string;
    }
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User',
        href: '/user',
    },
];

// Define table columns
const columns = [
    { key: 'id', label: 'No', class: 'w-12 text-center', sortable: false },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'role', label: 'Role', sortable: true },
    { key: 'phone_number', label: 'No. Telepon', sortable: true },
];

// Handle delete functionality
const userToDelete = ref<number | null>(null);

// Toggle password visibility
const showPassword = ref(false);
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

function handleDelete() {
    if (userToDelete.value) {
        router.delete(`/user/${userToDelete.value}`, {
            onSuccess: () => {
                toast.success('User berhasil dihapus', {
                    description: 'User telah dihapus dari sistem',
                });
                userToDelete.value = null;
            },
            onError: () => {
                toast.error('Gagal menghapus user', {
                    description: 'Terjadi kesalahan saat menghapus user',
                });
            },
            preserveScroll: true,
        });
    }
}

// Handle form dialog functionality
const isDialogOpen = ref(false);
const isEditing = ref(false);
const selectedUser = ref<User | null>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'admin', // Default to admin
    phone_number: '',
});

function openCreateDialog() {
    form.reset();
    form.clearErrors();
    form.role = 'admin'; // Set default role
    isEditing.value = false;
    selectedUser.value = null;
    isDialogOpen.value = true;
}

function openEditDialog(user: User) {
    form.reset();
    form.clearErrors();
    form.name = user.name;
    form.email = user.email;
    form.password = ''; // Password field should be empty for editing
    form.role = user.role;
    form.phone_number = user.phone_number || '';
    isDialogOpen.value = true;
    selectedUser.value = user;
    isEditing.value = true;
}

function handleSubmit() {    
    if (isEditing.value && selectedUser.value) {
        form.put(`/user/${selectedUser.value.id}`, {
            onSuccess: () => {
                isDialogOpen.value = false;
                toast.success('User berhasil diperbarui', {
                    description: 'Data user telah diperbarui dengan sukses',
                });
            },
            onError: () => {
                toast.error('Gagal memperbarui user', {
                    description: 'Terjadi kesalahan saat memperbarui data user',
                });
            },
            preserveScroll: true,
        });
    } else {        
        form.post('/user', {
            onSuccess: () => {
                isDialogOpen.value = false;
                toast.success('User berhasil ditambahkan', {
                    description: 'User baru telah ditambahkan ke sistem',
                });
            },
            onError: () => {
                toast.error('Gagal menambahkan user', {
                    description: 'Terjadi kesalahan saat menambahkan user',
                });
            },
            preserveScroll: true,
        });
    }
}

// Handle row actions from DataTable
function handleRowAction({ action, row }: { action: string; row: User }) {
    if (action === 'edit') {
        openEditDialog(row);
    } else if (action === 'delete') {
        userToDelete.value = row.id;
    }
}

const dialogTitle = computed(() => (isEditing.value ? 'Edit User' : 'Tambah User'));
</script>

<template>
    <Head title="User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="m-4">
            <CardHeader class="flex flex-row items-center justify-between">
                <div>
                    <CardTitle class="mb-2">Daftar User</CardTitle>
                    <CardDescription>Kelola user pengelola wisata di sini</CardDescription>
                </div>

                <!-- Tombol untuk membuka dialog tambah user -->
                <Button variant="default" @click="openCreateDialog">Tambah User</Button>
            </CardHeader>
            <CardContent>
                <!-- Use DataTable component instead of Table -->
                <DataTable
                    route="/user"
                    :data="users"
                    :columns="columns"
                    :filters="filters"
                    :searchable=true
                    search-placeholder="Cari user..."
                    @row-action="handleRowAction"
                >
                    <!-- Custom cell rendering for specific columns -->
                    <template #cell="{ column, index, row }">
                        <!-- Custom rendering for ID column -->
                        <template v-if="column.key === 'id'">
                            {{ index + 1 }}
                        </template>
                        <!-- Custom rendering for role column to show proper label -->
                        <template v-else-if="column.key === 'role'">
                            <span 
                                :class="[
                                    'px-2 py-1 rounded text-xs font-medium', 
                                    row.role === 'superadmin' 
                                        ? 'bg-purple-100 text-purple-800' 
                                        : 'bg-blue-100 text-blue-800'
                                ]"
                            >
                                {{ row.role }}
                            </span>
                        </template>
                    </template>
                    
                    <!-- Actions slot for buttons -->
                    <template #actions="{ row }">
                        <div class="flex space-x-2">
                            <!-- Edit button -->
                            <Button variant="outline" size="sm" @click="openEditDialog(row)">Edit</Button>

                            <!-- Delete button with confirmation -->
                            <AlertDialog>
                                <AlertDialogTrigger asChild>
                                    <Button variant="destructive" size="sm" @click="userToDelete = row.id">
                                        Hapus
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Apakah Anda yakin?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            Tindakan ini tidak dapat dibatalkan. Ini akan menghapus user secara permanen dari database.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel @click="userToDelete = null">Batal</AlertDialogCancel>
                                        <AlertDialogAction @click="handleDelete">Hapus</AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </div>
                    </template>
                </DataTable>
            </CardContent>
        </Card>

        <!-- Dialog untuk form tambah/edit user -->
        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        Masukkan data user di bawah ini. Klik {{ isEditing ? 'Perbarui' : 'Simpan' }} saat selesai.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4 py-4">
                    <div class="space-y-2">
                        <Label for="name">Nama User</Label>
                        <Input id="name" v-model="form.name" type="text" placeholder="Masukkan nama user" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" placeholder="Masukkan email user" />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="space-y-2">
                        <Label for="password">Password</Label>
                        <div class="relative">
                            <Input 
                                id="password" 
                                v-model="form.password" 
                                :type="showPassword ? 'text' : 'password'" 
                                :placeholder="isEditing ? 'Biarkan kosong jika tidak ingin mengubah password' : 'Masukkan password user'"
                            />
                            <button 
                                type="button" 
                                @click="togglePasswordVisibility" 
                                class="absolute right-3 top-1/2 -translate-y-1/2"
                            >
                                <Eye v-if="!showPassword" class="h-4 w-4 text-gray-500" />
                                <EyeOff v-else class="h-4 w-4 text-gray-500" />
                                <span class="sr-only">{{ showPassword ? 'Sembunyikan password' : 'Tampilkan password' }}</span>
                            </button>
                        </div>
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="space-y-2">
                        <Label for="role">Role</Label>
                        <Select v-model="form.role">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="admin">Admin</SelectItem>
                                <SelectItem value="superadmin">Superadmin</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.role" />
                    </div>

                    <div class="space-y-2">
                        <Label for="phone_number">Nomor Telepon</Label>
                        <Input id="phone_number" v-model="form.phone_number" type="text" placeholder="Masukkan nomor telepon" />
                        <InputError :message="form.errors.phone_number" />
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="isDialogOpen = false">Batal</Button>
                    <Button type="submit" @click="handleSubmit" :disabled="form.processing">
                        {{ isEditing ? 'Perbarui' : 'Simpan' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
