<template>
    <AuthenticatedLayout>
        <ProgressBar type="circle" v-if="isSyncing" :progress="progressBa">
        </ProgressBar>
        <Card>
            <div class="flex justify-between">
                <PrimaryButton @click="syncProducts" class="mb-3">
                    Sync Products In Nav
                </PrimaryButton>

                <input type="text" v-model="form.search"
                    class="w-[300px] border border-gray-300 rounded-md mb-2 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Search Item code" />

            </div>
            <Table class="" :pagination="false" bordered size="small" :data-source="records.data" :columns="columns">
            </Table>
            <Pagination class="mt-3" :datarecords="records" />
        </Card>
    </AuthenticatedLayout>
</template>

<script setup>

import { Card, Table, Button, Input } from 'ant-design-vue';
import { FastForwardOutlined } from '@ant-design/icons-vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import { usePage, router } from "@inertiajs/vue3";
import { onMounted, ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const page = usePage().props;

const props = defineProps({
    records: Object
})

const columns = ref([
    {
        title: 'Item Code',
        dataIndex: 'itemcode',
    },
    {
        title: 'Description',
        dataIndex: 'description',
    },
    {
        title: 'Unit of Measure',
        dataIndex: 'uom',
    },
    {
        title: 'Price',
        dataIndex: 'uom_price',
    },
    {
        title: 'Supplier',
        dataIndex: 'name',
    },
])
const isSyncing = ref(false);

const progressBa = ref([]);

const form = ref({
    search: '',
})

const search = () => {
    router.get(route('admin.search.products'), {
        search: form.value.search
    }, {
        preserveScroll: true,
        preserveState: true,
    })
}

watch(() => form.value.search, () => {
    search();
})

const syncProducts = () => {
    router.get(route('admin.sync.products'), {}, {
        onStart: () => {

            isSyncing.value = true;
        },
        onSuccess: () => {
            isSyncing.value = false;
        },
        onError: () => {
            isSyncing.value = false;
        }
    });
}

onMounted(() => {
    console.log(page.auth.user.id);
    window.Echo.private(`syncing-products.${page.auth.user.id}`)
        .listen(".start-syncing-products", (e) => {
            progressBa.value = e;
        });

})
</script>
