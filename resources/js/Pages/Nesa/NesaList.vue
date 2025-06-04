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
                <Table bordered :pagination="false" size="small" :columns="columns">

                </Table>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, Table } from 'ant-design-vue';
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
