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
                            <a-button type="primary" size="small" class="mr-1"
                                @click="() => router.get(route('nesa.view.list', record.itemcode))">
                                View
                            </a-button>
                            <a-button size="small" class="mr-1">
                                Tag
                            </a-button>
                        </template>
                    </template>
                </Table>

                <a-button class="mr-1 mt-1" @click="() => router.get(route('nesa.send.email'))">
                    Send Email
                </a-button>
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
