<template>
    <v-app>
        <!-- App Bar (Mobile) -->
        <v-app-bar
            v-if="$vuetify.display.smAndDown"
            color="white"
            elevation="2"
            class="mobile-toolbar"
        >
            <v-app-bar-nav-icon
                @click="drawer = !drawer"
                color="primary"
            ></v-app-bar-nav-icon>
            <v-toolbar-title class="d-flex align-center">
                <template v-if="logoUrl">
                    <img
                        :src="logoUrl"
                        :alt="displayName"
                        style="height: 24px"
                    />
                </template>
                <span v-else class="logo-placeholder">{{ displayName }}</span>
            </v-toolbar-title>
        </v-app-bar>

        <!-- Navigation Drawer -->
        <v-navigation-drawer
            v-model="drawer"
            :permanent="$vuetify.display.mdAndUp"
            :temporary="$vuetify.display.smAndDown"
            width="260"
            class="border-e"
        >
            <!-- Logo -->
            <div
                class="pa-4 text-center"
                style="height: 60px"
                v-if="$vuetify.display.mdAndUp"
            >
                <template v-if="logoUrl">
                    <img
                        :src="logoUrl"
                        :alt="displayName"
                        style="max-width: 160px; height: auto"
                    />
                </template>
                <div v-else class="logo-placeholder logo-placeholder--drawer">
                    {{ displayName }}
                </div>
            </div>

            <v-divider></v-divider>

            <!-- Menu Principal -->
            <div class="pa-3">
                <div class="text-caption text-medium-emphasis px-3 mb-2">
                    Menu Principal
                </div>
                <v-list density="compact" nav>
                    <v-list-item
                        prepend-icon="fi fi-br-comment-alt"
                        title="Mensagens"
                        :active="isActive('admin.messages.*')"
                        rounded="lg"
                        class="mb-1 text-text"
                        @click="navigate('admin.messages.index')"
                    ></v-list-item>

                    <v-list-item
                        prepend-icon="fi fi-br-tags"
                        title="Tipos de Mensagens"
                        :active="isActive('admin.message-types.*')"
                        rounded="lg"
                        class="mb-1 text-text"
                        @click="navigate('admin.message-types.index')"
                    ></v-list-item>

                    <v-list-item
                        prepend-icon="fi fi-br-users"
                        title="Usuários"
                        :active="isActive('admin.users.*')"
                        rounded="lg"
                        class="mb-1 text-text"
                        @click="navigate('admin.users.index')"
                    ></v-list-item>

                    <v-list-item
                        prepend-icon="fi fi-br-settings"
                        title="Configurações"
                        :active="isActive('admin.settings.*')"
                        rounded="lg"
                        class="mb-1 text-text"
                        @click="navigate('admin.settings.index')"
                    ></v-list-item>
                </v-list>
            </div>

            <template v-slot:append>
                <v-divider></v-divider>
                <div v-if="authUser" class="pa-3 drawer-footer">
                    <div class="d-flex align-center justify-space-between">
                        <div class="d-flex align-center">
                            <v-avatar color="primary" size="40" class="mr-3">
                                <span class="text-white font-weight-bold">
                                    <v-icon
                                        size="20"
                                        icon="fi fi-br-user"
                                    ></v-icon>
                                </span>
                            </v-avatar>
                            <div class="user-text">
                                <div
                                    class="text-body-2 font-weight-medium text-text"
                                >
                                    {{ authUser.name }}
                                </div>
                                <div
                                    class="text-caption text-medium-emphasis text-text"
                                >
                                    {{ authUser.email }}
                                </div>
                            </div>
                        </div>
                        <v-btn
                            variant="text"
                            icon
                            color="error"
                            @click="openLogoutDialog"
                            size="x-small"
                            class="ml-3"
                        >
                            <v-icon icon="fi fi-br-exit" size="16"></v-icon>
                        </v-btn>
                    </div>
                </div>
            </template>
        </v-navigation-drawer>

        <!-- Main Content -->
        <v-main class="bg-grey-lighten-4">
            <!-- Toolbar (apenas desktop) -->
            <v-toolbar
                v-if="$vuetify.display.mdAndUp"
                color="white"
                height="60"
                style="border-bottom: 1px solid #e4e4e4"
            >
                <v-toolbar-title class="text-h6 font-weight-medium">
                    Canal de Comunicação Interna
                </v-toolbar-title>
            </v-toolbar>

            <v-container
                fluid
                :class="$vuetify.display.mobile ? 'pa-3' : 'pa-6'"
            >
                <slot />
            </v-container>
        </v-main>

        <ToastDialog
            v-model="showSuccess"
            :message="successMessage"
            type="success"
            title="Tudo certo!"
        />
        <ToastDialog
            v-model="showError"
            :message="errorMessage"
            type="error"
            title="Ops..."
        />
        <ChangePasswordDialog
            v-model="showPasswordDialog"
            @success="handlePasswordDialogSuccess"
        />

        <LogoutDialog
            v-model="showLogoutDialog"
            :loading="logoutProcessing"
            @confirm="confirmLogout"
        />
    </v-app>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { useDisplay } from "vuetify";
