<script setup lang="ts">
import { type Destination } from '@/types';
import TestimonialForm from '@/components/testimonial/Form.vue';

defineProps({
    destinations: {
        type: Array as () => Destination[],
        required: true
    }
});
</script>

<template>
    <TestimonialForm 
        :destinations="destinations" 
        :isEditing="false"
    />
</template>