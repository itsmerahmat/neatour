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
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Testimonial, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref } from 'vue';

const props = defineProps({
    testimonials: {
        type: Array as () => Testimonial[],
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Testimonial',
        href: '/testimonial',
    },
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
        });
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
                <Button variant="default" as-child>
                    <Link href="/testimonial/create">Tambah Testimonial</Link>
                </Button>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>No</TableHead>
                            <TableHead>Destinasi</TableHead>
                            <TableHead>Komentar</TableHead>
                            <TableHead>Rating</TableHead>
                            <TableHead>Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(testimonial, index) in testimonials" :key="testimonial.id">
                            <TableCell>{{ index + 1 }}</TableCell>
                            <TableCell>{{ testimonial.destination?.name }}</TableCell>
                            <TableCell>{{ testimonial.comment }}</TableCell>
                            <TableCell>
                                <span class="text-yellow-500">{{ renderStars(testimonial.rating) }}</span>
                            </TableCell>
                            <TableCell>
                                <div class="flex space-x-2">
                                    <!-- Tombol lihat detail -->
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="`/testimonial/${testimonial.id}`">Lihat</Link>
                                    </Button>
                                    
                                    <!-- Tombol edit -->
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="`/testimonial/${testimonial.id}/edit`">Edit</Link>
                                    </Button>

                                    <!-- Dialog konfirmasi hapus -->
                                    <AlertDialog>
                                        <AlertDialogTrigger asChild>
                                            <Button variant="destructive" size="sm" @click="testimonialToDelete = testimonial.id">
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
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
            <CardFooter class="flex justify-between">
                <div class="text-muted-foreground text-sm">Menampilkan {{ testimonials.length }} testimonial</div>
            </CardFooter>
        </Card>
    </AppLayout>
</template>