import { router, usePage } from "@inertiajs/vue3";
import LogoutDialog from "@/Components/dialogs/LogoutDialog.vue";
import ToastDialog from "@/Components/dialogs/ToastDialog.vue";
import ChangePasswordDialog from "@/Components/dialogs/ChangePasswordDialog.vue";

// Estado do drawer (inicia aberto em desktop, fechado em mobile)
const display = useDisplay();
const page = usePage();
const branding = computed(() => page.props.branding ?? {});
const logoUrl = computed(() => branding.value?.logo_url || null);
const displayName = computed(() => branding.value?.display_name || "Whisper");
const drawer = ref(display.mdAndUp.value);

watch(
    () => display.smAndDown.value,
    (isMobile) => {
        drawer.value = !isMobile;
    },
    { immediate: true }
);

// Estado dos snackbars
const authUser = computed(() => page.props.auth?.user ?? null);
const showPasswordDialog = ref(false);
const showSuccess = ref(false);
const showError = ref(false);
const successMessage = ref("");
const errorMessage = ref("");

// Observar mudanças no flash
watch(
    () => page.props.flash,
    (flash) => {
        if (flash && flash.success) {
            successMessage.value = flash.success;
            showSuccess.value = true;
        }
        if (flash && flash.error) {
            errorMessage.value = flash.error;
            showError.value = true;
        }
    },
    { deep: true, immediate: true }
);

watch(
    () => authUser.value?.force_password_change,
    (value) => {
        showPasswordDialog.value = !!value;
    },
    { immediate: true }
);

// Função para verificar se a rota está ativa
const isActive = (routeName) => {
    return route().current(routeName);
};

const navigate = (routeName) => {
    if (!isActive(routeName)) {
        router.visit(route(routeName));
    }
};

// Diálogo de logout
const showLogoutDialog = ref(false);
const logoutProcessing = ref(false);

const openLogoutDialog = () => {
    showLogoutDialog.value = true;
};

const confirmLogout = () => {
    logoutProcessing.value = true;
    router.post(route("logout"), {
        onFinish: () => {
            logoutProcessing.value = false;
            showLogoutDialog.value = false;
        },
    });
};

const handlePasswordDialogSuccess = () => {
    showPasswordDialog.value = false;
};
</script>

<style scoped>
.border-e {
    border-right: 1px solid rgba(33, 33, 33, 0.12);
}

.drawer-footer {
    background-color: rgba(255, 255, 255, 0.9);
}

.drawer-footer .user-text {
    display: flex;
    flex-direction: column;
    line-height: 1.1;
}

.mobile-toolbar {
    border-bottom: 1px solid rgba(33, 33, 33, 0.12);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.logo-placeholder {
    font-size: 18px;
    font-weight: 600;
    color: rgb(var(--v-theme-primary));
    letter-spacing: 0.3px;
}

.logo-placeholder--drawer {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
