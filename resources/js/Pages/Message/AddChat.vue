<template>
    <div class="search-box p-4 flex-none">
        <div class="relative">
            <label>
                <input v-model="searchUsers"
                    class="rounded-full py-2 pr-6 pl-10 w-full border border-gray-800 focus:border-gray-700 bg-gray-800 focus:bg-gray-900 focus:outline-none text-gray-200 focus:shadow-md transition duration-300 ease-in"
                    type="text" placeholder="Search User" />
                <span class="absolute top-0 left-0 mt-2 ml-3 inline-block">
                    <svg viewBox="0 0 24 24" class="w-6 h-6">
                        <path fill="#bbb"
                            d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z" />
                    </svg>
                </span>
            </label>
        </div>
    </div>
    <!-- {{ users }} -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-8 rounded-2xl text-white">
        <div class="flex flex-col border-2 border-gray-500 p-8 rounded-2xl cursor-pointer text-white hover:bg-gray-800"
            v-for="item in users?.data" :key="item?.id" @click="message(item.user_id)">
            <div class="flex">
                <!-- {{ item }} -->
                <div class="flex gap-4">
                    <img class="w-16 h-16 rounded-full" :src="'http://172.16.161.34:8080/hrms' + item?.photo" alt="">
                    <div class="flex flex-col gap-1">
                        <div class="flex gap-3 items-center -mt-1">
                            <p class="font-semibold cursor-pointer">{{ item?.firstname }}</p>

                        </div>
                        <div class="font-light text-md text-white">{{ item?.username }}</div>
                    </div>
                </div>
            </div>
            <div class="italic mt-2 text-[18px] text-white font-normal">
                {{ item.name }}
            </div>
            <div class="flex gap-6 text-white text-[12px] mt-4">
                <span class="flex items-center gap-1 cursor-pointer"><span class="text-[8px]">▲</span>Message</span>
                <span class="cursor-pointer hover:text-[#ff6154]"></span>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';

const emit = defineEmits(['messageEmit']);


const searchUsers = ref();
const users = ref<any>();

const getUsers = async () => {
    const { data } = await axios.get(route('message.addChat'));
    users.value = data.users;
}

const message = (id: number) => {
    // alert();
    emit('messageEmit', id)
}

onMounted(() => {
    getUsers()
})

</script>
