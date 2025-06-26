<script setup lang="ts">
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';

// Define props for the component
interface RichTextEditorProps {
    modelValue?: string;
    placeholder?: string;
    error?: string;
    minHeight?: string;
}

const props = withDefaults(defineProps<RichTextEditorProps>(), {
    modelValue: '',
    placeholder: '',
    error: '',
    minHeight: '150px',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

// Define Quill editor options
const quillOptions = {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],
            ['link'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'list': 'check' }],
            [{ 'indent': '-1' }, { 'indent': '+1' }],          // outdent/indent
            [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
            [{ 'font': [] }],
            [{ 'align': [] }],
            ['clean']                                         // remove formatting button
        ]
    },
    placeholder: props.placeholder
};

// Local content state
const content = ref(props.modelValue);

// Watch for changes from parent
watch(() => props.modelValue, (newVal) => {
    if (newVal !== content.value) {
        content.value = newVal;
    }
}, { immediate: true });

// Watch local changes and emit to parent
watch(() => content.value, (newVal) => {
    emit('update:modelValue', newVal);
});
</script>

<template>
    <div class="space-y-0">
        <QuillEditor v-model:content="content" :options="quillOptions" contentType="html" :class="[
            'min-h-[150px]',
            error ? 'border-red-500' : 'border-input'
        ]" :style="{ minHeight: minHeight }" />
        <InputError v-if="error" :message="error" />
    </div>
</template>

<style scoped>
/* Custom styling for the Quill editor */
:deep(.ql-editor) {
    min-height: v-bind('minHeight');
}
</style>