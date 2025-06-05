<template>

    <Head title="Add User" />
    <AuthenticatedLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-10">
            <div class="mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Add New User</h1>
                    <p class="mt-2 text-sm text-gray-600">Creating user credentials for login access</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">User Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Column -->
                    <div class="space-y-4">
                        <!-- First Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Firstname</label>
                            <input v-model="forms.firstname" type="text" placeholder="Juan" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.firstname
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]" />
                            <p v-if="errors.firstname" class="text-red-500 text-sm mt-1">{{ errors.firstname }}</p>
                        </div>

                        <!-- Middle Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Middlename</label>
                            <input v-model="forms.middlename" type="text" placeholder="D" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.middlename
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]" />
                            <p v-if="errors.middlename" class="text-red-600 text-sm mt-1">{{ errors.middlename }}</p>
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input v-model="forms.username" type="text" placeholder="juan.delacruz" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.username
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]" />
                            <p v-if="errors.username" class="text-red-600 text-sm mt-1">{{ errors.username }}</p>
                        </div>
                        <!-- Business Unit  -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business Unit</label>
                            <select v-model="forms.businessUnit" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.businessUnit
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]">
                                <option v-for="item in props.businessUnit" :key="item.id" :value="item.id">{{ item.name
                                }}
                                </option>
                            </select>
                            <p v-if="errors.businessUnit" class="text-red-600 text-sm mt-1">{{ errors.businessUnit }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Last Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lastname</label>
                            <input v-model="forms.lastname" type="text" placeholder="Dela Cruz" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.lastname
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]" />
                            <p v-if="errors.lastname" class="text-red-600 text-sm mt-1">{{ errors.lastname }}</p>
                        </div>

                        <!-- Name Extension -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name Extension</label>
                            <input v-model="forms.nameExtention" type="text" placeholder="Jr, Sr, II, III" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.nameExtention
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]" />
                            <p v-if="errors.nameExtention" class="text-red-600 text-sm mt-1">{{ errors.nameExtention }}
                            </p>

                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input v-model="forms.password" type="password" placeholder="password" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.password
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]" />
                            <p v-if="errors.password" class="text-red-600 text-sm mt-1">{{ errors.password }}</p>

                        </div>
                        <!-- User type  -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Usertype</label>
                            <select v-model="forms.usertype" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.usertype
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]">
                                <option v-for="item in props.usertypes" :key="item.id" :value="item.id">{{ item.name }}
                                </option>
                            </select>
                            <p v-if="errors.usertype" class="text-red-600 text-sm mt-1">{{ errors.usertype }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 space-y-4">
                    <div class="flex justify-end mt-8">
                        <button @click="cancelButton" type="button"
                            class="px-6 py-2 mr-4 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit" @click="submitButton"
                            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Create User
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { Head, useForm } from '@inertiajs/vue3';
import { notification } from 'ant-design-vue';
import { computed } from 'vue';

const pages = usePage();
const errors = computed(() => pages.props.errors)

const props = defineProps<{
    usertypes: userType[];
    businessUnit: businessUnit[];
}>();
interface userType {
    name: string;
    id: number;
}
interface businessUnit {
    id: number;
    name: string
}
const forms = useForm({
    firstname: '',
    lastname: '',
    middlename: '',
    nameExtention: '',
    username: '',
    password: '',
    usertype: '',
    businessUnit: ''
});
const cancelButton = () => {
    router.get(route('dashboard'));
};
const submitButton = () => {
    router.post(route('admin.submitUser'), {
        firstname: forms.firstname,
        lastname: forms.lastname,
        middlename: forms.middlename,
        nameExtention: forms.nameExtention,
        username: forms.username,
        password: forms.password,
        usertype: forms.usertype,
        businessUnit: forms.businessUnit
    }, {
        onSuccess: (page: any) => {
            if (page?.props?.flash?.success) {
                notification.success({
                    message: 'Success',
                    description: page.props.flash.success
                });
                forms.reset();
            } else if (page?.props?.flash?.error) {
                notification.error({
                    message: 'Oops',
                    description: page.props.flash.error
                });
            }
        },
    });
};
</script>