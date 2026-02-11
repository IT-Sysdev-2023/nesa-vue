<template>
    <div
        :class="[
            'min-h-screen flex transition-colors duration-300 body',
            isDarkMode ? 'dark bg-gray-950' : 'bg-gray-100',
        ]"
    >
        <!-- Mobile sidebar overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 lg:hidden"
            @click="sidebarOpen = false"
        >
            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
        </div>

        <!-- Sidebar -->
        <div
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 shadow-xl transform transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                isDarkMode ? 'bg-gray-800' : 'bg-white',
            ]"
        >
            <div
                :class="[
                    'flex items-center justify-between h-16 px-6 border-b',
                    isDarkMode ? 'border-gray-700' : 'border-gray-200',
                ]"
            >
                <div class="flex items-center space-x-3">
                    <div
                        class="w-16 h-10 bg-gradient-to-r bg-gray-00 rounded-full flex items-center justify-center"
                    >
                      <div
                :class="[
                    'fixed top-0 left-0 w-64 p-4 border-t',
                    isDarkMode
                        ? 'border-gray-700 bg-gray-800'
                        : 'border-gray-200 bg-gray-50',
                ]"
            >
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center"
                    >
                        <span
                            class="text-white font-semibold text-sm rounded-full"
                        >
                            <img
                                :src="
                                    'http://172.16.161.34:8080/hrms' +
                                    userImageData
                                "
                                alt="image"
                                style="border-radius: 100%"
                            />
                        </span>
                    </div>
                    <div class="flex-1 min-w-0"><p
                            :class="[
                                'text-sm first-letter:uppercase font-medium truncate',
                                isDarkMode ? 'text-gray-200' : 'text-gray-900',
                            ]"
                        >
                         {{ page.auth.user.username }}
                        </p>

                        <p
                            :class="[
                                'text-xs truncate',
                                isDarkMode ? 'text-gray-400' : 'text-gray-500',
                            ]"
                        >
                            {{ page?.auth?.usertype }}
                        </p>
                    </div>
                    <button
                        :class="[
                            'p-1.5 rounded-md',
                            isDarkMode
                                ? 'text-gray-400 hover:text-gray-300 hover:bg-gray-700'
                                : 'text-gray-400 hover:text-gray-500 hover:bg-gray-200',
                        ]"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"
                            />
                        </svg>
                    </button>
                </div>
            </div>
                    </div>
                </div>
                <button
                    @click="sidebarOpen = false"
                    :class="[
                        'lg:hidden p-2 rounded-md hover:bg-opacity-10',
                        isDarkMode
                            ? 'text-gray-400 hover:text-gray-300 hover:bg-gray-700'
                            : 'text-gray-400 hover:text-gray-500 hover:bg-gray-100',
                    ]"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <ul class="space-y-2">
                    <li
                        v-for="item in menuItems"
                        :key="item.id"
                        class="border-b rounded-xl"
                    >
                        <!-- Menu item with dropdown -->
                        <div v-if="item.children">
                            <button
                                @click="toggleDropdown(item.id)"
                                :class="[
                                    'menu-item w-full flex items-center rounded-xl gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group relative overflow-hidden',
                                    isActiveParent(item)
                                        ? 'bg-gradient-to-br from-slate-950 via-blue-900 text-white to-slate-600 shadow-md shadow-blue-500/20'
                                        : isDarkMode
                                          ? 'text-gray-300 hover:bg-gray-700/60 hover:text-white'
                                          : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                                ]"
                            >
                                <!-- Active background glow effect -->
                                <div
                                    v-if="isActiveParent(item)"
                                    class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-blue-600/20 animate-pulse"
                                ></div>

                                <!-- Icon -->
                                <span
                                    :class="[
                                        'relative z-10 text-lg transition-transform duration-200',
                                        isActiveParent(item)
                                            ? 'scale-110'
                                            : 'group-hover:scale-105',
                                    ]"
                                >
                                    {{ item.icon }}
                                </span>

                                <!-- Label -->
                                <span class="relative z-10 flex-1 text-left">
                                    {{ item.name }}
                                </span>

                                <!-- Dropdown arrow -->
                                <svg
                                    :class="[
                                        'relative z-10 w-4 h-4 transition-transform duration-200',
                                        openDropdowns[item.id]
                                            ? 'rotate-180'
                                            : '',
                                    ]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>

                            <!-- Dropdown items -->
                            <div
                                v-show="openDropdowns[item.id]"
                                class="mt-1 ml-4 space-y-1"
                            >
                                <button
                                    v-for="child in item.children"
                                    :key="child.id"
                                    @click="handleMenuClick(child)"
                                    :class="[
                                        'w-full flex items-center gap-3 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                                        isActive(child.routeTo)
                                            ? isDarkMode
                                                ? 'bg-gray-700 text-white'
                                                : 'bg-gray-100 text-gray-900'
                                            : isDarkMode
                                              ? 'text-gray-400 hover:bg-gray-700/60 hover:text-white'
                                              : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900',
                                    ]"
                                >
                                    <span class="text-base">{{
                                        child.icon
                                    }}</span>
                                    <span>{{ child.name }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Regular menu item without dropdown -->
                        <button
                            v-else
                            @click="handleMenuClick(item)"
                            :class="[
                                'menu-item w-full flex items-center rounded-xl gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group relative overflow-hidden',
                                isActive(item.routeTo)
                                    ? 'bg-gradient-to-br from-slate-950 via-blue-900 text-white to-slate-600 shadow-md shadow-blue-500/20'
                                    : isDarkMode
                                      ? 'text-gray-300 hover:bg-gray-700/60 hover:text-white'
                                      : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                            ]"
                        >
                            <!-- Active background glow effect -->
                            <div
                                v-if="isActive(item.routeTo)"
                                class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-blue-600/20 animate-pulse"
                            ></div>

                            <!-- Icon -->
                            <span
                                :class="[
                                    'relative z-10 text-lg transition-transform duration-200',
                                    isActive(item.routeTo)
                                        ? 'scale-110'
                                        : 'group-hover:scale-105',
                                ]"
                            >
                                {{ item.icon }}
                            </span>

                            <!-- Label -->
                            <span class="relative z-10 flex-1 text-left">
                                {{ item.name }}
                            </span>

                            <!-- Active indicator (modern dot) -->
                            <div
                                v-if="isActive(item.routeTo)"
                                class="relative z-10 flex items-center gap-2"
                            >
                                <div
                                    class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"
                                ></div>
                            </div>

                            <!-- Hover arrow -->
                            <div
                                v-else
                                class="relative z-10 opacity-0 group-hover:opacity-100 group-hover:translate-x-0 -translate-x-2 transition-all duration-200"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </div>
                        </button>
                    </li>
                </ul>
            </nav>

            <!-- User Profile -->
           
        </div>

        <!-- Main content -->
        <div class="flex-1 lg:ml-0">
            <!-- Top header -->
            <header
                :class="[
                    'shadow-sm border-b transition-colors duration-300',
                    isDarkMode
                        ? 'bg-gray-800 border-gray-700'
                        : 'bg-white border-gray-200',
                ]"
            >
                <div class="flex items-center justify-between h-16 px-6">
                    <p>
                      Welcome Back, {{ page.auth.user.firstname }}, {{ page.auth.user.lastname }}
                    </p>
                    <div class="flex items-center space-x-4">
                        <button
                            @click="sidebarOpen = true"
                            :class="[
                                'lg:hidden p-2 rounded-md',
                                isDarkMode
                                    ? 'text-gray-400 hover:text-gray-300 hover:bg-gray-700'
                                    : 'text-gray-400 hover:text-gray-500 hover:bg-gray-100',
                            ]"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                        </button>
                        <h2
                            :class="[
                                'text-xl font-bold',
                                isDarkMode ? 'text-gray-200' : 'text-gray-800',
                            ]"
                        >
                            <!-- //for another purposes slot -->
                        </h2>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Dark Mode Toggle -->
                        <button
                            @click="toggleDarkMode"
                            :class="[
                                'p-2.5 rounded-xl transition-all duration-300 transform hover:scale-110',
                                isDarkMode
                                    ? 'bg-gray-700 text-yellow-400 hover:bg-gray-600 shadow-lg'
                                    : 'bg-gray-100 text-indigo-600 hover:bg-gray-200 shadow-md',
                            ]"
                            :title="
                                isDarkMode
                                    ? 'Switch to Light Mode'
                                    : 'Switch to Dark Mode'
                            "
                        >
                            <svg
                                v-if="isDarkMode"
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                            <svg
                                v-else
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                                />
                            </svg>
                        </button>

                        <!-- Messages -->
                        <button
                            @click="modalOpen"
                            :class="[
                                'relative p-2 rounded-xl transition-all duration-300 hover:scale-105',
                                isDarkMode
                                    ? 'text-gray-300 hover:bg-gray-700'
                                    : 'text-gray-700 hover:bg-gray-100',
                            ]"
                        >
                            <svg
                                class="w-6 h-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
                                />
                            </svg>
                        </button>
                        <div>
                            <!-- Overlay -->
                            <ModalComponent
                                :modelValue="isChatModal"
                                title="BONESA MESSAGES"
                                @closeModal="modalClose"
                            >
                                <MessageIndex :online-users="getOnlineUsers" />
                            </ModalComponent>
                        </div>

                        <!-- Search -->
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search..."
                                :class="[
                                    'w-64 pl-10 pr-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300',
                                    isDarkMode
                                        ? 'bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400 focus:bg-gray-600'
                                        : 'bg-gray-50 border-gray-300 text-gray-900 placeholder-gray-500 focus:bg-white',
                                ]"
                            />
                            <svg
                                :class="[
                                    'absolute left-3 top-3 w-5 h-5 pointer-events-none',
                                    isDarkMode
                                        ? 'text-gray-400'
                                        : 'text-gray-500',
                                ]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>

                        <!-- Logout -->
                        <button
                            @click="openLogout"
                            :class="[
                                'p-2 rounded-xl relative transition-all duration-300 hover:scale-105',
                                isDarkMode
                                    ? 'text-gray-400 hover:text-red-400 hover:bg-gray-700'
                                    : 'text-gray-500 hover:text-red-500 hover:bg-gray-100',
                            ]"
                        >
                            <img
                                class="h-7 w-7"
                                src="/icons/logouticon.svg"
                                alt=""
                                :style="
                                    isDarkMode ? 'filter: brightness(0.8)' : ''
                                "
                            />
                        </button>
                    </div>
                </div>
            </header>

            <a-float-button>
                <template #icon> </template>
            </a-float-button>

            <!-- Page content -->
            <main class="flex-1">
                <div class="max-w-8xl mx-auto">
                    <div
                        :class="[
                            'bgsvg transition-colors duration-300',
                            isDarkMode ? 'dark-mode' : '',
                        ]"
                    >
                        <div class="slot-wrapper">
                            <slot />
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import FlappyModal from "@/Components/DashboardComponents/FlappyModal.vue";
import MessageIndex from "@/Pages/Message/MessageIndex.vue";
import { router, usePage } from "@inertiajs/vue3";
import { template } from "lodash";
import Swal from "sweetalert2";
import {getMenuItems} from '@/Layouts/nav/nav.js';
import {
    ref,
    reactive,
    onMounted,
    computed,
    onBeforeUnmount,
    watch,
} from "vue";
import { useOnlineUsersStore } from "@/stores/online-store";
import ModalComponent from "@/Pages/Modal/ModalComponent.vue";



