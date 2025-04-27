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
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';
import { Destination, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref } from 'vue';

// Import DataTable component
import { DataTable } from '@/components/datatable';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps<{
    destinations: {
        current_page: number;
        data: Destination[];
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
        title: 'Destination',
        href: '/destination',
    },
];

// Define table columns
const columns = [
    { key: 'id', label: 'No', class: 'w-12 text-center', sortable: false },
    { key: 'name', label: 'Nama Destinasi', sortable: true },
    { key: 'pic', label: 'PIC', sortable: true },
    // { key: 'categories', label: 'Kategori', sortable: false },
    { key: 'published', label: 'Status', sortable: true },
    { key: 'thumb_image', label: 'Gambar', sortable: false },
];

// Handle delete functionality
const destinationToDelete = ref<string | null>(null);

function handleDelete() {
    if (destinationToDelete.value) {
        router.delete(`/destination/${destinationToDelete.value}`, {
            onSuccess: () => {
                toast.success('Destinasi berhasil dihapus', {
                    description: 'Destinasi telah dihapus dari sistem',
                });
                destinationToDelete.value = null;
            },
            onError: () => {
                toast.error('Gagal menghapus destinasi', {
                    description: 'Terjadi kesalahan saat menghapus destinasi',
                });
            },
            preserveScroll: true,
        });
    }
}

// Handle row actions from DataTable
function handleRowAction({ action, row }: { action: string; row: Destination }) {
    if (action === 'view') {
        router.visit(`/destination/${row.id}`);
    } else if (action === 'edit') {
        router.visit(`/destination/${row.id}/edit`);
    } else if (action === 'delete') {
        destinationToDelete.value = row.id;
    }
}

</script>

<template>
    <Head title="Destination" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="m-4">
            <CardHeader class="flex flex-row items-center justify-between">
                <div>
                    <CardTitle class="mb-2">Daftar Destinasi</CardTitle>
                    <CardDescription>Kelola destinasi wisata di sini</CardDescription>
                </div>

                <!-- Tombol untuk menambahkan destinasi baru -->
                <Button variant="default" as-child>
                    <Link href="/destination/create">Tambah Destinasi</Link>
                </Button>
            </CardHeader>
            <CardContent>
                <!-- Use DataTable component instead of Table -->
                <DataTable
                    route="/destination"
                    :data="destinations"
                    :columns="columns"
                    :filters="filters"
                    :searchable=true
                    search-placeholder="Cari destinasi..."
                    @row-action="handleRowAction"
                >
                    <!-- Custom cell rendering for specific columns -->
                    <template #cell="{ column, row, index }">
                        <!-- Custom rendering for ID column -->
                        <template v-if="column.key === 'id'">
                            {{ index + 1 }}
                        </template>

                        <!-- Custom rendering for pic.name column -->
                        <template v-else-if="column.key === 'pic'">
                            {{ row.pic ? row.pic.name : 'Tidak ada PIC' }}
                        </template>

                        <!-- Custom rendering for categories column -->
                        <template v-else-if="column.key === 'categories'">
                            <div class="flex flex-wrap gap-1">
                                <Badge v-for="category in row.categories" :key="category.id" variant="outline" class="mr-1">
                                    {{ category.name }}
                                </Badge>
                                <span v-if="!row.categories || row.categories.length === 0" class="text-gray-400">
                                    Tidak ada kategori
                                </span>
                            </div>
                        </template>

                        <!-- Custom rendering for published column -->
                        <template v-else-if="column.key === 'published'">
                            <Badge :variant="row.published ? 'default' : 'secondary'">
                                {{ row.published ? 'Published' : 'Draft' }}
                            </Badge>
                        </template>

                        <!-- Custom rendering for image column -->
                        <template v-else-if="column.key === 'thumb_image'">
                            <img v-if="row.thumb_image" :src="row.thumb_image" class="h-10 w-10 rounded-md object-cover" alt="Thumbnail" />
                            <span v-else class="text-gray-400">No image</span>
                        </template>
                    </template>
                    
                    <!-- Actions slot for buttons -->
                    <template #actions="{ row }">
                        <div class="flex space-x-2">
                            <!-- View button -->
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="`/destination/${row.id}`">Lihat</Link>
                            </Button>

                            <!-- Edit button -->
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="`/destination/${row.id}/edit`">Edit</Link>
                            </Button>

                            <!-- Delete button with confirmation -->
                            <AlertDialog>
                                <AlertDialogTrigger asChild>
                                    <Button variant="destructive" size="sm" @click="destinationToDelete = row.id">
                                        Hapus
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Apakah Anda yakin?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            Tindakan ini tidak dapat dibatalkan. Ini akan menghapus destinasi secara permanen dari database.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel @click="destinationToDelete = null">Batal</AlertDialogCancel>
                                        <AlertDialogAction @click="handleDelete">Hapus</AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </div>
                    </template>
                </DataTable>
            </CardContent>
        </Card>
    </AppLayout>
</template>