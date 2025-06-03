<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table bordered :pagination="false" size="small" :columns="columns">

            </a-table>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
const columns = ref([
    {
        title: 'Nesa No.',
        dataIndex: '',
    },
    {
        title: 'Nesa Date',
        dataIndex: '',
    },
    {
        title: 'Supplier',
        dataIndex: '',
    },
    {
        title: 'Item Description',
        dataIndex: '',
    },
    {
        title: 'action',
        key: 'action',
    },
])
</script>
