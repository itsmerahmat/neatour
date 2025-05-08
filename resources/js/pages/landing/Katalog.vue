<script setup lang="ts">
import { Category, Destination, Testimonial } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import Footer from '@/components/landing/Footer.vue';
import Navbar from '@/components/landing/Navbar.vue';
import DestinationCard from '@/components/landing/DestinationCard.vue';
import { ref, onMounted, watch, computed } from 'vue';
import { useLocation } from '@/composables/useLocation';
import { Button } from '@/components/ui/button';

// Define props for data passed from the controller
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
    },
    filters: {
        type: Object,
        default: () => ({
            searchQuery: '',
            categoryId: '',
            rating: ''
        })
    },
    pagination: {
        type: Object,
        default: () => ({
            page: 1,
            perPage: 9,
            totalCount: 0,
            lastPage: 1
        })
    }
});

// Use the location composable
const { getUserLocation, locationStatus } = useLocation();

// Computed property to determine if address should be shown instead of distance
const shouldShowAddress = computed(() => {
    return locationStatus.value === 'denied' || locationStatus.value === 'unsupported';
});

// Search functionality
const searchQuery = ref(props.filters.searchQuery || '');
const showFilter = ref(false);
const selectedCategory = ref(props.filters.categoryId || '');
const selectedRating = ref(props.filters.rating || '');
const isLoadingMore = ref(false);
const currentPage = ref(props.pagination.page || 1);

// Create custom debounce functionality
let searchTimeout: number | null = null;

// Watch for search query changes
watch(searchQuery, () => {
    // Clear existing timeout if any
    if (searchTimeout !== null) {
        clearTimeout(searchTimeout);
    }
    
    // Set new timeout
    searchTimeout = window.setTimeout(() => {
        applyServerFilters();
        searchTimeout = null;
    }, 500);
});

function toggleFilter() {
    showFilter.value = !showFilter.value;
}

function applyFilters() {
    showFilter.value = false;
    applyServerFilters();
}

function clearFilters() {
    selectedCategory.value = '';
    selectedRating.value = '';
    showFilter.value = false;
    applyServerFilters();
}

function applyServerFilters() {
    // Reset to first page when applying new filters
    currentPage.value = 1;
    
    router.visit('/katalog', {
        data: {
            searchQuery: searchQuery.value,
            categoryId: selectedCategory.value,
            rating: selectedRating.value,
            page: 1,
            perPage: props.pagination.perPage,
            latitude: getUserCoordinates().latitude,
            longitude: getUserCoordinates().longitude
        },
        preserveState: true,
        preserveScroll: true,
        only: ['nearbyDestinations', 'filters', 'pagination']
    });
}

function loadMore() {
    if (currentPage.value >= props.pagination.lastPage || isLoadingMore.value) {
        return;
    }
    
    isLoadingMore.value = true;
    const nextPage = currentPage.value + 1;
    
    router.visit('/katalog', {
        data: {
            searchQuery: searchQuery.value,
            categoryId: selectedCategory.value,
            rating: selectedRating.value,
            page: nextPage,
            perPage: props.pagination.perPage,
            latitude: getUserCoordinates().latitude,
            longitude: getUserCoordinates().longitude
        },
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            currentPage.value = nextPage;
            isLoadingMore.value = false;
        },
        onError: () => {
            isLoadingMore.value = false;
        },
        only: ['nearbyDestinations', 'pagination']
    });
}

