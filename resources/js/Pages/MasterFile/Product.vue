<template>
    <AuthenticatedLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-10">
            <!-- Header -->
            <div class=" mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Products</h1>
                    <p class="mt-2 text-sm text-gray-600">Viewing the complete product list</p>
                </div>
            </div>
            <ProgressBar type="circle" v-if="isSyncing" :progress="progressBa">
            </ProgressBar>
            <Card>
                <div class="flex justify-between">
                    <button @click="syncProducts" class="mb-3 bg-blue-600 hover:bg-blue-700 px-4 py-2 text-white rounded-md">
                        Sync Products In Nav
                    </button>

                    <input type="text" v-model="form.search"
                        class="w-[300px] border border-gray-300 rounded-md mb-2 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Search Item code" />

                </div>
                <Table class="" :pagination="false" bordered size="small" :data-source="records.data"
                    :columns="columns">
                </Table>
                <Pagination class="mt-5" :datarecords="records" />
            </Card>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Card, Table } from 'ant-design-vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import { usePage, router } from "@inertiajs/vue3";
import { onMounted, ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
declare global {
    interface Window {
        Echo: any;
    }
}

interface PageProps {
    auth: {
        user: {
            id: number;
        }
    };
};

const page = usePage().props as unknown as PageProps;

const props = defineProps({
    records: Object
});

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
]);
const isSyncing = ref(false);

const progressBa = ref([]);

const form = ref({
    search: '',
});

const search = () => {
    router.get(route('admin.search.products'), {
        search: form.value.search
    }, {
        preserveScroll: true,
        preserveState: true,
    })
};

watch(() => form.value.search, () => {
    search();
});

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
};

onMounted(() => {
    console.log(page.auth.user.id);
    window.Echo.private(`syncing-products.${page.auth.user.id}`)
        .listen(".start-syncing-products", (e) => {
            progressBa.value = e;
        });

});
</script>
