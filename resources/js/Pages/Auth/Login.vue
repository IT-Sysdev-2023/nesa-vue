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
                    <div class="bg-blue-100 rounded-full shadow-inner">
                        <img src="/images/logo.png" alt="Nesa Logo" class="h-24 w-24 object-cover">
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">NESA</h1>
                <h2 class="text-gray-600 mt-1 font-medium">Near Expiry Stock Advice</h2>
                <p class="text-gray-500 mt-3 text-sm">Welcome back, please sign in to continue</p>
            </div>

            <!-- Login Form -->
            <form @submit.prevent="submit" class="space-y-6">

                <div>
                    <InputLabel for="username" value="Username" class="text-gray-700" />
                    <TextInput id="username" type="text" v-model="form.username"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300 focus:outline-none"
                        placeholder="delacruz.juan" required autofocus autocomplete="username" />
                    <InputError class="mt-1 text-sm text-red-600" :message="form.errors.username" />
                </div>

                <div>
                    <InputLabel for="password" value="Password" class="text-gray-700" />
                    <TextInput id="password" type="password" v-model="form.password"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300 focus:outline-none"
                        placeholder="password" required autocomplete="current-password" />
                    <InputError class="mt-1 text-sm text-red-600" :message="form.errors.password" />
                </div>

                <button
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md shadow-md transition disabled:opacity-50 flex items-center justify-center"
                    :disabled="form.processing">
                    <span v-if="!form.processing">Log in</span>
                    <svg v-else class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                </button>

                <div class="mb-6 text-center">
                    <div
                        class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-blue-50 to-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium text-blue-700">{{ currentGreetings }}</span>
                    </div>
                </div>
                <div
                    class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200 flex items-start text-sm text-gray-700 justify-center">
                    <div>
                        <p class="font-medium text-gray-800 mb-3">For concerns please contact:</p>
                        <div class="space-y-2">
                            <!-- Phone Option -->
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-6 h-6 text-gray-800 dark:text-blue-600" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m17.0896 13.371 1.1431 1.1439c.1745.1461.3148.3287.4111.5349.0962.2063.1461.4312.1461.6588 0 .2276-.0499.4525-.1461.6587-.0963.2063-.4729.6251-.6473.7712-3.1173 3.1211-6.7739 1.706-9.90477-1.4254-3.13087-3.1313-4.54323-6.7896-1.41066-9.90139.62706-.61925 1.71351-1.14182 2.61843-.23626l1.1911 1.19193c1.1911 1.19194.3562 1.93533-.4926 2.80371-.92477.92481-.65643 1.72741 0 2.38391l1.8713 1.8725c.3159.3161.7443.4936 1.191.4936.4468 0 .8752-.1775 1.1911-.4936.8624-.8261 1.6952-1.6004 2.8382-.4565Zm-2.2152-4.39103 2.1348-2.13485m0 0 2.1597-1.90738m-2.1597 1.90738 2.1597 2.15076m-2.1597-2.15076-2.1348-1.90738" />
                                </svg>
                                <span>Extension 1951 or 1953</span>
                            </div>

                            <!-- People Option -->
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-6 h-6 text-gray-800 dark:text-blue-600" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <span>Find Claire or Hazel</span>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</template>
<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    username: '',
    password: '',
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const currentHour = new Date().getHours();
const currentGreetings = computed(() => {
    if (currentHour < 12) return 'Good Morning';
    if (currentHour < 18) return 'Good Afternoon';
    return 'Good Evening';
});
</script>
