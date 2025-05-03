<script setup lang="ts">
defineProps({
  iconSrc: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  }
});
</script>

<template>
  <div class="flex flex-col items-center gap-3 p-4 bg-[#DF6D2D] rounded-2xl shadow-md hover:shadow-lg hover:scale-102 hover:bg-[#E85A15] transition-all duration-300 ease-in-out cursor-pointer">
    <div class="w-12 h-12 flex items-center justify-center">
      <img :src="iconSrc" :alt="title" class="w-full h-full" />
    </div>
    <h3 class="text-xl font-semibold text-white text-center">
      {{ title }}
    </h3>
    <p class="text-sm text-center text-[#FDFBF6]">
      {{ description }}
    </p>
  </div>
</template>