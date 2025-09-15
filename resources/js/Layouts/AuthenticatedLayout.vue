<template>
    <div class="min-h-screen bg-gray-50 flex">
        <!-- Mobile sidebar overlay -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false">
            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
        </div>

        <!-- Sidebar -->
        <div :class="[
            'fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full'
        ]">
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r bg-gray-300 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">
                            <img src="/images/logocircle.png" alt="">
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800"><img class="w-[60px] h-10" src="/images/nesaLogo.png"
                            alt=""></h2>
                </div>
                <button @click="sidebarOpen = false"
                    class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <ul class="space-y-1">
                    <li v-for="item in menuItems" :key="item.id">
                        <button @click="handleMenuClick(item)" :class="[
                            'w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group',
                            isActive(item.routeTo)
                                ? 'bg-gradient-to-r from-blue-500 to-blue-700 text-white shadow-lg'
                                : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
                        ]">
                            <span class="mr-3 text-lg">{{ item.icon }}</span>
                            {{ item.name }}
                            <div v-if="isActive(item.routeTo)" class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                        </button>
                    </li>
                </ul>
            </nav>

            <!-- User Profile -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold text-sm">JD</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">John Doe</p>
                        <p class="text-xs text-gray-500 truncate">john@example.com</p>
                    </div>
                    <button class="p-1.5 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 lg:ml-0">
            <!-- Top header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-6">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = true"
                            class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h2 class="text-xl font-bold text-gray-800"><img class="w-[60px] h-10" src="/images/nesaLogo.png"
                            alt=""></h2>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="relative">
                            <input v-model="searchQuery" type="text" placeholder="Search..."
                                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <!-- Notifications -->
                        <button class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-5-5V9a9 9 0 10-9 0v3l-5 5h5m7.75-.75a3 3 0 01-4.5 0" />
                            </svg>
                            <span
                                class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { router, usePage } from '@inertiajs/vue3'
import { ref, reactive } from 'vue'
const page = usePage()

// Reactive data
const sidebarOpen = ref(false)
const activeItem = ref('')
const searchQuery = ref('')

// Menu items
const menuItems = reactive([
    { id: 'dashboard', name: 'Dashboard', icon: '📊', routeTo: 'dashboard' },
    { id: 'nesa', name: 'Nesa', icon: '📈', routeTo: 'nesa.get.dashboard' },
    { id: 'bad-order', name: 'Bad Order', icon: '🚚' , routeTo: 'nesa.get.badorder'},
    { id: 'settings', name: 'Settings', icon: '⚙️'  ,routeTo: 'nesa.get.settings'},
    { id: 'support', name: 'Support', icon: '💬' ,routeTo: 'nesa.get.support'},
])

// Stats data
const stats = reactive([
    { title: 'Total Revenue', value: '$45,231', change: '+20.1%', color: 'text-green-600' },
    { title: 'Orders', value: '1,234', change: '+15.3%', color: 'text-blue-600' },
    { title: 'Customers', value: '5,678', change: '+8.2%', color: 'text-purple-600' },
    { title: 'Growth Rate', value: '12.5%', change: '+2.4%', color: 'text-orange-600' }
])

// Methods
const handleMenuClick = (item) => {
    activeItem.value = item.routeTo
    sidebarOpen.value = false // Close mobile sidebar when item is clicked
    router.get(route(item.routeTo))

}

const isActive = (routeName) => {
    return route().current(routeName) // checks URL
}
</script>

<style scoped>
/* Custom styles if needed */
</style>
