<script setup lang="ts">
import { Destination, Testimonial } from '@/types';
import { ref, computed } from 'vue';
import Footer from '@/components/landing/Footer.vue';
import Navbar from '@/components/landing/Navbar.vue';

// Define props for data passed from the controller
const props = defineProps({
    destination: {
        type: Object as () => Destination,
        default: () => ({})

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

// Dummy data for recommended destinations
const dummyRecommendedDestinations = ref([
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
const destinationData = computed(() => Object.keys(props.destination).length > 0 ? props.destination : dummyDestination.value);
const testimonialsList = computed(() => props.testimonials.length > 0 ? props.testimonials : dummyTestimonials.value);
const recommendedDestinations = computed(() => props.relatedDestinations.length > 0 ? props.relatedDestinations : dummyRecommendedDestinations.value);

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
        <Navbar />

        <!-- Main Content -->
        <section class="py-8 px-8">
            <div class="container max-w-6xl mx-auto">
                <!-- Destination Header Section -->
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Main Image -->
                    <div class="w-full lg:w-1/2 rounded-3xl overflow-hidden h-[500px]">
                        <img :src="destinationData.thumb_image" alt="Destination" class="w-full h-full object-cover rounded-3xl">
                    </div>

                    <!-- Destination Info -->
                    <div class="w-full lg:w-1/2 flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <h1 class="text-6xl font-semibold">{{ destinationData.name }}</h1>
                            <div class="flex gap-3">
                                <button class="p-3 bg-[#DF6D2D] rounded-full">
                                    <img src="/images/icons/archive-tick.svg" alt="Save" class="w-6 h-6" />
                                </button>
                                <button class="p-3 bg-[#DF6D2D] rounded-full">
                                    <img src="/images/icons/export.svg" alt="Share" class="w-6 h-6" />
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <img src="/images/icons/medal-star.svg" alt="Rating" class="w-8 h-8" />
                            <span class="text-4xl font-semibold text-[#DF6D2D]">{{ "5.0" }}</span>
                        </div>

                        <div class="flex flex-col gap-6 mt-6">
                            <!-- Opening Hours -->
                            <div class="flex items-center gap-4">
                                <img src="/images/icons/clock.svg" alt="Clock" class="w-8 h-8" />
                                <span class="text-3xl text-[#565950]">{{ "Buka Pukul 05.00 - 16.00" }}</span>
                            </div>

                            <!-- Address -->
                            <div class="flex items-center gap-4">
                                <img src="/images/icons/location.svg" alt="Location" class="w-8 h-8" />
                                <span class="text-3xl text-[#565950]">{{ "Cempaka, Kec. Cemp., Kota Banjar Baru, Kalimantan Selatan 70661" }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mt-8">
                    <div class="text-3xl text-[#565950] text-justify" v-html="destinationData.content">
                    </div>
                </div>
            </div>
        </section>

        <!-- Facilities and Map Section -->
        <section class="py-8 px-8">
            <div class="container max-w-6xl mx-auto flex flex-col lg:flex-row gap-8">
                <!-- Map Section -->
                <div class="w-full lg:w-1/2">
                    <h2 class="text-6xl font-semibold mb-4">Peta</h2>
                    <div class="rounded-3xl overflow-hidden h-[400px] w-full shadow-lg">
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
                <div class="w-full lg:w-1/2">
                    <h2 class="text-6xl font-semibold mb-6">Fasilitas</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="(facility, index) in facilities" :key="index" class="flex items-center gap-3">
                            <img :src="`/images/icons/${getFacilityIcon(facility)}.svg`" alt="Facility" class="w-8 h-8" />
                            <span class="text-3xl text-[#565950]">{{ facility }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- User Reviews Section -->
        <section class="py-8 px-8">
            <div class="container max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-6xl font-semibold">Ulasan Pengguna</h2>
                    <button class="flex items-center gap-2 px-6 py-3 bg-[#DF6D2D] text-white rounded-full">
                        <img src="/images/icons/add-circle.svg" alt="Add" class="w-6 h-6" />
                        <span class="text-2xl font-semibold">Beri Ulasan</span>
                    </button>
                </div>

                <!-- Reviews List -->
                <div class="flex flex-col gap-6">
                    <div v-for="testimonial in testimonialsList" :key="testimonial.id" 
                        class="p-6 rounded-3xl bg-white shadow-md">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full overflow-hidden">
                                <img :src="'https://i.pravatar.cc/150'" alt="User" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-2xl font-semibold">{{ "User" }}</span>
                                <div class="flex items-center gap-2">
                                    <img src="/images/icons/medal-star.svg" alt="Rating" class="w-5 h-5" />
                                    <span class="text-xl font-semibold text-[#DF6D2D]">{{ testimonial.rating }}/5</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-2xl text-[#565950]">{{ testimonial.comment }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recommended Destinations Section -->
        <section class="py-16 px-8">
            <div class="container max-w-6xl mx-auto">
                <h2 class="text-6xl font-semibold text-center mb-8">Rekomendasi Wisata</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div v-for="destination in recommendedDestinations" :key="destination.id"
                        class="rounded-3xl overflow-hidden bg-white shadow-lg">
                        <div class="h-64 bg-cover bg-center p-4 flex justify-end rounded-t-3xl relative"
                            :style="{ backgroundImage: `url(${destination.thumb_image})` }">
                            <div class="absolute top-4 right-4 flex items-center gap-1 bg-white/20 backdrop-blur-sm px-2 py-1 rounded-full">
                                <img src="/images/icons/medal-star.svg" alt="Rating" class="w-6 h-6" />
                                <span class="text-2xl font-semibold text-white">{{ '5.0' }}</span>
                            </div>
                        </div>
                        <h3 class="text-2xl font-semibold p-4 text-[#33372C]">{{ destination.name }}</h3>
                        <div class="flex items-center gap-3 px-4 pb-4">
                            <img src="/images/icons/location.svg" alt="Location" class="w-6 h-6" />
                            <span class="text-xl text-[#565950]">{{ '10 Km' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center mt-12">
                    <button class="flex items-center gap-4 px-6 py-3 bg-[#DF6D2D] text-white text-3xl font-semibold rounded-full">
                        <span>Lihat Lebih Banyak</span>
                        <img src="/images/icons/arrow-circle-right-bold.svg" alt="View more" class="w-8 h-8" />
                    </button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<script lang="ts">
// This needs to be outside the setup function
export default {
    methods: {
        getFacilityIcon(facilityName: string): string {
            const iconMap: Record<string, string> = {
                'Pengawasan 24 Jam': 'clock',
                'Keamanan Terjaga': 'security',
                'Kenyamanan Dijamin': 'verify',
                'Villa & Penginapan': 'building-3',
                'Mobil Bisa Masuk': 'car'
            };
            
            // Check if facility name contains any of our keywords
            for (const [key, value] of Object.entries(iconMap)) {
                if (facilityName.includes(key)) {
                    return value;
                }
            }
            
            // Default icon if no match
            return 'verify';
        }
    }
}
</script>