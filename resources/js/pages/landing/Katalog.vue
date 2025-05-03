<script setup lang="ts">
import { Category, Destination, Testimonial } from '@/types';
import { Head } from '@inertiajs/vue3';
import Footer from '@/components/landing/Footer.vue';
import Navbar from '@/components/landing/Navbar.vue';
import DestinationCard from '@/components/landing/DestinationCard.vue';
import { ref, computed, onMounted } from 'vue';
import { useLocation } from '@/composables/useLocation';

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

// Use the location composable
const { getUserLocation } = useLocation();

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
        if (selectedRating.value && destination.avg_rating! < parseFloat(selectedRating.value)) {
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

onMounted(() => {
    // Request user location on page load
    getUserLocation(['nearbyDestinations']);
});
</script>

<template>
    <Head title="Katalog">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </Head>

    <div class="min-h-screen flex flex-col bg-white text-[#33372C] font-['Poppins']">
        <!-- Navigation Bar -->
        <Navbar activePage="catalog" />

        <!-- Nearby Destinations Section -->
        <section class="py-6 md:py-8 lg:pb-12 px-4 md:px-6">
            <div class="container lg:max-w-4/5 mx-auto">
                <div class="text-center mb-6 md:mb-8">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-semibold text-[#33372C]">
                        Cari Destinasi Wisata
                    </h2>
                </div>

                <!-- Search Bar Section Based on Figma Design -->
                <div class="flex flex-row gap-3 mb-6 md:mb-8">
                    <div class="relative flex-1">
                        <div class="flex items-center border-2 border-[#DF6D2D] rounded-full px-3 py-1.5 md:py-2 lg:py-2.5">
                            <img src="/images/icons/search-icon.svg" alt="Search" class="w-5 h-5 md:w-6 md:h-6" />
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="Cari" 
                                class="w-full ml-2 md:ml-3 text-base md:text-lg lg:text-xl text-[#898B85] focus:outline-none bg-transparent" 
                            />
                        </div>
                    </div>
                    <div class="relative">
                        <button 
                            @click="toggleFilter"
                            class="flex items-center justify-center gap-1.5 bg-[#DF6D2D] text-white rounded-full py-1.5 md:py-2 lg:py-2.5 px-3 md:px-6"
                        >
                            <img src="/images/icons/sort.svg" alt="Filter" class="w-5 h-5 md:w-6 md:h-6" />
                            <span class="hidden sm:inline text-base md:text-lg lg:text-xl font-medium">Filter</span>
                        </button>
                        
                        <!-- Filter Dropdown -->
                        <div v-if="showFilter" 
                            class="absolute right-0 top-full mt-2 bg-white rounded-xl shadow-lg p-3 md:p-4 min-w-[260px] sm:min-w-[280px] md:min-w-[320px] z-10 border border-gray-200">
                            <h3 class="text-base md:text-lg font-semibold mb-2 md:mb-3 text-[#33372C]">Filter Destinasi</h3>
                            
                            <div class="mb-2 md:mb-3">
                                <label class="block text-sm text-[#565950] mb-1">Kategori</label>
                                <select v-model="selectedCategory" 
                                    class="w-full p-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#DF6D2D]">
                                    <option value="">Semua Kategori</option>
                                    <option v-for="category in props.categories" :key="category.id" :value="category.id.toString()">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            
                            <div class="mb-2 md:mb-3">
                                <label class="block text-sm text-[#565950] mb-1">Minimal Rating</label>
                                <select v-model="selectedRating" 
                                    class="w-full p-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#DF6D2D]">
                                    <option value="">Semua Rating</option>
                                    <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                    <option value="4">⭐⭐⭐⭐ (4+)</option>
                                    <option value="3">⭐⭐⭐ (3+)</option>
                                </select>
                            </div>
                            
                            <div class="mb-2 md:mb-3">
                                <label class="block text-sm text-[#565950] mb-1">Jarak Maksimal (Km)</label>
                                <input type="range" v-model="distanceFilter" min="0" max="50" step="5" 
                                    class="w-full accent-[#DF6D2D]" />
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>0 km</span>
                                    <span>{{ distanceFilter }} km</span>
                                    <span>50 km</span>
                                </div>
                            </div>
                            
                            <div class="flex gap-2 mt-3 md:mt-4">
                                <button @click="clearFilters" 
                                    class="flex-1 py-1 border border-[#DF6D2D] text-[#DF6D2D] text-sm rounded-full hover:bg-gray-50">
                                    Reset
                                </button>
                                <button @click="applyFilters" 
                                    class="flex-1 py-1 bg-[#DF6D2D] text-white text-sm rounded-full hover:bg-[#c75b21]">
                                    Terapkan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5 lg:gap-8">
                    <DestinationCard 
                        v-for="destination in filteredDestinations" 
                        :key="destination.id"
                        :id="destination.id"
                        :name="destination.name"
                        :thumbImage="destination.thumb_image"
                        :rating="destination.avg_rating || 0"
                        :distance="'5 Km'"
                    />
                </div>

                <div class="flex justify-center mt-6 md:mt-8">
                    <button
                        class="flex items-center gap-2 px-4 py-2 md:py-2.5 bg-[#DF6D2D] text-white text-lg md:text-xl lg:text-2xl font-semibold rounded-full">
                        <span>Lihat Lebih Banyak</span>
                        <img src="/images/icons/arrow-circle-right-bold.svg" alt="View more" class="w-5 h-5 md:w-6 md:h-6" />
                    </button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <Footer />
    </div>
</template>