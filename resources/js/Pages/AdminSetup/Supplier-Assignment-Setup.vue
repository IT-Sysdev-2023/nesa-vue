<template>
    <a-modal v-model:open="props.open" width="90%" :footer="null" class="rounded-lg" centered
        @cancel="cancelSupplierButton" :closable="false">
        <div class="p-1">
            <div class="mb-6">

                <h2 class="text-2xl font-bold text-gray-800">Supplier Assignment</h2>
                <p class="text-gray-500">Manage suppliers for selected user</p>
            </div>

            <div class="flex gap-6">
                <!-- Suppliers Panel -->
                <div class="w-1/3 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="font-medium text-gray-700">Selected Suppliers</h3>
                        <a-tag class="ml-auto" v-if="selectedSuppliers.length > 0" color="blue">
                            {{ selectedSuppliers.length }} selected
                        </a-tag>
                    </div>

                    <div v-if="selectedSuppliers.length > 0"
                        class="bg-white rounded p-3 h-[500px] overflow-y-auto space-y-2">
                        <div v-for="(supplier, index) in selectedSuppliers" :key="index"
                            class="flex items-center justify-between p-2 bg-gray-50 rounded hover:bg-gray-100 transition">
                            <span><a-tag color="blue" class="text-center text-lg">{{ supplier.label
                            }}</a-tag></span>
                            <button @click="removeSupplier(index)" class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div v-else class="bg-white rounded p-3 h-[500px] overflow-y-auto">
                        <div class="text-center py-12 text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p>No suppliers selected</p>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <a-tag color="yellow" class="text-sm text-yellow-700 px-4 py-2 flex ">
                            <svg class="w-5 h-5 mr-2 text-yellow-800" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M5.93 19h12.14a2 2 0 001.74-2.99l-6.07-10.5a2 2 0 00-3.48 0L4.19 16.01A2 2 0 005.93 19z" />
                            </svg>
                            Make sure to click the confirm button after
                            selecting
                            suppliers</a-tag>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="w-2/3">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <!-- search  -->
                        <div class="flex justify-between">
                            <div class="flex justify-end ml-5 mt-3 mb-3">
                                <input type="text" v-model="supplierSearch"
                                    class="w-[300px] border border-gray-300 rounded-md mb-2 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Search Supplier" />
                            </div>
                            <!-- select all button  -->
                            <div class="mt-5">
                                <a-tag color="blue">
                                    <a-checkbox :checked="selectAll" @change="toggleSelectAll"
                                        :disabled="loadingSuppliers"
                                        :indeterminate="selectedSuppliers.length > 0 && !selectAll">
                                        <span v-if="selectAllLoading">
                                            <a-spin />
                                        </span>
                                        Select All
                                    </a-checkbox>
                                </a-tag>
                            </div>
                        </div>
                        <!-- table showing all the suppliers  -->
                        <a-spin :spinning="loadingSuppliers">
                            <a-table :data-source="allSupplier" :columns="supplierColumns" size="small" class="w-full"
                                :pagination="false" :loading="loadingSuppliers">
                                <template #bodyCell="{ column, record }">
                                    <template v-if="column.dataIndex === 'action'">
                                        <a-checkbox :checked="isSupplierSelected(record)"
                                            @change="(e) => toggleSupplier(record, e.target.checked)"
                                            :disabled="loadingSuppliers || selectAllLoading">
                                            Select
                                        </a-checkbox>
                                    </template>
                                </template>

                                <template #emptyText>
                                    <div class="py-8 text-center text-gray-400">
                                        <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p>No suppliers found</p>
                                    </div>
                                </template>
                            </a-table>
                            <div
                                class="flex justify-between items-center mt-4 px-2 py-2 bg-gray-50 border-t border-gray-200">
                                <div class="text-sm text-gray-600">
                                    Showing {{ from }} to {{ to }} of {{ total }} records
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="fetchSupplier(currentPage - 1)" :disabled="currentPage === 1"
                                        class="px-3 py-1 border rounded-md text-sm"
                                        :class="currentPage === 1 ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'">
                                        Previous
                                    </button>
                                    <span class="px-3 py-1 text-sm text-gray-700">
                                        Page {{ currentPage }} of {{ lastPage }}
                                    </span>
                                    <button @click="fetchSupplier(currentPage + 1)" :disabled="currentPage === lastPage"
                                        class="px-3 py-1 border rounded-md text-sm"
                                        :class="currentPage === lastPage ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </a-spin>
                    </div>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <button @click="exitButton" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition">
                    Close
                </button>
                <button @click="cancelSupplierButton"
                    class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-md transition">
                    Remove All
                </button>
                <button @click="selectSupplierButton"
                    class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-md transition shadow-sm">
                    Confirm Selection
                </button>
            </div>
        </div>
    </a-modal>
</template>
<script setup lang="ts">
import { ref, watch, defineProps, defineEmits } from 'vue';
import { debounce } from 'lodash';
import axios from 'axios';
import { notification } from 'ant-design-vue';

const props = defineProps({
    open: {
        type: Boolean,
        required: true
    },
    existingSupplier: {
        type: Array,
        default: () => []
    }
});
const emit = defineEmits(['update:open', 'update:selected-suppliers']);

