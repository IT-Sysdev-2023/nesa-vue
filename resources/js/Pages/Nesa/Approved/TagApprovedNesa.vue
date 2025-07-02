<template>
    <AuthenticatedLayout>
        <div class=" mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Approved Nesa Request</h1>
                <p class="mt-2 text-sm text-gray-600">Viewing Approved Requests</p>
            </div>
        </div>
        <a-table :pagination="false" size="small" bordered :data-source="records.data" :columns="columns">
            <template #bodyCell="{ column, record }">
                <template v-if="column.key == 'action'">
                    <div class="flex">
                        <svg @click="details(record)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6 cursor-pointer">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                </template>
            </template>
        </a-table>
        <Pagination :datarecords="records" class="mt-4" />
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
interface Record {
    tagby: string,
    appby: string,
    id: number,
    name: string,
}
const props = defineProps<{
    records: any
}>();


const columns = ref([
    {
        title: 'Supplier',
        dataIndex: 'name',
        key: 'name',

    },
    {
        title: 'Nesa Date',
        dataIndex: 'updated_at',
        key: 'updated_at',
    },
    {
        title: 'Tagged By',
        dataIndex: 'tagby',
        key: 'updated_at',
    },
    {
        title: 'Approved By',
        dataIndex: 'appby',
        key: 'updated_at',
    },
    {
        title: 'Action',
        dataIndex: 'action',
        key: 'action',
    },
]);

const details = (record: any) => {
    router.get(route('nesa.get.approved.details'), {
        item_code: record.item_code,
        supplier: record.supplier_code
    });
}

</script>
