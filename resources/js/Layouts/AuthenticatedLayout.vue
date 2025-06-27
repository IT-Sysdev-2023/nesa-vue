<template>
    <!-- Navigation  -->
    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a :href="route('dashboard')" class="flex items-center space-x-2">
                        <img class="w-10 h-10" src="/storage/images/logo.png" alt="Nesa Logo">
                        <span class="text-xl font-bold text-gray-900">Nesa</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <div class="hidden md:flex space-x-8">

                        <button
                            class="flex items-center gap-1 text-gray-900 hover:text-blue-600 px-1 py-2 text-sm font-medium transition-colors duration-200">
                            <HomeOutlined />

                            <Link :href="route('dashboard')"><span>Home</span></Link>
                        </button>
                        <div class="relative" v-if="[1, 5].includes(page.auth.user.usertype)">
                            <button @click="toggleDropdown"
                                class="text-gray-900 gap-1 hover:text-blue-600 px-1 py-2 text-sm font-medium flex items-center space-x-1 transition-colors duration-200">
                                <NotificationOutlined />
                                <span>Nesa</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 transform transition-transform duration-200"
                                    :class="{ 'rotate-180': showDropdown }">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div v-show="showDropdown"
                                class="absolute left-0 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none transition-all duration-200 transform scale-100 z-50">
                                <div class="py-1">
                                    <Link :href="route('nesa.get.list')"
                                        class="flex items-center gap-1 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    Near Expiry Stock Advise
                                    </Link>
                                    <Link :href="route('nesa.get.consolidated')"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    Consolidated List
                                    </Link>
                                    <Link :href="route('nesa.get.history')"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    Summary Of SWAF
                                    </Link>
                                    <Link :href="route('nesa.get.approved.nesa')"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    Approved Nesa
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div class="relative" v-if="[1, 6].includes(page.auth.user.usertype)">
                            <button @click="toggleDropdownApprove"
                                class="text-gray-900 gap-1 hover:text-blue-600 px-1 py-2 text-sm font-medium flex items-center space-x-1 transition-colors duration-200">
                                <NotificationOutlined />
                                <span>Approval</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 transform transition-transform duration-200"
                                    :class="{ 'rotate-180': showDropdownApprove }">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div v-show="showDropdownApprove"
                                class="absolute left-0 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none transition-all duration-200 transform scale-100 z-50">
                                <div class="py-1">
                                    <Link :href="route('nesa.get.pending.for.approval')"
                                        class="flex items-center gap-1 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    Pending For Approval
                                    </Link>
                                    <Link :href="route('nesa.get.approved.nesa')"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    Approved Nesa
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div class="relative" v-if="[1, '2'].includes(page.auth.user.usertype)">
                            <button @click="toggleMasterfileDropdown"
                                class="text-gray-900 gap-1 hover:text-blue-600 px-1 py-2 text-sm font-medium flex items-center space-x-1 transition-colors duration-200">
                                <DeploymentUnitOutlined />
                                <span>Masterfile</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 transform transition-transform duration-200"
                                    :class="{ 'rotate-180': showMasterfileDropdown }">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div v-show="showMasterfileDropdown"
                                class="absolute left-0 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none transition-all duration-200 transform scale-100 z-50">
                                <div class="py-1">
                                    <Link :href="route('admin.masterfile')"
                                        class="flex items-center gap-1 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    Products
                                    </Link>
                                    <Link :href="route('admin.supplier.list')"
                                        class="flex items-center gap-1 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    Supplier List
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div class="relative" v-if="[1, 2].includes(page.auth.user.usertype)">
                            <button @click="toggleSetupDropdown"
                                class="text-gray-900 gap-1 hover:text-blue-600 px-1 py-2 text-sm font-medium flex items-center space-x-1 transition-colors duration-200">
                                <SettingOutlined />
                                <span>Setup</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 transform transition-transform duration-200"
                                    :class="{ 'rotate-180': showSetupDropdown }">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div v-show="showSetupDropdown"
                                class="absolute left-0 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none transition-all duration-200 transform scale-100 z-50">
                                <div class="py-1">
                                    <Link :href="route('admin.addUser')"
                                        class="flex items-center gap-1 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    Add User
                                    </Link>
                                    <Link :href="route('admin.setupUser')"
                                        class="flex items-center gap-1 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    Setup Users
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <button
                            class="flex items-center gap-1 text-gray-900 hover:text-blue-600 px-1 py-2 text-sm font-medium transition-colors duration-200">
                            <Link :href="route('admin.about')">
                            <div class="flex items-center space-x-1">
                                <QuestionCircleOutlined />
                                <span>About</span>
                            </div>
                            </Link>
                        </button>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <div class="relative group mr-20">
                        <button @click="showProfile = !showProfile"
                            class="text-gray-900 hover:text-blue-600 px-1 py-2 text-sm font-medium flex items-center space-x-1 transition-colors duration-200">
                            <img class="w-10 h-10 rounded-full object-cover"
                                :src="userDetails && userDetails.employee_photo ? 'http://172.16.161.34:8080/hrms' + userDetails.employee_photo : '/storage/images/noUser.jpg'"
                                alt="">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-4 h-4 transform transition-transform duration-200"
                                :class="{ 'rotate-180': showProfile }">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div v-show="showProfile"
                            class="absolute left-0 mt-2 w-80 origin-top-left rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none transition-all duration-200 transform z-50 bg-white">
                            <div class="py-1">
                                <ProfileView />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="toggleMobileMenu" type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out"
                        :aria-expanded="isMobileMenuOpen" aria-controls="mobile-menu">
                        <span class="sr-only">Open main menu</span>
                        <svg :class="{ 'hidden': isMobileMenuOpen, 'block': !isMobileMenuOpen }" class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg :class="{ 'hidden': !isMobileMenuOpen, 'block': isMobileMenuOpen }" class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div v-show="isMobileMenuOpen" class="md:hidden origin-top" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="#"
                    class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-blue-600 hover:bg-gray-50">Home</a>

                <!-- Mobile dropdown -->
                <div class="px-3 py-2">
                    <button @click="toggleMobileProductsMenu"
                        class="w-full flex justify-between items-center text-base font-medium text-gray-900 hover:text-blue-600"
                        :aria-expanded="isMobileProductsMenuOpen">
                        <span>Products</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-5 h-5 transform transition-transform duration-200"
                            :class="{ 'rotate-180': isMobileProductsMenuOpen }">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div v-show="isMobileProductsMenuOpen" class="mt-2 space-y-1 pl-4">
                        <a href="#"
                            class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-blue-600 hover:bg-gray-50">Web
                            Design</a>
                        <a href="#"
                            class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-blue-600 hover:bg-gray-50">Mobile
                            Apps</a>
                        <a href="#"
                            class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-blue-600 hover:bg-gray-50">UI/UX</a>
                        <a href="#"
                            class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-blue-600 hover:bg-gray-50">Development</a>
                    </div>
                </div>
                <a href="#"
                    class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-blue-600 hover:bg-gray-50">About</a>
                <div class="px-3 py-2">
                    <button @click="toggleMobileSetupMenu"
                        class="w-full flex justify-between items-center text-base font-medium text-gray-900 hover:text-blue-600"
                        :aria-expanded="isMobileSetupMenuOpen">
                        <span>Setup</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-5 h-5 transform transition-transform duration-200"
                            :class="{ 'rotate-180': isMobileSetupMenuOpen }">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div v-show="isMobileSetupMenuOpen" class="mt-2 space-y-1 pl-4">
                        <a :href="route('admin.addUser')"
                            class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-blue-600 hover:bg-gray-50">Add
                            User</a>
                    </div>
                    <div v-show="isMobileSetupMenuOpen" class="mt-2 space-y-1 pl-4">
                        <a :href="route('admin.setupUser')"
                            class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-blue-600 hover:bg-gray-50">Setup
                            Users</a>
                    </div>
                </div>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="space-y-1 px-3">
                    <a href="#"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:text-blue-600 hover:bg-gray-50">Log
                        in</a>
                    <a href="#"
                        class="block w-full px-3 py-2 rounded-md text-base font-medium text-center text-white bg-blue-600 hover:bg-blue-700">Sign
                        up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Weather section  -->
    <a-popover trigger="click">
        <template #content>
            <weather-forecast-setup />
        </template>
        <a-float-button type="primary" style="display: flex; justify-content: center; align-items: center;">
            <template #icon>
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M5.99997 17c-.55229 0-1 .4477-1 1s.44771 1 1 1h.01c.55228 0 1-.4477 1-1s-.44772-1-1-1h-.01ZM18 17c-.5523 0-1 .4477-1 1s.4477 1 1 1h.01c.5523 0 1-.4477 1-1s-.4477-1-1-1H18Z" />
                    <path fill="currentColor" fill-rule="evenodd"
                        d="M12 13c.5523 0 1 .4477 1 1v.5858l.4142-.4142c.3905-.3905 1.0237-.3905 1.4142 0 .3905.3905.3905 1.0237 0 1.4142L14.4141 16l.5859.0001c.5523 0 1 .4477 1 1s-.4478 1-1.0001 1L14.4141 18l.4143.4143c.3905.3906.3905 1.0237 0 1.4142-.3906.3906-1.0237.3905-1.4143 0L13 19.4143v.5858c0 .5522-.4477 1-1 1s-1-.4478-1-1v-.5859l-.4143.4142c-.3905.3906-1.02365.3906-1.41417 0-.39053-.3905-.39053-1.0236 0-1.4142L9.58571 18l-.58572.0001c-.55228 0-1.00001-.4477-1.00002-1-.00001-.5523.44769-1 .99998-1L9.58571 16l-.41416-.4141c-.39054-.3905-.39056-1.0237-.00005-1.4142.39052-.3905 1.0237-.3906 1.4142 0l.4143.4142V14c0-.5523.4477-1 1-1Z"
                        clip-rule="evenodd" />
                    <path fill="currentColor"
                        d="M9.21869 3.96216c1.18841-.77809 2.61801-1.10041 4.02531-.90756 1.4073.19285 2.6974.88787 3.6327 1.95696.8431.96375 1.3466 2.17293 1.4406 3.44244.6029.16797 1.1584.48908 1.6088.93946C20.6137 10.0811 21 11.0137 21 11.9862c0 .9449-.3677 1.9573-1.0739 2.6636-.6417.6416-1.4561.9281-2.2516.9899-.1439-.2824-.3312-.539-.5532-.761 0-.7677-.2929-1.5355-.8787-2.1213-.5858-.5858-1.3535-.8787-2.1213-.8787-.5429-.5429-1.2929-.8786-2.1213-.8786-.8285 0-1.5785.3358-2.12139.8787-.76778 0-1.53555.2929-2.12133.8788-.58574.5857-.87861 1.3535-.87862 2.1212-.18576.1858-.34727.3958-.47938.6249-.77455-.2033-1.48895-.6091-2.06499-1.1851C3.47996 13.4642 3 12.3055 3 11.0973c0-1.1581.38455-2.34287 1.27157-3.22989.74279-.74279 1.74607-1.18271 2.75928-1.2962.45424-1.06098 1.21293-1.97073 2.18784-2.60905Z" />
                </svg>
            </template>
        </a-float-button>
    </a-popover>

    <!-- Content  -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mt-5"></div>
        <slot />
    </div>

    <!-- Footer -->
    <div class="mt-10 py-6 border-t border-gray-100">
        <div class="flex flex-col items-center justify-center gap-4 text-center">
            <div class="text-md text-gray-700">
                © {{ dayjs().year() }} Near Expiry Stock Advice (NESA)
            </div>

            <div class="flex flex-col items-center gap-2">
                <p class="text-sm text-gray-600">Powered by</p>
                <div class="flex items-center justify-center gap-4">
                    <div class="p-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-red-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linejoin="round"
                                d="m17 13 3.4641-2V7L17 5l-3.4641 2v4M17 13l-3.4641-2M17 13v4l-7.00001 4M17 13V9m0 4-7.00001 4m3.53591-6L10.5 12.7348M9.99999 21l-3.4641-2.1318M9.99999 21v-4m-3.4641 2v-.1318m0 0V15L10.5 12.7348m-3.96411 6.1334L3.5 17V5m0 0L7 3l3.5 2m-7 0 2.99999 2M10.5 5v7.7348M10.5 5 6.49999 7M17 9l3.5-2M17 9l-3.5-2M9.99999 17l-3.5-2m0 .5V7" />
                        </svg>
                    </div>
                    <div class="p-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-green-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14.5 3 12 7.156 9.857 3H2l10 18L22 3h-7.5ZM4.486 4.5h2.4L12 13.8l5.107-9.3h2.4L12 18.021 4.486 4.5Z" />
                        </svg>
                    </div>
                    <div class="p-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M15.907 11.998 10.332 9.23a.9.9 0 0 1-.16-.037l-.018-.007v6.554c0 .017.008.034.01.051l2.388-2.974 3.355-.82Z" />
                            <path
                                d="m11.463 4.054 5.579 3.323A4.02 4.02 0 0 1 18.525 9c.332.668.47 1.414.398 2.155a3.07 3.07 0 0 1-.745 1.65 3.108 3.108 0 0 1-1.55.951c-.022.007-.045.005-.07.01-.062.03-.126.057-.191.08l-2.72.667-1.992 2.48c-.18.227-.41.409-.67.534.047.034.085.077.137.107a2.05 2.05 0 0 0 1.995.035c.592-.33 2.15-1.201 4.636-2.892l.28-.19c1.328-.895 3.616-2.442 3.967-4.215a9.94 9.94 0 0 0-1.713-4.154 10.027 10.027 0 0 0-3.375-2.989 10.107 10.107 0 0 0-8.802-.418c1.162.287 2.287.704 3.354 1.243Z" />
                            <path
                                d="M5.382 17.082v-6.457a3.7 3.7 0 0 1 .45-1.761 3.733 3.733 0 0 1 1.238-1.34 3.915 3.915 0 0 1 3.433-.245c.176.03.347.084.508.161l5.753 2.856c.082.05.161.105.236.165a2.128 2.128 0 0 0-.953-1.455l-5.51-3.284c-1.74-.857-3.906-1.523-5.244-1.097a9.96 9.96 0 0 0-2.5 3.496 9.895 9.895 0 0 0 .283 8.368 9.973 9.973 0 0 0 2.73 3.322 17.161 17.161 0 0 1-.424-2.729Z" />
                            <path
                                d="m19.102 16.163-.272.183c-2.557 1.74-4.169 2.64-4.698 2.935a4.083 4.083 0 0 1-2 .53 3.946 3.946 0 0 1-1.983-.535 3.788 3.788 0 0 1-1.36-1.361 3.752 3.752 0 0 1-.51-1.85 1.812 1.812 0 0 1-.043-.26V9.143c0-.024.009-.046.01-.07-.056.02-.11.043-.162.07a1.796 1.796 0 0 0-.787 1.516v6.377a10.67 10.67 0 0 0 1.113 4.27 10.11 10.11 0 0 0 8.505-.53 10.022 10.022 0 0 0 3.282-2.858 9.936 9.936 0 0 0 1.75-3.97 19.615 19.615 0 0 1-2.845 2.216Z" />
                        </svg>
                    </div>
                    <div class="p-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 128 128">
                            <defs>
                                <linearGradient id="deviconAntdesign0" x1=".621" x2="1.082" y1="0" y2=".379"
                                    gradientTransform="matrix(94.54297 0 0 127.17188 .418 .406)"
                                    gradientUnits="userSpaceOnUse">
                                    <stop offset="0" stop-color="#4285eb" />
                                    <stop offset="1" stop-color="#2ec7ff" />
                                </linearGradient>
                                <linearGradient id="deviconAntdesign1" x1=".696" x2=".54" y1="0" y2="1.085"
                                    gradientTransform="matrix(94.54297 0 0 127.17188 .418 .406)"
                                    gradientUnits="userSpaceOnUse">
                                    <stop offset="0" stop-color="#29cdff" />
                                    <stop offset=".379" stop-color="#148eff" />
                                    <stop offset="1" stop-color="#0a60ff" />
                                </linearGradient>
                                <linearGradient id="deviconAntdesign2" x1=".697" x2=".167" y1="-.13" y2="1.174"
                                    gradientTransform="matrix(31.49219 0 0 49.5586 96.176 39.402)"
                                    gradientUnits="userSpaceOnUse">
                                    <stop offset="0" stop-color="#fa816e" />
                                    <stop offset=".415" stop-color="#f74a5c" />
                                    <stop offset="1" stop-color="#f51d2c" />
                                </linearGradient>
                                <linearGradient id="deviconAntdesign3" x1=".681" x2=".304" y1="-.357" y2="1.149"
                                    gradientTransform="matrix(30.21094 0 0 30.1836 49.227 49.188)"
                                    gradientUnits="userSpaceOnUse">
                                    <stop offset="0" stop-color="#fa8e7d" />
                                    <stop offset=".513" stop-color="#f74a5c" />
                                    <stop offset="1" stop-color="#f51d2c" />
                                </linearGradient>
                            </defs>
                            <path fill="url(#deviconAntdesign0)"
                                d="M58.617 2.672L2.676 58.566a7.672 7.672 0 0 0 0 10.868l55.941 55.894a7.684 7.684 0 0 0 10.871 0l23.457-23.437a6.891 6.891 0 0 0 0-9.746a6.9 6.9 0 0 0-9.754 0l-17.78 17.765c-.75.75-1.888.75-2.634 0L18.102 65.277c-.747-.75-.747-1.886 0-2.632l44.675-44.637c.746-.746 1.883-.746 2.633 0l17.781 17.765a6.9 6.9 0 0 0 9.754 0a6.891 6.891 0 0 0 0-9.746L69.492 2.594c-3.02-2.938-7.879-2.914-10.875.078Zm0 0" />
                            <path fill="url(#deviconAntdesign1)"
                                d="M58.617 2.672L2.676 58.566a7.672 7.672 0 0 0 0 10.868l55.941 55.894a7.684 7.684 0 0 0 10.871 0l23.457-23.437a6.891 6.891 0 0 0 0-9.746a6.9 6.9 0 0 0-9.754 0l-17.78 17.765c-.75.75-1.888.75-2.634 0L18.102 65.277c-.747-.75-.747-1.886 0-2.632l44.675-44.637c1.864-1.606 4.903-4.86 9.371-5.621c3.317-.567 6.946.68 10.883 3.734c-2.633-2.633-7.144-7.14-13.539-13.527c-3.02-2.938-7.879-2.914-10.875.078Zm0 0" />
                            <path fill="url(#deviconAntdesign2)"
                                d="M98.36 86.945a6.9 6.9 0 0 0 9.753 0l17.301-17.285a7.667 7.667 0 0 0 0-10.863l-17.453-17.379a6.91 6.91 0 0 0-9.762.012a6.884 6.884 0 0 0 0 9.746l11.79 11.777a1.826 1.826 0 0 1 0 2.629l-11.63 11.621a6.88 6.88 0 0 0 0 9.742Zm0 0" />
                            <path fill="url(#deviconAntdesign3)" fill-rule="evenodd"
                                d="M79.438 64.281c0-8.336-6.762-15.094-15.106-15.094c-8.34 0-15.105 6.758-15.105 15.094c0 8.332 6.765 15.09 15.105 15.09c8.344 0 15.106-6.758 15.106-15.09Zm0 0" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { HomeOutlined, NotificationOutlined, DeploymentUnitOutlined, SettingOutlined, QuestionCircleFilled, QuestionOutlined, QuestionCircleOutlined } from '@ant-design/icons-vue';
