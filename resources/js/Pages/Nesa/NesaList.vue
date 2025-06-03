<template>
    <AuthenticatedLayout>
        <Card>
            <Table bordered :pagination="false" size="small" :columns="columns">

            </Table>
        </Card>
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
