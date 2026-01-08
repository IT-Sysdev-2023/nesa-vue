<template>
    <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl border border-gray-100 p-8 max-w-md w-full">
        <div class="space-y-4">
            <h3 class="text-xl font-bold mb-6 text-center">{{ title }}</h3>

            <a-button  @click="() => router.get(route('nesa.tag.approved'))"
                class="group w-full py-4 px-6 rounded-xl font-semibold transition-all duration-300 flex items-center justify-between hover:shadow-lg hover:scale-105 border-2 border-blue-200 hover:border-blue-400 hover:bg-blue-50">
                <div class="flex items-center">
                    <div class=" p-2 mr-3 group-hover:bg-blue-200 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-blue-700">Approved List</span>
                </div>
                <a-badge :count="count?.approvedCount"></a-badge>

            </a-button>

            <!-- <a-button type="primary" ghost @click="() => router.get(route('nesa.get.approved.nesa'))"
                class="group w-full py-4 px-6 rounded-xl font-semibold transition-all duration-300 flex items-center justify-between hover:shadow-lg hover:scale-105 border-2 border-emerald-200 hover:border-emerald-400 hover:bg-emerald-50">
                <div class="flex items-center">
                    <div class=" p-2 mr-3 group-hover:bg-emerald-200 transition-colors">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-emerald-700">Approved List</span>
                </div>
                <a-badge :count="count.approvedCount"></a-badge>
            </a-button> -->
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

interface CountType {
    // apprvedCou?: number;
    approvedCount?: number;
}

const count = ref<CountType>({});

defineProps<{
    title: string;
}>();

const getCountNesaApproved = async () => {
    try {
        const response = await axios.get(route('nesa.count.data.approved'));
        const data = await response.data;

        count.value = data;

    } catch (error) {
        console.error('Error fetching Nesa count:', error);
    }
};
onMounted(() => {
    getCountNesaApproved()
});
</script>