function getUserCoordinates() {
    // Check localStorage for user coordinates
    const locationData = localStorage.getItem('user_location');
    if (locationData) {
        try {
            const parsedData = JSON.parse(locationData);
            return {
                latitude: parsedData.latitude,
                longitude: parsedData.longitude
            };
        } catch (error) {
            console.error('Error parsing location data:', error);
        }
    }
    
    return { latitude: null, longitude: null };
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

    <div class="min-h-screen flex flex-col bg-white text-gray-800 font-['Poppins']">
        <!-- Navigation Bar -->
        <Navbar activePage="catalog" />

        <!-- Nearby Destinations Section -->
        <section class="py-6 md:py-8 lg:pb-12 px-4 md:px-6">
            <div class="container lg:max-w-4/5 mx-auto">
                <div class="text-center mb-6 md:mb-8">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-semibold text-gray-800">
                        Cari Destinasi Wisata
                    </h2>
                </div>

                <!-- Search Bar Section Based on Figma Design -->
                <div class="flex flex-row gap-3 mb-6 md:mb-8 items-center">
                    <div class="relative flex-1">
                        <div class="flex items-center border-2 border-primary rounded-full px-3 py-1.5 md:py-2 lg:py-2.5">
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
                        <Button 
                            class="md:py-6"
                            @click="toggleFilter"
                            variant="landing"
                        >
                            <img src="/images/icons/sort.svg" alt="Filter" class="w-5 h-5 md:w-6 md:h-6" />
                            <span class="hidden sm:inline text-base md:text-lg lg:text-xl font-medium">Filter</span>
                        </Button>
                        
                        <!-- Filter Dropdown -->
                        <div v-if="showFilter" 
                            class="absolute right-0 top-full mt-2 bg-white rounded-xl shadow-lg p-3 md:p-4 min-w-[260px] sm:min-w-[280px] md:min-w-[320px] z-10 border border-gray-200">
                            <h3 class="text-base md:text-lg font-semibold mb-2 md:mb-3 text-gray-800">Filter Destinasi</h3>
                            
                            <div class="mb-2 md:mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Kategori</label>
                                <select v-model="selectedCategory" 
                                    class="w-full p-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">Semua Kategori</option>
                                    <option v-for="category in props.categories" :key="category.id" :value="category.id.toString()">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            
                            <div class="mb-2 md:mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Minimal Rating</label>
                                <select v-model="selectedRating" 
                                    class="w-full p-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">Semua Rating</option>
                                    <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                    <option value="4">⭐⭐⭐⭐ (4+)</option>
                                    <option value="3">⭐⭐⭐ (3+)</option>
                                </select>
                            </div>
                            
                            <div class="flex gap-2 mt-3 md:mt-4">
                                <Button @click="clearFilters" 
                                    variant="outline"
                                    class="flex-1 border border-primary text-primary text-sm rounded-full">
                                    Reset
                                </Button>
                                <Button @click="applyFilters" 
                                    variant="landing"
                                    class="flex-1 text-sm">
                                    Terapkan
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5 lg:gap-8">
                    <DestinationCard 
                        v-for="destination in props.nearbyDestinations" 
                        :key="destination.id"
                        :id="destination.id"
                        :name="destination.name"
                        :thumbImage="destination.thumb_image"
                        :rating="destination.avg_rating || 0"
                        :distance="destination.distance ? `${destination.distance} Km` : '-'"
                        :address="destination.address || ''"
                        :showAddress="shouldShowAddress"
                    />
                </div>
                
                <!-- No results message -->
                <div v-if="props.nearbyDestinations.length === 0" class="text-center py-8">
                    <p class="text-lg text-gray-500">Tidak ada destinasi yang cocok dengan filter yang dipilih</p>
                </div>

                <div class="flex justify-center mt-6 md:mt-8">
                    <Link 
                        v-if="currentPage < props.pagination.lastPage"
                        href="#"
                        class="flex"
                        @click.prevent="loadMore"
                    >
                        <Button
                            variant="landing"
                            class="flex items-center gap-2 px-4 py-1.5 md:py-6 text-base md:text-xl"
                            :disabled="isLoadingMore"
                        >
                            <span>{{ isLoadingMore ? 'Memuat...' : 'Lihat Lebih Banyak' }}</span>
                            <img v-if="!isLoadingMore" src="/images/icons/arrow-circle-right-bold.svg" alt="View more" class="w-4 h-4 md:w-6 md:h-6" />
                            <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </Button>
                    </Link>
                    <div v-else-if="props.nearbyDestinations.length > 0" class="py-2 md:py-2.5 text-gray-500 text-lg">
                        Semua destinasi telah ditampilkan
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <Footer />
    </div>
</template>