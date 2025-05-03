import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

interface UserLocation {
    latitude: number;
    longitude: number;
    timestamp: number;
}

export function useLocation() {
    // Storage key
    const LOCATION_STORAGE_KEY = 'user_location';
    const LOCATION_EXPIRY_TIME = 24 * 60 * 60 * 1000; // 24 hours in milliseconds
    
    // Reactive references
    const userLocation = ref<{latitude: number, longitude: number} | null>(null);
    const locationStatus = ref<'pending' | 'granted' | 'denied' | 'unsupported'>('pending');
    const locationLastUpdated = ref<number | null>(null);

    /**
     * Retrieve and validate location data from localStorage
     * @returns {boolean} Whether valid location data was found and loaded
     */
    const checkStoredLocation = (): boolean => {
        try {
            const storedData = localStorage.getItem(LOCATION_STORAGE_KEY);
            if (!storedData) return false;
            
            const parsedData = JSON.parse(storedData) as UserLocation;
            const now = Date.now();
            
            // Check if stored location is still valid (not expired)
            if (parsedData && parsedData.timestamp && (now - parsedData.timestamp) < LOCATION_EXPIRY_TIME) {
                userLocation.value = {
                    latitude: parsedData.latitude,
                    longitude: parsedData.longitude
                };
                locationLastUpdated.value = parsedData.timestamp;
                locationStatus.value = 'granted';
                return true;
            }
            
            // Data expired, remove it
            localStorage.removeItem(LOCATION_STORAGE_KEY);
            return false;
        } catch (error) {
            console.error('Error reading location from localStorage:', error);
            return false;
        }
    };

    /**
     * Save location data to localStorage
     * @param {number} latitude - User's latitude
     * @param {number} longitude - User's longitude
     */
    const saveLocationToStorage = (latitude: number, longitude: number): void => {
        try {
            const locationData = {
                latitude,
                longitude,
                timestamp: Date.now()
            };
            localStorage.setItem(LOCATION_STORAGE_KEY, JSON.stringify(locationData));
            locationLastUpdated.value = locationData.timestamp;
        } catch (error) {
            console.error('Error saving location to localStorage:', error);
        }
    };

    /**
     * Get user's geolocation: first tries localStorage, then browser geolocation API
     * @param {string[]} refreshInertiaProps - Optional array of Inertia props to refresh with location data
     */
    const getUserLocation = (refreshInertiaProps: string[] = ['nearbyDestinations']): void => {
        // First check if we have valid stored location
        if (checkStoredLocation()) {
            // If we have stored location, use it for Inertia refresh
            if (userLocation.value) {
                router.reload({
                    data: {
                        latitude: userLocation.value.latitude,
                        longitude: userLocation.value.longitude
                    },
                    only: refreshInertiaProps
                });
            }
            return;
        }
        
        // If no valid stored location, request new location
        if (!navigator.geolocation) {
            locationStatus.value = 'unsupported';
            return;
        }

        locationStatus.value = 'pending';
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLocation.value = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                locationStatus.value = 'granted';
                
                // Save location to localStorage for future use
                saveLocationToStorage(position.coords.latitude, position.coords.longitude);
                
                // Send location to server to get nearby destinations
                router.reload({
                    data: {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    },
                    only: refreshInertiaProps
                });
            },
            (error) => {
                console.error('Error getting location:', error);
                locationStatus.value = 'denied';
            }
        );
    };

    /**
     * Clear stored location data
     */
    const clearLocationData = (): void => {
        try {
            localStorage.removeItem(LOCATION_STORAGE_KEY);
            userLocation.value = null;
            locationStatus.value = 'pending';
            locationLastUpdated.value = null;
        } catch (error) {
            console.error('Error clearing location data:', error);
        }
    };

    return {
        userLocation,
        locationStatus,
        locationLastUpdated,
        getUserLocation,
        checkStoredLocation,
        saveLocationToStorage,
        clearLocationData,
    };
}