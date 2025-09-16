<template>
    <AuthenticatedLayout>
        <!-- Container: center + padding -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Optional header -->
              <div class="mb-3">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Nesa</h1>
                    <p class="mt-1 md:mt-2 text-sm text-gray-600">Welcome back! Here's what's happening today.</p>
                </div>


            <!-- Responsive 1 / 2 / 3 column grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Column item / card 1 -->
                <article >
                  <DashboardCard title="Near Expiry Stock Advice"/>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import DashboardCard from "@/Components/DashboardComponents/DashboardCard.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
</script>
