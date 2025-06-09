<template>
    <AuthenticatedLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-10">
            <!-- Header -->
            <div class=" mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Near Expiry Stock Advice</h1>
                    <p class="mt-2 text-sm text-gray-600">Viewing the complete NESA list</p>
                </div>
            </div>
            <a-card>
                <a-table bordered :pagination="false" size="small" :data-source="records" :columns="columns">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key == 'action'">
                            <div class="flex gap-2">
                                <button title="View"
                                    class="px-2 py-1 text-medium font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                                    @click="() => router.get(route('nesa.view.list', record.itemcode))">
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button title="Tag"
                                    class="px-2 py-1 text-medium font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M18.045 3.007 12.31 3a1.965 1.965 0 0 0-1.4.585l-7.33 7.394a2 2 0 0 0 0 2.805l6.573 6.631a1.957 1.957 0 0 0 1.4.585 1.965 1.965 0 0 0 1.4-.585l7.409-7.477A2 2 0 0 0 21 11.479v-5.5a2.972 2.972 0 0 0-2.955-2.972Zm-2.452 6.438a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </template>
                </a-table>
                <button @click="() => router.get(route('nesa.send.email'))"
                    class="flex mt-4 items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Send Email
                </button>
            </a-card>
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Nesa {
    name: string,
    description: string,
    expiry: string,
    nesa_no: number,
}
const props = defineProps<{
    records: Nesa[]
}>();

const columns = ref<any>([
    {
        title: 'Nesa No.',
        dataIndex: 'nesa_no',
    },
    {
        title: 'Nesa Date',
        dataIndex: 'expiry',
    },
    {
        title: 'Business Unit',
        dataIndex: 'buname',
    },
    {
        title: 'Supplier',
        dataIndex: 'supname',
    },
    {
        title: 'Item Description',
        dataIndex: 'description',
    },
    {
        title: 'Action',
        align: 'center',
        key: 'action',
    },
])
</script>
