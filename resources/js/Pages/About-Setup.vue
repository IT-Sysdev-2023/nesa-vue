<template>
    <AuthenticatedLayout>
        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-12 text-center">
                <h1 class="text-4xl font-bold text-gray-900">Our Team</h1>
                <p class="mt-3 text-xl text-gray-600">The people behind Nesa Development</p>
                <div class="mt-6 w-20 h-1 bg-indigo-500 mx-auto"></div>
            </div>

            <!-- Team Sections -->
            <div class="space-y-16">
                <!-- Supervised Section -->
                <section v-if="groupedEmployees.supervised.length > 0" class="flex flex-col items-center">
                    <div class="mb-8 text-center w-full">
                        <h2 class="text-2xl font-semibold text-gray-800">Leadership</h2>
                        <p class="mt-2 text-gray-500">Guiding our vision and strategy</p>
                    </div>
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center w-full max-w-4xl mx-auto">
                        <div v-for="(employee, index) in groupedEmployees.supervised" :key="'supervised-' + index"
                            class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 w-full max-w-xs">
                            <div class="p-6 flex flex-col items-center">
                                <!-- Avatar -->
                                <div class="relative mb-5">
                                    <img :src="getPhoto(employee.originalName)"
                                        class="w-40 h-40 rounded-full object-cover border-4 border-white shadow-lg"
                                        alt="Team member photo" />
                                    <div class="absolute -bottom-2 -right-2 bg-gray-200 rounded-full p-2 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-6 h-6 text-indigo-500">
                                            <path
                                                d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="text-center">
                                    <h3 class="text-xl font-semibold text-gray-800 capitalize">
                                        {{ employee.employee_name || employee.originalName }}
                                    </h3>
                                    <p class="mt-2 text-indigo-600 font-medium">
                                        {{ employee.employee_position || 'Team Member' }}
                                    </p>
                                </div>

                                <!-- Department -->
                                <div v-if="employee.employee_dept"
                                    class="mt-4 px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-600">
                                    <div class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2H4a1 1 0 110-2V4zm3 1h2v2H7V5zm0 4h2v2H7V9zm0 4h2v2H7v-2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ employee.employee_dept }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Programmers Section -->
                <section v-if="groupedEmployees.programmers.length > 0" class="flex flex-col items-center">
                    <div class="mb-8 text-center w-full">
                        <h2 class="text-2xl font-semibold text-gray-800">Development Team</h2>
                        <p class="mt-2 text-gray-500">Building innovative solutions</p>
                    </div>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center w-full max-w-6xl mx-auto">
                        <div v-for="(employee, index) in groupedEmployees.programmers" :key="'programmer-' + index"
                            class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 w-full max-w-xs">
                            <div class="p-6 flex flex-col items-center">
                                <!-- Avatar -->
                                <div class="relative mb-5">
                                    <img :src="getPhoto(employee.originalName)"
                                        class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg"
                                        alt="Team member photo" />
                                    <div class="absolute -bottom-2 -right-2 bg-gray-200 rounded-full p-2 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-6 h-6 text-indigo-600">
                                            <path fill-rule="evenodd"
                                                d="M14.447 3.027a.75.75 0 01.527.92l-4.5 16.5a.75.75 0 01-1.448-.394l4.5-16.5a.75.75 0 01.921-.526zM16.72 6.22a.75.75 0 011.06 0l5.25 5.25a.75.75 0 010 1.06l-5.25 5.25a.75.75 0 11-1.06-1.06L21.44 12l-4.72-4.72a.75.75 0 010-1.06zm-9.44 0a.75.75 0 010 1.06L2.56 12l4.72 4.72a.75.75 0 11-1.06 1.06L.97 12.53a.75.75 0 010-1.06l5.25-5.25a.75.75 0 011.06 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="text-center">
                                    <h3 class="text-xl font-semibold text-gray-800 capitalize">
                                        {{ employee.employee_name || employee.originalName }}
                                    </h3>
                                    <p class="mt-2 text-indigo-600 font-medium">
                                        {{ employee.employee_position || 'Team Member' }}
                                    </p>
                                </div>

                                <!-- Department -->
                                <div v-if="employee.employee_dept"
                                    class="mt-4 px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-600">
                                    <div class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2H4a1 1 0 110-2V4zm3 1h2v2H7V5zm0 4h2v2H7V9zm0 4h2v2H7v-2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ employee.employee_dept }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Analysts Section -->
                <section v-if="groupedEmployees.analysts.length > 0" class="flex flex-col items-center">
                    <div class="mb-8 text-center w-full">
                        <h2 class="text-2xl font-semibold text-gray-800">Analysis Team</h2>
                        <p class="mt-2 text-gray-500">Transforming data into insights</p>
                    </div>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center w-full max-w-4xl mx-auto">
                        <div v-for="(employee, index) in groupedEmployees.analysts" :key="'analyst-' + index"
                            class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 w-full max-w-xs">
                            <div class="p-6 flex flex-col items-center">
                                <!-- Avatar -->
                                <div class="relative mb-5">
                                    <img :src="getPhoto(employee.originalName)"
                                        class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg"
                                        alt="Team member photo" />
                                    <div class="absolute -bottom-2 -right-2 bg-gray-200 rounded-full p-2 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-6 h-6 text-indigo-600">
                                            <path
                                                d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="text-center">
                                    <h3 class="text-xl font-semibold text-gray-800 capitalize">
                                        {{ employee.employee_name || employee.originalName }}
                                    </h3>
                                    <p class="mt-2 text-indigo-600 font-medium">
                                        {{ employee.employee_position || 'Team Member' }}
                                    </p>
                                </div>

                                <!-- Department -->
                                <div v-if="employee.employee_dept"
                                    class="mt-4 px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-600">
                                    <div class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2H4a1 1 0 110-2V4zm3 1h2v2H7V5zm0 4h2v2H7V9zm0 4h2v2H7v-2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ employee.employee_dept }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