const loadingSuppliers = ref<boolean>(false);
interface Supplier {
    label: string;
    value: string;
}

const selectedSuppliers = ref<Supplier[]>([]);
const allSupplier = ref<any[]>([]);
const supplierColumns = ref<[] | any>([]);
const supplierSearch = ref<string>('');
const selectAll = ref<boolean>(false);
const selectAllLoading = ref<boolean>(false);

// Watch for modal open and initialize
watch(() => props.open, async (newVal) => {
    if (newVal) {
        await fetchSupplier(); // First fetch all suppliers

        // Initialize selected suppliers after we have allSupplier data
        if (props.existingSupplier && props.existingSupplier.length > 0) {
            selectedSuppliers.value = props.existingSupplier
                .map(code => {
                    // Find the corresponding supplier in allSupplier to get the name
                    const foundSupplier = allSupplier.value.find(s => s.supplierCode === code);
                    return {
                        value: String(code),
                        label: foundSupplier ? foundSupplier.supplierName : `Supplier ${code}`
                    } as Supplier;
                })
                .filter(s => s !== null); // Filter out any null entries if supplier not found
        } else {
            selectedSuppliers.value = [];
        }
    }
});

const currentPage = ref<any | []>([]);
const perPage = ref<any | []>([]);
const total = ref<any | []>([]);
const lastPage = ref<any | []>([]);
const from = ref<any | []>([]);
const to = ref<any | []>([]);


const fetchSupplier = async (page = 1) => {
    try {
        loadingSuppliers.value = true;
        const response = await axios.get(route('admin.selectedSupplier'), {
            params: {
                page: page,
                search: supplierSearch.value
            }
        });

        allSupplier.value = response.data.specifiedProducts || [];
        supplierColumns.value = response.data.productColumns || [];

        currentPage.value = response.data.pagination?.current_page || 1;
        perPage.value = response.data.pagination?.per_page || 10;
        total.value = response.data.pagination?.total || 0;
        lastPage.value = response.data.pagination?.last_page || 1;
        from.value = response.data.pagination?.from || 0;
        to.value = response.data.pagination?.to || 0;

    } catch (error) {
        console.error('Error fetching supplier', error);
        notification.error({
            message: 'Error',
            description: 'Failed to load suppliers'
        });
    } finally {
        loadingSuppliers.value = false;
    }
};

const toggleSupplier = (record: Record<string, any>, checked: boolean) => {
    if (checked) {
        if (!selectedSuppliers.value.some(s => s.value === record.supplierCode)) {
            selectedSuppliers.value.push({
                label: record.supplierName,
                value: record.supplierCode
            });
        }
    } else {
        const index = selectedSuppliers.value.findIndex(
            s => s.value === record.supplierCode
        );
        if (index !== -1) {
            selectedSuppliers.value.splice(index, 1);
        }
        if (selectAll.value) {
            selectAll.value = false;
        }
    }
};

const isSupplierSelected = (record: Record<string, any>) => {
    return selectedSuppliers.value.some(s => s.value === record.supplierCode);
};

const selectSupplierButton = () => {
    // Emit just the supplier codes (values) back to parent
    const selectedValues = selectedSuppliers.value.map(s => s.value);
    emit('update:selected-suppliers', selectedValues);
    emit('update:open', false);
};

const removeSupplier = (index: number) => {
    selectedSuppliers.value.splice(index, 1);
};

const cancelSupplierButton = () => {
    selectedSuppliers.value = [];
    selectAll.value = false;
};

const exitButton = () => {
    emit('update:open', false);
};

const toggleSelectAll = async (checked: boolean) => {
    selectAllLoading.value = true;

    try {
        if (checked) {
            // Fetch ALL suppliers that match current search
            const response = await axios.get(route('admin.getSuppliers'), {
                params: {
                    search: supplierSearch.value
                }
            });

            const allMatchingSuppliers = response.data || [];

            // Create a map for quick lookup
            const existingSelections = new Set(selectedSuppliers.value.map(s => s.value));

            // Add only new suppliers that aren't already selected
            selectedSuppliers.value = [
                ...selectedSuppliers.value,
                ...allMatchingSuppliers
                    .filter(supplier => !existingSelections.has(supplier.value))
                    .map(supplier => ({
                        label: supplier.label,
                        value: supplier.value
                    }))
            ];

            selectAll.value = true;
        } else {
            if (supplierSearch.value) {
                // If searching, remove only suppliers matching the search
                const response = await axios.get(route('admin.getAllSupplierIds'), {
                    params: {
                        search: supplierSearch.value
                    }
                });

                const codesToRemove = new Set(response.data.map(s => s.value));
                selectedSuppliers.value = selectedSuppliers.value.filter(
                    s => !codesToRemove.has(s.value)
                );
            } else {
                selectedSuppliers.value = [];
            }
            selectAll.value = false;
        }
    } catch (error) {
        console.error('Error in select all:', error);
        notification.error({
            message: 'Error',
            description: 'Failed to process select all'
        });
    } finally {
        selectAllLoading.value = false;
    }
};
watch(supplierSearch, debounce(() => {
    searchFunction();
}, 300));

const searchFunction = async () => {
    await fetchSupplier(1);
};
</script>
