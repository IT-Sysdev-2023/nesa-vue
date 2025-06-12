<template>
    <AuthenticatedLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-10">
            <!-- Header -->
            <div class=" mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Supplier</h1>
                    <p class="mt-2 text-sm text-gray-600">Viewing the complete supplier list</p>
                </div>
            </div>
            <a-alert v-if="isAlert" message="Syncing Successful" class="mb-3" description="Supplier is up to date!"
                type="success" show-icon />
            <Card>
                <ProgressBar v-if="isSyncing" type="circle" :progress="progressbar" />
                <div class="flex justify-between">
                    <button @click="syncSupplier"
                        class="mb-3 bg-blue-600 hover:bg-blue-700 px-4 py-2 text-white rounded-md">
                        Sync Supplier
                    </button>
                    <div>
                        <input
                            class="w-[300px] border border-gray-300 rounded-md mb-2 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            type="text" v-model="supplierSearch" placeholder="Search supplier">
                    </div>
                </div>
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
            </Card>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Card } from 'ant-design-vue';
import Pagination from '@/Components/Pagination.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';


const props = defineProps({
    records: Object
});
interface AuthUser {
    id: number;
}

interface PageProps {
    auth: {
        user: AuthUser;
    };
}

const page = usePage().props as unknown as PageProps;

const progressbar = ref([]);
const isSyncing = ref(false);
const isAlert = ref(false);

const syncSupplier = () => {
    router.get(route('admin.sync.supplier'), {}, {
        onStart: () => {
            isSyncing.value = true;
        },
        onSuccess: () => {
            isSyncing.value = false;
            isAlert.value = true;
        },
        onError: () => {
            isSyncing.value = false;
        },
        onFinish: () => {
            isSyncing.value = false;
        }
    })
};

onMounted(() => {
    window.Echo.private(`syncing-products.${page.auth.user.id}`)
        .listen(".start-syncing-products", (e) => {
            progressbar.value = e;
        });

});
const supplierSearch = ref<string>('');

watch(supplierSearch, () => {
    router.get(route('admin.supplier.list'), {
        search: supplierSearch.value
    }, {
        preserveState: true,
        preserveScroll: true

    });
});
</script>
