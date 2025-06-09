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
            <Card>
                <Table bordered :pagination="false" size="small" :data-source="records" :columns="columns">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key == 'action'">
                            <div class="flex gap-4">
                                <button size="small"class="px-6 py-2 text-medium font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                                @click="() => router.get(route('nesa.view.list', record.itemcode))">
                                View
                            </button>
                            <button size="small"class="px-6 py-2 text-medium font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                Tag
                            </button>
                            </div>
                        </template>
                    </template>
                </Table>

                <button class="mr-1 mt-1" @click="() => router.get(route('nesa.send.email'))">
                    Send Email
                </button>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import { Card, Table } from 'ant-design-vue';
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