const page = usePage().props;

const menuItems = getMenuItems(page);

// Dark mode state
const isDarkMode = ref(false);

// Dropdown state
const openDropdowns = ref({});

// Load dark mode preference from localStorage
onMounted(() => {
    const savedMode = localStorage.getItem("darkMode");
    if (savedMode !== null) {
        isDarkMode.value = savedMode === "true";
    } else {
        // Check system preference
        isDarkMode.value = window.matchMedia(
            "(prefers-color-scheme: dark)",
        ).matches;
    }
    userImage();

    if (page.auth) {
        window.Echo.join("online.users")
            .here((users) => setOnlineUsers(users))
            .joining(async (user) => addOnlineUser(user))
            .leaving(async (user) => removeOnlineUser(user));
    }
});

// Watch for dark mode changes and save to localStorage
watch(isDarkMode, (newValue) => {
    localStorage.setItem("darkMode", newValue.toString());
});

// Toggle dark mode
const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    window.location.reload();
};

// Toggle dropdown
const toggleDropdown = (itemId) => {
    openDropdowns.value[itemId] = !openDropdowns.value[itemId];
};

// Reactive data
const sidebarOpen = ref(false);
const activeItem = ref("");
const searchQuery = ref("");

const getOnlineUsers = computed(() => onlineUsersStore.onlineUsers);

