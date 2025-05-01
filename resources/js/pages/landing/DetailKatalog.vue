<script setup lang="ts">
import { Destination, Testimonial } from '@/types';
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Footer from '@/components/landing/Footer.vue';
import Navbar from '@/components/landing/Navbar.vue';
import DestinationCard from '@/components/landing/DestinationCard.vue';

// Define props for data passed from the controller
const props = defineProps({
    destination: {
        type: Object as () => Destination,
        default: () => ({})

    },
    nearbyDestinations: {
        type: Array as () => Destination[],
        default: () => []
    },
    relatedDestinations: {
        type: Array as () => Destination[],
        default: () => []
    },
    testimonials: {
        type: Array as () => Testimonial[],
        default: () => []
    }
});

// Dummy data for destination details
const dummyDestination = ref({
    id: 1,
    name: 'Tahura Sultan Adam',
    thumb_image: 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
    content: 'Tahura Sultan Adam adalah kawasan konservasi alam yang terletak di Kalimantan Selatan, Indonesia. Taman Hutan Raya (Tahura) ini memiliki luas lebih dari 112.000 hektare, mencakup berbagai ekosistem seperti hutan hujan tropis, perbukitan, dan sungai yang indah. Sebagai destinasi wisata alam, Tahura Sultan Adam menawarkan berbagai aktivitas menarik seperti trekking, camping, dan menikmati pemandangan alam yang masih asri.',
    facility: 'Pengawasan 24 Jam, Keamanan Terjaga, Kenyamanan Dijamin, Villa & Penginapan, Mobil Bisa Masuk',
    lat: -3.4433238,
    lon: 114.7446383,
    operating_hours: 'Buka Pukul 05.00 - 16.00',
    address: 'Cempaka, Kec. Cemp., Kota Banjar Baru, Kalimantan Selatan 70661',
    rating: 5.0,
    published: true,
});

// Dummy data for testimonials
const dummyTestimonials = ref([
    {
        id: 1,
        user: {
            name: 'Muhammad Ridha Lesmana',
            avatar: 'https://i.pravatar.cc/150?img=1'
        },
        rating: 5,
        comment: 'Saya sangat menikmati perjalanan ke Tahura Sultan Adam! Suasananya masih sangat alami dengan udara yang sejuk dan pemandangan hutan yang indah. Saya mengunjungi Menara Pandang Mandiangin, dan dari atas saya bisa melihat hamparan hutan yang luas, benar-benar memanjakan mata. Trekking ke air terjun juga seru, meskipun medannya cukup menantang. Cocok untuk pecinta alam dan fotografi!'
    },
    {
        id: 2,
        user: {
            name: 'Muhammad Ridha Lesmana',
            avatar: 'https://i.pravatar.cc/150?img=2'
        },
        rating: 5,
        comment: 'Saya sangat menikmati perjalanan ke Tahura Sultan Adam! Suasananya masih sangat alami dengan udara yang sejuk dan pemandangan hutan yang indah. Saya mengunjungi Menara Pandang Mandiangin, dan dari atas saya bisa melihat hamparan hutan yang luas, benar-benar memanjakan mata. Trekking ke air terjun juga seru, meskipun medannya cukup menantang. Cocok untuk pecinta alam dan fotografi!'
    }
]);

// Use data from props or dummy data
const destinationData = computed(() => Object.keys(props.destination).length > 0 ? props.destination : dummyDestination.value);
const testimonialsList = computed(() => props.testimonials.length > 0 ? props.testimonials : dummyTestimonials.value);

// Format facilities as array
const facilities = computed(() => {
    if (typeof destinationData.value.facility === 'string') {
        return destinationData.value.facility.split(',').map(item => item.trim());
    }
    return [];
});
</script>

