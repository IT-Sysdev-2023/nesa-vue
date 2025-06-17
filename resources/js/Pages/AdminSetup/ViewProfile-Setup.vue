<template>
    <AuthenticatedLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-10">
            <div class="mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">User Profile</h1>
                    <p class="mt-2 text-sm text-gray-600">Viewing {{ userDetails.employee_name }} profile details</p>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'profile'" :class="{
                        'border-blue-500 text-blue-600': activeTab === 'profile',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'profile'
                    }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Personal Details
                    </button>
                    <button @click="activeTab = 'credentials'" :class="{
                        'border-blue-500 text-blue-600': activeTab === 'credentials',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'credentials'
                    }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Login Credentials
                    </button>
                    <button @click="activeTab = 'activity'" :class="{
                        'border-blue-500 text-blue-600': activeTab === 'activity',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'activity'
                    }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Activity
                    </button>
                </nav>
            </div>

            <div>
                <div v-if="activeTab === 'profile'" class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex flex-col sm:flex-row gap-8 items-start">
                        <div v-if="userDetails" class="flex flex-col items-center text-center">
                            <img :src="userDetails && userDetails.employee_photo ? 'http://172.16.161.34:8080/hrms' + userDetails.employee_photo : '/storage/images/noUser.jpg'"
                                alt=""
                                class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm mb-4" />
                            <h2 class="text-xl font-semibold text-gray-800">{{ userDetails.employee_name }}</h2>
                            <p class="text-gray-600">{{ userDetails.employee_position }}</p>
                        </div>
                        <div v-else class="flex flex-col items-center text-center">
                            <img src="/storage/images/noUser.jpg" alt="User Image"
                                class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm mb-4" />
                            <h2 class="text-xl font-semibold text-gray-800">Fetching User image</h2>
                            <p class="text-gray-600">Please wait</p>
                        </div>

                        <!-- Details Section -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 flex-1">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-3 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm font-medium text-gray-500">Employee ID</p>
                                </div>
                                <p class="text-lg font-semibold text-gray-800 pl-8">{{ userDetails.employee_id }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-3 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z" />
                                    </svg>
                                    <p class="text-sm font-medium text-gray-500">Department</p>
                                </div>
                                <p class="text-lg font-semibold text-gray-800 pl-8">{{ userDetails.employee_dept }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-3 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm font-medium text-gray-500">Business Unit</p>
                                </div>
                                <p class="text-lg font-semibold text-gray-800 pl-8">{{ userDetails.employee_bunit }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-3 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                                            clip-rule="evenodd" />
                                        <path
                                            d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                                    </svg>
                                    <p class="text-sm font-medium text-gray-500">Employee Type</p>
                                </div>
                                <p class="text-lg font-semibold text-gray-800 pl-8">{{ userDetails.employee_type }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Credentials form  -->
                <div v-if="activeTab === 'credentials'" class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex flex-col sm:flex-row gap-12 items-start">
                        <!-- Profile Image Section -->
                        <div class="flex flex-col items-center md:items-start text-center md:text-left">
                            <div v-if="userDetails" class="flex flex-col items-center">
                                <img :src="userDetails && userDetails.employee_photo ? 'http://172.16.161.34:8080/hrms' + userDetails.employee_photo : '/storage/images/noUser.jpg'"
                                    alt=""
                                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm mb-4" />
                                <h2 class="text-xl font-semibold text-gray-800">{{ userDetails.employee_name }}</h2>
                                <p class="text-gray-600">{{ userDetails.employee_position }}</p>
                            </div>
                            <div v-else class="flex flex-col items-center">
                                <img src="/storage/images/noUser.jpg" alt="User Image"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm mb-4" />
                                <h2 class="text-xl font-semibold text-gray-800">Fetching User image</h2>
                                <p class="text-gray-600">Please wait</p>
                            </div>
                        </div>

                        <!-- Credentials Content -->
                        <div class="flex-1 w-full">
                            <h3 class="text-lg font-medium text-gray-900 mb-5">Update Credentials</h3>
                            <nav class="-mb-px flex space-x-8">
                                <button @click="credentialsTab = 'username'" :class="{
                                    'border-blue-500 text-blue-600': credentialsTab === 'username',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': credentialsTab !== 'username'
                                }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Update Username
                                </button>
                                <button @click="credentialsTab = 'password'" :class="{
                                    'border-blue-500 text-blue-600': credentialsTab === 'password',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': credentialsTab !== 'password'
                                }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Update Password
                                </button>
                            </nav>

                            <!-- Username Form -->
                            <div v-if="credentialsTab === 'username'" class="mt-6">
                                <div class="flex flex-col sm:flex-row gap-4 items-end">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                        <input @keyup.enter="updateModal = true;" type="text"
                                            v-model="page.auth.user.username" :class="[
                                                'w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-1',
                                                errors.username
                                                    ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                                                    : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
                                            ]" />
                                        <p v-if="errors.username" class="text-red-600 text-sm mt-1">{{ errors.username
                                            }}</p>
                                    </div>
                                </div>
                                <div class="mt-10">
                                    <button @click="updateModal = true;"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 whitespace-nowrap">
                                        Update Username
                                    </button>
                                </div>
                            </div>

                            <!-- Password Form -->
                            <div v-if="credentialsTab === 'password'" class="mt-6">
                                <div class="flex flex-col sm:flex-row gap-4 items-end">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Old Password</label>
                                        <input @keyup.enter="updatePassword" type="password" v-model="form.oldPassword"
                                            :class="[
                                                'w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-1',
                                                errors.oldPassword
                                                    ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                                                    : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
                                            ]" />
                                        <p v-if="errors.oldPassword" class="text-red-600 text-sm mt-1">{{
                                            errors.oldPassword }}</p>
                                    </div>

                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                        <input @keyup.enter="updatePassword" type="password" v-model="form.password"
                                            :class="[
                                                'w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-1',
                                                errors.password
                                                    ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                                                    : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
                                            ]" />
                                        <p v-if="errors.password" class="text-red-600 text-sm mt-1">{{ errors.password
                                            }}</p>
                                    </div>

                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm
                                            Password</label>
                                        <input @keyup.enter="updatePassword" type="password"
                                            v-model="form.confirmPassword" :class="[
                                                'w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-1',
                                                errors.confirmPassword
                                                    ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                                                    : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
                                            ]" />
                                        <p v-if="errors.confirmPassword" class="text-red-600 text-sm mt-1">{{
                                            errors.confirmPassword }}</p>
                                    </div>
                                </div>
                                <div class="mt-10">
                                    <button @click="updatePasswordModal = true"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Update Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Activity Tab -->
                <div v-if="activeTab === 'activity'" class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex flex-col sm:flex-row gap-8 items-start">
                        <!-- Profile Image Section -->
                        <div class="flex flex-col items-center md:items-start text-center md:text-left">
                            <div v-if="userDetails" class="flex flex-col items-center">
                                <img :src="userDetails && userDetails.employee_photo ? 'http://172.16.161.34:8080/hrms' + userDetails.employee_photo : '/storage/images/noUser.jpg'"
                                    alt=""
                                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm mb-4" />
                                <h2 class="text-xl font-semibold text-gray-800">{{ userDetails.employee_name }}</h2>
                                <p class="text-gray-600">{{ userDetails.employee_position }}</p>
                            </div>
                            <div v-else class="flex flex-col items-center">
                                <img src="/storage/images/noUser.jpg" alt="User Image"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm mb-4" />
                                <h2 class="text-xl font-semibold text-gray-800">Fetching User image</h2>
                                <p class="text-gray-600">Please wait</p>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col items-center justify-center py-12">
                            <div class="text-center max-w-md">
                                <div
                                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Under Construction</h3>
                                <p class="text-gray-500">
                                    This section is currently being worked on. We'll have it ready for you soon!
                                </p>
                                <div class="mt-6">
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-500 rounded-full animate-pulse" style="width: 10%">
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Development in progress</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a-modal v-model:open="updateModal" @ok="updateUsername" title="Security Verification" width="300" centered
            class="rounded-xl overflow-hidden">
            <div class="p-5">
                <label class="block mb-2 text-sm font-medium text-gray-900">
                    Confirm your password
                </label>
                <div class="relative mb-4">
                    <input @keyup.enter="updateUsername" v-model="ConfirmPassword.password" type="password"
                        placeholder="Enter your password" :class="['w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-1',
                            errors.confirmPass ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                                : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
                        ]" />
                </div>
                <p v-if="errors.confirmPass" class="text-red-600 text-sm mt-1">{{ errors.confirmPass }}</p>
                <p class="text-xs text-gray-500 mt-5 leading-relaxed">
                    For security reasons, please confirm your password to update your username.
                </p>
                <p
                    class="flex items-center justify-center text-yellow-800 bg-yellow-100 border border-yellow-400 text-sm mt-5 p-3 rounded-md max-w-sm mx-auto">
                    <svg class="w-5 h-5 mr-2 text-yellow-800" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M5.93 19h12.14a2 2 0 001.74-2.99l-6.07-10.5a2 2 0 00-3.48 0L4.19 16.01A2 2 0 005.93 19z" />
                    </svg>
                    Please remember your username for future logins.
                </p>
            </div>
        </a-modal>
        <a-modal v-model:open="updatePasswordModal" centered :footer="null" width="400px">
            <div class="p-6 text-center">
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Confirmation</h3>
                    <p class="text-gray-600">Are you sure, {{ page.auth.user.firstname }}?</p>
                </div>

                <div class="flex items-start bg-yellow-50 rounded-lg p-4 mb-6 text-left">
                    <svg class="flex-shrink-0 w-5 h-5 mt-0.5 text-yellow-500" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.74 4h12.52a2 2 0 001.74-2.99l-6.07-10.5a2 2 0 00-3.48 0L4.19 16.01A2 2 0 005.93 19z" />
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">Please make sure to remember your password, as it will be
                            required the next time
                            you log in</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button @click="updatePasswordModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Cancel
                    </button>
                    <button @click="updatePassword"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Confirm
                    </button>
                </div>
            </div>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { onMounted, createVNode } from 'vue';
