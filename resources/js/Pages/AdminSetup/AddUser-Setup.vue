<template>
    <Head title="Add User" />
    <AuthenticatedLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-10">
            <!-- Header -->
            <div class="mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Add New User</h1>
                    <p class="mt-2 text-sm text-gray-600">Creating user credentials for login access</p>
                </div>
            </div>

            <!-- Stats Cards (existing code remains the same) -->
            <!-- ... -->

            <!-- Form Section -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">User Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Column -->
                    <div class="space-y-4">
                        <!-- First Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Firstname</label>
                            <input 
                                type="text" 
                                placeholder="Juan" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            />
                        </div>

                        <!-- Middle Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Middlename</label>
                            <input 
                                type="text" 
                                placeholder="D" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            />
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input 
                                type="text" 
                                placeholder="juan.delacruz" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            />
                        </div>
                    </div>

                    <!-- Second Column -->
                    <div class="space-y-4">
                        <!-- Last Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lastname</label>
                            <input 
                                type="text" 
                                placeholder="Cruz" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            />
                        </div>

                        <!-- Name Extension -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name Extension</label>
                            <input 
                                type="text" 
                                placeholder="Jr, Sr" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            />
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input 
                                type="password" 
                                placeholder="password" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            />
                        </div>
                    </div>
                </div>

                <!-- Full-width fields -->
                <div class="mt-6 space-y-4">
                    <!-- User Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Usertype</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="1">Admin</option>
                            <option value="2">User</option>
                            <option value="3">Buyer</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-8">
                        <button 
                            type="button" 
                            class="px-6 py-2 mr-4 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
                        >
                            Create User
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>