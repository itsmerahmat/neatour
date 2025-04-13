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
import { Category, type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps({
    categories: {
        type: Array as () => Category[],
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Category',
        href: '/category',
    },
];

// Handle delete functionality
const categoryToDelete = ref<number | null>(null);

function handleDelete() {
    if (categoryToDelete.value) {
        router.delete(`/category/${categoryToDelete.value}`, {
            onSuccess: () => {
                toast.success('Kategori berhasil dihapus', {
                    description: 'Kategori telah dihapus dari sistem',
                });
                categoryToDelete.value = null;
            },
            onError: () => {
                toast.error('Gagal menghapus kategori', {
                    description: 'Terjadi kesalahan saat menghapus kategori',
                });
            },
        });
    }
}

// Handle form dialog functionality
const isDialogOpen = ref(false);
const isEditing = ref(false);
const selectedCategory = ref<Category | null>(null);
const imagePreview = ref<string | null>(null);

const form = useForm({
    name: '',
    img: null as File | null,
});

function handleFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        form.img = file;

        // Generate image preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
}

function openCreateDialog() {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
    selectedCategory.value = null;
    imagePreview.value = null;
    isDialogOpen.value = true;
}

function openEditDialog(category: Category) {
    form.reset();
    form.clearErrors();
    form.name = category.name;
    // Don't set form.img here since it's now a file input
    isEditing.value = true;
    selectedCategory.value = category;

    // Set the existing image as preview if it exists
    if (category.img) {
        imagePreview.value = category.img;
    } else {
        imagePreview.value = null;
    }

    isDialogOpen.value = true;
}

function handleSubmit() {
    if (isEditing.value && selectedCategory.value) {
        // For edit, we need to use post with _method: 'put' for file uploads
        form.post(`/category/${selectedCategory.value.id}?_method=PUT`, {
            onSuccess: () => {
                isDialogOpen.value = false;
                imagePreview.value = null;
                toast.success('Kategori berhasil diperbarui', {
                    description: 'Data kategori telah diperbarui dengan sukses',
                });
            },
            onError: () => {
                toast.error('Gagal memperbarui kategori', {
                    description: 'Terjadi kesalahan saat memperbarui data kategori',
                });
            },
        });
    } else {
        form.post('/category', {
            onSuccess: () => {
                isDialogOpen.value = false;
                imagePreview.value = null;
                toast.success('Kategori berhasil ditambahkan', {
                    description: 'Kategori baru telah ditambahkan ke sistem',
                });
            },
            onError: () => {
                toast.error('Gagal menambahkan kategori', {
                    description: 'Terjadi kesalahan saat menambahkan kategori',
                });
            },
        });
    }
}

const dialogTitle = computed(() => (isEditing.value ? 'Edit Kategori' : 'Tambah Kategori'));

// Reset image preview when dialog closes
watch(isDialogOpen, (newValue) => {
    if (!newValue) {
        imagePreview.value = null;
    }
});
</script>

<template>
    <Head title="Category" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="m-4">
            <CardHeader class="flex flex-row items-center justify-between">
                <div>
                    <CardTitle class="mb-2">Daftar Kategori</CardTitle>
                    <CardDescription>Kelola kategori wisata di sini</CardDescription>
                </div>

                <!-- Tombol untuk membuka dialog tambah kategori -->
                <Button variant="default" @click="openCreateDialog">Tambah Kategori</Button>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>No</TableHead>
                            <TableHead>Nama</TableHead>
                            <TableHead>Gambar</TableHead>
                            <TableHead>Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(category, index) in categories" :key="category.id">
                            <TableCell>{{ index + 1 }}</TableCell>
                            <TableCell>{{ category.name }}</TableCell>
                            <TableCell>
                                <img v-if="category.img" :src="category.img" class="h-10 w-10 rounded-md object-cover" alt="Category image" />
                                <span v-else class="text-gray-400">No image</span>
                            </TableCell>
                            <TableCell>
                                <div class="flex space-x-2">
                                    <!-- Tombol edit untuk membuka dialog edit -->
                                    <Button variant="outline" size="sm" @click="openEditDialog(category)">Edit</Button>

                                    <AlertDialog>
                                        <AlertDialogTrigger asChild>
                                            <Button variant="destructive" size="sm" @click="categoryToDelete = category.id"> Hapus </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle>Apakah Anda yakin?</AlertDialogTitle>
                                                <AlertDialogDescription>
                                                    Tindakan ini tidak dapat dibatalkan. Ini akan menghapus kategori secara permanen dari database.
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel @click="categoryToDelete = null">Batal</AlertDialogCancel>
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
                <div class="text-muted-foreground text-sm">Menampilkan {{ categories.length }} kategori</div>
            </CardFooter>
        </Card>

        <!-- Dialog untuk form tambah/edit kategori -->
        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        Masukkan data kategori di bawah ini. Klik {{ isEditing ? 'Perbarui' : 'Simpan' }} saat selesai.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4 py-4">
                    <div class="space-y-2">
                        <Label for="name">Nama Kategori</Label>
                        <Input id="name" v-model="form.name" type="text" placeholder="Masukkan nama kategori" />
                        <div v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</div>
                    </div>

                    <div class="grid w-full items-center gap-2">
                        <Label for="img">Gambar Kategori</Label>
                        <Input id="img" type="file" accept="image/*" @change="handleFileChange" />
                        <div v-if="form.errors.img" class="text-sm text-red-500">{{ form.errors.img }}</div>

                        <!-- Image Preview -->
                        <div v-if="imagePreview" class="mt-2">
                            <p class="text-muted-foreground mb-2 text-sm">Preview:</p>
                            <div class="flex items-center justify-center">
                                <div class="relative h-50 w-50 overflow-hidden rounded-md border">
                                    <img :src="imagePreview" class="h-full w-full object-cover" alt="Preview" />
                                </div>
                            </div>
                        </div>
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