import axios from "axios";
import { router } from '@inertiajs/vue3';
import { notification } from 'ant-design-vue';

const page = usePage().props as unknown as PageProps;
const pages = usePage();
const errors = computed(() => pages.props.errors);

const activeTab = ref('profile');
const credentialsTab = ref('username');

const form = useForm({
    oldPassword: '',
    password: '',
    confirmPassword: ''
});

interface User {
    image?: string;
    firstname?: string;
    middlename?: string;
    lastname?: string;
    usertype?: string;
    username?: string;
    id?: string;
};

interface PageProps {
    auth: {
        user: User;
    };
};

const formattedName = computed(() => {
    return `${page.auth.user.lastname}, ${page.auth.user.firstname}`;
});
const value = ref<string>(formattedName.value);

const userDetails = ref<any>({})
const getUserDetails = async () => {
    const response = await axios.get('http://172.16.161.34/api/gc/filter/employee/name', {
        params: {
            q: value.value
        }
    });
    userDetails.value = response.data.data.employee[0];
}
onMounted(() => {
    getUserDetails();
});
const updatePasswordModal = ref<boolean>(false);
const updatePassword = () => {
    router.post(route('admin.updatePassword'), {
        oldPassword: form.oldPassword,
        password: form.password,
        confirmPassword: form.confirmPassword,
        id: page.auth.user.id
    }, {
        onSuccess: (page: any) => {
            if (page.props.flash.success) {
                notification.success({
                    message: 'Success',
                    description: page.props.flash.success
                });
                form.reset();
                updatePasswordModal.value = false;
            } else if (page.props.flash.error) {
                notification.error({
                    message: 'Failed',
                    description: page.props.flash.error
                });
                updatePasswordModal.value = false;
            }
        }
    });
};
const ConfirmPassword = useForm({
    password: ''
});
const updateModal = ref<boolean>(false);
const updateUsername = () => {
    router.post(route('admin.updateUsername'), {
        username: page.auth.user.username,
        id: page.auth.user.id,
        confirmPass: ConfirmPassword.password
    }, {
        onSuccess: (page: any) => {
            if (page.props.flash.success) {
                notification.success({
                    message: 'Success',
                    description: page.props.flash.success
                });
                updateModal.value = false;
            } else if (page.props.flash.error) {
                notification.error({
                    message: 'Failed',
                    description: page.props.flash.error
                });
            }
        }
    });
};
</script>