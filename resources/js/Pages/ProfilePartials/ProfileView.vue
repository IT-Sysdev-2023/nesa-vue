<template>
    <div
        class="relative rounded-xl overflow-hidden flex flex-col items-center shadow-lg bg-white font-sans w-full max-w-xs">
        <!-- Background header with blur effect -->
        <div class="h-20 w-full bg-gray-100 relative">
            <div v-if="userImageData" class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
        </div>

        <!-- Profile content -->
        <div class="w-full flex flex-col items-center px-6 pb-6 -mt-10 z-10">
            <!-- Avatar -->
            <div class="relative mb-4">
                <div class="h-[130px] w-[130px] rounded-full border-4 border-white bg-white shadow-md overflow-hidden">
                    <img v-if="userImageData" class="h-full w-full object-cover"
                        :src="'http://172.16.161.34:8080/hrms' + userImageData" alt="Profile">
                    <img v-else class="h-full w-full object-cover" src="/storage/images/noUser.jpg" alt="Profile">
                </div>
                <!-- <div class="absolute -bottom-1 -right-1 bg-green-400 h-5 w-5 rounded-full border-2 border-white"></div> -->
            </div>

            <!-- User info -->
            <div class="text-center mb-6">
                <div v-if="page.auth.user.employee_id === '1000048642'">
                    <p class="text-medium italic font-bold text-gray-600 mb-1 tracking-wide">Oy Bossing ,</p>
                </div>
                <div v-else-if="page.auth.user.employee_id === '02484-2023'">
                    <p class="text-medium italic font-bold text-gray-600 mb-1 tracking-wide">Ohayou gozaimasu Bossing ,
                    </p>
                </div>
                <div v-else-if="page.auth.user.employee_id === '1000049981'">
                    <p class="text-medium italic font-bold text-gray-600 mb-1 tracking-wide">O-genki desu ka Bossing ,
                    </p>
                </div>
                <div v-else-if="page.auth.user.employee_id === '1000052515'">
                    <p class="text-medium italic font-bold text-gray-600 mb-1 tracking-wide">sa-wat-dee kâ ,
                    </p>
                </div>
                <div v-else-if="page.auth.user.employee_id === '1000033976'">
                    <p class="text-medium italic font-bold text-gray-600 mb-1 tracking-wide"> Anyeong beshie ,</p>
                </div>
                <div v-else>
                    <p class="text-medium italic font-bold text-gray-600 mb-1 tracking-wide">Hello ,</p>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">{{ fullName }}</h3>
                <div v-if="userTypes" class="mt-2">
                    <a-tag color="green">
                        {{ userTypes.name }}
                    </a-tag>
                </div>
            </div>

            <!-- Actions -->
            <div class="w-full space-y-3">
                <button @click="viewProfileButton"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>View Profile</span>
                </button>
                <button @click="logoutModal = true"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Logout</span>
                </button>
            </div>
        </div>
    </div>
    <a-modal v-model:open="logoutModal" centered width="500px" :closable="false" :maskClosable="false">
        <template #title>
            <div class="flex items-center gap-3 border-b pb-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Confirm Logout</h3>
            </div>
        </template>
        <p class="text-gray-700 text-lg text-center mb-10">Are you sure you want to logout?</p>
        <template #footer>
            <button @click="logoutModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-md">
                Cancel
            </button>
            <button @click="logoutModal = false; router.post(route('logout'))"
                class="ml-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-md">
                Logout
            </button>
        </template>
    </a-modal>
</template>

<script setup lang="ts">
import { usePage, router } from "@inertiajs/vue3";
import { onMounted } from 'vue';
import { ref, computed } from 'vue';
import axios from "axios";


interface User {
    image?: string;
    firstname?: string;
    middlename?: string;
    lastname?: string;
    usertype?: string;
    id?: string;
    employee_id: string
}

interface PageProps {
    auth: {
        user: User;
    };
}

const page = usePage().props as unknown as PageProps;

const logoutModal = ref<boolean>(false);
// const logout = () => {
//     logoutModal.value = true;
//     // router.post(route('logout'));
// };
const userTypes = ref<Types | null>(null);

interface Types {
    name: string;
}

onMounted(() => {
    viewProfile();
});

const viewProfile = async () => {
    const response = await axios.get(route('admin.userType'));
    userTypes.value = response.data.usertypes
}

const userNameFormat = computed(() => {
    return `${page.auth.user.lastname.toLowerCase()}, ${page.auth.user.firstname.toLowerCase()}`;
});
const value = ref<string>(userNameFormat.value);
const fullName = ref<string>('');
const userImageData = ref<string>('');
const userImage = async () => {
    const response = await axios.get('http://172.16.161.34/api/gc/filter/employee/name', {
        params: {
            q: value.value
        }
    });
    userImageData.value = response.data.data.employee[0].employee_photo;
    fullName.value = response.data.data.employee[0].employee_name
};
onMounted(() => {
    userImage();
});

const viewProfileButton = () => {
    router.get(route('admin.viewProfile'));
}
</script>
