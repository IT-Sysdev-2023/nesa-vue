<template>
    <div class="space-y-2 mt-3 mb-3">
        <div class="flex justify-between text-sm text-gray-600">
            <div>
                <span>{{ progress?.message }}</span>
            </div>
            <div>
                <span>{{ progress?.currentRow }}</span> / <span>{{ progress?.totalRows }}</span>
            </div>
        </div>

        <div class="flex justify-center">
            <Progress :type="type" :stroke-color="{
                '0%': '#108ee9',
                '100%': '#87d068',
            }" :percent="progress.percentage" />
        </div>
    </div>
</template>
<script setup>
import { Progress} from 'ant-design-vue';
const props = defineProps({
    progress: Object,
    type: String
})
</script>
