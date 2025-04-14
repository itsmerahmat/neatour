<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Category, type Destination, type User } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { ComboboxAnchor, ComboboxContent, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxItemIndicator, ComboboxLabel, ComboboxRoot, ComboboxTrigger, ComboboxViewport, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText, TagsInputRoot } from 'reka-ui';
import { ChevronDown, X } from 'lucide-vue-next';

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
    lat: props.destination?.lat || 0,
    lon: props.destination?.lon || 0,
    pic_id: props.destination?.pic_id ? String(props.destination.pic_id) : '',
    published: props.destination?.published || false,
    categories: props.destination?.categories?.map(category => category.id) || [] as string[],
    _method: props.isEditing ? 'PUT' : 'POST',
});

const imagePreview = ref<string | null>(props.destination?.thumb_image || null);

function handleFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        form.thumb_image = file;

        // Generate image preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
}

function handleSubmit() {
    const url = props.isEditing ? `/destination/${props.destination?.id}` : '/destination';
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

// Search term for category combobox
const searchTerm = ref('');

// Reset search term when selected categories change
watch(() => form.categories, () => {
    searchTerm.value = '';
}, { deep: true });

// Category options for combobox
const categoryOptions = computed(() => {
    return props.categories.map(category => ({
        id: category.id,
        label: category.name
    }));
});

// Filtered category options
const filteredCategoryOptions = computed(() => {
    if (!searchTerm.value) {
        return categoryOptions.value.filter(option => !form.categories.includes(option.id));
    }
    
    const search = searchTerm.value.toLowerCase();
    return categoryOptions.value.filter(option => 
        !form.categories.includes(option.id) && 
        option.label.toLowerCase().includes(search)
    );
});

// Get category label by ID
function getCategoryLabel(id: string): string {
    const category = props.categories.find(cat => cat.id === Number(id));
    return category ? category.name : id;
}
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

                        <div class="space-y-2">
                            <Label for="thumb_image">Gambar Thumbnail</Label>
                            <Input id="thumb_image" type="file" accept="image/*" @change="handleFileChange" />
                            <div v-if="form.errors.thumb_image" class="text-sm text-red-500">{{ form.errors.thumb_image }}</div>

                            <!-- Image Preview -->
                            <div v-if="imagePreview" class="mt-2">
                                <p class="text-muted-foreground mb-2 text-sm">Preview:</p>
                                <div class="flex items-center justify-center">
                                    <div class="relative h-40 w-60 overflow-hidden rounded-md border">
                                        <img :src="imagePreview" class="h-full w-full object-cover" alt="Preview" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="categories">Kategori</Label>
                            <div class="relative">
                                <ComboboxRoot
                                    v-model="form.categories"
                                    v-model:search-term="searchTerm"
                                    multiple
                                    class="relative"
                                >
                                    <ComboboxAnchor class="w-full inline-flex items-center justify-between rounded-lg p-2 text-[13px] leading-none gap-[5px] bg-white text-foreground shadow-sm border hover:bg-muted/50 focus:shadow-[0_0_0_2px] focus:shadow-ring outline-none">
                                        <TagsInputRoot
                                            v-slot="{ modelValue: tags }"
                                            v-model="form.categories"
                                            delimiter=""
                                            class="flex gap-2 items-center rounded-lg flex-wrap"
                                        >
                                            <TagsInputItem
                                                v-for="categoryId in tags"
                                                :key="String(categoryId)"
                                                :value="categoryId"
                                                class="flex items-center justify-center gap-2 text-white bg-primary rounded px-2 py-1"
                                            >
                                                <TagsInputItemText class="text-sm">{{ getCategoryLabel(String(categoryId)) }}</TagsInputItemText>
                                                <TagsInputItemDelete class="hover:bg-primary-foreground/10 rounded">
                                                    <X class="h-3.5 w-3.5" />
                                                </TagsInputItemDelete>
                                            </TagsInputItem>

                                            <ComboboxInput as-child>
                                                <TagsInputInput
                                                    placeholder="Ketik kategori..."
                                                    class="text-xs focus:outline-none flex-1 rounded bg-transparent px-1"
                                                    @keydown.enter.prevent
                                                />
                                            </ComboboxInput>
                                        </TagsInputRoot>

                                        <ComboboxTrigger>
                                             <ChevronDown class="h-3.5 w-3.5" />
                                        </ComboboxTrigger>
                                    </ComboboxAnchor>

                                    <ComboboxContent class="absolute z-50 w-full mt-2 bg-white overflow-hidden rounded shadow-md will-change-[opacity,transform] data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade">
                                        <ComboboxViewport class="p-1">
                                            <ComboboxEmpty class="text-muted-foreground text-xs font-medium text-center py-2">
                                                Tidak ada kategori yang tersedia
                                            </ComboboxEmpty>

                                            <ComboboxGroup>
                                                <ComboboxLabel class="px-2 text-xs leading-6 text-muted-foreground">
                                                    Kategori
                                                </ComboboxLabel>

                                                <ComboboxItem
                                                    v-for="option in filteredCategoryOptions"
                                                    :key="option.id"
                                                    class="text-sm leading-none text-foreground rounded flex items-center h-8 pr-9 pl-8 relative select-none data-[disabled]:text-muted-foreground data-[disabled]:pointer-events-none data-[highlighted]:outline-none data-[highlighted]:bg-primary data-[highlighted]:text-primary-foreground"
                                                    :value="option.id"
                                                >
                                                    <ComboboxItemIndicator class="absolute left-2 flex items-center justify-center">
                                                        <X class="h-3.5 w-3.5" />
                                                    </ComboboxItemIndicator>
                                                    <span>{{ option.label }}</span>
                                                </ComboboxItem>
                                            </ComboboxGroup>
                                        </ComboboxViewport>
                                    </ComboboxContent>
                                </ComboboxRoot>
                                <div v-if="form.errors.categories" class="text-sm text-red-500">{{ form.errors.categories }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="space-y-4">
                        <h3 class="font-medium text-lg">Detail Konten</h3>
                        
                        <div class="space-y-2">
                            <Label for="content">Deskripsi</Label>
                            <Textarea id="content" v-model="form.content" rows="5" placeholder="Masukkan deskripsi destinasi" />
                            <div v-if="form.errors.content" class="text-sm text-red-500">{{ form.errors.content }}</div>
                        </div>

                        <div class="space-y-2">
                            <Label for="facility">Fasilitas</Label>
                            <Textarea id="facility" v-model="form.facility" rows="3" placeholder="Masukkan fasilitas yang tersedia" />
                            <div v-if="form.errors.facility" class="text-sm text-red-500">{{ form.errors.facility }}</div>
                        </div>
                    </div>

                    <!-- Location Section -->
                    <div class="space-y-4">
                        <h3 class="font-medium text-lg">Lokasi</h3>
                        
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="lat">Latitude</Label>
                                <Input id="lat" v-model="form.lat" type="number" step="any" placeholder="Contoh: -7.797068" />
                                <div v-if="form.errors.lat" class="text-sm text-red-500">{{ form.errors.lat }}</div>
                            </div>

                            <div class="space-y-2">
                                <Label for="lon">Longitude</Label>
                                <Input id="lon" v-model="form.lon" type="number" step="any" placeholder="Contoh: 110.370529" />
                                <div v-if="form.errors.lon" class="text-sm text-red-500">{{ form.errors.lon }}</div>
                            </div>
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