const flappy = ref(true);

// Menu items with children for dropdowns


const openLogout = () => {
    Swal.fire({
        title: "Logout",
        text: "Are you sure you want to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Confirm",
        background: isDarkMode.value ? "#1f2937" : "#fff",
        color: isDarkMode.value ? "#f3f4f6" : "#111827",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route("logout"));
        }
    });
};

const getMessageCount = async () => {
    await window.Echo.private(`message-count.${page.auth.user.id}`).listen(
        ".message-count-event",
        (e) => {
            console.log(e);
        },
    );
};

// Stats data
const stats = reactive([
    {
        title: "Total Revenue",
        value: "$45,231",
        change: "+20.1%",
        color: "text-green-600",
    },
    {
        title: "Orders",
        value: "1,234",
        change: "+15.3%",
        color: "text-blue-600",
    },
    {
        title: "Customers",
        value: "5,678",
        change: "+8.2%",
        color: "text-purple-600",
    },
    {
        title: "Growth Rate",
        value: "12.5%",
        change: "+2.4%",
        color: "text-orange-600",
    },
]);

const onlineUsersStore = useOnlineUsersStore();

const { setOnlineUsers, addOnlineUser, removeOnlineUser } = onlineUsersStore;

// Methods
const handleMenuClick = (item) => {
    activeItem.value = item.routeTo;
    sidebarOpen.value = false;
    router.get(route(item.routeTo));
};

