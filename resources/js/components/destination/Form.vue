<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { FileUpload } from '@/components/ui/file-upload';
import { MultiSelect, type Option } from '@/components/ui/multi-select';
import { LocationMap } from '@/components/ui/location-map';
import { RichTextEditor } from '@/components/ui/rich-text-editor';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Category, type Destination, type User } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { toast } from 'vue-sonner';

interface FormProps {
  destination?: Destination;
  categories: Category[];
  users: User[];
  isEditing: boolean;
}

const props = defineProps<FormProps>();

const pageTitle = computed(() => props.isEditing ? 'Edit Destination' : 'Add Destination');
const cardTitle = computed(() => props.isEditing ? 'Edit Destinasi' : 'Tambah Destinasi');
const cardDescription = computed(() => props.isEditing 
  ? 'Perbarui informasi destinasi wisata' 
  : 'Tambahkan destinasi wisata baru');
const submitLabel = computed(() => props.isEditing ? 'Perbarui' : 'Simpan');
const successMessage = computed(() => props.isEditing 
  ? 'Destinasi berhasil diperbarui' 
  : 'Destinasi berhasil ditambahkan');
const successDescription = computed(() => props.isEditing 
  ? 'Data destinasi telah diperbarui dengan sukses' 
  : 'Destinasi baru telah ditambahkan ke sistem');
const errorMessage = computed(() => props.isEditing 
  ? 'Gagal memperbarui destinasi' 
  : 'Gagal menambahkan destinasi');
const errorDescription = computed(() => props.isEditing 
  ? 'Terjadi kesalahan saat memperbarui data destinasi' 
  : 'Terjadi kesalahan saat menambahkan destinasi');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Destination',
        href: '/destination',
    },
    {
        title: pageTitle.value,
        href: props.isEditing ? `/destination/${props.destination?.id}/edit` : '/destination/create',
    },
];

// Initialize form with destination data or default values
const form = useForm({
    name: props.destination?.name || '',
    content: props.destination?.content || '',
    facility: props.destination?.facility || '',
    thumb_image: null as File | null,
    lat: props.destination?.lat || -6.200000, // Default to Indonesia's approximate coordinates
    lon: props.destination?.lon || 106.816666,
    address: props.destination?.address || '',
    opening_hours: props.destination?.operating_hours || '',
    pic_id: props.destination?.pic_id ? String(props.destination.pic_id) : '',
    published: props.destination?.published || false,
    categories: props.destination?.categories?.map(category => category.id) || [] as string[],
    _method: props.isEditing ? 'PUT' : 'POST',
});

// Initialize image preview from existing destination
const imagePreview = ref<string | null>(props.destination?.thumb_image || null);

function handleSubmit() {
    const url = props.isEditing ? `/destination/${props.destination?.id}` : '/destination';
    form.post(url, {
        // Process server-side validation errors
        onSuccess: () => {
            toast.success(successMessage.value, {
                description: successDescription.value,
            });
        },
        onError: (errors) => {
            console.error('Form errors:', errors);
            toast.error(errorMessage.value, {
                description: errorDescription.value,
            });
        },
    });
}

// Search term for category combobox
const searchTerm = ref('');

// Reset search term when selected categories change
watch(() => form.categories, () => {
    searchTerm.value = '';
}, { deep: true });

// Mengubah format kategori untuk digunakan oleh MultiSelect
const categoryOptions = computed<Option[]>(() => {
    return props.categories.map(category => ({
        id: category.id,
        label: category.name
    }));
});

// Location data binding with reusable component
const locationData = computed({
    get: () => ({ lat: form.lat, lon: form.lon }),
    set: (value: { lat: number; lon: number }) => {
        form.lat = value.lat;
        form.lon = value.lon;
    }
});
</script>

