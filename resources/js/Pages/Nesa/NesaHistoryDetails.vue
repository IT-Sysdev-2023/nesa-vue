<template>
    <AuthenticatedLayout>
        <div class=" mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Near Expiry History</h1>
                <p class="mt-2 text-sm text-gray-600">Viewing History</p>
            </div>
        </div>
        <a-card>
            <div class="flex justify-between mb-3">
                <a-button @click="tag" type="primary" :disabled="records[0].data.filter(item => item.coa == null).length" style="width: 100px;">
                    Tag
                </a-button>
                <a-button @click="router.get(route('nesa.get.history'))">
                    Back to Summary
                </a-button>
            </div>
            <a-table size="small" bordered :pagination="false" :data-source="records[0].data" :columns="columns">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key == 'signature'">
                        <a-image :src="'/storage/singatures/' + record.signature" />
                    </template>
                    <template v-if="column.key == 'quantity'">
                        {{ record.quantity }} pcs
                    </template>
                    <template v-if="column.key == 'action'">
                        <a-select :value="record.coa" placeholder="Select Type" ref="select" style="width: 100%"
                            @change="(value) => handleChangeCourseOfAction(value, record.id)">
                            <a-select-option v-for="record in coa" :value="record.id">{{ record.name
                            }}</a-select-option>
                        </a-select>
                    </template>
                </template>
            </a-table>
            <Pagination :datarecords="records[0]" class="mt-3" />
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';
import type { TableColumnType } from 'ant-design-vue';
import Pagination from '@/Components/Pagination.vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps<{
    records: DataType,
    supplier: string,
    coa: any,
    id: number,
}>();

interface DataType {
    id: string;
    itemcode: number;
    quantity: number;
    description: string;
    signature: string;
}[]

const columns = ref<TableColumnType<DataType>[]>([
    {
        title: 'Item Code',
        dataIndex: 'itemcode',
        key: 'name',
    },
    {
        title: 'Description',
        dataIndex: 'description',
        key: 'address',
    },
    {
        title: 'Business Units',
        dataIndex: 'name',
        key: 'age',
    },
    {
        title: 'Quantity',
        dataIndex: 'quantity',
        key: 'quantity',
        align: 'center',
    },
    {
        title: 'Signature',
        key: 'signature',
        align: 'center',
    },
    {
        title: 'Course of Action',
        key: 'action',
        align: 'center',
    },
]);


const handleChangeCourseOfAction = (value: number, id: number) => {
    router.put(route('nesa.update.course_of_action'), {
        id: id,
        coa: value,
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
                    icon: "success",
                });
            }
        }
    })
    // form.coa = value;
}

const tag = () => {
    router.put(route('nesa.tag.coa'), {
        id: props.id
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
                router.get(route('nesa.get.history'));
            }
        }
    })
}

</script>
