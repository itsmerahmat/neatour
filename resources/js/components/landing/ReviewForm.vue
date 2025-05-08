<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    destinationId: {
        type: Number,
        required: true
    },
    open: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:open']);

// Review form
const reviewForm = useForm({
    destination_id: props.destinationId,
    name: '',
    rating: 0,
    comment: '',
});

// Update modal state
const updateOpen = (value: boolean) => {
    emit('update:open', value);
};

// Submit review
const submitReview = () => {
    reviewForm.post('/testimonial', {
        preserveScroll: true,
        onSuccess: () => {
            updateOpen(false);
            reviewForm.reset();
        }
    });
};
</script>

<template>
    <Dialog :open="open" @update:open="updateOpen">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Beri Ulasan</DialogTitle>
                <DialogDescription>
                    Bagikan pengalaman Anda dengan destinasi wisata ini
                </DialogDescription>
            </DialogHeader>
            
            <form @submit.prevent="submitReview">
                <!-- Name input -->
                <div class="space-y-2 mb-4">
                    <Label for="name">Nama</Label>
                    <Input
                        id="name"
                        v-model="reviewForm.name"
                        type="text"
                        placeholder="Masukkan nama Anda..."
                    />
                    <InputError :message="reviewForm.errors.name" />
                </div>
                
                <!-- Rating selection -->
                <div class="space-y-2 mb-4">
                    <Label>Rating</Label>
                    <div class="flex gap-2">
                        <button 
                            v-for="star in 5" 
                            :key="star" 
                            type="button"
                            @click="reviewForm.rating = star" 
                            class="focus:outline-none">
                            <img 
                                :src="reviewForm.rating >= star ? '/images/icons/medal-star-primary.svg' : '/images/icons/medal-star-invert.svg'" 
                                alt="star" 
                                class="w-6 h-6"
                            >
                        </button>
                    </div>
                    <InputError :message="reviewForm.errors.rating" />
                </div>
                
                <!-- Comment input -->
                <div class="space-y-2 mb-6">
                    <Label for="comment">Komentar</Label>
                    <Textarea
                        id="comment"
                        v-model="reviewForm.comment"
                        rows="4"
                        placeholder="Bagikan pengalaman Anda..."
                    />
                    <InputError :message="reviewForm.errors.comment" />
                </div>
                
                <DialogFooter>
                    <Button type="button" variant="outline" @click="updateOpen(false)">Batal</Button>
                    <Button
                        type="submit"
                        :disabled="reviewForm.processing"
                    >
                        {{ reviewForm.processing ? 'Memproses...' : 'Kirim Ulasan' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>