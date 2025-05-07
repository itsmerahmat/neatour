<script setup lang="ts">
import { Destination } from '@/types';
import { ref, onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import Footer from '@/components/landing/Footer.vue';
import FeatureCard from '@/components/landing/FeatureCard.vue';
import Navbar from '@/components/landing/Navbar.vue';
import DestinationCard from '@/components/landing/DestinationCard.vue';
import { useLocation } from '@/composables/useLocation';

// Define props for data passed from the controller
const props = defineProps({
    nearbyDestinations: {
        type: Array as () => Destination[],
        default: () => []
    },
    images: {
        type: Array as () => string[],
        default: () => []
    }
});

// Use the location composable
const { getUserLocation, locationStatus } = useLocation();

// Computed property to determine if address should be shown instead of distance
const shouldShowAddress = computed(() => {
    return locationStatus.value === 'denied' || locationStatus.value === 'unsupported';
});

// Carousel state
const currentSlide = ref(0);
const totalSlides = ref(props.images.length);

// Default background image for fallback
const defaultBgImage = 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80';

// Carousel functions
const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % totalSlides.value;
};

const prevSlide = () => {
    currentSlide.value = (currentSlide.value - 1 + totalSlides.value) % totalSlides.value;
};

// Auto-play carousel
let autoplayInterval: number | null = null;

const startAutoplay = () => {
    stopAutoplay(); // Clear existing interval if any
    autoplayInterval = window.setInterval(() => {
        nextSlide();
    }, 5000); // Change slide every 5 seconds
};

const stopAutoplay = () => {
    if (autoplayInterval !== null) {
        clearInterval(autoplayInterval);
        autoplayInterval = null;
    }
};

onMounted(() => {
    if (props.images.length > 1) {
        startAutoplay();
    }
    
    // Request user location on page load
    getUserLocation(['nearbyDestinations']);
});
</script>

