<template>
    <AuthenticatedLayout>
        <a-card>
            <template #title>
                <div class="flex justify-between">
                    {{ records[0].supname }}
                    <a-button class="mb-2" @click="() => router.get(route('nesa.get.list'))">
                        Back to list
                    </a-button>
                </div>

            </template>

            <a-table size="small" bordered :data-source="records" :columns="columns">

            </a-table>

        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
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
    records: Nesa[]
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
