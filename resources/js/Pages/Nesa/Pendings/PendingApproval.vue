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
                        <a-button type="primary" class="mx-2" @click="approve(record.id)">
                            Approve
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
import Swal from 'sweetalert2';

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

const approve = (id: number) => {
    router.put(route('nesa.approve.pending.nesa'), {
        id,
    }, {
        onSuccess: (e: any) => {
            if (e.props.flash.status == 'error') {
                Swal.fire({
                    title: e.props.flash.title,
                    text: e.props.flash.msg,
                    icon: "error"
                });
            }
            if (e.props.flash.status == 'success') {
                Swal.fire({
                    title: e.props.flash.title,
                    text: e.props.flash.msg,
                    icon: "success"
                });
            }
        }
    });
}
</script>
