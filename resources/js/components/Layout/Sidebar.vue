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
            class="mb-1 rounded-md group hover:bg-slate-700/90 transition-colors duration-150"
        >
            <router-link
                to="/dashboard"
                class="p-3 flex items-center transition-all duration-200"
                :class="[isSidebarExpanded ? 'w-full delay-500' : 'w-12']"
            >
                <i
                    class="fas fa-chart-line fa-lg text-gray-400 group-hover:text-gray-100 transition-colors duration-200"
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
            class="mt-3 rounded-md group hover:bg-slate-700/90 transition-colors duration-150"
        >
            <router-link
                :to="{ name: 'product' }"
                class="p-3 flex items-center transition-all duration-200"
                :class="[isSidebarExpanded ? 'w-full delay-500' : 'w-12']"
            >
                <i
                    class="fas fa-box-open fa-lg text-gray-400 group-hover:text-gray-100 transition-colors duration-200"
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
                    >
                        Produk
                    </span>
                </div>
            </router-link>
        </div>
        <!-- Dropdown Menu -->
        <div class="rounded-md group">
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
                            class="text-gray-400 group-hover:text-gray-100 ml-4 transition-color duration-200"
                        >
                            Kategori
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
                            >Tambah Kategori</span
                        >
                    </router-link>
                </li>
            </ul>
        </div>
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
