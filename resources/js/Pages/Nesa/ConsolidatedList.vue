<template>
    <AuthenticatedLayout>
        <div class=" mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Consolidated Nesa</h1>
                <p class="mt-2 text-sm text-gray-600">All Consolidated Nesa List</p>
            </div>
        </div>
        <a-card>
            <a-table size="small" :pagination="false" bordered :data-source="records.data" :columns="columns">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key == 'itcode'">
                        <a-tag v-for="item in record.item_code" class="mt-1" color="default">{{ item }}</a-tag>
                    </template>
                    <template v-if="column.key == 'desc'">
                        <a-tag v-for="item in record.desc" class="mt-1" color="blue">{{ item }}</a-tag>
                    </template>
                    <template v-if="column.key == 'action'" class="text-center">
                        <a-button>
                            Download
                        </a-button>
                        <a-button type="primary" class="ml-1" success @click="sendEmail(record)">
                           {{ isSending ? 'Sending...' : 'Send Email'}}
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
        <Pagination :datarecords="records" class="mt-3" />
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref, watch } from 'vue';
interface Records {
    data: {
        supplier_code: string,
        item_code: [],
        bunit: number,
        documents: string,
        batch: number,
    }[]
}
const props = defineProps<{
    records: Records
}>();

const columns = ref([
    {
        title: 'Supplier',
        dataIndex: 'name',
        key: 'name',
    },
    {
        title: 'Item Code',
        dataIndex: 'item_code',
        key: 'itcode',
    },
    {
        title: 'Description',
        dataIndex: 'desc',
        key: 'desc',
    },
    {
        title: 'Action',
        dataIndex: 'action',
        key: 'action',
    },
]);
const isSending = ref<boolean>(false);
const sendEmail = (data: any) => {
    router.get(route('nesa.send.email'), {
        sup: data.supplier_code,
        docs: data.documents,
    }, {
        onStart: () => {
            isSending.value = true;
        },
        onSuccess: () => {
            Swal.fire({
                title: "Success",
                text: "Email Successfuly sent!",
                icon: "success"
            });
        }
    });
}
</script>
