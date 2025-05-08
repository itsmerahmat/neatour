<script setup lang="ts">
import { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { Button } from '@/components/ui/button';

// Get auth user information
const page = usePage<SharedData>();
const user = computed(() => page.props.auth.user);

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
            <Button @click="toggleMenu" variant="ghost" class="lg:hidden flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path v-if="!isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </Button>
            
            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex gap-4">
                <Link href="/" :class="[
                    'font-medium text-lg xl:text-xl px-3',
                    activePage === 'home' ? 'font-semibold border-b-2 border-primary text-primary' : ''
                ]">
                Beranda
                </Link>
                <Link href="/katalog" :class="[
                    'font-medium text-lg xl:text-xl px-3',
                    activePage === 'catalog' ? 'font-semibold border-b-2 border-primary text-primary' : ''
                ]">
                Katalog
                </Link>
            </nav>
            
            <!-- Desktop Login Button -->
            <Link :href="user ? '/dashboard' : '/login'" class="hidden lg:flex">
                <Button variant="landing">
                    <img src="/images/icons/profile-circle.svg" alt="Profile" class="w-4 h-4 md:w-5 md:h-5" />
                    <span class="font-medium text-lg xl:text-xl">{{ user ? 'Admin' : 'Login' }}</span>
                </Button>
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
                        activePage === 'home' ? 'text-primary font-semibold' : ''
                    ]">
                    Beranda
                </Link>
                <Link href="/katalog" 
                    @click="isMenuOpen = false"
                    :class="[
                        'font-medium text-lg py-1.5',
                        activePage === 'catalog' ? 'text-primary font-semibold' : ''
                    ]">
                    Katalog
                </Link>
                <Link :href="user ? '/dashboard' : '/login'"
                    @click="isMenuOpen = false">
                    <Button variant="landing">
                        <img src="/images/icons/profile-circle.svg" alt="Profile" class="w-4 h-4" />
                        <span class="font-medium text-lg">{{ user ? 'Admin' : 'Login' }}</span>
                    </Button>
                </Link>
            </nav>
        </div>
    </div>
    
    <!-- Add padding element to prevent content from jumping when navbar becomes fixed -->
    <div class="h-12 md:h-16"></div>
</template>