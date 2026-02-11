<template>
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
        <div class="space-y-3">
            <h3 class="text-xl font-semibold mb-4 text-center text-gray-800">{{ title }}</h3>

            <button @click="() => router.get(route('nesa.get.pending.for.approval'))"
                class="w-full py-3 px-4 rounded-lg font-medium transition-all duration-200 flex items-center justify-between hover:bg-gray-50 border border-gray-200 hover:border-gray-300">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-700">Pending For Approval</span>
                </div>
                <span v-if="count?.approvalCount" class="text-sm font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded-full min-w-[24px] text-center">
                    {{ count.approvalCount }}
                </span>
            </button>

            <button @click="() => router.get(route('nesa.get.approved.nesa'))"
                class="w-full py-3 px-4 rounded-lg font-medium transition-all duration-200 flex items-center justify-between hover:bg-gray-50 border border-gray-200 hover:border-gray-300">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="text-gray-700">Approved List</span>
                </div>
                <span v-if="count?.approvedCount" class="text-sm font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded-full min-w-[24px] text-center">
                    {{ count.approvedCount }}
                </span>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

interface CountType {
    approvalCount?: number;
    approvedCount?: number;
}

const count = ref<CountType>({});

defineProps<{
    title: string;
}>();

const getCountNesaApproval = async () => {
    try {
        const response = await axios.get(route('nesa.count.data.approval'));
        const data = await response.data;

        count.value = data;

    } catch (error) {
        console.error('Error fetching Nesa count:', error);
    }
};

onMounted(() => {
    getCountNesaApproval()
});
</script>