const isActive = (routeName) => {
    return route().current(routeName);
};

const isActiveParent = (item) => {
    if (item.routeTo && isActive(item.routeTo)) {
        return true;
    }
    if (item.children) {
        return item.children.some((child) => isActive(child.routeTo));
    }
    return false;
};

const userNameFormat = computed(() => {
    return `${page.auth.user.lastname.toLowerCase()}, ${page.auth.user.firstname.toLowerCase()}`;
});
const value = ref(userNameFormat.value);

const userImageData = ref("");

const userImage = async () => {
    const response = await axios.get(
        "http://172.16.161.34/api/gc/filter/employee/name",
        {
            params: {
                q: value.value,
            },
        },
    );
    userImageData.value = response.data.data.employee[0].employee_photo;
};

onBeforeUnmount(() => {
    if (page.auth) {
        window.Echo.leaveAllChannels();
    }
});

const openMessModal = ref(false);

const openMessageModal = () => {
    openMessModal.value = true;
};
const isChatModal = ref(false);

const modalOpen = () => {
    isChatModal.value = true;
};

const modalClose = () => {
    isChatModal.value = false;
};
</script>

<style scoped>
.slot-wrapper {
    height: 90vh;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.slot-wrapper::-webkit-scrollbar {
    width: 8px;
}

.slot-wrapper::-webkit-scrollbar-track {
    background: transparent;
}

.slot-wrapper::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 20px;
}

.slot-wrapper::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.7);
}

.bgsvg {
    position: relative;
    /* background-image: url("/images/bgblue.svg"); */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 12px;
    padding: 24px;
    background-color: rgba(255, 255, 255, 0.95);
    background-blend-mode: overlay;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.bgsvg.dark-mode {
    background-color: rgba(17, 24, 39, 0.95);
    background-blend-mode: multiply;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
}

.custom-pop .ant-popover-inner {
    background-color: #111824;
}

/* Smooth transitions for theme switching */
* {
    transition-property: background-color, border-color, color, box-shadow;
    transition-duration: 300ms;
    transition-timing-function: ease-in-out;
}

/* Animation for notification badge */
@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Menu item hover effects */
.menu-item {
    position: relative;
    overflow: hidden;
}

.menu-item::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.1),
        transparent
    );
    transition: left 0.5s;
}

.menu-item:hover::before {
    left: 100%;
}
</style>
