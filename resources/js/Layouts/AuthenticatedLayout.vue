<template>
    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="#" class="flex items-center space-x-2">

                        <img class="w-10 h-10" src="/storage/images/logo.png" alt="iamge">
                        <span class="text-xl font-bold text-gray-900">Nesa</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <div class="hidden md:flex space-x-8">

                        <button
                            class="flex items-center gap-1 text-gray-900 hover:text-indigo-600 px-1 py-2 text-sm font-medium transition-colors duration-200">
                            <HomeOutlined />
                            <Link :href="route('dashboard')"><span>Home</span></Link>
                        </button>
                        <div class="relative group">
                            <button
                                class="text-gray-900  gap-1  hover:text-indigo-600 px-1 py-2 text-sm font-medium flex items-center space-x-1 transition-colors duration-200">
                                <NotificationOutlined />
                                <span>Nesa</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 transform group-hover:rotate-180 transition-transform duration-200">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div
                                class="absolute left-0 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all duration-200 transform z-50">
                                <div class="py-1">
                                    <Link :href="route('nesa.get.list')"
                                        class="flex items-center gap-1 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        Near Expiry Stock Advise</Link>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        Summary Of Supplier</a>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Summary
                                        Of SWAF</a>
                                </div>
                            </div>
                        </div>

                        <div class="relative group" v-if="page.auth.user.usertype == 1">
                            <button
                                class="text-gray-900  gap-1  hover:text-indigo-600 px-1 py-2 text-sm font-medium flex items-center space-x-1 transition-colors duration-200">
                                <DeploymentUnitOutlined />
                                <span>Masterfile</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 transform group-hover:rotate-180 transition-transform duration-200">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div
                                class="absolute left-0 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all duration-200 transform z-50">
                                <div class="py-1">
                                    <Link :href="route('admin.masterfile')"
                                        class="flex items-center gap-1 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    Products</Link>
                                    <Link :href="route('admin.supplier.list')"
                                        class="flex items-center gap-1 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    Supplier List</Link>

                                </div>
                            </div>
                        </div>


                        <!-- <a href="#"
                            class="text-gray-900 hover:text-indigo-600 px-1 py-2 text-sm font-medium transition-colors duration-200">Pricing</a> -->
                        <a href="#"
                            class="text-gray-900 hover:text-indigo-600 px-1 py-2 text-sm font-medium transition-colors duration-200">About</a>
                        <!-- <a href="#"
                            class="text-gray-900 hover:text-indigo-600 px-1 py-2 text-sm font-medium transition-colors duration-200">Contact</a> -->
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <div class="relative group">
                        <button
                            class="text-gray-900 hover:text-indigo-600 px-1 py-2 text-sm font-medium flex items-center space-x-1 transition-colors duration-200">
                            <img class="w-10 h-10"
                                :src="page.auth.user.image ? page.auth.user.image : 'https://www.shutterstock.com/image-vector/user-profile-icon-vector-avatar-600nw-2247726673.jpg'"
                                alt="">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-4 h-4 transform group-hover:rotate-180 transition-transform duration-200">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div
                            class="absolute left-0 mt-2 w-56 origin-top-left rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all duration-200 transform z-50">
                            <div class="py-1">
                                <ProfileView />
                                <ProfileView />
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="toggleMobileMenu" type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out"
                        :aria-expanded="isMobileMenuOpen.toString()" aria-controls="mobile-menu">
                        <span class="sr-only">Open main menu</span>
                        <svg :class="{ 'hidden': isMobileMenuOpen, 'block': !isMobileMenuOpen }" class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg :class="{ 'hidden': !isMobileMenuOpen, 'block': isMobileMenuOpen }" class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div v-show="isMobileMenuOpen" class="md:hidden origin-top" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="#"
                    class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-indigo-600 hover:bg-gray-50">Home</a>

                <!-- Mobile dropdown -->
                <div class="px-3 py-2">
                    <button @click="toggleMobileProductsMenu"
                        class="w-full flex justify-between items-center text-base font-medium text-gray-900 hover:text-indigo-600"
                        :aria-expanded="isMobileProductsMenuOpen.toString()">
                        <span>Products</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-5 h-5 transform transition-transform duration-200"
                            :class="{ 'rotate-180': isMobileProductsMenuOpen }">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div v-show="isMobileProductsMenuOpen" class="mt-2 space-y-1 pl-4">
                        <a href="#"
                            class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-indigo-600 hover:bg-gray-50">Web
                            Design</a>
                        <a href="#"
                            class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-indigo-600 hover:bg-gray-50">Mobile
                            Apps</a>
                        <a href="#"
                            class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-indigo-600 hover:bg-gray-50">UI/UX</a>
                        <a href="#"
                            class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-indigo-600 hover:bg-gray-50">Development</a>
                    </div>
                </div>

                <a href="#"
                    class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-indigo-600 hover:bg-gray-50">Pricing</a>
                <a href="#"
                    class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-indigo-600 hover:bg-gray-50">About</a>
                <a href="#"
                    class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-indigo-600 hover:bg-gray-50">Contact</a>
                <div class="px-3 py-2">
                    <button @click="toggleMobileSetupMenu"
                        class="w-full flex justify-between items-center text-base font-medium text-gray-900 hover:text-indigo-600"
                        :aria-expanded="isMobileSetupMenuOpen.toString()">
                        <span>Setup</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-5 h-5 transform transition-transform duration-200"
                            :class="{ 'rotate-180': isMobileSetupMenuOpen }">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div v-show="isMobileSetupMenuOpen" class="mt-2 space-y-1 pl-4">
                        <a href="#"
                            class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-indigo-600 hover:bg-gray-50">Add
                            User</a>
                    </div>
                </div>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="space-y-1 px-3">
                    <a href="#"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:text-indigo-600 hover:bg-gray-50">Log
                        in</a>
                    <a href="#"
                        class="block w-full px-3 py-2 rounded-md text-base font-medium text-center text-white bg-indigo-600 hover:bg-indigo-700">Sign
                        up</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mt-5"></div>
        <slot />
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { HomeOutlined, NotificationOutlined, DeploymentUnitOutlined } from '@ant-design/icons-vue';
import { ref } from 'vue';
import { usePage } from "@inertiajs/vue3";
import ProfileView from "../Pages/ProfilePartials/ProfileView.vue"

const page = usePage().props;

const isMobileMenuOpen = ref(false);
const isMobileProductsMenuOpen = ref(false);
const isMobileSetupMenuOpen = ref(false);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const toggleMobileProductsMenu = () => {
    isMobileProductsMenuOpen.value = !isMobileProductsMenuOpen.value;
};

const toggleMobileSetupMenu = () => {
    isMobileSetupMenuOpen.value = !isMobileSetupMenuOpen.value
}
</script>
