<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
// import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
        <!-- Komponen Toaster untuk menampilkan notifikasi -->
        <Toaster />
    </AppLayout>
</template>
