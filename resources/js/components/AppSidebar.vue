<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { BookOpen, Box, Folder, LayoutGrid, User, Map, Star } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

// Menggunakan usePage dengan tipe SharedData untuk mendapatkan informasi user yang sedang login
const page = usePage<SharedData>();
const user = computed(() => page.props.auth.user);

// Item menu yang selalu ditampilkan
const baseNavItems: NavItem[] = [
    {
        title: 'Destination',
        href: '/destination',
        icon: Map,
    },
    {
        title: 'Testimonial',
        href: '/testimonial',
        icon: Star,
    },
];

// Item menu berdasarkan role
const roleBasedNavItems: Record<string, NavItem[]> = {
    superadmin: [
        // {
        //     title: 'Dashboard',
        //     href: '/dashboard',
        //     icon: LayoutGrid,
        // },
        {
            title: 'User',
            href: '/user',
            icon: User,
        },
        {
            title: 'Category',
            href: '/category',
            icon: Box,
        },
    ],
};

// Menggabungkan menu berdasarkan role
const mainNavItems = computed(() => {
    const role = user.value.role;
    // return [...baseNavItems, ...(roleBasedNavItems[role] || [])];
    return [...(roleBasedNavItems[role] || []), ...baseNavItems];
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
