<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Category, type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table';
import { Button } from '@/components/ui/button';

const props = defineProps({
  categories: {
    type: Array as () => Category[],
    required: true
  }
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Category',
    href: '/category',
  },
];

function deleteCategory(id: number) {
//   if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
//     router.delete(`/category/${id}`);
//   }
}
</script>

<template>
  <Head title="Category" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <Card class="m-4">
      <CardHeader class="flex flex-row items-center justify-between">
        <div>
          <CardTitle>Daftar Kategori</CardTitle>
          <CardDescription>Kelola kategori produk di sini</CardDescription>
        </div>
        <Link href="/category/create">
          <Button variant="default">Tambah Kategori</Button>
        </Link>
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
            <TableRow v-for="category in categories" :key="category.id">
              <TableCell>{{ category.id }}</TableCell>
              <TableCell>{{ category.name }}</TableCell>
              <TableCell>
                <img v-if="category.img" :src="category.img" class="h-8 w-8 object-cover" alt="Category image" />
                <span v-else class="text-gray-400">No image</span>
              </TableCell>
              <TableCell>
                <div class="flex space-x-2">
                  <Link :href="`/category/${category.id}/edit`">
                    <Button variant="outline" size="sm">Edit</Button>
                  </Link>
                  <Button 
                    variant="destructive" 
                    size="sm" 
                    @click="deleteCategory(category.id)"
                  >
                    Hapus
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
      <CardFooter class="flex justify-between">
        <div class="text-sm text-muted-foreground">
          Menampilkan {{ categories.length }} kategori
        </div>
      </CardFooter>
    </Card>
  </AppLayout>
</template>