import { ref } from 'vue';
import { usePage } from "@inertiajs/vue3";
import ProfileView from "../Pages/ProfilePartials/ProfileView.vue"
import { computed } from 'vue';
import { onMounted } from 'vue';
import axios from "axios";
import dayjs from 'dayjs';
import WeatherForecastSetup from './Weather-Forecast-Setup.vue';

const showProfile = ref(false);

const isMobileMenuOpen = ref(false);
const isMobileProductsMenuOpen = ref(false);
const isMobileSetupMenuOpen = ref(false);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const toggleMobileProductsMenu = () => {
    isMobileProductsMenuOpen.value = !isMobileProductsMenuOpen.value;
};
const toggleDropdownApprove = () => {
    showDropdownApprove.value = !showDropdownApprove.value;
};

const toggleMobileSetupMenu = () => {
    isMobileSetupMenuOpen.value = !isMobileSetupMenuOpen.value
}


const showDropdown = ref(false);

const showMasterfileDropdown = ref(false);
const showDropdownApprove = ref(false);

function toggleDropdown() {
    showDropdown.value = !showDropdown.value;
    showMasterfileDropdown.value = false;
    showSetupDropdown.value = false;
}

function toggleMasterfileDropdown() {
    showMasterfileDropdown.value = !showMasterfileDropdown.value;
    showDropdown.value = false;
    showSetupDropdown.value = false;
}

const showSetupDropdown = ref(false);

function toggleSetupDropdown() {
    showSetupDropdown.value = !showSetupDropdown.value;
    showMasterfileDropdown.value = false;
    showDropdown.value = false;

}
const page = usePage().props as unknown as PageProps;

interface User {
    image?: string;
    firstname?: string;
    middlename?: string;
    lastname?: string;
    usertype?: number;
    username?: string;
    id?: number;
}

interface PageProps {
    auth: {
        user: User;
    };
};

const formattedName = computed(() => {
    return `${page.auth.user.lastname}, ${page.auth.user.firstname}`;
});
const value = ref<string>(formattedName.value);

const userDetails = ref<any>({})
const getUserDetails = async () => {
    const response = await axios.get('http://172.16.161.34/api/gc/filter/employee/name', {
        params: {
            q: value.value
        }
    });
    userDetails.value = response.data.data.employee[0];
}
onMounted(() => {
    getUserDetails();
});
</script>
