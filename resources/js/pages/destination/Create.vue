<script setup lang="ts">
import { type Category, type User } from '@/types';
// import DestinationForm from './Form.vue';
import DestinationForm from '@/components/destination/Form.vue';

defineProps({
    categories: {
        type: Array as () => Category[],
        required: true,
    },
    users: {
        type: Array as () => User[],
        required: true,
    },
});
</script>

<template>
    <DestinationForm 
        :categories="categories" 
        :users="users" 
        :isEditing="false"
    />
</template>