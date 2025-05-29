<template>
  <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <a href="#" class="flex items-center space-x-2">
            <span class="text-indigo-600">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                <path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.75a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.913-.143z" clip-rule="evenodd" />
              </svg>
            </span>
            <span class="text-xl font-bold text-gray-900">Modern</span>
          </a>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center space-x-8">
          <div class="hidden md:flex space-x-8">
            <a href="#" class="text-gray-900 hover:text-indigo-600 px-1 py-2 text-sm font-medium transition-colors duration-200">Home</a>

            <!-- Dropdown -->
            <div class="relative group">
              <button class="text-gray-900 hover:text-indigo-600 px-1 py-2 text-sm font-medium flex items-center space-x-1 transition-colors duration-200">
                <span>Products</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 transform group-hover:rotate-180 transition-transform duration-200">
                  <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
              </button>
              <div class="absolute left-0 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all duration-200 transform z-50">
                <div class="py-1">
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Web Design</a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Mobile Apps</a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">UI/UX</a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Development</a>
                </div>
              </div>
            </div>

            <a href="#" class="text-gray-900 hover:text-indigo-600 px-1 py-2 text-sm font-medium transition-colors duration-200">Pricing</a>
            <a href="#" class="text-gray-900 hover:text-indigo-600 px-1 py-2 text-sm font-medium transition-colors duration-200">About</a>
            <a href="#" class="text-gray-900 hover:text-indigo-600 px-1 py-2 text-sm font-medium transition-colors duration-200">Contact</a>
          </div>
        </div>

        <!-- CTA Buttons -->
        <div class="hidden md:flex items-center space-x-4">
          <a href="#" class="text-gray-900 hover:text-indigo-600 px-3 py-2 text-sm font-medium transition-colors duration-200">Log in</a>
          <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 shadow-sm">Sign up</a>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden flex items-center">
          <button
            @click="toggleMobileMenu"
            type="button"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out"
            :aria-expanded="isMobileMenuOpen.toString()"
            aria-controls="mobile-menu"
          >
            <span class="sr-only">Open main menu</span>
            <svg :class="{'hidden': isMobileMenuOpen, 'block': !isMobileMenuOpen}" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg :class="{'hidden': !isMobileMenuOpen, 'block': isMobileMenuOpen}" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div
      v-show="isMobileMenuOpen"
      class="md:hidden origin-top"
      id="mobile-menu"
    >
      <div class="pt-2 pb-3 space-y-1">
        <a href="#" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-indigo-600 hover:bg-gray-50">Home</a>

        <!-- Mobile dropdown -->
        <div class="px-3 py-2">
          <button
            @click="toggleMobileProductsMenu"
            class="w-full flex justify-between items-center text-base font-medium text-gray-900 hover:text-indigo-600"
            :aria-expanded="isMobileProductsMenuOpen.toString()"
          >
            <span>Products</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 transform transition-transform duration-200" :class="{'rotate-180': isMobileProductsMenuOpen}">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
          </button>
          <div v-show="isMobileProductsMenuOpen" class="mt-2 space-y-1 pl-4">
            <a href="#" class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-indigo-600 hover:bg-gray-50">Web Design</a>
            <a href="#" class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-indigo-600 hover:bg-gray-50">Mobile Apps</a>
            <a href="#" class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-indigo-600 hover:bg-gray-50">UI/UX</a>
            <a href="#" class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-indigo-600 hover:bg-gray-50">Development</a>
          </div>
        </div>

        <a href="#" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-indigo-600 hover:bg-gray-50">Pricing</a>
        <a href="#" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-indigo-600 hover:bg-gray-50">About</a>
        <a href="#" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-indigo-600 hover:bg-gray-50">Contact</a>
      </div>
      <div class="pt-4 pb-3 border-t border-gray-200">
        <div class="space-y-1 px-3">
          <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:text-indigo-600 hover:bg-gray-50">Log in</a>
          <a href="#" class="block w-full px-3 py-2 rounded-md text-base font-medium text-center text-white bg-indigo-600 hover:bg-indigo-700">Sign up</a>
        </div>
      </div>
    </div>
  </nav>
  <div>
    <slot/>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const isMobileMenuOpen = ref(false);
const isMobileProductsMenuOpen = ref(false);

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const toggleMobileProductsMenu = () => {
  isMobileProductsMenuOpen.value = !isMobileProductsMenuOpen.value;
};
</script>
