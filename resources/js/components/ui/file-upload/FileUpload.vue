<script setup lang="ts">
import { ref, watch } from 'vue';
import { Input } from '../input';
import { Label } from '../label';

interface FileUploadProps {
  label?: string;
  id?: string;
  modelValue: File | null;
  existingPreview?: string | null;
  accept?: string;
  error?: string;
  helpText?: string;
}

const props = withDefaults(defineProps<FileUploadProps>(), {
  label: 'Upload File',
  id: 'file-upload',
  accept: 'image/*',
  existingPreview: null,
  error: '',
  helpText: ''
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: File | null): void;
}>();

const localPreview = ref<string | null>(props.existingPreview);

// Watch for external changes to existingPreview
watch(() => props.existingPreview, (newValue) => {
  if (newValue) {
    localPreview.value = newValue;
  }
});

function handleFileChange(e: Event) {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    const file = target.files[0];
    emit('update:modelValue', file);

    // Generate image preview
    const reader = new FileReader();
    reader.onload = (e) => {
      localPreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  } else {
    emit('update:modelValue', null);
  }
}
</script>

<template>
  <div class="space-y-2">
    <Label :for="id" v-if="label">{{ label }}</Label>
    <Input :id="id" type="file" :accept="accept" @change="handleFileChange" />
    <p v-if="helpText" class="text-sm text-muted-foreground">{{ helpText }}</p>
    <div v-if="error" class="text-sm text-red-500">{{ error }}</div>

    <!-- Image Preview -->
    <div v-if="localPreview" class="mt-2">
      <p class="text-muted-foreground mb-2 text-sm">Preview:</p>
      <div class="flex items-center justify-center">
        <div class="relative h-40 w-40 overflow-hidden rounded-md border">
          <img :src="localPreview" class="h-full w-full object-cover" alt="Preview" />
        </div>
      </div>
    </div>
  </div>
</template>