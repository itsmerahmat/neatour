<script setup lang="ts">
import {
    Table, TableBody, TableCell, TableHead, TableHeader, TableRow
} from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button'; // Import komponen Button
import { router } from '@inertiajs/vue3';
import { Search, ChevronUp, ChevronDown } from 'lucide-vue-next';
import { ref, watch } from 'vue';

// Debounce function
function useDebounce<T extends (...args: any[]) => any>(fn: T, wait: number) {
    let timeout: ReturnType<typeof setTimeout> | null = null;
    
    return function(...args: Parameters<T>) {
        if (timeout !== null) {
            clearTimeout(timeout);
        }
        
        timeout = setTimeout(() => {
            fn(...args);
            timeout = null;
        }, wait);
    };
}

interface Column {
    key: string;
    label: string;
    sortable?: boolean;
    class?: string;
    render?: (row: any) => any;
}

interface Pagination {
    current_page: number;
    data: any[];
    from: number;
    last_page: number;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    path: string;
    per_page: number;
    to: number;
    total: number;
}

const props = defineProps<{
    // The route to fetch data from
    route: string;
    // The data with pagination info
    data: Pagination;
    // The columns to display
    columns: Column[];
    // Current filters
    filters: {
        search: string;
        perPage: number;
        sortField: string;
        sortDirection: string;
    };
    // Show the search input
    searchable?: boolean;
    // Placeholder for the search input
    searchPlaceholder?: string;
}>();

const emit = defineEmits<{
    (e: 'rowAction', data: { action: string, row: any }): void
}>();

// Define available per page options
const perPageOptions = [
    { label: '10 per halaman', value: '10' },
    { label: '25 per halaman', value: '25' },
    { label: '50 per halaman', value: '50' },
    { label: '100 per halaman', value: '100' },
];

// Table state
const search = ref(props.filters.search);
const perPage = ref(props.filters.perPage.toString());
const sortField = ref(props.filters.sortField);
const sortDirection = ref(props.filters.sortDirection);

