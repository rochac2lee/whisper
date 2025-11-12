<template>
    <v-app>
        <!-- Toolbar Moderno -->
        <v-app-bar
            elevation="0"
            color="white"
            :height="$vuetify.display.mobile ? 64 : 80"
            class="border-b"
        >
            <v-container
                fluid
                :class="$vuetify.display.mobile ? 'px-4' : 'px-6'"
                class="toolbar-container"
            >
                <!-- Logo (posição absoluta esquerda) -->
                <div class="toolbar-logo" v-if="logoUrl">
                    <img
                        :src="logoUrl"
                        :alt="displayName"
                        :style="
                            $vuetify.display.mobile
                                ? 'height: 28px; width: auto; object-fit: contain;'
                                : 'height: 36px; width: auto; object-fit: contain;'
                        "
                    />
                </div>
                <div class="toolbar-logo toolbar-logo--placeholder" v-else>
                    <span class="toolbar-logo__placeholder">{{
                        displayName
                    }}</span>
                </div>

                <!-- Título Centralizado -->
                <div class="toolbar-title d-none d-sm-block">
                    <h2
                        class="text-h6 text-primary font-weight-bold text-center mb-0"
                    >
                        Canal de Comunicação Interna
                    </h2>
                </div>

                <!-- Botão Login ou Dashboard (posição absoluta direita) -->
                <div class="toolbar-actions">
                    <a
                        v-if="!page.props.auth?.user"
                        :href="route('login')"
                        style="text-decoration: none"
                    >
                        <v-btn
                            color="primary"
                            variant="flat"
                            rounded="pill"
                            :class="
                                $vuetify.display.mobile
                                    ? 'px-3 text-none'
                                    : 'px-6 text-none'
                            "
                        >
                            <span class="d-sm-inline text-capitalize"
                                >LOGIN</span
                            >
                        </v-btn>
                    </a>
                    <a
                        v-else
                        :href="route('admin.dashboard')"
                        style="text-decoration: none"
                    >
                        <v-btn
                            color="primary"
                            variant="flat"
                            rounded="pill"
                            :class="
                                $vuetify.display.mobile
                                    ? 'px-3 text-none'
                                    : 'px-6 text-none'
                            "
                        >
                            <span class="d-sm-inline text-capitalize"
                                >DASHBOARD</span
                            >
                        </v-btn>
                    </a>
                </div>
            </v-container>
        </v-app-bar>

        <!-- Conteúdo Principal -->
        <v-main class="bg-grey-lighten-4">
            <slot />
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
    </v-app>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import ToastDialog from "@/Components/dialogs/ToastDialog.vue";

const page = usePage();
const branding = computed(() => page.props.branding ?? {});
const logoUrl = computed(() => branding.value?.logo_url || null);
const displayName = computed(() => branding.value?.display_name || "Whisper");
const showSuccess = ref(false);
const showError = ref(false);
const successMessage = ref("");
const errorMessage = ref("");

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
</script>

<style scoped>
.border-b {
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}

.border-t {
    border-top: 1px solid rgba(0, 0, 0, 0.12);
}

/* Toolbar com layout absoluto para centralização perfeita */
.toolbar-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.toolbar-logo {
    position: absolute;
    left: 24px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
}

.toolbar-logo__placeholder {
    font-size: 18px;
    font-weight: 600;
    color: rgb(var(--v-theme-primary));
    letter-spacing: 0.3px;
}

.toolbar-title {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.toolbar-actions {
    position: absolute;
    right: 24px;
    top: 50%;
    transform: translateY(-50%);
}

@media (max-width: 600px) {
    .toolbar-logo {
        left: 16px;
    }

    .toolbar-logo__placeholder {
        font-size: 16px;
    }

    .toolbar-actions {
        right: 16px;
    }
}
</style>
