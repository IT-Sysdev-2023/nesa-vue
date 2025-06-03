<template>
    <AuthenticatedLayout>
        <Card>
            <ProgressBar v-if="isSyncing" type="circle" :progress="progressbar" />
            <PrimaryButton @click="syncSupplier" class="mb-3">
                Sync Supplier
            </PrimaryButton>
            <Table :pagination="false" bordered size="small" :data-source="records.data" :columns="[
                {
                    title: 'Supplier Code',
                    dataIndex: 'supplier_code',
                },
                {
                    title: 'Supplier Name',
                    dataIndex: 'name',
                },
            ]">

            </Table>
            <Pagination :datarecords="records" class="mt-5" />
        </Card>
    </AuthenticatedLayout>
</template>

<script setup>
import { Card, Table, Button, Input } from 'ant-design-vue';
import Pagination from '@/Components/Pagination.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';


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
