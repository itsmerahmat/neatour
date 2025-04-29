<script setup lang="ts">
import { Category, Destination, Testimonial } from '@/types';
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import Footer from '@/components/landing/Footer.vue';
import FeatureCard from '@/components/landing/FeatureCard.vue';
import Navbar from '@/components/landing/Navbar.vue';
import DestinationCard from '@/components/landing/DestinationCard.vue';

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
    }
});

// Dummy data for destinations
const dummyDestinations = ref([
    {
        id: 1,
        name: 'Tahura Sultan Adam',
        thumb_image: 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
        rating: '5.0',
        distance: '10 Km'
    },
    {
        id: 2,
        name: 'Bukit Banyu Hirang',
        thumb_image: 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
        rating: '4.9',
        distance: '15 Km'
    },
    {
        id: 3,
        name: 'Air Terjun Bajuin',
        thumb_image: 'https://images.unsplash.com/photo-1496434059439-62081c11e4a9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
        rating: '4.8',
        distance: '20 Km'
    }
]);

// Use data from props or dummy data
const nearbyDestinations = ref(props.nearbyDestinations.length > 0 ? props.nearbyDestinations : dummyDestinations.value);

// Carousel state
const currentSlide = ref(0);
const totalSlides = ref(nearbyDestinations.value.length);

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
    if (nearbyDestinations.value.length > 1) {
        startAutoplay();
    }
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
        <section class="pb-8 md:pb-12 lg:pb-16 px-5 md:px-8 mt-2">
            <div 
                class="container lg:max-w-4/5 mx-auto relative min-h-[500px] md:min-h-[700px] lg:min-h-[800px] flex flex-col bg-cover bg-center rounded-2xl overflow-hidden transition-all duration-500 ease-in-out" 
                :style="{
                    backgroundImage: `url('${
                        nearbyDestinations[currentSlide]?.thumb_image || defaultBgImage
                    }')`,
                }"
            >
                <div class="absolute inset-0 bg-black/30 rounded-2xl"></div>

                <div class="relative z-10 flex flex-col gap-4 flex-grow p-6 md:p-10 lg:p-16">
                    <div class="max-w-full md:max-w-7xl">
                        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-semibold leading-tight text-white mb-3 md:mb-6">
                            <!-- {{ nearbyDestinations[currentSlide]?.name || "Jelajahi Keindahan Alam yang Ada di Sekitarmu !" }} -->
                            Jelajahi Keindahan Alam yang Ada di Sekitarmu !
                        </h1>
                        <p class="text-lg md:text-xl lg:text-2xl leading-relaxed text-white mb-4 md:mb-8 max-w-3xl">
                            Temukan destinasi wisata alam terbaik, panduan lengkap,
                            dan pengalaman tak terlupakan di satu tempat.
                        </p>
                        <div class="flex flex-wrap gap-3 md:gap-4">
                            <button class="px-4 py-2 font-semibold text-base md:text-xl lg:text-2xl bg-[#DF6D2D] text-white rounded-full">
                                Mulai Petualanganmu
                            </button>
                            <button class="px-4 py-2 font-semibold text-base md:text-xl lg:text-2xl border border-white text-white rounded-full">
                                Rekomendasi Wisata
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-auto">
                        <!-- carousel navigation buttons -->
                        <div class="flex justify-start gap-2 md:gap-3">
                            <button class="bg-transparent border-none cursor-pointer" @click="prevSlide">
                                <img src="/images/icons/arrow-circle-left.svg" alt="Previous" class="w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12" />
                            </button>
                            <button class="bg-transparent border-none cursor-pointer" @click="nextSlide">
                                <img src="/images/icons/arrow-circle-right.svg" alt="Next" class="w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12" />
                            </button>
                        </div>
                        
                        <!-- carousel indicators -->
                        <div class="flex gap-2">
                            <button 
                                v-for="(_, index) in nearbyDestinations" 
                                :key="index" 
                                @click="currentSlide = index"
                                class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white opacity-50 transition-opacity duration-300"
                                :class="{ 'opacity-100 w-4 md:w-6': currentSlide === index }"
                            ></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-8 md:py-12 lg:pb-16 px-5 md:px-8">
            <div class="container lg:max-w-4/5 mx-auto">
                <div class="text-center mx-auto mb-8 md:mb-12 lg:mb-16 max-w-4xl">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold mb-3 md:mb-6 text-[#33372C]">
                        Kenapa Harus NeaTour?
                    </h2>
                    <p class="text-lg md:text-xl lg:text-2xl text-[#898B85]">
                        Temukan pengalaman wisata alam terbaik dengan fitur lengkap yang memudahkan perjalananmu!
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
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
        <section class="py-8 md:py-12 lg:pb-16 px-5 md:px-8">
            <div class="container lg:max-w-4/5 mx-auto">
                <div class="text-center mb-8 md:mb-12 lg:mb-16">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold text-[#33372C]">
                        Temukan Wisata Terdekat !
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 lg:gap-12">
                    <DestinationCard 
                        v-for="destination in nearbyDestinations" 
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