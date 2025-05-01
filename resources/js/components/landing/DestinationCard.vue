<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps({
  id: {
    type: [Number, String],
    required: true
  },
  name: {
    type: String,
    required: true
  },
  thumbImage: {
    type: String,
    required: true
  },
  rating: {
    type: [String, Number],
    default: "5.0"
  },
  distance: {
    type: String,
    default: "5 Km"
  }
});
</script>

<template>
  <Link 
    :href="`/katalog/${id}`"
    class="rounded-2xl overflow-hidden bg-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 ease-in-out block"
  >
    <div 
      class="h-64 bg-cover bg-center p-4 flex justify-end rounded-b-2xl relative"
      :style="{ backgroundImage: `url(${thumbImage})` }"
    >
      <div
        class="absolute top-4 right-4 flex items-center gap-1 bg-white/20 backdrop-blur-sm px-2 py-1 rounded-full"
      >
        <img src="/images/icons/medal-star.svg" alt="Rating" class="w-6 h-6" />
        <span class="text-2xl font-semibold text-white">{{ rating }}</span>
      </div>
    </div>
    <h3 class="text-2xl font-semibold p-4 text-[#33372C] group-hover:text-[#DF6D2D]">{{ name }}</h3>
    <div class="flex items-center gap-3 px-4 pb-4">
      <img src="/images/icons/location-primary.svg" alt="Location" class="w-6 h-6" />
      <span class="text-xl text-[#565950]">{{ distance }}</span>
    </div>
  </Link>
</template>