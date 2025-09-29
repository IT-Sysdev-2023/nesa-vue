<template>
    <div class="min-h-screen bg-gray-100 text-gray-900 flex justify-center">
        <div class="max-w-screen-lg  m-0 sm:m-10 bg-white shadow sm:rounded-[2rem] flex justify-center flex-1">
            <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12 ">
                <div>
                    <img src="/images/nesaLogo.png" class="w-[150px] mx-auto" />
                </div>
                <div class="mt-12 flex flex-col items-center">
                    <h1 class="text-2xl xl:text-3xl font-extrabold">Sign In</h1>
                    <div class="w-full flex-1 mt-8">
                        <div class="mx-auto max-w-xs">
                            <input @keyup.enter="submit"
                                class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                                v-model="form.username" @change="form.errors.username = null" type="text"
                                placeholder="Username" />
                            <InputError class="mt-1 ml-3 text-sm text-red-600" :message="form.errors.username" />

                            <div class="relative mt-5">
                                <input @keyup.enter="submit" @change="form.errors.password = null"
                                    class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                                    v-model="form.password" :type="showPassword ? 'text' : 'password'"
                                    placeholder="Password" aria-label="Password" />

                                <!-- Toggle button -->
                                <button type="button" @click="togglePassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-1 flex items-center justify-center"
                                    :aria-pressed="showPassword"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                    <!-- Eye (visible) -->
                                    <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>

                                    <!-- Eye-off (hidden) -->
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.94 10.94a3 3 0 104.12 4.12" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.88 5.43C11.12 5.15 12.55 5 14 5c4.477 0 8.268 2.943 9.542 7-.462 1.47-1.257 2.78-2.28 3.9M6.6 6.6C5.4 7.9 4.6 9.6 4.16 11.5" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-1 ml-3 text-sm text-red-600" :message="form.errors.password" />

                            <button @click="submit"
                                class="mt-5 tracking-wide font-semibold bg-indigo-500 text-gray-100 w-full py-4 rounded-lg hover:bg-indigo-700 transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none">
                                <LoadingOutlined v-if="form.processing" style="font-size: 22px" />
                                <svg v-else class="w-6 h-6 -ml-2" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                    <circle cx="8.5" cy="7" r="4" />
                                    <path d="M20 8v6M23 11h-6" />
                                </svg>
                                <span class="ml-3">
                                    {{
                                        form.processing
                                            ? "Loggin in..."
                                            : "Login"
                                    }}
                                </span>
                            </button>
                            <p class="mt-6 text-xs text-gray-600 text-center">
                                I agree to abide by templatana's
                                <a href="#" class="border-b border-gray-500 border-dotted">
                                    Terms of Service
                                </a>
                                and its
                                <a href="#" class="border-b border-gray-500 border-dotted">
                                    Privacy Policy
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-1 bg-indigo-100 text-center  hidden lg:flex  rounded-r-[2rem]">
                <img src="/images/loginside.svg" class="m-12 xl:m-16 w-full bg-contain bg-center bg-no-repeat" />
                <!-- </img> -->
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import InputError from "@/Components/InputError.vue";
import { LoadingOutlined } from "@ant-design/icons-vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

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

const togglePassword = () => {
    showPassword.value = !showPassword.value
}

</script>
