<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-10">
            <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="mt-1 md:mt-2 text-sm text-gray-600">Welcome back! Here's what's happening today.</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative">
                        <a-tooltip color="#108ee9">
                            <template #title>
                                Hello {{ page.auth.user.firstname }}, I'm bot chokoy.
                            </template>
                            <img src="/storage/images/robot.webp"
                                class="w-16 h-16 md:w-20 md:h-20 object-contain hover:scale-105 transition-transform robot-wave"
                                alt="Assistant Robot" />
                        </a-tooltip>
                    </div>
                    <div>
                        <p class="text-md italic text-gray-900 font-bold">Hello</p>
                        <p class="text-sm md:text-base font-medium text-gray-700">{{ user.employee_name }}</p>
                    </div>
                </div>
            </div>
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
                            <p v-if="usersCount" class="text-2xl font-semibold text-gray-900">{{ usersCount }}</p>
                            <p v-else class="text-lg font-semibold text-gray-900">Counting...</p>
                            <p class="text-xs text-green-600 mt-1">{{ usersCountToday }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-green-100 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Nesa</h3>
                            <p class="text-2xl font-semibold text-gray-900">{{ nesaCount }}</p>
                            <p class="text-xs text-green-600 mt-1">{{ nesaThisMonth }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Active Projects</h3>
                            <p class="text-2xl font-semibold text-gray-900">42</p>
                            <p class="text-xs text-red-600 mt-1">-2 from last month</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-purple-100 text-purple-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Satisfaction Rate</h3>
                            <p class="text-2xl font-semibold text-gray-900">94%</p>
                            <p class="text-xs text-green-600 mt-1">+3% from last month</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Main Chart -->
                <div class="bg-white p-4 w-full rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-medium text-gray-900">Revenue Overview</h2>
                        <select
                            class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option>Last 7 days</option>
                            <option>Last 30 days</option>
                            <option selected>Last 12 months</option>
                        </select>
                    </div>
                    <div class="h-80 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400">
                        [Revenue Chart Placeholder]
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white w-full p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Recent Activity</h2>
                    <div class="space-y-4">
                        <div class="flex items">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">New user registered</p>
                                <p class="text-sm text-gray-500">John Smith just signed up</p>
                                <p class="text-xs text-gray-400 mt-1">2 minutes ago</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">New order received</p>
                                <p class="text-sm text-gray-500">Order #12345 for $249.99</p>
                                <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">System updated</p>
                                <p class="text-sm text-gray-500">Version 2.3.5 deployed</p>
                                <p class="text-xs text-gray-400 mt-1">3 hours ago</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">New review</p>
                                <p class="text-sm text-gray-500">5-star rating from {{ user.employee_name }}</p>
                                <p class="text-xs text-gray-400 mt-1">5 hours ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { ref, computed } from 'vue';
import axios from 'axios';


const info = ref<[] | null>(null);
const usersCount = ref<number | null>(null);
const usersCountToday = ref<number | null>(null);
const nesaThisMonth = ref<number | null>(null);
const nesaCount = ref<number | null>(null);
const fetchingInfo = async () => {
    try {
        const response = await axios.get(route('dashboardInfo'));
        info.value = response.data.user
        usersCount.value = response.data.usersCount
        usersCountToday.value = response.data.usersCountToday
        nesaThisMonth.value = response.data.nesaThisMonth
        nesaCount.value = response.data.nesaCount
    } catch (error) {
        console.log(error);
    }
};
onMounted(() => {
    fetchingInfo();
});

const page = usePage().props as unknown as PageProps;
interface PageProps {
    auth: {
        user: User;
    }
};
interface User {
    firstname: string;
    lastname: string
};

const formatName = computed(() => {
    return `${page.auth.user.lastname}, ${page.auth.user.firstname}`;
});
const value = ref<string>(formatName.value);
const user = ref<any>({});
const fetchUser = async () => {
    const response = await axios.get('http://172.16.161.34/api/gc/filter/employee/name', {
        params: {
            q: value.value
        }
    });
    user.value = response.data.data.employee[0];
};
onMounted(() => {
    fetchUser();
});
</script>
<style>
/* Robot waving animation */
@keyframes wave {
  0%, 100% {
    transform: rotate(0deg);
  }
  25% {
    transform: rotate(20deg);
  }
  75% {
    transform: rotate(-20deg);
  }
}

.robot-wave {
  transition: all 0.3s ease;
  transform-origin: bottom center;
  will-change: transform;
}

.robot-wave:hover {
  animation: wave 0.8s ease-in-out 2;
  transform: scale(1.05);
}
</style>
