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
                        <button @click="downloadPdf(record.documents)"
                            class="p-2 rounded-full bg-green-500 text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button @click="sendEmail(record)"
                            class="p-2 rounded-full bg-blue-500 ml-1 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </button>
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
        id: number,
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
    console.log(data);
    router.get(route('nesa.send.email'), {
        sup: data.supplier_code,
        docs: data.documents,
        id: data.id
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
const downloadPdf = (filename: string) => {
    window.location.href = `/nesa/download/${filename}`;
};
</script>
