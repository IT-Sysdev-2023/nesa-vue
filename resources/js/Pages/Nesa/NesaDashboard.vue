<template>
    <AuthenticatedLayout>
        <!-- Container: center + padding -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Optional header -->
              <div :class="isDarkMode ? ' text-white mb-10' : 'text-black mb-10'">
                    <h1 class="text-2xl md:text-3xl font-bold ">Nesa</h1>
                    <p class="mt-1 md:mt-2 text-sm">Welcome back! Here's what's happening today.</p>
                </div>


            <!-- Responsive 1 / 2 / 3 column grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Column item / card 1 -->
                <article>
                  <DashboardCard :class="isDarkMode ? 'dark:bg-gray-800 text-white' : 'text-black'" v-if="[5,1,2].includes(page.auth.user.usertype)" title="Near Expiry Stock Advice"/>
                </article>
                 <article >
                  <ApprovalDashboard :class="isDarkMode ? 'dark:bg-gray-800 text-white' : 'text-black'" v-if="[6,1,2].includes(page.auth.user.usertype)" title="Nesa Approval"/>
                </article>
                    <article >
                  <ApprovedListDashboard  :class="isDarkMode ? 'dark:bg-gray-800 text-white' : 'text-black'"  v-if="[7,1,2].includes(page.auth.user.usertype)" title="Nesa Approved List"/>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import ApprovalDashboard from "@/Components/DashboardComponents/ApprovalDashboard.vue";
import ApprovedListDashboard from "@/Components/DashboardComponents/ApprovedListDashboard.vue";
import DashboardCard from "@/Components/DashboardComponents/DashboardCard.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

const page = usePage().props;

// Dark mode state - synced with layout
const isDarkMode = ref(false);

onMounted(() => {
    const savedMode = localStorage.getItem('darkMode');
    if (savedMode !== null) {
        isDarkMode.value = savedMode === 'true';
    }

    // Watch for changes in localStorage
    window.addEventListener('storage', (e) => {
        if (e.key === 'darkMode') {
            isDarkMode.value = e.newValue === 'true';
        }
    });
});

</script>
