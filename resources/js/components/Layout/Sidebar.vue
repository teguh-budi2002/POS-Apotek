<template>
    <div
        :class="[
            'bg-gradient-to-b from-slate-900 to-slate-900/90 transition-all duration-200 h-screen fixed p-2',
            isSidebarExpanded
                ? 'sidebar-expanded delay-200'
                : 'w-[68px] delay-300',
        ]"
        class=""
        @mouseover="expandSidebar"
        @mouseout="collapseSidebar"
    >
        <div class="logo flex justify-center mt-1 mb-4">
            <p
                class="text-gray-300 font-semibold text-xl transition-all duration-300 whitespace-nowrap"
                :class="[
                    isSidebarExpanded
                        ? 'translate-x-0 opacity-100 delay-500'
                        : '-translate-x-full opacity-0 delay-200',
                ]"
            >
                APOTEK DASHBOARD
            </p>
        </div>
        <div
            class="mb-1 rounded-md group hover:bg-slate-700/90 transition-colors duration-150 overflow-x-hidden"
             :class="[$route.name === 'dashboard' ? 'bg-slate-700/90' : '']"
        >
            <router-link
                to="/dashboard"
                class="p-3 flex items-center transition-all duration-200"
                :class="[isSidebarExpanded ? 'w-full delay-500' : 'w-12']"
            >
                <i
                    class="fas fa-chart-line fa-lg group-hover:text-blue-300 transition-colors duration-200"
                    :class="[$route.name === 'dashboard' ? 'text-blue-300' : 'text-blue-400']"
                ></i>
                <div
                    :class="[
                        isSidebarExpanded
                            ? 'opacity-100 delay-500 transition-all duration-100'
                            : 'opacity-0 transition-all duration-100',
                    ]"
                >
                    <span
                        class="text-gray-400 group-hover:text-gray-100 ml-4 transition-color duration-200"
                        :class="[$route.name === 'dashboard' ? 'text-gray-300' : 'text-gray-400']"
                    >
                        Dashboard
                    </span>
                </div>
            </router-link>
        </div>
        <p
            class="text-sm text-gray-500 font-bold transition-all duration-300 ml-2"
            :class="[
                isSidebarExpanded
                    ? 'opacity-100 delay-300'
                    : 'opacity-0 delay-0',
            ]"
        >
            MENU
        </p>
        <!-- Single Menu -->
        <div
            class="mt-3 rounded-md group hover:bg-slate-700/90 transition-colors duration-150 overflow-x-hidden"
            :class="[$route.name === 'apotek' ? 'bg-slate-700/90' : '']"
        >
            <router-link
                :to="{ name: 'apotek' }"
                class="p-3 flex items-center transition-all duration-200"
                :class="[isSidebarExpanded ? 'w-full delay-500' : 'w-12']"
            >
                <i
                    class="fas fa-hospital fa-lg group-hover:text-sky-300 transition-colors duration-200"
                    :class="[$route.name === 'apotek' ? 'text-sky-300' : 'text-sky-400']"
                ></i>
                <div
                    :class="[
                        isSidebarExpanded
                            ? 'opacity-100 delay-500 transition-all duration-100'
                            : 'opacity-0 transition-all duration-100',
                    ]"
                >
                    <span
                        class="group-hover:text-gray-100 ml-3 transition-color duration-200"
                        :class="[$route.name === 'apotek' ? 'text-gray-300' : 'text-gray-400']"
                    >
                        Apotek
                    </span>
                </div>
            </router-link>
        </div>
        <div
            class="mt-3 rounded-md group hover:bg-slate-700/90 transition-colors duration-150 overflow-x-hidden"
            :class="[$route.name === 'product' ? 'bg-slate-700/90' : '']"
        >
            <router-link
                :to="{ name: 'product' }"
                class="p-3 flex items-center transition-all duration-200"
                :class="[isSidebarExpanded ? 'w-full delay-500' : 'w-12']"
            >
                <i
                    class="fas fa-box-open fa-lg group-hover:text-yellow-300 transition-colors duration-200"
                    :class="[$route.name === 'product' ? 'text-yellow-300' : 'text-yellow-400']"
                ></i>
                <div
                    :class="[
                        isSidebarExpanded
                            ? 'opacity-100 delay-500 transition-all duration-100'
                            : 'opacity-0 transition-all duration-100',
                    ]"
                >
                    <span
                        class="group-hover:text-gray-100 ml-3 transition-color duration-200"
                        :class="[$route.name === 'product' ? 'text-gray-300' : 'text-gray-400']"
                    >
                        Produk
                    </span>
                </div>
            </router-link>
        </div>
        <div
            class="mt-1 rounded-md group hover:bg-slate-700/90 transition-colors duration-150 overflow-x-hidden"
            :class="[$route.name === 'category' ? 'bg-slate-700/90' : '']"
        >
            <router-link
                :to="{ name: 'category' }"
                class="p-3 flex items-center transition-all duration-200"
                :class="[isSidebarExpanded ? 'w-full delay-500' : 'w-12']"
            >
                <i
                    class="fas fa-layer-group fa-lg group-hover:text-teal-300 transition-colors duration-200"
                    :class="[$route.name === 'category' ? 'text-teal-300' : 'text-teal-400']"
                ></i>
                <div
                    :class="[
                        isSidebarExpanded
                            ? 'opacity-100 delay-500 transition-all duration-100'
                            : 'opacity-0 transition-all duration-100',
                    ]"
                >
                    <span
                        class="text-gray-400 group-hover:text-gray-100 ml-3 transition-color duration-200"
                        :class="[$route.name === 'category' ? 'text-gray-300' : 'text-gray-400']"
                    >
                        Kategori
                    </span>
                </div>
            </router-link>
        </div>
        <div
            class="mt-1 rounded-md group hover:bg-slate-700/90 transition-colors duration-150 overflow-x-hidden"
            :class="[$route.name === 'unit' ? 'bg-slate-700/90' : '']"
        >
            <router-link
                :to="{ name: 'unit' }"
                class="p-3 flex items-center transition-all duration-200"
                :class="[isSidebarExpanded ? 'w-full delay-500' : 'w-12']"
            >
                <i
                    class="fas fa-boxes-stacked fa-lg group-hover:text-lime-300 transition-colors duration-200"
                    :class="[$route.name === 'unit' ? 'text-lime-300' : 'text-lime-400']"
                ></i>
                <div
                    :class="[
                        isSidebarExpanded
                            ? 'opacity-100 delay-500 transition-all duration-100'
                            : 'opacity-0 transition-all duration-100',
                    ]"
                >
                    <span
                        class="text-gray-400 group-hover:text-gray-100 ml-3 transition-color duration-200"
                        :class="[$route.name === 'unit' ? 'text-gray-300' : 'text-gray-400']"
                    >
                        Satuan
                    </span>
                </div>
            </router-link>
        </div>
        <!-- Dropdown Menu -->
        <!-- <div class="rounded-md group">
            <div
                class="p-3 flex justify-between items-center transition-all duration-200 cursor-pointer"
                :class="[isSidebarExpanded ? 'w-full delay-500' : 'w-12']"
                @click="isDropdownOpen = !isDropdownOpen"
            >
                <div class="flex items-center">
                    <i
                        class="fas fa-layer-group fa-lg text-gray-400 group-hover:text-gray-100 transition-colors duration-200"
                    ></i>
                    <div
                        :class="[
                            isSidebarExpanded
                                ? 'opacity-100 delay-500 transition-all duration-100'
                                : 'opacity-0 delay-0 transition-all duration-100',
                        ]"
                    >
                        <span
                            class="text-gray-400 group-hover:text-gray-100 ml-3 transition-color duration-200"
                        >
                            Dropdown Menu
                        </span>
                    </div>
                </div>
                <div
                    :class="[
                        isSidebarExpanded
                            ? 'opacity-100 delay-500 transition-all duration-100'
                            : 'opacity-0 delay-400 transition-all duration-100',
                    ]"
                >
                    <i
                        class="fas fa-caret-down fa-lg text-gray-300 group-hover:text-gray-100 transition-all duration-200"
                        :class="{
                            'rotate-180': isDropdownOpen,
                            'rotate-0': !isDropdownOpen,
                        }"
                    ></i>
                </div>
            </div>
            <ul
                class="ml-12 transition-dropdown overflow-hidden"
                :class="[
                    isSidebarExpanded && isDropdownOpen
                        ? 'opacity-100 max-h-96 visible'
                        : 'opacity-0 max-h-0 invisible',
                ]"
            >
                <li>
                    <router-link
                        to=""
                        class="hover:bg-slate-700/90 group transition-colors duration-150 p-2 w-full block"
                    >
                        <span
                            class="text-sm whitespace-nowrap text-gray-300 group-hover:text-gray-100"
                            >Testing</span
                        >
                    </router-link>
                </li>
            </ul>
        </div> -->
    </div>
</template>

<script setup>
import { defineProps, defineEmits, ref } from "vue";

const props = defineProps({
    isSidebarExpanded: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(["update:isSidebarExpanded"]);
const isDropdownOpen = ref(false);

const expandSidebar = () => {
    emit("update:isSidebarExpanded", true);
};

const collapseSidebar = () => {
    emit("update:isSidebarExpanded", false);
};
</script>

<style scoped>
.sidebar-expanded {
    width: 18rem;
}

.transition-dropdown {
    transition: opacity 0.3s ease-in-out, max-height 0.3s ease-in-out,
        visibility 0.3s ease-in-out;
}
</style>
