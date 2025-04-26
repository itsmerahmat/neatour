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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { FileUpload } from '@/components/ui/file-upload';
import AppLayout from '@/layouts/AppLayout.vue';
import { Category, type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { ChevronUp, ChevronDown, Search } from 'lucide-vue-next';

// Custom debounce function implementation
function useDebounce<T extends (...args: any[]) => any>(fn: T, wait: number) {
    let timeout: ReturnType<typeof setTimeout> | null = null;
    
    return function(...args: Parameters<T>) {
        if (timeout !== null) {
            clearTimeout(timeout);
        }
        
        timeout = setTimeout(() => {
            fn(...args);
            timeout = null;
        }, wait);
    };
}

// Props now include pagination, search, and sorting
const props = defineProps<{
    categories: {
        data: Category[];
        links: any[];
        meta: {
            current_page: number;
            from: number;
            last_page: number;
            links: any[];
            path: string;
            per_page: number;
            to: number;
            total: number;
        }
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

// Server-side table functionality
const search = ref(props.filters.search);
const perPage = ref(props.filters.perPage.toString());
const sortField = ref(props.filters.sortField);
const sortDirection = ref(props.filters.sortDirection);

const perPageOptions = [
    { label: '10 per halaman', value: '10' },
    { label: '25 per halaman', value: '25' },
    { label: '50 per halaman', value: '50' },
    { label: '100 per halaman', value: '100' },
];

// Using our custom debounce function instead of Lodash
const performSearch = () => {
    router.get('/category', {
        search: search.value,
        perPage: perPage.value,
        sortField: sortField.value,
        sortDirection: sortDirection.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};
const debouncedSearch = useDebounce(performSearch, 300);

// Watch for search input changes
watch(search, () => {
    debouncedSearch();
});

// Handle per page change
function handlePerPageChange(value: string) {
    perPage.value = value;
    router.get('/category', {
        search: search.value,
        perPage: perPage.value,
        sortField: sortField.value,
        sortDirection: sortDirection.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

// Handle sorting
function sort(field: string) {
    sortDirection.value = field === sortField.value 
        ? sortDirection.value === 'asc' 
            ? 'desc' 
            : 'asc'
        : 'asc';
    sortField.value = field;

    router.get('/category', {
        search: search.value,
        perPage: perPage.value,
        sortField: sortField.value,
        sortDirection: sortDirection.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
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
                <!-- Search and per page controls -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-4 space-y-2 sm:space-y-0">
                    <div class="relative w-full sm:w-64">
                        <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-500" />
                        <Input
                            v-model="search"
                            placeholder="Cari kategori..."
                            class="pl-8 w-full"
                        />
                    </div>
                    <div>
                        <Select v-model="perPage" @update:modelValue="String(handlePerPageChange)">
                            <SelectTrigger class="w-[180px]">
                                <SelectValue placeholder="Pilih jumlah per halaman" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="option in perPageOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
                
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-12 text-center">No</TableHead>
                            <TableHead @click="sort('name')" class="cursor-pointer">
                                <div class="flex items-center">
                                    Nama
                                    <ChevronUp v-if="sortField === 'name' && sortDirection === 'asc'" class="h-4 w-4 ml-1" />
                                    <ChevronDown v-else-if="sortField === 'name' && sortDirection === 'desc'" class="h-4 w-4 ml-1" />
                                </div>
                            </TableHead>
                            <TableHead>Gambar</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(category, index) in categories?.data || []" :key="category.id">
                            <TableCell class="text-center">{{ (categories?.meta?.from || 0) + index }}</TableCell>
                            <TableCell>{{ category.name }}</TableCell>
                            <TableCell>
                                <img v-if="category.img" :src="category.img" class="h-10 w-10 rounded-md object-cover" alt="Category image" />
                                <span v-else class="text-gray-400">No image</span>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex space-x-2 justify-end">
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
                        <TableRow v-if="!categories?.data?.length">
                            <TableCell colspan="4" class="text-center py-8">
                                Tidak ada data kategori yang ditemukan
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                
                <!-- Pagination controls - Added safety checks -->
                <div v-if="categories?.meta?.last_page > 1" class="flex items-center justify-between border-t px-4 py-4 sm:px-6 mt-4">
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ categories?.meta?.from || 0 }}</span>
                                sampai
                                <span class="font-medium">{{ categories?.meta?.to || 0 }}</span>
                                dari
                                <span class="font-medium">{{ categories?.meta?.total || 0 }}</span>
                                kategori
                            </p>
                        </div>
                        <div v-if="categories?.meta?.links">
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <Link
                                    v-for="(link, i) in categories.meta.links"
                                    :key="i"
                                    v-if="link.url && i !== 0 && i !== categories.meta.links.length - 1"
                                    :href="link.url"
                                    :class="[
                                        link.active 
                                            ? 'z-10 bg-primary text-white focus-visible:outline-primary' 
                                            : 'text-gray-900 hover:bg-gray-50 focus:outline-offset-0',
                                        'relative inline-flex items-center px-4 py-2 text-sm font-medium focus:z-20'
                                    ]"
                                    preserve-scroll
                                >
                                    {{ link.label }}
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </CardContent>
            <CardFooter class="flex justify-between">
                <div class="text-muted-foreground text-sm">
                    Menampilkan {{ categories?.data?.length || 0 }} dari {{ categories?.meta?.total || 0 }} kategori
                </div>
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
