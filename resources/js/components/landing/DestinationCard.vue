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
  },
  address: {
    type: String,
    default: ""
  },
  showAddress: {
    type: Boolean,
    default: false
  }
});
</script>

<template>
  <Link 
    :href="`/katalog/${id}`"
    class="rounded-xl overflow-hidden bg-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 ease-in-out block"
  >
    <div 
      class="h-52 bg-cover bg-center p-3 flex justify-end rounded-b-xl relative"
      :style="{ backgroundImage: `url(${thumbImage})` }"
    >
      <div
        class="absolute top-3 right-3 flex items-center gap-1 bg-white/20 backdrop-blur-sm px-1.5 py-0.5 rounded-full"
      >
        <img src="/images/icons/medal-star.svg" alt="Rating" class="w-5 h-5" />
        <span class="text-xl font-semibold text-white">{{ rating }}</span>
      </div>
    </div>
    <h3 class="text-xl font-semibold p-3 text-gray-800 group-hover:text-primary">{{ name }}</h3>
    <div class="flex items-center gap-2 px-3 pb-3">
      <img src="/images/icons/location-primary.svg" alt="Location" class="w-5 h-5" />
      <span class="text-lg text-gray-600 line-clamp-1">{{ showAddress && address ? address : distance }}</span>
    </div>
  </Link>
</template>