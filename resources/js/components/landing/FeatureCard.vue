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
  <div class="flex flex-col items-center gap-4 p-6 bg-[#DF6D2D] rounded-3xl shadow-lg hover:shadow-xl hover:scale-105 hover:bg-[#E85A15] transition-all duration-300 ease-in-out cursor-pointer">
    <div class="w-16 h-16 flex items-center justify-center">
      <img :src="iconSrc" :alt="title" class="w-full h-full" />
    </div>
    <h3 class="text-2xl font-semibold text-white text-center">
      {{ title }}
    </h3>
    <p class="text-base text-center text-[#FDFBF6]">
      {{ description }}
    </p>
  </div>
</template>