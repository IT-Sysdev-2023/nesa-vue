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
            <a-progress :type="type" :stroke-color="{
                '0%': '#108ee9',
                '100%': '#87d068',
            }" :percent="progress.percentage" />
        </div>
    </div>
</template>
<script setup>

const props = defineProps({
    progress: Object,
    type: String
})
</script>
