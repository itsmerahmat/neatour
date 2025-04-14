<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { 
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Destination, Testimonial } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { computed } from 'vue';

interface FormProps {
  testimonial?: Testimonial;
  destinations: Destination[];
  isEditing: boolean;
}

const props = defineProps<FormProps>();

const pageTitle = computed(() => props.isEditing ? 'Edit Testimonial' : 'Add Testimonial');
const cardTitle = computed(() => props.isEditing ? 'Edit Testimonial' : 'Tambah Testimonial');
const cardDescription = computed(() => props.isEditing 
  ? 'Perbarui data testimonial destinasi wisata' 
  : 'Tambahkan testimonial baru untuk destinasi wisata');
const submitLabel = computed(() => props.isEditing ? 'Perbarui' : 'Simpan');
const successMessage = computed(() => props.isEditing 
  ? 'Testimonial berhasil diperbarui' 
  : 'Testimonial berhasil ditambahkan');
const successDescription = computed(() => props.isEditing 
  ? 'Data testimonial telah diperbarui dengan sukses' 
  : 'Testimonial baru telah ditambahkan ke sistem');
const errorMessage = computed(() => props.isEditing 
  ? 'Gagal memperbarui testimonial' 
  : 'Gagal menambahkan testimonial');
const errorDescription = computed(() => props.isEditing 
  ? 'Terjadi kesalahan saat memperbarui data testimonial' 
  : 'Terjadi kesalahan saat menambahkan testimonial');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Testimonial',
        href: '/testimonial',
    },
    {
        title: pageTitle.value,
        href: props.isEditing && props.testimonial ? `/testimonial/${props.testimonial.id}/edit` : '/testimonial/create',
    }
];

// Initialize form with testimonial data or default values
const form = useForm({
    destination_id: props.testimonial?.destination_id || '',
    comment: props.testimonial?.comment || '',
    rating: props.testimonial?.rating || 5,
    _method: props.isEditing ? 'PUT' : 'POST',
});

function handleSubmit() {
    const url = props.isEditing && props.testimonial 
        ? `/testimonial/${props.testimonial.id}` 
        : '/testimonial';
    
    form.post(url, {
        onSuccess: () => {
            toast.success(successMessage.value, {
                description: successDescription.value,
            });
        },
        onError: () => {
            toast.error(errorMessage.value, {
                description: errorDescription.value,
            });
        },
    });
}

const ratings = [
    { value: 1, label: '★☆☆☆☆' },
    { value: 2, label: '★★☆☆☆' },
    { value: 3, label: '★★★☆☆' },
    { value: 4, label: '★★★★☆' },
    { value: 5, label: '★★★★★' },
];
</script>

<template>
    <Head :title="pageTitle" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="m-4">
            <CardHeader>
                <CardTitle class="mb-2">{{ cardTitle }}</CardTitle>
                <CardDescription>{{ cardDescription }}</CardDescription>
            </CardHeader>
            <form @submit.prevent="handleSubmit">
                <CardContent class="space-y-4">
                    <div class="space-y-2">
                        <Label for="destination_id">Destinasi</Label>
                        <Select v-model="form.destination_id">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Pilih destinasi" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem 
                                    v-for="destination in destinations" 
                                    :key="destination.id" 
                                    :value="destination.id"
                                >
                                    {{ destination.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div v-if="form.errors.destination_id" class="text-sm text-red-500">
                            {{ form.errors.destination_id }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="rating">Rating</Label>
                        <Select v-model="form.rating">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Pilih rating" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem 
                                    v-for="rating in ratings" 
                                    :key="rating.value" 
                                    :value="rating.value"
                                >
                                    {{ rating.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div v-if="form.errors.rating" class="text-sm text-red-500">
                            {{ form.errors.rating }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="comment">Komentar</Label>
                        <Input 
                            id="comment"
                            v-model="form.comment"
                            placeholder="Masukkan komentar testimonial"
                        />
                        <div v-if="form.errors.comment" class="text-sm text-red-500">
                            {{ form.errors.comment }}
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="mt-5 flex justify-end space-x-2">
                    <Link href="/testimonial">
                        <Button variant="outline">Kembali</Button>
                    </Link>
                    <Button type="submit" :disabled="form.processing">{{ submitLabel }}</Button>
                </CardFooter>
            </form>
        </Card>
    </AppLayout>
</template>