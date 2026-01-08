<template>
    <div class="min-h-screen transition-colors duration-300" :class="isDark ? 'bg-gray-900' : 'bg-gradient-to-br from-indigo-50 via-white to-purple-50'">
        <!-- Theme Toggle Button -->
        <button
            @click="toggleTheme"
            class="fixed top-6 right-6 z-50 p-3 rounded-full shadow-lg transition-all duration-300 hover:scale-110"
            :class="isDark ? 'bg-gray-800 text-yellow-400 hover:bg-gray-700' : 'bg-white text-indigo-600 hover:bg-gray-50'"
            aria-label="Toggle theme"
        >
            <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <div class="flex justify-center items-center min-h-screen p-4">
            <div class="max-w-6xl w-full m-0 sm:m-10 flex justify-center">
                <div class="w-full flex flex-col lg:flex-row rounded-2xl shadow-2xl overflow-hidden transition-colors duration-300"
                    :class="isDark ? 'bg-gray-800' : 'bg-white'">

                    <!-- Left Side - Form -->
                    <div class="lg:w-1/2 p-8 sm:p-12">
                        <div class="flex justify-center mb-8">
                            <div class="relative">
                                <img src="/images/nesaLogo.png" class="w-40 transition-all duration-300"
                                    :class="isDark ? 'brightness-110' : ''" />
                            </div>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="text-center mb-8">
                                <h1 class="text-3xl xl:text-4xl font-bold mb-2 transition-colors duration-300"
                                    :class="isDark ? 'text-white' : 'text-gray-900'">
                                    Welcome Back
                                </h1>
                                <p class="transition-colors duration-300"
                                    :class="isDark ? 'text-gray-400' : 'text-gray-600'">
                                    Sign in to continue to your account
                                </p>
                            </div>

                            <div class="w-full max-w-sm">
                                <!-- Username Input -->
                                <div class="mb-5">
                                    <label class="block text-sm font-medium mb-2 transition-colors duration-300"
                                        :class="isDark ? 'text-gray-300' : 'text-gray-700'">
                                        Username
                                    </label>
                                    <input
                                        @keyup.enter="submit"
                                        class="w-full px-4 py-3.5 rounded-xl font-medium transition-all duration-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                        :class="isDark
                                            ? 'bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:bg-gray-600'
                                            : 'bg-gray-50 border border-gray-200 text-gray-900 placeholder-gray-500 focus:bg-white'"
                                        v-model="form.username"
                                        @change="form.errors.username = null"
                                        type="text"
                                        placeholder="Enter your username"
                                    />
                                    <InputError class="mt-2 ml-1 text-sm text-red-500" :message="form.errors.username" />
                                </div>

                                <!-- Password Input -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium mb-2 transition-colors duration-300"
                                        :class="isDark ? 'text-gray-300' : 'text-gray-700'">
                                        Password
                                    </label>
                                    <div class="relative">
                                        <input
                                            @keyup.enter="submit"
                                            @change="form.errors.password = null"
                                            class="w-full px-4 py-3.5 rounded-xl font-medium transition-all duration-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none pr-12"
                                            :class="isDark
                                                ? 'bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:bg-gray-600'
                                                : 'bg-gray-50 border border-gray-200 text-gray-900 placeholder-gray-500 focus:bg-white'"
                                            v-model="form.password"
                                            :type="showPassword ? 'text' : 'password'"
                                            placeholder="Enter your password"
                                        />
                                        <button
                                            type="button"
                                            @click="togglePassword"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 p-1 transition-colors duration-300 hover:scale-110"
                                            :class="isDark ? 'text-gray-400 hover:text-gray-300' : 'text-gray-500 hover:text-gray-700'"
                                        >
                                            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.94 10.94a3 3 0 104.12 4.12" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.43C11.12 5.15 12.55 5 14 5c4.477 0 8.268 2.943 9.542 7-.462 1.47-1.257 2.78-2.28 3.9M6.6 6.6C5.4 7.9 4.6 9.6 4.16 11.5" />
                                            </svg>
                                        </button>
                                    </div>
                                    <InputError class="mt-2 ml-1 text-sm text-red-500" :message="form.errors.password" />
                                </div>

                                <!-- Login Button -->
                                <button
                                    @click="submit"
                                    class="w-full py-3.5 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                    :class="form.processing
                                        ? 'bg-indigo-400 cursor-not-allowed'
                                        : 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700'"
                                    :disabled="form.processing"
                                >
                                    <LoadingOutlined v-if="form.processing" class="text-2xl text-white" />
                                    <svg v-else class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                        <circle cx="8.5" cy="7" r="4" />
                                        <path d="M20 8v6M23 11h-6" />
                                    </svg>
                                    <span class="ml-3 text-white text-base">
                                        {{ form.processing ? "Signing in..." : "Sign In" }}
                                    </span>
                                </button>

                                <!-- Terms -->
                                <p class="mt-8 text-xs text-center transition-colors duration-300"
                                    :class="isDark ? 'text-gray-400' : 'text-gray-600'">
                                    By signing in, you agree to our
                                    <a href="#" class="font-medium underline hover:no-underline transition-colors duration-300"
                                        :class="isDark ? 'text-indigo-400 hover:text-indigo-300' : 'text-indigo-600 hover:text-indigo-700'">
                                        Terms of Service
                                    </a>
                                    and
                                    <a href="#" class="font-medium underline hover:no-underline transition-colors duration-300"
                                        :class="isDark ? 'text-indigo-400 hover:text-indigo-300' : 'text-indigo-600 hover:text-indigo-700'">
                                        Privacy Policy
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Image -->
                    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden transition-colors duration-300"
                        :class="isDark ? 'bg-gradient-to-br from-indigo-900 to-purple-900' : 'bg-gradient-to-br from-indigo-500 to-purple-600'">
                        <div class="absolute inset-0 flex items-center justify-center p-12">
                            <img src="/images/loginside.svg"
                                class="w-full h-full object-contain transition-all duration-300 drop-shadow-2xl"
                                :class="isDark ? 'opacity-90' : 'opacity-100'"
                            />
                        </div>
                        <!-- Decorative Elements -->
                        <div class="absolute top-10 right-10 w-32 h-32 bg-white opacity-10 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-10 left-10 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import InputError from "@/Components/InputError.vue";
import { LoadingOutlined } from "@ant-design/icons-vue";
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    username: "" as string,
    password: "" as string,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};

const showPassword = ref(false);
const isDark = ref(false);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    isDark.value = savedTheme === 'dark';
});
</script>
