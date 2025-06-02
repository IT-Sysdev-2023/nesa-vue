<template>
    <AuthenticatedLayout>
        <a-card>
            <ProgressBar v-if="isSyncing" type="circle" :progress="progressbar" />
            <a-button @click="syncSupplier" class="mb-3">
                Sync Supplier
            </a-button>
            <a-table :pagination="false" bordered size="small" :data-source="records.data" :columns="[
                {
                    title: 'Supplier Code',
                    dataIndex: 'supplier_code',
                },
                {
                    title: 'Supplier Name',
                    dataIndex: 'name',
                },
            ]">

            </a-table>
            <Pagination :datarecords="records" class="mt-5" />
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>

import Pagination from '@/Components/Pagination.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';


const props = defineProps({
    records: Object
})
const page = usePage().props;

const progressbar = ref([]);
const isSyncing = ref(false);

const syncSupplier = () => {
    router.get(route('admin.sync.supplier'), {}, {
        onStart: () => {
            isSyncing.value = true;
        },
        onSuccess: () => {
            isSyncing.value = false;
        },
        onError: () => {
            isSyncing.value = false;
        },
        onFinish: () => {
            isSyncing.value = false;
        }
    })
}

onMounted(() => {
    console.log(page.auth.user.id);
    window.Echo.private(`syncing-products.${page.auth.user.id}`)
        .listen(".start-syncing-products", (e) => {
            progressbar.value = e;
        });

})




</script>
