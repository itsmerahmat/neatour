<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';

// Define props for highlighting the active route
defineProps({
    activePage: {
        type: String,
        default: 'home',
        validator: (value: string) => ['home', 'catalog'].includes(value)
    }
});

// Add scroll behavior for sticky navbar
const isScrolled = ref(false);
// Menu toggle for mobile navigation
const isMenuOpen = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

// Add and remove scroll event listener
onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <header
        class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center py-2 px-4 md:px-6 w-full transition-all duration-300"
        :class="[
            isScrolled ? 'bg-white shadow-md' : 'bg-white',
        ]">
        <div class="container lg:max-w-4/5 mx-auto flex justify-between items-center">
            <Link href="/" class="h-7 md:h-12">
                <img src="/images/logo/logo.png" alt="NeaTour Logo" class="h-full" />
            </Link>
            
            <!-- Mobile Menu Button -->
            <button @click="toggleMenu" class="lg:hidden flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#DF6D2D]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path v-if="!isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex gap-4">
                <Link href="/" :class="[
                    'font-medium text-lg xl:text-xl px-3',
                    activePage === 'home' ? 'font-semibold border-b-2 border-[#DF6D2D] text-[#DF6D2D]' : ''
                ]">
                Beranda
                </Link>
                <Link href="/katalog" :class="[
                    'font-medium text-lg xl:text-xl px-3',
                    activePage === 'catalog' ? 'font-semibold border-b-2 border-[#DF6D2D] text-[#DF6D2D]' : ''
                ]">
                Katalog
                </Link>
            </nav>
            
            <!-- Desktop Login Button -->
            <Link href="/login" class="hidden lg:flex items-center gap-1.5 px-3 py-1 bg-[#DF6D2D] text-white rounded-full">
                <img src="/images/icons/profile-circle.svg" alt="Profile" class="w-4 h-4 md:w-5 md:h-5" />
                <span class="font-medium text-lg xl:text-xl">Login</span>
            </Link>
        </div>
    </header>
    
    <!-- Mobile Navigation Menu -->
    <div v-show="isMenuOpen" class="fixed top-[53px] left-0 right-0 bg-white shadow-lg z-40 lg:hidden transition-all duration-300">
        <div class="container mx-auto py-3 px-5">
            <nav class="flex flex-col space-y-3">
                <Link href="/" 
                    @click="isMenuOpen = false"
                    :class="[
                        'font-medium text-lg py-1.5',
                        activePage === 'home' ? 'text-[#DF6D2D] font-semibold' : ''
                    ]">
                    Beranda
                </Link>
                <Link href="/katalog" 
                    @click="isMenuOpen = false"
                    :class="[
                        'font-medium text-lg py-1.5',
                        activePage === 'catalog' ? 'text-[#DF6D2D] font-semibold' : ''
                    ]">
                    Katalog
                </Link>
                <Link href="/login">
                    @click="isMenuOpen = false"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-[#DF6D2D] text-white rounded-full w-fit">
                    <img src="/images/icons/profile-circle.svg" alt="Profile" class="w-4 h-4" />
                    <span class="font-medium text-lg">Login</span>
                </Link>
            </nav>
        </div>
    </div>
    
    <!-- Add padding element to prevent content from jumping when navbar becomes fixed -->
    <div class="h-12 md:h-16"></div>
</template>