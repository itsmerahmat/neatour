<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';

// Import Vue Leaflet components
import { LMap, LTileLayer, LMarker, LIcon } from "@vue-leaflet/vue-leaflet";
import "leaflet/dist/leaflet.css";

interface Props {
  modelValue: {
    lat: number;
    lon: number;
  };
  errorLat?: string;
  errorLon?: string;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'update:modelValue', value: { lat: number; lon: number }): void;
}>();

// Map state
const zoom = ref(13);
const center = ref([props.modelValue.lat, props.modelValue.lon]);
const markerLatLng = ref([props.modelValue.lat, props.modelValue.lon]);
const map = ref(null);
const searchQuery = ref('');

// Fix Leaflet icon issue with webpack
const leafletIcon = ref({
  iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
  iconRetinaUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon-2x.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  tooltipAnchor: [16, -28],
  shadowSize: [41, 41]
});

// Handle map click to update marker position
function handleMapClick(e: any) {
  const { lat, lng } = e.latlng;
  markerLatLng.value = [lat, lng];
  updateCoordinates(lat, lng);
}

// Update marker position on drag end
function onMarkerDragEnd(event: any) {
  const marker = event.target;
  const position = marker.getLatLng();
  markerLatLng.value = [position.lat, position.lng];
  updateCoordinates(position.lat, position.lng);
}

// Update coordinates and emit changes
function updateCoordinates(lat: number, lon: number) {
  emit('update:modelValue', { lat, lon });
}

// Function to search for a location using Nominatim (OpenStreetMap's search API)
function searchLocation() {
  if (!searchQuery.value) return;
  
  const query = encodeURIComponent(searchQuery.value);
  
  // Use Nominatim API (OpenStreetMap's free geocoding service)
  fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&limit=1`, {
    headers: {
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data && data.length > 0) {
      const result = data[0];
      const lat = parseFloat(result.lat);
      const lon = parseFloat(result.lon);
      
      // Update map and marker through reactivity
      markerLatLng.value = [lat, lon];
      center.value = [lat, lon];
      zoom.value = 14;
      
      // Emit updated values
      updateCoordinates(lat, lon);
    } else {
      toast.error('Lokasi tidak ditemukan', {
        description: 'Coba cari dengan kata kunci yang lebih spesifik',
      });
    }
  })
  .catch(error => {
    console.error('Error searching location:', error);
    toast.error('Gagal mencari lokasi', {
      description: 'Terjadi kesalahan saat mencari lokasi',
    });
  });
}

// Watch for prop changes to update the marker and center
watch(() => props.modelValue, (newValue) => {
  markerLatLng.value = [newValue.lat, newValue.lon];
  center.value = [newValue.lat, newValue.lon];
}, { deep: true });

function onLatChange(event: Event) {
  const target = event.target as HTMLInputElement;
  const lat = parseFloat(target.value);
  if (!isNaN(lat)) {
    updateCoordinates(lat, props.modelValue.lon);
  }
}

function onLonChange(event: Event) {
  const target = event.target as HTMLInputElement;
  const lon = parseFloat(target.value);
  if (!isNaN(lon)) {
    updateCoordinates(props.modelValue.lat, lon);
  }
}
</script>

<template>
  <div class="space-y-4">
    <h3 class="font-medium text-lg">Lokasi</h3>
    
    <!-- Location search -->
    <div class="space-y-2">
      <Label for="location-search">Cari Lokasi</Label>
      <div class="flex space-x-2">
        <Input 
          id="location-search"
          v-model="searchQuery"
          type="text" 
          placeholder="Cari lokasi (contoh: Malioboro, Yogyakarta)"
          @keydown.enter.prevent="searchLocation"
        />
        <Button type="button" @click="searchLocation" variant="secondary">Cari</Button>
      </div>
    </div>

    <!-- Vue Leaflet map -->
    <div class="space-y-2">
      <div class="w-full h-[400px] rounded-lg border border-gray-300">
        <LMap
          ref="map"
          v-model:zoom="zoom"
          v-model:center="center"
          :use-global-leaflet="false"
          @click="handleMapClick"
          class="h-full w-full rounded-lg z-0"
        >
          <LTileLayer
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            layer-type="base"
            name="OpenStreetMap"
            attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          />
          <LMarker
            :lat-lng="markerLatLng"
            draggable
            @dragend="onMarkerDragEnd"
          >
            <LIcon :icon-url="leafletIcon.iconUrl" :shadow-url="leafletIcon.shadowUrl" />
          </LMarker>
        </LMap>
      </div>
      <p class="text-sm text-muted-foreground">
        Klik pada peta untuk menandai lokasi atau seret pin untuk menyesuaikan posisi
      </p>
    </div>

    <!-- Coordinate inputs -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
      <div class="space-y-2">
        <Label for="lat">Latitude</Label>
        <Input 
          id="lat" 
          v-model="modelValue.lat"
          @input="onLatChange"
          type="number" 
          step="any" 
          placeholder="Contoh: -7.797068" 
        />
        <div v-if="errorLat" class="text-sm text-red-500">{{ errorLat }}</div>
      </div>

      <div class="space-y-2">
        <Label for="lon">Longitude</Label>
        <Input 
          id="lon" 
          v-model="modelValue.lon"
          @input="onLonChange"
          type="number" 
          step="any" 
          placeholder="Contoh: 110.370529" 
        />
        <div v-if="errorLon" class="text-sm text-red-500">{{ errorLon }}</div>
      </div>
    </div>
  </div>
</template>

<style>
/* Vue Leaflet custom styling */
.leaflet-container {
  font-family: inherit;
}

.leaflet-popup-content {
  font-size: 14px;
  padding: 8px;
}

.leaflet-control-zoom {
  border: none !important;
  box-shadow: 0 1px 5px rgba(0,0,0,0.2) !important;
}

.leaflet-control-zoom a {
  border-radius: 4px !important;
  background-color: white !important;
  color: #333 !important;
}

.leaflet-control-attribution {
  font-size: 10px !important;
}

.leaflet-div-icon {
  background: transparent;
  border: none;
}
</style>