// Using debounce function for search
const performSearch = () => {
    router.get(props.route, {
        search: search.value,
        perPage: perPage.value,
        sortField: sortField.value,
        sortDirection: sortDirection.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const debouncedSearch = useDebounce(performSearch, 300);

// Watch for search input changes
watch(search, () => {
    debouncedSearch();
});

// Handle per page change
function handlePerPageChange(value: string | null) {
    if (value === null) return;
    perPage.value = value;
    router.get(props.route, {
        search: search.value,
        perPage: perPage.value,
        sortField: sortField.value,
        sortDirection: sortDirection.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

// Handle sorting
function sort(column: Column) {
    if (!column.sortable) return;
    
    sortDirection.value = column.key === sortField.value 
        ? sortDirection.value === 'asc' 
            ? 'desc' 
            : 'asc'
        : 'asc';
    sortField.value = column.key;

    router.get(props.route, {
        search: search.value,
        perPage: perPage.value,
        sortField: sortField.value,
        sortDirection: sortDirection.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

// Expose function to handle row click or actions
function handleAction(action: string, row: any) {
    emit('rowAction', { action, row });
}

// Slot API definition
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const slots = defineSlots<{
    // Slot for custom cell rendering
    'cell'(props: { column: Column; row: any; index: number }): any,
    // Slot for action buttons
    'actions'(props: { row: any; onAction: (action: string, row: any) => void }): any,
    // Slot for custom pagination
    'pagination'(props: { data: Pagination }): any,
    // Slot for custom empty state
    'empty'(): any,
    // Slot for custom header content
    'header'(): any,
    // Slot for custom filters
    'filters'(): any
}>()
</script>

<template>
    <div class="w-full">
        <!-- Search and filters area -->
        <div class="flex flex-col sm:flex-row mb-4 space-y-2 sm:space-y-0 w-full">
            <div class="w-auto">
                <slot name="header">
                    <!-- Default header content -->
                </slot>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-2 items-center justify-between w-full">
                <slot name="filters">
                    <!-- Default filters content -->
                    <div v-if="searchable !== false" class="relative w-full sm:w-80">
                        <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-500" />
                        <Input
                            v-model="search"
                            :placeholder="searchPlaceholder || 'Search...'"
                            class="pl-8 w-full"
                        />
                    </div>
                    
                    <div>
                        <Select v-model="perPage" @update:modelValue="(val) => handlePerPageChange(val as string)">
                            <SelectTrigger class="w-[180px]">
                                <SelectValue placeholder="Rows per page" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="option in perPageOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </slot>
            </div>
        </div>
        
        <!-- Table -->
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead 
                        v-for="column in columns" 
                        :key="column.key"
                        :class="[column.class, column.sortable ? 'cursor-pointer' : '']"
                        @click="column.sortable ? sort(column) : null"
                    >
                        <div class="flex items-center">
                            {{ column.label }}
                            <template v-if="column.sortable">
                                <ChevronUp v-if="sortField === column.key && sortDirection === 'asc'" class="h-4 w-4 ml-1" />
                                <ChevronDown v-else-if="sortField === column.key && sortDirection === 'desc'" class="h-4 w-4 ml-1" />
                            </template>
                        </div>
                    </TableHead>
                    <!-- Action column header if actions slot exists -->
                    <TableHead v-if="$slots.actions">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="(row, index) in data?.data || []" :key="row.id ?? index">
                    <template v-for="column in columns" :key="column.key">
                        <TableCell :class="column.class">
                            <!-- Use custom cell slot if provided -->
                            <slot name="cell" :column="column" :row="row" :index="index">
                                <!-- Render function if provided -->
                                <template v-if="column.render">
                                    <component :is="column.render(row)" />
                                </template>
                                <template v-else>
                                    {{ row[column.key] }}
                                </template>
                            </slot>
                        </TableCell>
                    </template>
                    
                    <!-- Actions column if actions slot exists -->
                    <TableCell v-if="$slots.actions">
                        <slot name="actions" :row="row" :onAction="handleAction" />
                    </TableCell>
                </TableRow>
                
                <!-- Empty state -->
                <TableRow v-if="!data?.data?.length">
                    <TableCell :colspan="$slots.actions ? columns.length + 1 : columns.length" class="text-center py-8">
                        <slot name="empty">
                            Tidak ada data yang ditemukan
                        </slot>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        
        <!-- Pagination -->
        <div class="mt-4 w-full">
            <slot name="pagination" :data="data">
                <!-- Default pagination -->
                <div v-if="data?.data?.length > 0" class="flex flex-col sm:flex-row items-center justify-between w-full gap-2">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ data?.from || 0 }}</span>
                            to
                            <span class="font-medium">{{ data?.to || 0 }}</span>
                            of
                            <span class="font-medium">{{ data?.total || 0 }}</span>
                            results
                        </p>
                    </div>
                    <!-- Pagination section only -->
                    <div v-if="data?.links && data.links.length > 2">
                        <nav class="isolate rounded-md shadow-sm" aria-label="Pagination">
                            <Button
                                v-for="(link, i) in data.links"
                                :key="i"
                                :disabled="!link.url"
                                :variant="link.active ? 'default' : 'outline'"
                                :class="[
                                    'rounded-none px-3 py-1 text-sm',
                                    !link.url && 'opacity-50 cursor-not-allowed',
                                    i === 0 ? 'rounded-l-md' : '',
                                    i === data.links.length - 1 ? 'rounded-r-md' : ''
                                ]"
                                @click="link.url ? router.get(link.url, {}, { preserveScroll: true }) : null"
                            >
                                <span v-html="link.label"></span>
                            </Button>
                        </nav>
                    </div>
                </div>
            </slot>
        </div>
    </div>
</template>