<template>
    <div>
        <Button
            type="button"
            label="Logout"
            severity="danger"
            icon="fas fa-arrow-right"
            iconPos="right"
            outlined
            text
            raised
            size="small"
            class="btn-logout"
            @click.prevent="visibleLogoutModal = true"
        />
        <Dialog
            v-model:visible="visibleLogoutModal"
            pt:root:class="!border-0"
            pt:mask:class="backdrop-blur-sm"
        >
            <template #container="{ closeCallback }">
                <div class="w-full bg-white p-4 rounded-md">
                    <p class="text-slate-600">
                        Apakah anda yakin ingin melakukan logout?
                    </p>
                    <div class="flex items-center gap-4 mt-4">
                        <Button
                            label="Logout"
                            severity="danger"
                            @click="logout"
                            text
                            class="!p-2 w-full !text-white !border !border-white/30 !bg-rose-500 hover:!bg-rose-300"
                        ></Button>
                        <Button
                            label="Tidak"
                            @click="closeCallback"
                            text
                            class="!p-2 w-full !text-gray-600 !border !border-gray-400 hover:!bg-gray-100"
                        ></Button>
                    </div>
                </div>
            </template>
        </Dialog>
    </div>
</template>
<script>
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";
import { ref } from "vue";
export default {
    setup() {
        const authStore = useAuthStore();
        const router = useRouter();
        const visibleLogoutModal = ref(false);

        const logout = () => {
            authStore.logout();
            router.replace({ path: "/login", query: { isLogout: true } });
        };

        return { logout, visibleLogoutModal };
    },
};
</script>
