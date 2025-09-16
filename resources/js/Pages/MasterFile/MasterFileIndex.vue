<template>
    <AuthenticatedLayout>
        <!-- Optional header -->
        <div class="mb-3">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                MasterFile
            </h1>
        </div>
        <div class="max-w-7xl mx-auto px-1 sm:px-6 lg:px-8 py-6">
            <!-- Responsive 1 / 2 / 3 column grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Column item / card 1 -->
                <article>
                    <MasterFileCard title="Products" routeTo="admin.masterfile"/>
                </article>
                <article>
                    <MasterFileCard title="Suppliers" routeTo="admin.supplier.list"/>
                </article>
                 <article>
                    <MasterFileCard title="Users" routeTo="admin.setupUser"/>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import MasterFileCard from "@/Components/MasterFileComponents/MasterFileCard.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
</script>
