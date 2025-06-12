<template>
    <AuthenticatedLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-10">
            <div class="mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Setup Users</h1>
                    <p class="mt-2 text-sm text-gray-600">User Information & Credential Management</p>
                </div>
                <div class="mt-5">
                    <a-card>
                        <div class="flex justify-end">
                            <input type="text" v-model="search"
                                class="w-[300px] border border-gray-300 rounded-md mb-2 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Search User" />
                        </div>
                        <a-table :data-source="filterDisplay" :columns="props.columns" :pagination="false" size="small">
                            <template #bodyCell="{ record, column }">
                                <template v-if="column.dataIndex === 'action'">
                                    <div class="flex gap-2">
                                        <button @click="updateButton(record)" title="Update"
                                            class="flex items-center gap-2 px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
                                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd"
                                                    d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z"
                                                    clip-rule="evenodd" />
                                                <path fill-rule="evenodd"
                                                    d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button @click="deleteButton(record)" title="Delete"
                                            class="flex items-center gap-2 px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
                                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd"
                                                    d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button v-if="user.auth.user.usertype == '1'" title="View"
                                            @click="viewButton(record)"
                                            class="flex items-center gap-2 px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
                                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd"
                                                    d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </template>
                        </a-table>
                        <Pagination :datarecords="props.users" class="mt-5" />
                    </a-card>
                </div>
            </div>
        </div>
        <!-- update modal  -->
        <a-modal v-model:open="updateModal" width="50%" title="Update User Credentials">
            <div class="space-y-4 mt-10">
                <!-- Username -->
                <div class="w-full max-w-md mx-auto">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input v-model="updateForm.username" type="text" :class="[
                        'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                        errors.username
                            ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                            : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
                    ]">
                    <p class="text-red-600 text-sm mt-1">{{ errors.username }}</p>
                </div>

                <!-- Name Fields Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Firstname</label>
                        <input v-model="updateForm.firstname" type="text" :class="[
                            'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                            errors.firstname
                                ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
                        ]">
                        <p class="text-red-600 text-sm mt-1">{{ errors.firstname }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Middlename</label>
                        <input v-model="updateForm.middlename" type="text" :class="[
                            'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                            errors.middlename
                                ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
                        ]">
                        <p class="text-red-600 text-sm mt-1">{{ errors.middlename }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lastname</label>
                        <input v-model="updateForm.lastname" type="text" :class="[
                            'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                            errors.lastname
                                ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
                        ]">
                        <p class="text-red-600 text-sm mt-1">{{ errors.lastname }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name Extension</label>
                        <input v-model="updateForm.nameExtention" type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Dropdowns Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Business Unit</label>
                        <select v-model="updateForm.businessUnit" :class="[
                            'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                            errors.businessUnit
                                ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
                        ]">
                            <option v-for="item in props.businessUnit" :key="item.id" :value="item.id">{{ item.name }}
                            </option>
                        </select>
                        <p class="text-red-600 text-sm mt-1">{{ errors.businessUnit }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User Type</label>
                        <select v-model="updateForm.usertype" :class="[
                            'w-full px-4 py-2 border rounded-lg focus:ring-2 transition',
                            errors.usertype
                                ? 'border-red-500 focus:ring-red-300 focus:border-red-500'
                                : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
                        ]">
                            <option v-for="item in filterUsertype" :key="item.id" :value="item.id">{{ item.name }}
                            </option>
                        </select>
                        <p class="text-red-600 text-sm mt-1">{{ errors.usertype }}</p>
                    </div>
                </div>
                <div class="flex justify-center mt-5">
                    <button @click="openSupplierModal"
                        class="bg-green-700 hover:bg-green-800 text-white px-4 py-3 rounded-md flex justify-between gap-2">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z"
                                clip-rule="evenodd" />
                            <path fill-rule="evenodd"
                                d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z"
                                clip-rule="evenodd" />
                        </svg>
                        Update Specified Supplier</button>
                </div>
                <p class="text-red-600 text-center ">{{ errors.specifiedSupplier }}</p>
                <SupplierAssignmentSetup v-model:open="showSupplierModal"
                    :existing-supplier="updateForm.specifiedSupplier"
                    @update:selected-suppliers="handleSelectedSuppliers" />
            </div>

            <!-- Modal Footer -->
            <template #footer>
                <div class="flex justify-end space-x-3">
                    <button @click="updateModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button @click="submitUpdate"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update
                    </button>
                </div>
            </template>
        </a-modal>
        <!-- view user modal  -->
        <a-modal v-model:open="viewUserModal" :footer="false" width="800px">
            <div class="p-6">
                <!-- Main Profile Section -->
                <div class="flex flex-col md:flex-row gap-8 items-start">
                    <!-- Profile Image Column -->
                    <div class="w-full md:w-auto flex flex-col items-center">
                        <div v-if="userData" class="text-center">
                            <img :src="userData.employee_photo ? 'http://172.16.161.34:8080/hrms' + userData.employee_photo : '/storage/images/noUser.jpg'"
                                alt="User Image"
                                class="w-60 h-60 rounded-full object-cover border-4 border-white shadow-lg mb-4">
                            <h2 class="text-xl font-semibold text-gray-800">{{ userData.employee_name }}
                            </h2>
                            <p class="text-gray-500 text-sm">{{ userData.employee_position }}</p>
                        </div>
                        <div v-else class="animate-pulse">
                            <div class="w-32 h-32 rounded-full bg-gray-200 mb-4"></div>
                            <div class="h-4 bg-gray-200 rounded w-40 mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-32"></div>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                        <!-- Employee ID Card -->
                        <div
                            class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm hover:shadow transition-shadow">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-blue-50 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID
                                    </p>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ userData?.employee_id }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Department Card -->
                        <div
                            class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm hover:shadow transition-shadow">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-green-50 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Department</p>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ userData?.employee_dept }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Business Unit Card -->
                        <div
                            class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm hover:shadow transition-shadow">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-purple-50 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Business Unit
                                    </p>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ userData?.employee_bunit }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Type Card -->
                        <div
                            class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm hover:shadow transition-shadow">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-orange-50 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                                            clip-rule="evenodd" />
                                        <path
                                            d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Type
                                    </p>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ userData?.employee_type }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- employee company  -->
                        <div
                            class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm hover:shadow transition-shadow">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-orange-50 rounded-full">
                                    <svg class="w-6 h-6 text-blue-800 dark:text-blue" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 12c.263 0 .524-.06.767-.175a2 2 0 0 0 .65-.491c.186-.21.333-.46.433-.734.1-.274.15-.568.15-.864a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 12 9.736a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 16 9.736c0 .295.052.588.152.861s.248.521.434.73a2 2 0 0 0 .649.488 1.809 1.809 0 0 0 1.53 0 2.03 2.03 0 0 0 .65-.488c.185-.209.332-.457.433-.73.1-.273.152-.566.152-.861 0-.974-1.108-3.85-1.618-5.121A.983.983 0 0 0 17.466 4H6.456a.986.986 0 0 0-.93.645C5.045 5.962 4 8.905 4 9.736c.023.59.241 1.148.611 1.567.37.418.865.667 1.389.697Zm0 0c.328 0 .651-.091.94-.266A2.1 2.1 0 0 0 7.66 11h.681a2.1 2.1 0 0 0 .718.734c.29.175.613.266.942.266.328 0 .651-.091.94-.266.29-.174.537-.427.719-.734h.681a2.1 2.1 0 0 0 .719.734c.289.175.612.266.94.266.329 0 .652-.091.942-.266.29-.174.536-.427.718-.734h.681c.183.307.43.56.719.734.29.174.613.266.941.266a1.819 1.819 0 0 0 1.06-.351M6 12a1.766 1.766 0 0 1-1.163-.476M5 12v7a1 1 0 0 0 1 1h2v-5h3v5h7a1 1 0 0 0 1-1v-7m-5 3v2h2v-2h-2Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Employee
                                        Company
                                    </p>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ userData?.employee_company }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- employee section  -->
                        <div
                            class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm hover:shadow transition-shadow">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-orange-50 rounded-full">
                                    <svg class="w-6 h-6 text-red-800 dark:text-red" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M12 6a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Zm-1.5 8a4 4 0 0 0-4 4 2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 4 4 0 0 0-4-4h-3Zm6.82-3.096a5.51 5.51 0 0 0-2.797-6.293 3.5 3.5 0 1 1 2.796 6.292ZM19.5 18h.5a2 2 0 0 0 2-2 4 4 0 0 0-4-4h-1.1a5.503 5.503 0 0 1-.471.762A5.998 5.998 0 0 1 19.5 18ZM4 7.5a3.5 3.5 0 0 1 5.477-2.889 5.5 5.5 0 0 0-2.796 6.293A3.501 3.501 0 0 1 4 7.5ZM7.1 12H6a4 4 0 0 0-4 4 2 2 0 0 0 2 2h.5a5.998 5.998 0 0 1 3.071-5.238A5.505 5.505 0 0 1 7.1 12Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Employee
                                        Section
                                    </p>
                                    <p class="text-lg font-semibold text-gray-800 mt-1">{{ userData?.employee_section }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a-modal>
        <!-- {{ users }} -->
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { createVNode } from 'vue';
import { Modal, notification } from 'ant-design-vue'
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import SupplierAssignmentSetup from './Supplier-Assignment-Setup.vue';


const user = usePage().props as unknown as PageProps;
interface PageProps {
    auth: {
        user: Users;
    }
};
interface Users {
    usertype: number | string
}

const filterUsertype = computed(() => {
    if (user.auth.user.usertype !== 1) {
        return props.usertype.filter(item => item.id !== 1);
    }
    return props.usertype;
});

const page = usePage();
const errors = computed(() => page.props.errors)
interface User {
    id?: number;
    username?: string;
    firstname?: string;
    lastname?: string;
    middlename?: string;
    name_extention?: string;
    usertype?: number;
    bu?: number;
    name?: string;
    selected_supplier?: any;
};
interface Usertype {
    id: number;
    name: string;
};
interface Businessunit {
    id: number;
    name: string;
};

const props = defineProps<{
    users: {
        data: User[]
    }
    columns;
    search: string;
    usertype: Usertype[];
    businessUnit: Businessunit[];
}>();

const filterDisplay = computed(() => {
    if (user.auth.user.usertype !== 1) {
        return props.users.data.filter(item => item.usertype !== 1);
    }
    return props.users.data;
});

const updateForm = useForm({
    username: '',
    firstname: '',
    middlename: '',
    lastname: '',
    nameExtention: '',
    businessUnit: '',
    usertype: '',
    id: '',
    specifiedSupplier: []
});

const updateModal = ref<boolean>(false)
const updateButton = (user: User) => {
    updateModal.value = true;
    updateForm.username = user.username || '';
    updateForm.firstname = user.firstname || '';
    updateForm.middlename = user.middlename || '';
    updateForm.lastname = user.lastname || '';
    updateForm.nameExtention = user.name_extention || '';
    updateForm.businessUnit = user.bu ? String(user.bu) : '';
    updateForm.usertype = user.usertype ? String(user.usertype) : '';
    updateForm.id = user.id ? String(user.id) : '';
    updateForm.specifiedSupplier = user.selected_supplier || []

};
const submitUpdate = () => {
    router.post(route('admin.updateUserDetails'), {
        username: updateForm.username,
        firstname: updateForm.firstname,
        middlename: updateForm.middlename,
        lastname: updateForm.lastname,
        nameExtention: updateForm.nameExtention,
        businessUnit: updateForm.businessUnit,
        usertype: updateForm.usertype,
        id: updateForm.id,
        specifiedSupplier: updateForm.specifiedSupplier
    }, {
        onSuccess: (page: any) => {
            if (page.props.flash.success) {
                notification.success({
                    message: 'Success',
                    description: page.props.flash.success
                });
                updateForm.reset();
                updateModal.value = false;
            } else if (page.props.flash.error) {
                notification.error({
                    message: 'Failed',
                    description: page.props.flash.error
                });
            };
        }
    });
};
const deleteButton = (record) => {
    Modal.confirm({
        title: 'Confirmation',
        icon: createVNode(ExclamationCircleOutlined),
        content: `Are you sure you want to delete ${record.firstname} ${record.lastname} account?`,
        onOk() {
            router.post(route('admin.deleteUserAccount'), {
                id: record.id
            });
            return new Promise((resolve, reject) => {
                setTimeout(Math.random() > 0.5 ? resolve : reject, 1000);
            }).catch(() => console.log('Oops errors!'));

        },
        onCancel() { },
    });
};
const viewUserModal = ref<boolean>(false);
const userData = ref<any>(null);
const selectedUserId = ref<number | null>(null);

const viewButton = async (record) => {
    try {
        const value = `${record.lastname}, ${record.firstname}`;
        selectedUserId.value = record.id;
        await getUserDetails(value);
        viewUserModal.value = true;
    } catch (error) {
        notification.warning({
            message: 'Unable to fetch user details. This user was not found in HRMS'
        });
    };
};

const getUserDetails = async (query: string) => {
    const response = await axios.get('http://172.16.161.34/api/gc/filter/employee/name', {
        params: {
            q: query
        }
    });
    console.log(response.data.data.employee[0]);
    userData.value = response.data.data.employee[0];
};
const search = ref<string>(props.search);
const searchFunction = () => {
    router.get(route('admin.setupUser'), {
        search: search.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
watch(() => search.value, () => {
    searchFunction();
});

const showSupplierModal = ref<boolean>(false);

const openSupplierModal = () => {
    showSupplierModal.value = true;
};

const handleSelectedSuppliers = (suppliers: number[]) => {
    updateForm.specifiedSupplier = suppliers;
};

</script>