<template>
    <Head title="Detail Destinasi">
        <!-- Google font poppins -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </Head>

    <div class="min-h-screen flex flex-col bg-white text-[#33372C] font-['Poppins']">
        <!-- Navigation Bar -->
        <Navbar activePage="catalog" />

        <!-- Main Content -->
        <section class="py-8 md:py-12 lg:pb-16 px-5 md:px-8">
            <div class="container lg:max-w-4/5 mx-auto">
                <!-- Destination Header Section -->
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Main Image -->
                    <div class="w-full lg:w-1/2 rounded-3xl overflow-hidden h-[300px] sm:h-[400px] md:h-[450px] lg:h-[500px]">
                        <img :src="destinationData.thumb_image" alt="Destination" class="w-full h-full object-cover rounded-3xl">
                    </div>

                    <!-- Destination Info -->
                    <div class="w-full lg:w-1/2 flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold">{{ destinationData.name }}</h1>
                            <div class="flex gap-3">
                                <button class="p-2 md:p-3 bg-[#DF6D2D] rounded-full">
                                    <img src="/images/icons/export.svg" alt="Share" class="w-5 h-5 md:w-6 md:h-6" />
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <img src="/images/icons/medal-star-primary.svg" alt="Rating" class="w-6 h-6 md:w-8 md:h-8 lg:w-10 lg:h-10" />
                            <span class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-semibold text-[#DF6D2D]">{{ "5.0" }}</span>
                        </div>

                        <div class="flex flex-col gap-4 md:gap-6 mt-4 md:mt-6">
                            <!-- Opening Hours -->
                            <div class="flex items-center gap-3 md:gap-4">
                                <img src="/images/icons/clock.svg" alt="Clock" class="w-6 h-6 md:w-8 md:h-8" />
                                <span class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-[#565950]">{{ "Buka Pukul 05.00 - 16.00" }}</span>
                            </div>

                            <!-- Address -->
                            <div class="flex items-center gap-3 md:gap-4">
                                <img src="/images/icons/location.svg" alt="Location" class="w-6 h-6 md:w-8 md:h-8" />
                                <span class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-[#565950]">{{ "Cempaka, Kec. Cemp., Kota Banjar Baru, Kalimantan Selatan 70661" }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mt-6 md:mt-8">
                    <div class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-[#565950] text-justify" v-html="destinationData.content">
                    </div>
                </div>
            </div>
        </section>

        <!-- Facilities and Map Section -->
        <section class="py-8 md:py-12 lg:pb-16 px-5 md:px-8">
            <div class="container lg:max-w-4/5 mx-auto flex flex-col lg:flex-row gap-8">
                <!-- Map Section -->
                <div class="w-full lg:w-1/2">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold mb-4">Peta</h2>
                    <div class="rounded-3xl overflow-hidden h-[300px] md:h-[350px] lg:h-[400px] w-full shadow-lg">
                        <iframe 
                            width="100%" 
                            height="100%" 
                            style="border:0" 
                            loading="lazy" 
                            allowfullscreen 
                            :src="`https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q=${destinationData.lat},${destinationData.lon}`">
                        </iframe>
                    </div>
                </div>

                <!-- Facilities Section -->
                <div class="w-full lg:w-1/2 mt-8 lg:mt-0">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold mb-4 md:mb-6">Fasilitas</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="(facility, index) in facilities" :key="index" class="flex items-center gap-3">
                            <!-- <img :src="`/images/icons/${getFacilityIcon(facility)}.svg`" alt="Facility" class="w-6 h-6 md:w-8 md:h-8" /> -->
                            <span class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-[#565950]">{{ facility }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- User Reviews Section -->
        <section class="py-8 md:py-12 lg:pb-16 px-5 md:px-8">
            <div class="container lg:max-w-4/5 mx-auto">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold mb-4 sm:mb-0">Ulasan Pengguna</h2>
                    <Link href="/ulasan/create" class="flex items-center gap-2 px-4 md:px-6 py-2 md:py-3 bg-[#DF6D2D] text-white rounded-full">
                        <img src="/images/icons/add-circle.svg" alt="Add" class="w-5 h-5 md:w-6 md:h-6" />
                        <span class="text-lg md:text-xl lg:text-2xl font-semibold">Beri Ulasan</span>
                    </Link>
                </div>

                <!-- Reviews List -->
                <div class="flex flex-col gap-4 md:gap-6">
                    <div v-for="testimonial in testimonialsList" :key="testimonial.id" 
                        class="p-4 md:p-6 rounded-3xl bg-white shadow-md">
                        <div class="flex items-center gap-3 md:gap-4 mb-3 md:mb-4">
                            <div class="w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 rounded-full overflow-hidden">
                                <img :src="'https://i.pravatar.cc/150'" alt="User" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-lg md:text-xl lg:text-2xl font-semibold">{{ "User" }}</span>
                                <div class="flex items-center gap-2">
                                    <img src="/images/icons/medal-star-primary.svg" alt="Rating" class="w-4 h-4 md:w-5 md:h-5" />
                                    <span class="text-base md:text-lg lg:text-xl font-semibold text-[#DF6D2D]">{{ testimonial.rating }}/5</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-base md:text-lg lg:text-2xl text-[#565950]">{{ testimonial.comment }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recommended Destinations Section -->
        <section class="py-8 md:py-12 lg:py-16 px-5 md:px-8">
            <div class="container lg:max-w-4/5 mx-auto">
                <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold text-center mb-6 md:mb-8">Rekomendasi Wisata</h2>

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
                    <Link href="/katalog" class="flex items-center gap-3 md:gap-4 px-5 md:px-6 py-2 md:py-3 bg-[#DF6D2D] text-white text-xl md:text-2xl lg:text-3xl font-semibold rounded-full">
                        <span>Lihat Lebih Banyak</span>
                        <img src="/images/icons/arrow-circle-right-bold.svg" alt="View more" class="w-6 h-6 md:w-7 md:h-7 lg:w-8 lg:h-8" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <Footer />
    </div>
</template>