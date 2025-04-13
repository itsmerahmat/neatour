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
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Destination, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps({
    destinations: {
        type: Array as () => Destination[],
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Destination',
        href: '/destination',
    },
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
        });
    }
}

function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(date);
}
</script>

<template>
    <Head title="Destinations" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="m-4">
            <CardHeader class="flex flex-row items-center justify-between">
                <div>
                    <CardTitle class="mb-2">Daftar Destinasi</CardTitle>
                    <CardDescription>Kelola destinasi wisata di sini</CardDescription>
                </div>

                <!-- Tombol untuk halaman tambah destinasi -->
                <Link href="/destination/create">
                    <Button variant="default">Tambah Destinasi</Button>
                </Link>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>No</TableHead>
                            <TableHead>Nama</TableHead>
                            <TableHead>Gambar</TableHead>
                            <TableHead>Kategori</TableHead>
                            <TableHead>PIC</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(destination, index) in destinations" :key="destination.id">
                            <TableCell>{{ index + 1 }}</TableCell>
                            <TableCell>{{ destination.name }}</TableCell>
                            <TableCell>
                                <img v-if="destination.thumb_image" :src="destination.thumb_image" class="h-12 w-20 rounded-md object-cover" alt="Destination thumbnail" />
                                <span v-else class="text-gray-400">No image</span>
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-wrap gap-1">
                                    <Badge v-for="category in destination.categories" :key="category.id" variant="secondary">
                                        {{ category.name }}
                                    </Badge>
                                    <span v-if="!destination.categories || destination.categories.length === 0" class="text-gray-400">No categories</span>
                                </div>
                            </TableCell>
                            <TableCell>{{ destination.pic?.name || 'N/A' }}</TableCell>
                            <TableCell>
                                <Badge :variant="destination.published ? 'default' : 'secondary'">
                                    {{ destination.published ? 'Published' : 'Draft' }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex space-x-2">
                                    <Link :href="`/destination/${destination.id}`">
                                        <Button variant="outline" size="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" /><circle cx="12" cy="12" r="3" /></svg>
                                        </Button>
                                    </Link>

                                    <Link :href="`/destination/${destination.id}/edit`">
                                        <Button variant="outline" size="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" /><path d="m15 5 4 4" /></svg>
                                        </Button>
                                    </Link>

                                    <AlertDialog>
                                        <AlertDialogTrigger asChild>
                                            <Button variant="destructive" size="icon" @click="destinationToDelete = destination.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18" /><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" /><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" /><line x1="10" x2="10" y1="11" y2="17" /><line x1="14" x2="14" y1="11" y2="17" /></svg>
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
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
            <CardFooter class="flex justify-between">
                <div class="text-muted-foreground text-sm">Menampilkan {{ destinations.length }} destinasi</div>
            </CardFooter>
        </Card>
    </AppLayout>
</template>