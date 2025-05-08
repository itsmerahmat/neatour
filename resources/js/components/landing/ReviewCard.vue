<script setup lang="ts">
defineProps({
    id: {
        type: Number,
        required: true
    },
    name: {
        type: String,
        default: "Anonymous"
    },
    rating: {
        type: Number,
        required: true
    },
    comment: {
        type: String,
        required: true
    },
    avatarUrl: {
        type: String,
        default: "https://i.pravatar.cc/150"
    }
});
</script>

<template>
    <div class="p-3 md:p-4 rounded-2xl bg-white shadow-md">
        <div class="flex items-center gap-2 md:gap-3 mb-2 md:mb-3">
            <div class="w-7 h-7 md:w-8 md:h-8 lg:w-10 lg:h-10 rounded-full overflow-hidden">
                <img :src="avatarUrl" alt="User" class="w-full h-full object-cover" />
            </div>
            <div class="flex flex-col">
                <span class="text-base md:text-lg lg:text-xl font-semibold">{{ name }}</span>
                <div class="flex items-center gap-1.5">
                    <img src="/images/icons/medal-star-primary.svg" alt="Rating" class="w-3.5 h-3.5 md:w-4 md:h-4" />
                    <span class="text-sm md:text-base font-semibold text-primary">{{ rating }}/5</span>
                </div>
            </div>
        </div>
        <p class="text-sm md:text-base lg:text-lg text-gray-600">{{ comment }}</p>
    </div>
</template>