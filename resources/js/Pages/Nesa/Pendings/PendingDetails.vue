<template>
    <AuthenticatedLayout>
        <div class=" mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Approval Expiry History</h1>
                <p class="mt-2 text-sm text-gray-600">Viewing History</p>
            </div>
        </div>
        <a-card>
            <div class="flex justify-end mb-3">
                <a-button @click="router.get(route('nesa.get.pending.for.approval'))">
                    Back to Summary
                </a-button>
            </div>
            <a-table size="small" bordered :pagination="false" :data-source="records[0].data" :columns="columns">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key == 'signature'">
                        <a-image :src="'/storage/singatures/' + record.signature" />
                    </template>
                    <template v-if="column.key == 'quantity'">
                        {{ record.quantity }} pcs
                    </template>
                    <template v-if="column.key == 'action'">
                        {{coa.find(item => item.id === record.coa)?.name || 'N/A'}}
                    </template>
                </template>
            </a-table>
            <Pagination :datarecords="records[0]" class="mt-3" />
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';
import type { TableColumnType } from 'ant-design-vue';
import Pagination from '@/Components/Pagination.vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps<{
    records: DataType,
    supplier: string,
    coa: any[]
}>();

interface DataType {
    id: string;
    itemcode: number;
    quantity: number;
    description: string;
    signature: string;
}[]


const columns = ref<TableColumnType<DataType>[]>([
    {
        title: 'Item Code',
        dataIndex: 'itemcode',
        key: 'name',
    },
    {
        title: 'Description',
        dataIndex: 'description',
        key: 'address',
    },
    {
        title: 'Business Units',
        dataIndex: 'name',
        key: 'age',
    },
    {
        title: 'Quantity',
        dataIndex: 'quantity',
        key: 'quantity',
        align: 'center',
    },
    {
        title: 'Signature',
        key: 'signature',
        align: 'center',
    },
    {
        title: 'Course of Action',
        key: 'action',
        align: 'center',
    },
]);

</script>

