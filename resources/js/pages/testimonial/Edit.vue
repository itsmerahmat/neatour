<script setup lang="ts">
import { type Destination, type Testimonial } from '@/types';
import TestimonialForm from '@/components/testimonial/Form.vue';

defineProps({
    testimonial: {
        type: Object as () => Testimonial,
        required: true
    },
    destinations: {
        type: Array as () => Destination[],
        required: true
    }
});
</script>

<template>
    <TestimonialForm 
        :testimonial="testimonial" 
        :destinations="destinations" 
        :isEditing="true"
    />
</template>