<template>
    <Head title="Beranda">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </Head>

    <div class="min-h-screen flex flex-col bg-white text-[#33372C] font-['Poppins']">
        <!-- Navigation Bar -->
        <Navbar />

        <!-- Hero Section with Carousel -->
        <section class="pb-6 md:pb-8 lg:pb-12 px-4 md:px-6 mt-2">
            <div 
                class="container lg:max-w-4/5 mx-auto relative min-h-[400px] md:min-h-[500px] lg:min-h-[600px] flex flex-col bg-cover bg-center rounded-2xl overflow-hidden transition-all duration-500 ease-in-out" 
                :style="{
                    backgroundImage: `url('${
                        props.images[currentSlide] || defaultBgImage
                    }')`,
                }"
            >
                <div class="absolute inset-0 bg-black/30 rounded-2xl"></div>

                <div class="relative z-10 flex flex-col gap-3 flex-grow p-5 md:p-8 lg:p-12">
                    <div class="max-w-full md:max-w-7xl">
                        <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-semibold leading-tight text-white mb-2 md:mb-4">
                            Jelajahi Keindahan Alam yang Ada di Sekitarmu !
                        </h1>
                        <p class="text-base md:text-lg lg:text-xl leading-relaxed text-white mb-3 md:mb-6 max-w-3xl">
                            Temukan destinasi wisata alam terbaik, panduan lengkap,
                            dan pengalaman tak terlupakan di satu tempat.
                        </p>
                        <div class="flex flex-wrap gap-2 md:gap-3">
                            <button class="px-3 py-1.5 font-semibold text-sm md:text-base lg:text-lg bg-[#DF6D2D] text-white rounded-full">
                                Mulai Petualanganmu
                            </button>
                            <Link href="/katalog" class="px-3 py-1.5 font-semibold text-sm md:text-base lg:text-lg border border-white text-white rounded-full">
                                Rekomendasi Wisata
                            </Link>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-auto">
                        <!-- carousel navigation buttons -->
                        <div class="flex justify-start gap-2">
                            <button class="bg-transparent border-none cursor-pointer" @click="prevSlide">
                                <img src="/images/icons/arrow-circle-left.svg" alt="Previous" class="w-6 h-6 md:w-7 md:h-7 lg:w-8 lg:h-8" />
                            </button>
                            <button class="bg-transparent border-none cursor-pointer" @click="nextSlide">
                                <img src="/images/icons/arrow-circle-right.svg" alt="Next" class="w-6 h-6 md:w-7 md:h-7 lg:w-8 lg:h-8" />
                            </button>
                        </div>
                        
                        <!-- carousel indicators -->
                        <div class="flex gap-1.5">
                            <button 
                                v-for="(_, index) in Array(totalSlides)" 
                                :key="index" 
                                @click="currentSlide = index"
                                class="w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-white opacity-50 transition-opacity duration-300"
                                :class="{ 'opacity-100 w-3 md:w-4': currentSlide === index }"
                            ></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-6 md:py-8 lg:pb-12 px-4 md:px-6">
            <div class="container lg:max-w-4/5 mx-auto">
                <div class="text-center mx-auto mb-6 md:mb-8 lg:mb-10 max-w-4xl">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-semibold mb-2 md:mb-4 text-[#33372C]">
                        Kenapa Harus NeaTour?
                    </h2>
                    <p class="text-base md:text-lg lg:text-xl text-[#898B85]">
                        Temukan pengalaman wisata alam terbaik dengan fitur lengkap yang memudahkan perjalananmu!
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                    <!-- Feature 1 -->
                    <FeatureCard 
                        title="Temukan Destinasi Wisata di Sekitarmu"
                        description="Cari tempat wisata alam terbaik di sekitarmu dengan rekomendasi yang sesuai dengan lokasi dan minatmu."
                        iconSrc="/images/icons/global.svg"
                    />
                    <!-- Feature 2 -->
                    <FeatureCard 
                        title="Informasi Lengkap & Akurat"
                        description="Dapatkan detail perjalanan, rute, harga tiket, serta tips bermanfaat untuk liburan yang lebih nyaman."
                        iconSrc="/images/icons/location-tick.svg"
                    />
                    <!-- Feature 3 -->
                    <FeatureCard 
                        title="Navigasi Mudah & Interaktif"
                        description="Gunakan peta interaktif untuk menemukan lokasi wisata dan rencana perjalanan dengan lebih praktis."
                        iconSrc="/images/icons/routing.svg"
                    />
                    <!-- Feature 4 -->
                    <FeatureCard 
                        title="Ulasan & Rekomendasi Traveler"
                        description="Baca pengalaman wisatawan lain dan dapatkan rekomendasi terbaik sebelum memulai petualanganmu."
                        iconSrc="/images/icons/like-shapes.svg"
                    />
                </div>
            </div>
        </section>

        <!-- Nearby Destinations Section -->
        <section class="py-6 md:py-8 lg:pb-12 px-4 md:px-6">
            <div class="container lg:max-w-4/5 mx-auto">
                <div class="text-center mb-6 md:mb-8 lg:mb-10">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-semibold text-[#33372C]">
                        Temukan Wisata {{ shouldShowAddress ? 'Terbaik' : 'Terdekat' }}!
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5 lg:gap-8">
                    <DestinationCard 
                        v-for="destination in props.nearbyDestinations" 
                        :key="destination.id"
                        :id="destination.id"
                        :name="destination.name"
                        :thumbImage="destination.thumb_image"
                        :rating="destination.avg_rating || 0"
                        :distance="destination.distance ? `${destination.distance} Km` : ' - '"
                        :address="destination.address || ''"
                        :showAddress="shouldShowAddress"
                    />
                </div>

                <div class="flex justify-center mt-6 md:mt-8">
                    <Link href="/katalog" class="flex items-center gap-2 md:gap-3 px-4 py-2 md:py-2.5 bg-[#DF6D2D] text-white text-lg md:text-xl lg:text-2xl font-semibold rounded-full">
                        <span>Lihat Lebih Banyak</span>
                        <img src="/images/icons/arrow-circle-right-bold.svg" alt="View more" class="w-6 h-6 md:w-7 md:h-7" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <Footer />
    </div>
</template>