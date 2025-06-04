<template>
    <AuthenticatedLayout>
        <a-card>
            <div class="flex justify-end">
                <a-button class="mb-2" @click="() => router.get(route('nesa.get.list'))">
                    Back to list
                </a-button>
            </div>
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
        title: 'Expiry',
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
