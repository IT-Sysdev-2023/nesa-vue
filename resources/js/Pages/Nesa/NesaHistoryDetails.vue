<template>
    <AuthenticatedLayout>
        <div class=" mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Near Expiry History</h1>
                <p class="mt-2 text-sm text-gray-600">Viewing History</p>
            </div>
        </div>
        <a-card>
            <p class="text-center">{{ supplier }}</p>
            <a-directory-tree v-model:expandedKeys="expandedKeys" v-model:selectedKeys="selectedKeys" multiple
                :tree-data="treeData" />
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps<{
    records: any[],
    supplier: string,
}>();

const expandedKeys = ref<string[]>([]);
const selectedKeys = ref<string[]>([]);

const treeData = computed(() => {
    const tree: any[] = [];
    const itemGroups: Record<string, any> = {};

    // Flatten all records and group by itemcode
    props.records.flat().forEach(item => {
        if (!itemGroups[item.itemcode]) {
            itemGroups[item.itemcode] = {
                title: `${item.description}`,
                key: `item-${item.itemcode}`,
                children: []
            };
        }

        itemGroups[item.itemcode].children.push({
            title: `${item.name} - ${item.quantity} pcs expires on ${item.expiry}`,
            key: `item-${item.itemcode}-bu-${item.bu}`,
            isLeaf: true
        });
    });

    // Add all item groups to the tree
    Object.values(itemGroups).forEach(group => {
        tree.push(group);
    });

    return tree;
});
</script>
