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
import AppLayout from '@/layouts/AppLayout.vue';
import { SharedData, Testimonial, type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { computed, ref } from 'vue';

// Import DataTable component
import { DataTable } from '@/components/datatable';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps<{
    testimonials: {
        current_page: number;
        data: Testimonial[];
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
        title: 'Testimonial',
        href: '/testimonial',
    },
];

// Menggunakan usePage dengan tipe SharedData untuk mendapatkan informasi user yang sedang login
const page = usePage<SharedData>();
const user = computed(() => page.props.auth.user);

// Define table columns
const columns = [
    { key: 'id', label: 'No', class: 'w-12 text-center', sortable: false },
    // { key: 'destination.name', label: 'Destinasi', sortable: true },
    { key: 'destination', label: 'Destinasi', sortable: true },
    { key: 'comment', label: 'Komentar', sortable: true },
    { key: 'rating', label: 'Rating', sortable: true },
];

// Handle delete functionality
const testimonialToDelete = ref<number | null>(null);

function handleDelete() {
    if (testimonialToDelete.value) {
        router.delete(`/testimonial/${testimonialToDelete.value}`, {
            onSuccess: () => {
                toast.success('Testimonial berhasil dihapus', {
                    description: 'Testimonial telah dihapus dari sistem',
                });
                testimonialToDelete.value = null;
            },
            onError: () => {
                toast.error('Gagal menghapus testimonial', {
                    description: 'Terjadi kesalahan saat menghapus testimonial',
                });
            },
            preserveScroll: true,
        });
    }
}

// Handle row actions from DataTable
function handleRowAction({ action, row }: { action: string; row: Testimonial }) {
    if (action === 'view') {
        router.visit(`/testimonial/${row.id}`);
    } else if (action === 'edit') {
        router.visit(`/testimonial/${row.id}/edit`);
    } else if (action === 'delete') {
        testimonialToDelete.value = row.id;
    }
}

// Function to render star rating
function renderStars(rating: number) {
    return '★'.repeat(rating) + '☆'.repeat(5 - rating);
}
</script>

<template>
    <Head title="Testimonial" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="m-4">
            <CardHeader class="flex flex-row items-center justify-between">
                <div>
                    <CardTitle class="mb-2">Daftar Testimonial</CardTitle>
                    <CardDescription>Kelola testimonial destinasi wisata di sini</CardDescription>
                </div>

                <!-- Tombol untuk menambah testimonial -->
                <Button variant="default" as-child v-if="user.role === 'superadmin'">
                    <Link href="/testimonial/create">Tambah Testimonial</Link>
                </Button>
            </CardHeader>
            <CardContent>
                <!-- Use DataTable instead of Table -->
                <DataTable
                    route="/testimonial"
                    :data="testimonials"
                    :columns="columns"
                    :filters="filters"
                    :searchable=true
                    search-placeholder="Cari testimonial..."
                    @row-action="handleRowAction"
                >
                    <!-- Custom cell rendering for specific columns -->
                    <template #cell="{ column, row, index }">

                        <!-- Custom rendering for ID column -->
                        <template v-if="column.key === 'id'">
                            {{ index + 1 }}
                        </template>

                        <!-- Custom rendering for destination column -->
                        <template v-else-if="column.key === 'destination'">
                            {{ row.destination ? row.destination.name : 'Tidak ada destinasi' }}
                        </template>

                        <!-- Custom rendering for rating column -->
                        <template v-if="column.key === 'rating'">
                            <span class="text-yellow-500">{{ renderStars(row.rating) }}</span>
                        </template>
                    </template>
                    
                    <!-- Actions slot for buttons -->
                    <template #actions="{ row }">
                        <div class="flex space-x-2">
                            <!-- View button -->
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="`/testimonial/${row.id}`">Lihat</Link>
                            </Button>
                            
                            <!-- Edit button -->
                            <Button variant="outline" size="sm" as-child v-if="user.role === 'superadmin'">
                                <Link :href="`/testimonial/${row.id}/edit`">Edit</Link>
                            </Button>

                            <!-- Delete button with confirmation -->
                            <AlertDialog v-if="user.role === 'superadmin'">
                                <AlertDialogTrigger asChild>
                                    <Button variant="destructive" size="sm" @click="testimonialToDelete = row.id">
                                        Hapus
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Apakah Anda yakin?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            Tindakan ini tidak dapat dibatalkan. Ini akan menghapus testimonial secara permanen dari database.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel @click="testimonialToDelete = null">Batal</AlertDialogCancel>
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