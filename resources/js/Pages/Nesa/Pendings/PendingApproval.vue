<template>
    <AuthenticatedLayout>
        <div class=" mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Approval Nesa Pending</h1>
                <p class="mt-2 text-sm text-gray-600">Viewing Pending</p>
            </div>
        </div>
        <a-card>
            <a-table :pagination="false" size="small" bordered :data-source="records.data" :columns="columns">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key == 'action'">
                        <a-button @click="details(record)">
                            <details></details>
                        </a-button>
                    </template>
                </template>
            </a-table>
            <Pagination :datarecords="records" class="mt-4" />
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';

import { ref, watch } from 'vue';
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
        title: 'Approved By',
        dataIndex: 'full_name',
        key: 'updated_at',
    },
    {
        title: 'Action',
        dataIndex: 'action',
        key: 'action',
    },
]);
const details = (record: any) => {
    router.get(route('nesa.get.pending.details'), {
        item_code: record.item_code,
        supplier: record.supplier_code
    });
}
</script>
