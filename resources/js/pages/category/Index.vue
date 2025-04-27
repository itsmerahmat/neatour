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
import { FileUpload } from '@/components/ui/file-upload';
import AppLayout from '@/layouts/AppLayout.vue';
import { Category, type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

// Import the DataTable component
import { DataTable } from '@/components/datatable';

// Props now include pagination, search, and sorting
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps<{
    categories: {
        current_page: number;
        data: Category[];
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
        title: 'Category',
        href: '/category',
    },
];

// Define table columns
const columns = [
    { key: 'id', label: 'No', class: 'w-12 text-center', sortable: false },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'img', label: 'Gambar', sortable: false },
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
            preserveScroll: true,
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
            preserveScroll: true,
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
            preserveScroll: true,
        });
    }
}

// Handle row actions from DataTable
function handleRowAction({ action, row }: { action: string; row: Category }) {
    if (action === 'edit') {
        openEditDialog(row);
    } else if (action === 'delete') {
        categoryToDelete.value = row.id;
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
                <!-- Use the DataTable component -->
                <DataTable
                    route="/category"
                    :data="categories"
                    :columns="columns"
                    :filters="filters"
                    :searchable=true
                    search-placeholder="Cari kategori..."
                    @row-action="handleRowAction"
                >
                    <!-- Custom cell rendering for specific columns -->
                    <template #cell="{ column, row, index }">
                        <!-- Custom rendering for ID column -->
                        <template v-if="column.key === 'id'">
                            {{ (categories?.from || 0) + index }}
                        </template>
                        
                        <!-- Custom rendering for image column -->
                        <template v-else-if="column.key === 'img'">
                            <img v-if="row.img" :src="row.img" class="h-10 w-10 rounded-md object-cover" alt="Category image" />
                            <span v-else class="text-gray-400">No image</span>
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
                                    <Button variant="destructive" size="sm" @click="categoryToDelete = row.id">
                                        Hapus
                                    </Button>
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
                    </template>

                </DataTable>
            </CardContent>
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

                    <FileUpload
                        id="img"
                        v-model="form.img"
                        label="Gambar Kategori"
                        :existingPreview="imagePreview"
                        accept="image/*"
                        :error="form.errors.img"
                        helpText="Unggah gambar untuk kategori (format: JPG, JPEG, PNG)"
                    />
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
