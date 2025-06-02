<template>
    <div class="relative rounded-xl overflow-hidden flex flex-col items-center shadow-lg bg-white font-Roboto-light">
        <div class="h-24 w-full">
            <img class="h-24 w-full blur-sm"
                :src="page.auth.user.image ? page.auth.user.image : 'https://www.shutterstock.com/image-vector/user-profile-icon-vector-avatar-600nw-2247726673.jpg'"
                alt="">
        </div>
        <div class="top-16 z-10 flex items-center flex-col gap-4 px-5 py-5">
            <div class="-mt-20">
                <img class="h-24 w-full rounded-full"
                    :src="page.auth.user.image ? page.auth.user.image : 'https://www.shutterstock.com/image-vector/user-profile-icon-vector-avatar-600nw-2247726673.jpg'"
                    alt="">
            </div>

            <div class="flex items-center flex-col">
                <p title="name/نام" class="text-black font-Roboto-md">{{ page.auth.user.firstname }},{{ page.auth.user.lastname}}</p>
                <p title="bio/بیوگرافی" class="text-xs text-gray-500 font-medium">
                    {{ page.auth.user.usertype ? page.auth.user.usertype : 'Admin' }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button @click="logout"
                    class="bg-blue-900 transition-all gradient text-[15px] text-white px-3 py-[6px] rounded-full flex items-center gap-1">
                    Logout
                   <LogoutOutlined />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage, router } from "@inertiajs/vue3";
import { LogoutOutlined } from '@ant-design/icons-vue';

const page = usePage().props;

const logout = () => {
    router.post(route('logout'));
};
</script>
