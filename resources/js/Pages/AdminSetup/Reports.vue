<script setup lang="ts">
import MasterFileCard from "@/Components/MasterFileComponents/MasterFileCard.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
</script>

<template>
    <AuthenticatedLayout>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mt-5">
            <div class="">
                <MasterFileCard title="Near Expiry Reports" routeTo="admin.masterfile" />
            </div>
             <div class="">
                <MasterFileCard title="Bad Order Reports" routeTo="admin.masterfile" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
