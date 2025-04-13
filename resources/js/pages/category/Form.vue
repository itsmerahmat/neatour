<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    category: {
        type: Object,
        default: null,
    },
    isEditing: {
        type: Boolean,
        default: false,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Category',
        href: '/category',
    },
    {
        title: props.isEditing ? 'Edit Kategori' : 'Tambah Kategori',
        href: props.isEditing ? `/category/${props.category?.id}/edit` : '/category/create',
    },
];

const form = useForm({
    name: props.category?.name || '',
    img: props.category?.img || '',
});

const pageTitle = computed(() => (props.isEditing ? 'Edit Kategori' : 'Tambah Kategori'));

function handleSubmit() {
    if (props.isEditing) {
        form.put(`/category/${props.category.id}`);
    } else {
        form.post('/category');
    }
}
</script>

<template>
    <Head :title="pageTitle" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="mx-auto max-w-2xl">
            <CardHeader>
                <CardTitle>{{ pageTitle }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div>
                        <Label for="name">Nama Kategori</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="Masukkan nama kategori"
                            :error="form.errors.name"
                            class="mt-1 block w-full"
                        />
                        <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <Label for="img">URL Gambar</Label>
                        <Input
                            id="img"
                            v-model="form.img"
                            type="text"
                            placeholder="Masukkan URL gambar (opsional)"
                            :error="form.errors.img"
                            class="mt-1 block w-full"
                        />
                        <div v-if="form.errors.img" class="mt-1 text-sm text-red-500">{{ form.errors.img }}</div>
                    </div>
                </form>
            </CardContent>
            <CardFooter class="flex justify-end space-x-4">
                <Button variant="outline">Batal</Button>
                <Button type="button" @click="handleSubmit" :disabled="form.processing">
                    {{ props.isEditing ? 'Perbarui' : 'Simpan' }}
                </Button>
            </CardFooter>
        </Card>
    </AppLayout>
</template>
