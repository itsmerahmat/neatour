<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Testimonial, type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    testimonial: {
        type: Object as () => Testimonial,
        required: true
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Testimonial',
        href: '/testimonial',
    },
    {
        title: 'Detail',
        href: `/testimonial/${props.testimonial.id}`,
    }
];

// Function to render star rating
function renderStars(rating: number) {
    return '★'.repeat(rating) + '☆'.repeat(5 - rating);
}
</script>

<template>

    <Head title="Detail Testimonial" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="m-4">
            <CardHeader>
                <CardTitle class="mb-2">Detail Testimonial</CardTitle>
                <CardDescription>Informasi lengkap testimonial untuk destinasi wisata</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <h3 class="font-medium text-sm">Destinasi</h3>
                        <p v-if="testimonial.destination">{{ testimonial.destination.name }}</p>
                    </div>
                    <div class="space-y-2">
                        <h3 class="font-medium text-sm">Rating</h3>
                        <p class="text-yellow-500">{{ renderStars(testimonial.rating) }}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <h3 class="font-medium text-sm">Nama</h3>
                    <p>{{ testimonial.name || 'Anonymous' }}</p>
                </div>
                <div class="space-y-2">
                    <h3 class="font-medium text-sm">Komentar</h3>
                    <p>{{ testimonial.comment }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <h3 class="font-medium text-sm">Dibuat Pada</h3>
                        <p>{{ new Date(testimonial.created_at).toLocaleDateString('id-ID') }}</p>
                    </div>
                    <div class="space-y-2">
                        <h3 class="font-medium text-sm">Terakhir Diperbarui</h3>
                        <p>{{ new Date(testimonial.updated_at).toLocaleDateString('id-ID') }}</p>
                    </div>
                </div>
            </CardContent>
            <CardFooter class="mt-5 flex justify-end space-x-2">
                <Link href="/testimonial">
                    <Button variant="outline">Kembali</Button>
                </Link>
                <Link :href="`/testimonial/${testimonial.id}/edit`">
                    <Button variant="default">Edit</Button>
                </Link>
            </CardFooter>
        </Card>
    </AppLayout>
</template>