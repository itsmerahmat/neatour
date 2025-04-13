<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Destination } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps({
    destination: {
        type: Object as () => Destination,
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Destination',
        href: '/destination',
    },
    {
        title: props.destination.name,
        href: `/destination/${props.destination.id}`,
    },
];

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
    <Head :title="destination.name" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="m-4 space-y-6">
            <!-- Header Section with Image -->
            <div class="relative h-60 overflow-hidden rounded-xl lg:h-80">
                <img v-if="destination.thumb_image" :src="destination.thumb_image" class="h-full w-full object-cover" :alt="destination.name" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6">
                    <h1 class="text-3xl font-bold text-white">{{ destination.name }}</h1>
                    <div class="mt-2 flex items-center space-x-2">
                        <Badge :variant="destination.published ? 'default' : 'secondary'">
                            {{ destination.published ? 'Published' : 'Draft' }}
                        </Badge>
                        <div class="flex flex-wrap gap-1">
                            <Badge v-for="category in destination.categories" :key="category.id" variant="outline" class="bg-white/20 text-white">
                                {{ category.name }}
                            </Badge>
                        </div>
                    </div>
                </div>
                <div class="absolute right-4 top-4 space-x-2">
                    <Link :href="`/destination/${destination.id}/edit`">
                        <Button>Edit Destination</Button>
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Deskripsi</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="prose max-w-none">
                                <p class="whitespace-pre-line">{{ destination.content }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Fasilitas</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="prose max-w-none">
                                <p class="whitespace-pre-line">{{ destination.facility }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Testimonials Section -->
                    <Card v-if="destination.testimonials && destination.testimonials.length > 0">
                        <CardHeader>
                            <CardTitle>Ulasan Pengunjung</CardTitle>
                            <CardDescription>Apa kata mereka tentang destinasi ini</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-for="testimonial in destination.testimonials" :key="testimonial.id" class="rounded-lg border p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="font-medium">{{ testimonial.user?.name || 'Anonymous' }}</div>
                                    </div>
                                    <div class="flex items-center text-yellow-500">
                                        <span class="mr-1">{{ testimonial.rating }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-2 text-gray-600">{{ testimonial.comment }}</p>
                                <div class="mt-2 text-sm text-gray-500">{{ formatDate(testimonial.created_at) }}</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Details Card -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div>
                                    <h3 class="font-medium text-sm">Person in Charge</h3>
                                    <p>{{ destination.pic?.name || 'N/A' }}</p>
                                </div>
                                <div>
                                    <h3 class="font-medium text-sm">Lokasi GPS</h3>
                                    <p>{{ destination.lat }}, {{ destination.lon }}</p>
                                </div>
                                <div>
                                    <h3 class="font-medium text-sm">Tanggal Dibuat</h3>
                                    <p>{{ formatDate(destination.created_at) }}</p>
                                </div>
                                <div>
                                    <h3 class="font-medium text-sm">Terakhir Diupdate</h3>
                                    <p>{{ formatDate(destination.updated_at) }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Map Card -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Peta Lokasi</CardTitle>
                        </CardHeader>
                        <CardContent class="p-0">
                            <div class="aspect-square bg-gray-200 flex items-center justify-center">
                                <iframe
                                    width="100%"
                                    height="100%"
                                    frameborder="0"
                                    scrolling="no"
                                    marginheight="0"
                                    marginwidth="0"
                                    :src="`https://www.openstreetmap.org/export/embed.html?bbox=${destination.lon - 0.01},${destination.lat - 0.01},${destination.lon + 0.01},${destination.lat + 0.01}&layer=mapnik&marker=${destination.lat},${destination.lon}`"
                                ></iframe>
                            </div>
                        </CardContent>
                        <CardFooter>
                            <a 
                                :href="`https://www.openstreetmap.org/?mlat=${destination.lat}&mlon=${destination.lon}#map=15/${destination.lat}/${destination.lon}`"
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="text-sm text-blue-600 hover:underline"
                            >
                                Lihat di OpenStreetMap
                            </a>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>