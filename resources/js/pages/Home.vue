<script setup lang="ts">
import { Category, Destination, Testimonial } from '@/types';
import { ref } from 'vue';
import Footer from '@/components/landing/Footer.vue';

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
        image_url: 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
        rating: '5.0',
        distance: '10 Km'
    },
    {
        id: 2,
        name: 'Bukit Banyu Hirang',
        image_url: 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
        rating: '4.9',
        distance: '15 Km'
    },
    {
        id: 3,
        name: 'Air Terjun Bajuin',
        image_url: 'https://images.unsplash.com/photo-1496434059439-62081c11e4a9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
        rating: '4.8',
        distance: '20 Km'
    }
]);

// Use data from props or dummy data
const nearbyDestinations = ref(props.nearbyDestinations.length > 0 ? props.nearbyDestinations : dummyDestinations.value);
</script>

<template>
    <Head title="Beranda">
        <!-- tambahkan google font poppins  -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </Head>

    <div class="min-h-screen flex flex-col bg-white text-[#33372C] font-['Poppins']">
        <!-- Navigation Bar -->
        <header class="flex justify-between items-center py-4 px-8 bg-white w-full">
            <div class="container max-w-6xl mx-auto flex justify-between items-center">
                <div class="h-12">
                    <img src="/images/icons/global.svg" alt="NeaTour Logo" class="h-full" />
                </div>
                <nav class="flex gap-4">
                    <router-link to="/"
                        class="font-semibold text-2xl px-4 border-b-2 border-[#DF6D2D]">Beranda</router-link>
                    <router-link to="/katalog" class="font-medium text-2xl px-4">Katalog</router-link>
                </nav>
                <router-link to="/login"
                    class="flex items-center gap-2 px-4 py-1.5 bg-[#DF6D2D] text-white rounded-full">
                    <img src="/images/icons/profile-circle.svg" alt="Profile" class="w-6 h-6" />
                    <span class="font-medium text-2xl">Login</span>
                </router-link>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative py-16 px-8 min-h-[800px] flex flex-col bg-cover bg-center"
            style="background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');">
            <div class="absolute inset-0 bg-black/30"></div>

            <div class="container max-w-6xl mx-auto relative z-10 flex flex-col gap-4 flex-grow">
                <div class="max-w-4xl">
                    <h1 class="text-7xl font-semibold leading-tight text-white mb-6">
                        Jelajahi Keindahan Alam yang Ada di Sekitarmu !
                    </h1>
                    <p class="text-2xl leading-relaxed text-white mb-8">
                        Temukan destinasi wisata alam terbaik, panduan lengkap,
                        dan pengalaman tak terlupakan di satu tempat.
                    </p>
                    <div class="flex gap-4">
                        <button class="px-6 py-2 font-semibold text-2xl bg-[#DF6D2D] text-white rounded-full">
                            Mulai Petualanganmu
                        </button>
                        <button class="px-6 py-2 font-semibold text-2xl border border-white text-white rounded-full">
                            Rekomendasi Wisata
                        </button>
                    </div>
                </div>

                <div class="flex justify-between mt-auto">
                    <button class="bg-transparent border-none cursor-pointer">
                        <img src="/images/icons/arrow-circle-left.svg" alt="Previous" class="w-12 h-12" />
                    </button>
                    <button class="bg-transparent border-none cursor-pointer">
                        <img src="/images/icons/arrow-circle-right.svg" alt="Next" class="w-12 h-12" />
                    </button>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-16 px-8">
            <div class="container max-w-6xl mx-auto">
                <div class="text-center mx-auto mb-16 max-w-4xl">
                    <h2 class="text-6xl font-semibold mb-6 text-[#33372C]">
                        Kenapa Harus NeaTour?
                    </h2>
                    <p class="text-2xl text-[#898B85]">
                        Temukan pengalaman wisata alam terbaik dengan fitur lengkap yang memudahkan perjalananmu!
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Feature 1 -->
                    <div class="flex flex-col items-center gap-4 p-6 bg-[#DF6D2D] rounded-3xl shadow-lg">
                        <div class="w-16 h-16 flex items-center justify-center">
                            <img src="/images/icons/global.svg" alt="Global" class="w-full h-full" />
                        </div>
                        <h3 class="text-2xl font-semibold text-white text-center">
                            Temukan Destinasi Wisata di Sekitarmu
                        </h3>
                        <p class="text-base text-center text-[#FDFBF6]">
                            Cari tempat wisata alam terbaik di sekitarmu dengan rekomendasi yang sesuai dengan lokasi
                            dan minatmu.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="flex flex-col items-center gap-4 p-6 bg-[#DF6D2D] rounded-3xl shadow-lg">
                        <div class="w-16 h-16 flex items-center justify-center">
                            <img src="/images/icons/location-tick.svg" alt="Location" class="w-full h-full" />
                        </div>
                        <h3 class="text-2xl font-semibold text-white text-center">
                            Informasi Lengkap & Akurat
                        </h3>
                        <p class="text-base text-center text-[#FDFBF6]">
                            Dapatkan detail perjalanan, rute, harga tiket, serta tips bermanfaat untuk liburan yang
                            lebih nyaman.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex flex-col items-center gap-4 p-6 bg-[#DF6D2D] rounded-3xl shadow-lg">
                        <div class="w-16 h-16 flex items-center justify-center">
                            <img src="/images/icons/routing.svg" alt="Routing" class="w-full h-full" />
                        </div>
                        <h3 class="text-2xl font-semibold text-white text-center">
                            Navigasi Mudah & Interaktif
                        </h3>
                        <p class="text-base text-center text-[#FDFBF6]">
                            Gunakan peta interaktif untuk menemukan lokasi wisata dan rencana perjalanan dengan lebih
                            praktis.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="flex flex-col items-center gap-4 p-6 bg-[#DF6D2D] rounded-3xl shadow-lg">
                        <div class="w-16 h-16 flex items-center justify-center">
                            <img src="/images/icons/like-shapes.svg" alt="Recommendations" class="w-full h-full" />
                        </div>
                        <h3 class="text-2xl font-semibold text-white text-center">
                            Ulasan & Rekomendasi Traveler
                        </h3>
                        <p class="text-base text-center text-[#FDFBF6]">
                            Baca pengalaman wisatawan lain dan dapatkan rekomendasi terbaik sebelum memulai
                            petualanganmu.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Nearby Destinations Section -->
        <section class="py-16 px-8">
            <div class="container max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-6xl font-semibold text-[#33372C]">
                        Temukan Wisata Terdekat !
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    <div v-for="destination in nearbyDestinations" :key="destination.id"
                        class="rounded-3xl overflow-hidden bg-white shadow-lg">
                        <div class="h-64 bg-cover bg-center p-4 flex justify-end rounded-t-3xl relative"
                            :style="{ backgroundImage: `url(${destination.thumb_image})` }">
                            <div
                                class="absolute top-4 right-4 flex items-center gap-1 bg-white/20 backdrop-blur-sm px-2 py-1 rounded-full">
                                <img src="/images/icons/medal-star.svg" alt="Rating" class="w-6 h-6" />
                                <!-- <span class="text-2xl font-semibold text-white">{{ destination.rating }}</span> -->
                                <span class="text-2xl font-semibold text-white">5</span>
                            </div>
                        </div>
                        <h3 class="text-2xl font-semibold p-4 text-[#33372C]">{{ destination.name }}</h3>
                        <div class="flex items-center gap-3 px-4 pb-4">
                            <img src="/images/icons/location.svg" alt="Location" class="w-6 h-6" />
                            <!-- <span class="text-xl text-[#565950]">{{ destination.distance }}</span> -->
                            <span class="text-xl text-[#565950]">5Km</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center mt-12">
                    <button
                        class="flex items-center gap-4 px-6 py-3 bg-[#DF6D2D] text-white text-3xl font-semibold rounded-full">
                        <span>Lihat Lebih Banyak</span>
                        <img src="/images/icons/arrow-circle-right-bold.svg" alt="View more" class="w-8 h-8" />
                    </button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <Footer />
        <!-- <footer class="bg-[#638C6D] py-8 px-8 text-white mt-auto">
            <div class="container max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                    <div class="">
                        <img src="/images/logo/logo_nea.png" alt="NeaTour" class="h-full filter" />
                    </div>
                    <div class="flex flex-col gap-4">
                        <h4 class="text-2xl font-semibold mb-2">Jelajahi</h4>
                        <a href="#" class="text-base font-medium no-underline text-white hover:text-gray-200">Destinasi
                            Populer</a>
                        <a href="#" class="text-base font-medium no-underline text-white hover:text-gray-200">Wisata
                            Terdekat</a>
                        <a href="#"
                            class="text-base font-medium no-underline text-white hover:text-gray-200">Rekomendasi
                            Traveler</a>
                        <a href="#" class="text-base font-medium no-underline text-white hover:text-gray-200">Kategori
                            Wisata</a>
                    </div>

                    <div class="flex flex-col gap-4">
                        <h4 class="text-2xl font-semibold mb-2">Informasi</h4>
                        <a href="#" class="text-base font-medium no-underline text-white hover:text-gray-200">Tentang
                            Kami</a>
                        <a href="#" class="text-base font-medium no-underline text-white hover:text-gray-200">Blog &
                            Artikel</a>
                        <a href="#" class="text-base font-medium no-underline text-white hover:text-gray-200">Panduan
                            Wisata</a>
                        <a href="#" class="text-base font-medium no-underline text-white hover:text-gray-200">Kebijakan
                            Privasi</a>
                    </div>

                    <div class="flex flex-col gap-4">
                        <h4 class="text-2xl font-semibold mb-2">Hubungi Kami</h4>
                        <p class="text-base font-medium">Email : eavy.coompany@gmail.com</p>
                        <p class="text-base font-medium">Alamat : Gedung 2 FMIPA ULM</p>
                        <p class="text-base font-medium">Instagram : @eavy.tech</p>
                        <p class="text-base font-medium">WhatsApp : 0812-3456-7890</p>
                    </div>
                </div>
            </div>
        </footer> -->
    </div>
</template>