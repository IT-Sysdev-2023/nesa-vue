<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center relative overflow-hidden">

        <!-- Background Image Layer -->
        <div class="absolute inset-0">
            <div
                class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80')] bg-cover bg-center opacity-80 blur-sm">
            </div>
        </div>

        <!-- Login Card -->
        <div
            class="relative z-10 w-full max-w-md mx-auto p-8 rounded-2xl shadow-2xl bg-white/90 backdrop-blur-md border border-blue-100 shadow-blue-300">

            <Head title="Log in" />

            <!-- Logo & Title -->
            <div class="text-center mb-6">
                <div class="flex justify-center mb-3">
                    <div class="bg-blue-100 p-4 rounded-full shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="h-10 w-10 text-blue-600"
                            fill="none" stroke="currentColor">
                            <rect x="4" y="28" width="18" height="24" rx="2" stroke-width="2" fill="white" />
                            <path d="M4 28l9 6 9-6" stroke-width="2" />
                            <rect x="26" y="20" width="10" height="32" rx="2" stroke-width="2" fill="white" />
                            <rect x="28.5" y="14" width="5" height="6" rx="1" stroke-width="2" fill="white" />
                            <rect x="42" y="32" width="12" height="20" rx="2" stroke-width="2" fill="white" />
                            <line x1="42" y1="36" x2="54" y2="36" stroke-width="1.5" />
                            <line x1="42" y1="40" x2="54" y2="40" stroke-width="1.5" />
                            <circle cx="10" cy="56" r="2" fill="currentColor" />
                            <circle cx="32" cy="56" r="2" fill="currentColor" />
                            <circle cx="48" cy="56" r="2" fill="currentColor" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Welcome to Nesa</h1>
                <p class="text-gray-600 mt-1">Login to access your account</p>
            </div>

            <!-- Login Form -->
            <form @submit.prevent="submit" class="space-y-6">

                <div>
                    <InputLabel for="email" value="Username" class="text-gray-700" />
                    <TextInput id="email" type="text" v-model="form.email"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300 focus:outline-none"
                        placeholder="username.sample" required autofocus autocomplete="username" />
                    <InputError class="mt-1 text-sm text-red-600" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Password" class="text-gray-700" />
                    <TextInput id="password" type="password" v-model="form.password"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300 focus:outline-none"
                        placeholder="••••••••" required autocomplete="current-password" />
                    <InputError class="mt-1 text-sm text-red-600" :message="form.errors.password" />
                </div>

                <div>
                    <label>
                        <Checkbox name="remember" v-model:checked="form.remember" class="mr-2" />
                        <span class="text-gray-700 text-sm">Remember me</span>
                    </label>
                </div>

                <PrimaryButton
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md shadow-md transition disabled:opacity-50"
                    :disabled="form.processing">
                    <span v-if="!form.processing">Sign in</span>
                    <svg v-else class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                </PrimaryButton>

            </form>
        </div>
    </div>
</template>
