<script setup lang="ts">
import { Category, Destination, Testimonial } from '@/types';
import { Head } from '@inertiajs/vue3';
import Footer from '@/components/landing/Footer.vue';
import Navbar from '@/components/landing/Navbar.vue';
import DestinationCard from '@/components/landing/DestinationCard.vue';
import { ref, computed } from 'vue';

// Define props for data passed from the controller
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps({
    nearbyDestinations: {
        type: Array as () => Destination[],
        default: () => []
    },
    categories: {
        type: Array as () => Category[],
        default: () => []
    },
    testimonials: {
        type: Array as () => Testimonial[],
        default: () => []
    }
});

// Search functionality
const searchQuery = ref('');
const showFilter = ref(false);
const selectedCategory = ref('');
const selectedRating = ref('');
const distanceFilter = ref(0);

const filteredDestinations = computed(() => {
    if (!searchQuery.value && !selectedCategory.value && !selectedRating.value && !distanceFilter.value) {
        return props.nearbyDestinations;
    }
    
    return props.nearbyDestinations.filter(destination => {
        // Search query filter
        if (searchQuery.value && !destination.name.toLowerCase().includes(searchQuery.value.toLowerCase())) {
            return false;
        }
        
        // Category filter
        if (selectedCategory.value && 
            (!destination.categories || 
             !destination.categories.some(cat => cat.id.toString() === selectedCategory.value))) {
            return false;
        }
        
        // Rating filter - assuming rating is a property of destination
        if (selectedRating.value && destination.rating! < parseFloat(selectedRating.value)) {
            return false;
        }
        
        // Distance filter - assuming distance is a property of destination
        if (distanceFilter.value && destination.distance! > distanceFilter.value) {
            return false;
        }
        
        return true;
    });
});

function toggleFilter() {
    showFilter.value = !showFilter.value;
}

function applyFilters() {
    showFilter.value = false;
}

function clearFilters() {
    selectedCategory.value = '';
    selectedRating.value = '';
    distanceFilter.value = 0;
    showFilter.value = false;
}

</script>

<template>
    <Head title="Katalog">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </Head>

    <div class="min-h-screen flex flex-col bg-white text-[#33372C] font-['Poppins']">
        <!-- Navigation Bar -->
        <Navbar activePage="catalog" />

        <!-- Nearby Destinations Section -->
        <section class="py-8 md:py-12 lg:pb-16 px-5 md:px-8">
            <div class="container lg:max-w-4/5 mx-auto">
                <div class="text-center mb-8 md:mb-12 lg:mb-16">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold text-[#33372C]">
                        Cari Destinasi Wisata
                    </h2>
                </div>

                <!-- Search Bar Section Based on Figma Design -->
                <div class="flex flex-row gap-4 mb-8 md:mb-12">
                    <div class="relative flex-1">
                        <div class="flex items-center border-[3px] border-[#DF6D2D] rounded-full px-5 py-2 md:py-3 lg:py-4">
                            <img src="/images/icons/search-icon.svg" alt="Search" class="w-6 h-6 md:w-7 md:h-7 lg:w-8 lg:h-8" />
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="Cari" 
                                class="w-full ml-3 md:ml-4 text-xl md:text-2xl lg:text-3xl text-[#898B85] focus:outline-none bg-transparent" 
                            />
                        </div>
                    </div>
                    <div class="relative">
                        <button 
                            @click="toggleFilter"
                            class="flex items-center justify-center gap-2 bg-[#DF6D2D] text-white rounded-full py-2 md:py-3 lg:py-4 px-4 md:px-8"
                        >
                            <img src="/images/icons/sort.svg" alt="Filter" class="w-6 h-6 md:w-7 md:h-7 lg:w-8 lg:h-8" />
                            <span class="hidden sm:inline text-xl md:text-2xl lg:text-3xl font-medium">Filter</span>
                        </button>
                        
                        <!-- Filter Dropdown -->
                        <div v-if="showFilter" 
                            class="absolute right-0 top-full mt-2 bg-white rounded-xl shadow-lg p-4 md:p-5 min-w-[260px] sm:min-w-[300px] md:min-w-[400px] z-10 border border-gray-200">
                            <h3 class="text-base sm:text-lg md:text-xl font-semibold mb-3 md:mb-4 text-[#33372C]">Filter Destinasi</h3>
                            
                            <div class="mb-3 md:mb-4">
                                <label class="block text-sm sm:text-base text-[#565950] mb-1 md:mb-2">Kategori</label>
                                <select v-model="selectedCategory" 
                                    class="w-full p-2 border border-gray-300 rounded-md text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#DF6D2D]">
                                    <option value="">Semua Kategori</option>
                                    <option v-for="category in props.categories" :key="category.id" :value="category.id.toString()">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            
                            <div class="mb-3 md:mb-4">
                                <label class="block text-sm sm:text-base text-[#565950] mb-1 md:mb-2">Minimal Rating</label>
                                <select v-model="selectedRating" 
                                    class="w-full p-2 border border-gray-300 rounded-md text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#DF6D2D]">
                                    <option value="">Semua Rating</option>
                                    <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                    <option value="4">⭐⭐⭐⭐ (4+)</option>
                                    <option value="3">⭐⭐⭐ (3+)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3 md:mb-4">
                                <label class="block text-sm sm:text-base text-[#565950] mb-1 md:mb-2">Jarak Maksimal (Km)</label>
                                <input type="range" v-model="distanceFilter" min="0" max="50" step="5" 
                                    class="w-full accent-[#DF6D2D]" />
                                <div class="flex justify-between text-xs sm:text-sm text-gray-500">
                                    <span>0 km</span>
                                    <span>{{ distanceFilter }} km</span>
                                    <span>50 km</span>
                                </div>
                            </div>
                            
                            <div class="flex gap-2 sm:gap-3 mt-4 md:mt-6">
                                <button @click="clearFilters" 
                                    class="flex-1 py-1.5 sm:py-2 border border-[#DF6D2D] text-[#DF6D2D] text-sm sm:text-base rounded-full hover:bg-gray-50">
                                    Reset
                                </button>
                                <button @click="applyFilters" 
                                    class="flex-1 py-1.5 sm:py-2 bg-[#DF6D2D] text-white text-sm sm:text-base rounded-full hover:bg-[#c75b21]">
                                    Terapkan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 lg:gap-12">
                    <DestinationCard 
                        v-for="destination in filteredDestinations" 
                        :key="destination.id"
                        :id="destination.id"
                        :name="destination.name"
                        :thumbImage="destination.thumb_image"
                        :rating="'5.0'"
                        :distance="'5 Km'"
                    />
                </div>

                <div class="flex justify-center mt-8 md:mt-12">
                    <button
                        class="flex items-center gap-2 md:gap-4 px-4 md:px-6 py-2 md:py-3 bg-[#DF6D2D] text-white text-xl md:text-2xl lg:text-3xl font-semibold rounded-full">
                        <span>Lihat Lebih Banyak</span>
                        <img src="/images/icons/arrow-circle-right-bold.svg" alt="View more" class="w-6 h-6 md:w-7 md:h-7 lg:w-8 lg:h-8" />
                    </button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <Footer />
    </div>
</template>