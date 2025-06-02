<template>
    <AuthenticatedLayout>
        <ProgressBar type="circle" v-if="isSyncing" :progress="progressBa">
        </ProgressBar>
        <a-card>
            <div class="flex justify-between">
                <a-button @click="syncProducts" class="mb-2" :loading="isSyncing">
                    <template #icon>
                        <FastForwardOutlined />
                    </template>
                    Sync Products
                </a-button>
                <input type="text" v-model="form.search"
                    class="border mb-2 border-gray-300 rounded-md px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Search Item code">

            </div>

            <a-table class="" :pagination="false" bordered size="small" :data-source="records.data" :columns="columns">
            </a-table>
            <Pagination class="mt-3" :datarecords="records" />
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import { FastForwardOutlined } from '@ant-design/icons-vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import { usePage, router } from "@inertiajs/vue3";
import { onMounted, ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';

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
