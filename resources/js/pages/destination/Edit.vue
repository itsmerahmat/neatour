<script setup lang="ts">
import { type Category, type Destination, type User } from '@/types';
// import DestinationForm from './Form.vue';
import DestinationForm from '@/components/destination/Form.vue';

defineProps({
    destination: {
        type: Object as () => Destination,
        required: true,
    },
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
        :destination="destination" 
        :categories="categories" 
        :users="users" 
        :isEditing="true"
    />
</template>