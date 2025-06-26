<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
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

// Generate image URL without compression for card display
const optimizedImage = computed(() => {
  if (!props.thumbImage) return '/images/placeholder.jpg';
  
  // If it's an ImageKit URL, resize without compression
  if (props.thumbImage.includes('ik.imagekit.io')) {
    // Extract the path and add transformations
    try {
      const url = new URL(props.thumbImage);
      const path = url.pathname;
      
      return `${url.origin}${path}`;
    } catch (error) {
      console.warn('Error parsing image URL:', error);
      return props.thumbImage;
    }
  }
  
  // For non-ImageKit URLs, return as is
  return props.thumbImage;
});
</script>

<template>
  <Link 
    :href="`/katalog/${id}`"
    class="rounded-xl overflow-hidden bg-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 ease-in-out block"
  >
    <div 
      class="h-52 bg-cover bg-center p-3 flex justify-end rounded-b-xl relative"
      :style="{ backgroundImage: `url(${optimizedImage})` }"
    >
      <div
        class="absolute top-3 right-3 flex items-center gap-1 bg-white/20 backdrop-blur-sm px-1.5 py-0.5 rounded-full"
      >
        <img src="/images/icons/medal-star.svg" alt="Rating" class="w-5 h-5" />
        <span class="text-xl font-semibold text-white">{{ rating }}</span>
      </div>
    </div>
    <h3 class="text-xl font-semibold p-3 text-[#33372C] group-hover:text-[#DF6D2D]">{{ name }}</h3>
    <div class="flex items-center gap-2 px-3 pb-3">
      <img src="/images/icons/location-primary.svg" alt="Location" class="w-5 h-5" />
      <span class="text-lg text-[#565950]">{{ distance }}</span>
    </div>
  </Link>
</template>