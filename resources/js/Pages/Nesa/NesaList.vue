<template>
    <AuthenticatedLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-10">
            <!-- Header -->
            <div class=" mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Near Expiry Stock Advice</h1>
                    <p class="mt-2 text-sm text-gray-600">Viewing the complete NESA list</p>
                </div>
            </div>
            <Card>
                <template #title>
                    <div class="flex justify-between">
                        <div>
                            <PrimaryButton v-if="canBeConsolidate && records.data.length" class="mr-1 mt-1"
                                @click="() => router.get(route('nesa.send.email'))">
                                Consolidate Nesa
                            </PrimaryButton>
                        </div>
                        <div class="relative">
                            <a-input type="text" v-model:value="form.search" placeholder="Search Supplier"
                                class="w-[450px] py-1 pl-10 pr-4 rounded-lg border border-gray-300 " />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                    </div>

                </template>
                <Table bordered :pagination="false" size="small" :data-source="records.data" :columns="columns">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key == 'action'">
                            <a-button type="primary" size="small" class="mr-1"
                                @click="() => router.get(route('nesa.view.list', record.item_code))">
                                View
                            </a-button>
                            <a-button size="small" class="mr-1">
                                Tag
                            </a-button>
                        </template>
                    </template>
                </Table>
                <Pagination class="mt-3" :datarecords="records">

                </Pagination>


            </Card>
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import { Card, Table } from 'ant-design-vue';
import { onMounted, ref, watch } from 'vue';

interface Nesa {
    data: {
        name: string,
        description: string,
        nesa_date: string,
        nesa_no: number,
    }[]
}
const props = defineProps<{
    records: Nesa
}>();

const columns = ref<any>([
    {
        title: 'Item Code',
        dataIndex: 'item_code',
        align: 'center',
    },
    {
        title: 'Nesa Date',
        dataIndex: 'nesa_date',
    },
    {
        title: 'Business Unit',
        dataIndex: 'buname',
    },
    {
        title: 'Supplier',
        dataIndex: 'supname',
    },
    {
        title: 'Item Description',
        dataIndex: 'description',
    },
    {
        title: 'Action',
        align: 'center',
        key: 'action',
    },
])

const form = ref({
    search: null as string
})

const canBeConsolidate = ref<boolean>(false);


const search = () => {
    router.get(route('nesa.search.supplier'), {
        search: form.value.search
    }, {
        onSuccess: () => {
            if (form.value.search == '') {
                canBeConsolidate.value = false;
            } else {
                canBeConsolidate.value = true;
            }
        },
        preserveScroll: true,
        preserveState: true,
    })
};

watch(() => form.value.search, () => {
    search();

});


</script>
