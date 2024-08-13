<template>
    <div class="w-full min min-h-screen h-full overflow-x-hidden">
        <div class="custom-bg-clip"></div>
        <div class="flex">
            <!-- Sidebar -->
            <Sidebar
                :isSidebarExpanded="isSidebarExpanded"
                @update:isSidebarExpanded="updateSidebarState"
            />

            <!-- Main Content -->
            <div
                :class="[
                    'flex flex-col transition-all duration-200 w-full',
                    isSidebarExpanded
                        ? 'content-expanded delay-200'
                        : 'content-collapsed delay-300',
                ]"
            >
                <!-- Navbar -->
                <div class="flex justify-center">
                    <div class="w-11/12 mt-4">
                        <div
                            class="w-full flex justify-between items-center p-5 bg-white shadow-md rounded-md"
                        >
                            <div class="user-info flex items-center space-x-3">
                                <div class="flex items-center space-x-3">
                                    <img
                                        src="https://picsum.photos/300/300"
                                        class="w-9 h-9 rounded-full"
                                        alt=""
                                    />
                                    <p class="text-slate-600 font-semibold">
                                        Teguh Budi Laksono
                                    </p>
                                    <div
                                        class="flex items-center space-x-1 animate-pulse"
                                    >
                                        <div
                                            class="w-2 h-2 rounded-full bg-green-500"
                                        ></div>
                                        <p class="text-green-500 text-xs">
                                            Online
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="nav-item text-slate-600 flex items-center space-x-3"
                            >
                                <ButtonLogout />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Content -->
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "../components/Layout/Sidebar.vue";
import ButtonLogout from "../components/ButtonLogout.vue";
import { ref } from "vue";

export default {
    components: { Sidebar, ButtonLogout },
    setup() {
        const isSidebarExpanded = ref(false);

        const updateSidebarState = (newState) => {
            isSidebarExpanded.value = newState;
        };
        return {
            isSidebarExpanded,
            updateSidebarState,
        };
    },
};
</script>

<style>
.btn-logout:hover {
    background-color: #e74c3c !important;
    color: white !important;
}

.sidebar-expanded {
    width: 18rem;
}
.content-expanded {
    margin-left: 17rem;
}
.content-collapsed {
    margin-left: 3rem;
}

.custom-bg-clip {
  background-color: #022f45;
  clip-path: polygon(55% 0%, 100% 0%, 100% 100%, 45% 100%);
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: -1;
}

.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #4a5568 #edf2f7;
}

.custom-scrollbar::-webkit-scrollbar {
   width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #4a5568;
    border-radius: 10px;
    border: 2px solid #edf2f7;
}

/* Fileupload PrimeVue Style */
div.p-fileupload-header > button.p-button.p-component.p-button-secondary.p-fileupload-upload-button {
  display: none !important;
}
</style>
