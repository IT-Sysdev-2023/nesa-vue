<template>
    <div class="relative rounded-xl overflow-hidden flex flex-col items-center shadow-lg bg-white font-Roboto-light">
        <div v-if="userImageData" class="h-24 w-full">
            <img class="h-24 w-full blur-sm"
                :src="page.auth.user.image ? page.auth.user.image : 'https://www.shutterstock.com/image-vector/user-profile-icon-vector-avatar-600nw-2247726673.jpg'"
                alt="">
        </div>
        <div class="top-16 z-10 flex items-center flex-col gap-4 px-5 py-5">
            <div v-if="userImageData" class="-mt-20">
                <img class="h-24 w-full rounded-full" :src="'http://172.16.161.34:8080/hrms' + userImageData" alt="">
            </div>
            <div v-else>
                <img class="h-24 w-full rounded-full" src="/storage/images/noUser.jpg" />
            </div>

            <div class="flex items-center flex-col">
                <p class="text-black font-Roboto-md">{{ page.auth.user.firstname }} {{
                    page.auth.user.middlename }}. {{ page.auth.user.lastname }}</p>
                <p v-if="userTypes" class="text-xs text-gray-500 font-medium">
                    {{ userTypes.name }}
                </p>

            </div>

            <div class="flex flex-col items-center gap-2 w-full px-4">
                <button @click="viewProfileButton"
                    class="w-full bg-blue-900 text-white text-[15px] px-4 py-2 rounded-full flex justify-center items-center gap-1 shadow hover:bg-blue-800 transition">
                    View Profile
                </button>
                <button @click="logout"
                    class="w-full bg-blue-900 text-white text-[15px] px-4 py-2 rounded-full flex justify-center items-center gap-1 shadow hover:bg-blue-800 transition">
                    Logout
                    <LogoutOutlined />
                </button>
            </div>

        </div>
    </div>
</template>

<script setup lang="ts">
import { usePage, router } from "@inertiajs/vue3";
import { LogoutOutlined } from '@ant-design/icons-vue';
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
}

interface PageProps {
    auth: {
        user: User;
    };
}

const page = usePage().props as unknown as PageProps;

const logout = () => {
    router.post(route('logout'));
};
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

const userImageData = ref<string>('');
const userImage = async () => {
    const response = await axios.get('http://172.16.161.34/api/gc/filter/employee/name', {
        params: {
            q: value.value
        }
    });
    userImageData.value = response.data.data.employee[0].employee_photo;
};
onMounted(() => {
    userImage();
});

const viewProfileButton = () => {
    router.get(route('admin.viewProfile'));
}
</script>