interface Employee {
    employee_photo?: string;
    employee_name?: string;
    employee_position?: string;
    employee_dept?: string;
    originalName: string;
}

const supervised = ['fuertes, maria', 'evarle, maria'];
const programmers = ['gamale, teofredo', 'barace, harvey', 'abarquez, kent'];
const analysts = ['pantilag, hazel', 'cagas, claire'];

const allNames = [...supervised, ...programmers, ...analysts];
const employeeRecords = ref<Record<string, Employee>>({});

const groupedEmployees = computed(() => {
    const groups = {
        supervised: [] as Employee[],
        programmers: [] as Employee[],
        analysts: [] as Employee[]
    };

    supervised.forEach(name => {
        if (employeeRecords.value[name]) {
            groups.supervised.push({
                ...employeeRecords.value[name],
                originalName: name
            });
        }
    });

    programmers.forEach(name => {
        if (employeeRecords.value[name]) {
            groups.programmers.push({
                ...employeeRecords.value[name],
                originalName: name
            });
        }
    });

    analysts.forEach(name => {
        if (employeeRecords.value[name]) {
            groups.analysts.push({
                ...employeeRecords.value[name],
                originalName: name
            });
        }
    });

    return groups;
});

const getPhoto = (name: string) => {
    const photo = employeeRecords.value[name]?.employee_photo;
    return photo
        ? `http://172.16.161.34:8080/hrms${photo}`
        : '/storage/images/noUser.jpg';
};

const getTeam = async () => {
    try {
        const requests = allNames.map(name =>
            axios.get('http://172.16.161.34/api/gc/filter/employee/name', {
                params: { q: name },
            })
        );
        const responses = await Promise.all(requests);
        responses.forEach((response, index) => {
            const name = allNames[index];
            const data = response.data?.data?.employee?.[0];
            if (data) employeeRecords.value[name] = data;
        });
    } catch (error) {
        console.error('Error loading team members:', error);
    }
};

onMounted(getTeam);
</script>