<template>
    <Head :title="pageTitle" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="m-4">
            <form @submit.prevent="handleSubmit">
                <CardHeader>
                    <CardTitle class="mb-2">{{ cardTitle }}</CardTitle>
                    <CardDescription>{{ cardDescription }}</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="font-medium text-lg">Informasi Dasar</h3>
                        
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Nama Destinasi</Label>
                                <Input id="name" v-model="form.name" type="text" placeholder="Masukkan nama destinasi" />
                                <div v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</div>
                            </div>

                            <div class="space-y-2">
                                <Label for="pic_id">Person in Charge (PIC)</Label>
                                <Select v-model="form.pic_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih PIC" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="user in users" :key="user.id" :value="user.id.toString()">
                                            {{ user.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <div v-if="form.errors.pic_id" class="text-sm text-red-500">{{ form.errors.pic_id }}</div>
                            </div>
                        </div>

                        <!-- Replace FilePond with our FileUpload component -->
                        <div class="space-y-2">
                            <Label for="thumb_image">Gambar Thumbnail</Label>
                            <FileUpload
                                v-model="form.thumb_image"
                                :existingPreview="imagePreview"
                                accept="image/png,image/jpeg,image/jpg"
                                :error="form.errors.thumb_image"
                                helpText="Unggah gambar untuk destinasi (format: JPG, JPEG, PNG, maks. 5MB)"
                            />
                        </div>

                        <!-- Menggunakan komponen MultiSelect yang reusable -->
                        <div class="space-y-2">
                            <Label for="categories">Kategori</Label>
                            <MultiSelect
                                v-model="form.categories"
                                :options="categoryOptions"
                                placeholder="Ketik kategori..."
                                :errorMessage="form.errors.categories"
                                emptyMessage="Tidak ada kategori yang tersedia"
                                groupLabel="Kategori"
                            />
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="space-y-4">
                        <h3 class="font-medium text-lg">Detail Konten</h3>
                        
                        <!-- Menggunakan komponen RichTextEditor yang baru dibuat -->
                        <div class="space-y-2">
                            <Label for="content">Deskripsi</Label>
                            <RichTextEditor 
                                v-model="form.content"
                                id="content"
                                label="Deskripsi"
                                placeholder="Masukkan deskripsi destinasi..."
                                :error="form.errors.content"
                                minHeight="200px"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="facility">Fasilitas</Label>
                            <Textarea id="facility" v-model="form.facility" rows="3" placeholder="Masukkan fasilitas yang tersedia" />
                            <div v-if="form.errors.facility" class="text-sm text-red-500">{{ form.errors.facility }}</div>
                        </div>
                    </div>

                    <!-- Using the reusable LocationMap component -->
                    <LocationMap 
                        v-model="locationData"
                        :errorLat="form.errors.lat"
                        :errorLon="form.errors.lon"
                    />

                    <!-- Location Details -->
                    <div class="space-y-4">
                        <h3 class="font-medium text-lg">Detail Lokasi</h3>
                        
                        <div class="space-y-2">
                            <Label for="address">Alamat</Label>
                            <Textarea id="address" v-model="form.address" rows="2" placeholder="Masukkan alamat lengkap destinasi" />
                            <div v-if="form.errors.address" class="text-sm text-red-500">{{ form.errors.address }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="opening_hours">Jam Operasional</Label>
                            <Input id="opening_hours" v-model="form.opening_hours" type="text" placeholder="Contoh: 08:00 - 17:00" />
                            <div v-if="form.errors.opening_hours" class="text-sm text-red-500">{{ form.errors.opening_hours }}</div>
                        </div>
                    </div>

                    <!-- Publishing Options -->
                    <div class="space-y-4">
                        <h3 class="font-medium text-lg">Opsi Publikasi</h3>
                        
                        <div class="flex items-center space-x-2">
                            <Checkbox id="published" v-model="form.published" />
                            <Label for="published">Publikasikan destinasi ini</Label>
                            <div v-if="form.errors.published" class="text-sm text-red-500">{{ form.errors.published }}</div>
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="mt-5 flex justify-end space-x-2">
                    <Link href="/destination">
                        <Button variant="outline">Kembali</Button>
                    </Link>
                    <Button type="submit" :disabled="form.processing">{{ submitLabel }}</Button>
                </CardFooter>
            </form>
        </Card>
    </AppLayout>
</template>
