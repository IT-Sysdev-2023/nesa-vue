<template>
    <div class=" bg-gray-50 p-4 md:p-8 flex items-center justify-center">

        <div class="w-full max-w-md">
            <div class="mb-6 flex items-center justify-center gap-2">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.3333 14.6528c.7073 0 1.3856-.2101 1.8857-.7101.5-.5001.781-1.2493.781-1.9565 0-.7073-.281-1.3856-.781-1.8857-.5001-.50007-1.1784-.78102-1.8857-.78102h-.0222c.0133-.14755.0222-.296.0222-.44444-.0033-1.17924-.4328-2.31753-1.2092-3.20508-.7765-.88756-1.8476-1.46455-3.0159-1.62465-1.1683-.1601-2.3551.10749-3.34174.75344-.98658.64596-1.70644 1.62675-2.0269 2.76162-.06223-.00355-.12089-.01866-.184-.01866-.943 0-1.91009.36598-2.57689 1.03277C4.31188 9.24128 4 10.1543 4 11.0973c0 .943.3746 1.8473 1.0414 2.5141.45292.4529 1.01546.7711 1.62527.9285M12 14v3m0 0v3m0-3-2.12134-2.1212M12 17l2.1213 2.1214M12 17H9m3 0h3m-3 0-2.12134 2.1213M12 17l2.1213-2.1213M6 18h.01M18 18h.01" />
                    </svg>
                    Weather Forecast
                </h1>
            </div>
            <!-- Search Bar -->
            <div class="relative mb-6">
                <input v-model="form.city" type="text" placeholder="Search city..."
                    class="w-full p-4 pl-12 pr-16 rounded-xl border-none shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white"
                    @keyup.enter="fetchWeather" />
                <div class="absolute left-4 top-4 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button @click="fetchWeather"
                    class="absolute right-2 top-2 bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Weather Card -->
            <div v-if="weatherData && !cityNotFound?.length"
                class="bg-white rounded-xl shadow-md overflow-hidden transition-all hover:shadow-lg">
                <!-- Main Weather Info -->
                <div class="p-6 text-center">
                    <h1 class="text-2xl font-bold text-gray-800 mb-1">{{ weatherData.name }}</h1>
                    <p class="text-gray-500 text-sm">{{ new Date(weatherData.dt * 1000).toLocaleDateString('en-US', {
                        weekday: 'long', month: 'short', day: 'numeric'
                    }) }}</p>

                    <div class="flex items-center justify-center my-4">
                        <img :src="`https://openweathermap.org/img/wn/${weatherData.weather[0].icon}@2x.png`"
                            :alt="weatherData.weather[0].description" class="w-24 h-24" />
                        <div class="text-left ml-4">
                            <p class="text-5xl font-bold text-gray-800">{{ Math.round(weatherData.main.temp) }}°</p>
                            <p class="text-gray-500 capitalize">{{ weatherData.weather[0].description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Weather Details -->
                <div class="bg-gray-100 px-6 py-4 grid grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                        <span>Humidity: {{ weatherData.main.humidity }}%</span>
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Wind: {{ weatherData.wind.speed }} m/s</span>
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Sunrise: {{ new Date(weatherData.sys.sunrise * 1000).toLocaleTimeString([], {
                            hour:
                                '2-digit', minute: '2-digit'
                        }) }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <span>Sunset: {{ new Date(weatherData.sys.sunset * 1000).toLocaleTimeString([], {
                            hour:
                                '2-digit', minute: '2-digit'
                        }) }}</span>
                    </div>
                </div>
            </div>
            <!-- city not found  -->
            <div v-else-if="cityNotFound" class="text-center py-8 px-4 bg-red-50 rounded-xl border border-red-100">
                <div class="flex flex-col items-center">
                    <!-- Error Icon -->
                    <div class="w-16 h-16 mb-4 text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-full w-full" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <!-- Error Message -->
                    <h3 class="text-lg font-medium text-red-800 mb-1">{{ cityNotFound.message }}</h3>

                    <!-- Try Again Button -->
                    <button @click="form.city = ''; cityNotFound = null"
                        class="mt-4 px-4 py-2 bg-white text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Try another city
                    </button>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <div class="mx-auto w-24 h-24 mb-4 text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <p class="text-gray-500">Search for a city to see the weather</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const form = useForm({
    city: ''
});

const cityNotFound = ref<any | null>(null);
const weatherData = ref<any | null>(null);
const fetchWeather = async () => {
    try {
        const response = await axios.get(route('fetchWeather'), {
            params: {
                city: form.city
            }
        });
        if (response.data.cod === '404') {
            cityNotFound.value = response.data;
            weatherData.value = null;
        } else {
            weatherData.value = response.data;
        }
    } catch (error) {
        console.log(error);
        weatherData.value = null;
        cityNotFound.value = { message: 'Failed to fetch weather data' };
    }
};
onMounted(() => {
    if (!form.city) {
        form.city = 'Tagbilaran';
        fetchWeather();
    }
});
</script>