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
                <div class="flex justify-center mb-10">
                    <!-- Search User  -->
                    <div class="w-full max-w-md">
                        <label class="block text-lg font-medium text-gray-700 mb-1 text-center">
                            Search User
                        </label>
                        <a-auto-complete v-model:value="searchUser" :options="showOptions"
                            placeholder="Lastname, Firstname" class="w-full" @select="selectedName" />
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-medium font-medium text-gray-700 mb-1">Firstname</label>
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
                            <label class="block text-medium font-medium text-gray-700 mb-1">Middlename</label>
                            <input v-model="forms.middlename" type="text" placeholder="D" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.middlename
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]" />
                            <p v-if="errors.middlename" class="text-red-600 text-medium mt-1">{{ errors.middlename }}
                            </p>
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block text-medium font-medium text-gray-700 mb-1">Username</label>
                            <input v-model="forms.username" type="text" placeholder="juan.delacruz" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.username
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]" />
                            <p v-if="errors.username" class="text-red-600 text-medium mt-1">{{ errors.username }}</p>
                        </div>
                        <!-- Business Unit  -->
                        <div>
                            <label class="block text-medium font-medium text-gray-700 mb-1">Business Unit</label>
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
                            <p v-if="errors.businessUnit" class="text-red-600 text-medium mt-1">{{ errors.businessUnit
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Last Name -->
                        <div>
                            <label class="block text-medium font-medium text-gray-700 mb-1">Lastname</label>
                            <input v-model="forms.lastname" type="text" placeholder="Dela Cruz" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.lastname
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]" />
                            <p v-if="errors.lastname" class="text-red-600 text-medium mt-1">{{ errors.lastname }}</p>
                        </div>

                        <!-- Name Extension -->
                        <div>
                            <label class="block text-medium font-medium text-gray-700 mb-1">Name Extension</label>
                            <input v-model="forms.nameExtention" type="text" placeholder="Jr, Sr, II, III" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.nameExtention
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]" />
                            <p v-if="errors.nameExtention" class="text-red-600 text-medium mt-1">{{ errors.nameExtention
                                }}
                            </p>

                        </div>

                        <!-- User type  -->
                        <div>
                            <label class="block text-medium font-medium text-gray-700 mb-1">Usertype</label>
                            <select v-model="forms.usertype" :class="[
                                'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                                errors.usertype
                                    ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                            ]">
                                <option v-for="item in props.usertypes" :key="item.id" :value="item.id">{{ item.name }}
                                </option>
                            </select>
                            <p v-if="errors.usertype" class="text-red-600 text-medium mt-1">{{ errors.usertype }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-end mt-2">
                        <button @click="cancelButton" type="button"
                            class="px-6 py-2 mr-4 text-medium font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit" @click="submitButton"
                            class="px-6 py-2 text-medium font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Create User
                        </button>
                    </div>
                    <div>
                        <p
                            class="flex items-start text-sm text-blue-800 bg-blue-50 border border-blue-300 mt-5 p-3 rounded-md max-w-sm mx-auto">
                            <span><strong>Note:</strong> <span class="text-blue-900"> NESA2025 is the default
                                    password.</span></span>
                        </p>
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
import { computed, ref, watch } from 'vue';
import axios from 'axios';

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
    businessUnit: '',
    employee_no: ''
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
        businessUnit: forms.businessUnit,
        employee_id: forms.employee_no

    }, {
        onSuccess: (page: any) => {
            if (page?.props?.flash?.success) {
                notification.success({
                    message: 'Success',
                    description: page.props.flash.success
                });
                forms.reset();
                searchUser.value = null;
            } else if (page?.props?.flash?.error) {
                notification.error({
                    message: 'Oops',
                    description: page.props.flash.error
                });
            }
        },
    });
};

const searchUser = ref<string>('');
interface Employee {
    employee_name: string;
    username?: string;
    lastname?: string;
    firstname?: string;
    middlename?: string;
    employee_id?: string;
    employee_no: string;
};
const options = ref<Employee[]>([]);
const autoFillData = ref<Employee[]>([]);

const selectedName = computed(() => {
    forms.username = autoFillData.value[0]?.username || '';
    forms.firstname = autoFillData.value[0]?.firstname || '';
    forms.lastname = autoFillData.value[0]?.lastname || '';
    forms.middlename = autoFillData.value[0]?.middlename || '';
    forms.employee_no = autoFillData.value[0]?.employee_no || '';
});

const getUserInfo = async () => {
    try {
        const response = await axios.get('http://172.16.161.34/api/gc/filter/employee/name', {
            params: {
                q: searchUser.value
            }
        });

        const employeeList = response.data?.data?.employee;
        console.log(employeeList);

        if (Array.isArray(employeeList) && employeeList.length > 0) {
            options.value = employeeList.map(employee => {
                const [lastname, restOfName] = employee.employee_name.split(', ').map(part => part.trim());
                const nameParts = restOfName.split(' ');

                const middlename = nameParts.length > 1 ? nameParts.pop() : '';
                const firstname = nameParts.join(' ');

                return {
                    ...employee,
                    username: `${lastname}.${firstname.replace(' ', '')}`,
                    lastname,
                    firstname,
                    middlename,
                };
            });

            const firstEmployee = employeeList[0];
            const [lastname, restOfName] = firstEmployee.employee_name.split(', ').map(part => part.trim());
            const nameParts = restOfName.split(' ');
            const middlename = nameParts.length > 1 ? nameParts.pop() : '';
            const firstname = nameParts.join(' ');

            autoFillData.value = [{
                employee_name: firstEmployee.employee_name,
                username: `${lastname}.${firstname.replace(' ', '')}`,
                lastname,
                firstname,
                middlename,
                employee_no: employeeList[0].employee_no
            }];
        } else {
            options.value = [];
            autoFillData.value = [];
        }
    } catch (error) {
        console.error('Error fetching user details', error);
        options.value = [];
        autoFillData.value = [];
    }
};

watch(() => searchUser.value, () => {
    getUserInfo();
});

const showOptions = computed(() => {
    return options.value.map(employee => ({
        value: employee.employee_name,
        label: employee.employee_name,
    }));
});

</script>