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
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps({
    users: {
        type: Array as () => User[],
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User',
        href: '/user',
    },
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
});

function openCreateDialog() {
    form.reset();
    form.clearErrors();
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
        });
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
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>No</TableHead>
                            <TableHead>Nama</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(user, index) in users" :key="user.id">
                            <TableCell>{{ index + 1 }}</TableCell>
                            <TableCell>{{ user.name }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell>
                                <div class="flex space-x-2">
                                    <!-- Tombol edit untuk membuka dialog edit -->
                                    <Button variant="outline" size="sm" @click="openEditDialog(user)">Edit</Button>

                                    <AlertDialog>
                                        <AlertDialogTrigger asChild>
                                            <Button variant="destructive" size="sm" @click="userToDelete = user.id"> Hapus </Button>
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
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
            <CardFooter class="flex justify-between">
                <div class="text-muted-foreground text-sm">Menampilkan {{ users.length }} user</div>
            </CardFooter>
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
                        <div v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</div>
                    </div>

                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" placeholder="Masukkan email user" />
                        <div v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</div>
                    </div>


                    <div class="space-y-2">
                        <Label for="password">Password</Label>
                        <div class="relative">
                            <Input 
                                id="password" 
                                v-model="form.password" 
                                :type="showPassword ? 'text' : 'password'" 
                                placeholder="Masukkan password user"
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
                        <div v-if="form.errors.password" class="text-sm text-red-500">{{ form.errors.password }}</div>
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
