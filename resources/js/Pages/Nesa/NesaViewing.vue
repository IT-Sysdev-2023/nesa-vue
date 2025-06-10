<template>
    <AuthenticatedLayout>
        <a-card>
<<<<<<< HEAD
            <div class="flex justify-end">
                <button class="mb-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                    @click="() => router.get(route('nesa.get.list'))">
                    Back to list
                </button>
            </div>
            <a-table size="small" bordered :data-source="records.data" :columns="columns" :pagination="false">
=======
            <template #title>
                <div class="flex justify-between">
                    {{ records[0].supname }}
                    <a-button class="mb-2" @click="() => router.get(route('nesa.get.list'))">
                        Back to list
                    </a-button>
                </div>

            </template>

            <a-table size="small" bordered :data-source="records" :columns="columns">

>>>>>>> kenji
            </a-table>
            <Pagination :datarecords="props.records" class="mt-5" />
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Nesa {
    supname: string,
    description: string,
    expiry: string,
    nesa_no: number,
}

const props = defineProps<{
    records: {
        data: Nesa[]
    }
}>();

const columns = ref<any>([
    {
        title: 'Nesa No.',
        dataIndex: 'nesa_no',
    },
    {
        title: 'Expiry',
        dataIndex: 'expiry',
    },
    {
        title: 'Business Unit',
        dataIndex: 'buname',
    },
    {
        title: 'Item Description',
        dataIndex: 'description',
    },
    {
        title: 'Unit Of Measure',
        dataIndex: 'uom',
    },
    {
        title: 'Quantity',
        dataIndex: 'quantity',
    },
    // {
    //     title: 'Action',
    //     align: 'center',
    //     key: 'action',
    // },
])
</script>
