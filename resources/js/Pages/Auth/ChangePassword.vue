<template>
    <v-app>
        <v-main>
            <v-container class="fill-height" fluid>
                <v-row justify="center" align="center">
                    <v-col cols="12" sm="8" md="6" lg="5">
                        <!-- Logo -->
                        <div class="text-center mb-8">
                            <template v-if="logoUrl">
                                <img
                                    :src="logoUrl"
                                    :alt="displayName"
                                    class="auth-logo"
                                />
                            </template>
                            <div v-else class="auth-logo-placeholder">
                                {{ displayName }}
                            </div>
                        </div>

                        <!-- Card de Alteração de Senha -->
                        <v-card rounded="lg" elevation="8">
                            <v-card-title
                                class="text-h6 font-weight-bold pa-6 bg-warning text-white"
                            >
                                ⚠️ Alterar Senha
                            </v-card-title>

                            <v-card-text class="pa-6">
                                <!-- Alerta -->
                                <v-alert
                                    type="warning"
                                    variant="tonal"
                                    class="mb-4"
                                    rounded="lg"
                                    density="compact"
                                >
                                    Por segurança, você deve alterar sua senha
                                    no primeiro acesso.
                                </v-alert>

                                <v-form @submit.prevent="submit">
                                    <!-- Senha Atual -->
                                    <v-text-field
                                        v-model="form.current_password"
                                        label="Senha Atual"
                                        :type="
                                            showCurrentPassword
                                                ? 'text'
                                                : 'password'
                                        "
                                        prepend-inner-icon="fi fi-br-lock-alt"
                                        :append-inner-icon="
                                            showCurrentPassword
                                                ? 'fi fi-br-eye-crossed'
                                                : 'fi fi-br-eye'
                                        "
                                        @click:append-inner="
                                            showCurrentPassword =
                                                !showCurrentPassword
                                        "
                                        :error-messages="
                                            errors.current_password
                                        "
                                        required
                                        rounded="lg"
                                        variant="outlined"
                                        density="comfortable"
                                        class="mb-3"
                                    ></v-text-field>

                                    <!-- Nova Senha -->
                                    <v-text-field
                                        v-model="form.password"
                                        label="Nova Senha"
                                        :type="
                                            showPassword ? 'text' : 'password'
                                        "
                                        prepend-inner-icon="fi fi-br-lock-open-alt"
                                        :append-inner-icon="
                                            showPassword
                                                ? 'fi fi-br-eye-crossed'
                                                : 'fi fi-br-eye'
                                        "
                                        @click:append-inner="
                                            showPassword = !showPassword
                                        "
                                        :error-messages="errors.password"
                                        required
                                        rounded="lg"
                                        variant="outlined"
                                        density="comfortable"
                                        class="mb-3"
                                    ></v-text-field>

                                    <!-- Confirmar Nova Senha -->
                                    <v-text-field
                                        v-model="form.password_confirmation"
                                        label="Confirmar Nova Senha"
                                        :type="
                                            showPasswordConfirmation
                                                ? 'text'
                                                : 'password'
                                        "
                                        prepend-inner-icon="fi fi-br-shield-check"
                                        :append-inner-icon="
                                            showPasswordConfirmation
                                                ? 'fi fi-br-eye-crossed'
                                                : 'fi fi-br-eye'
                                        "
                                        @click:append-inner="
                                            showPasswordConfirmation =
                                                !showPasswordConfirmation
                                        "
                                        :error-messages="
                                            errors.password_confirmation
                                        "
                                        required
                                        rounded="lg"
                                        variant="outlined"
                                        density="comfortable"
                                        class="mb-4"
                                    ></v-text-field>
                                    <v-btn
                                        type="submit"
                                        color="primary"
                                        rounded="lg"
                                        block
                                        :loading="loading"
                                    >
                                        Alterar Senha
                                    </v-btn>
                                </v-form>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script setup>
import { computed, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";

// Estado do formulário
const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const errors = ref({});
const loading = ref(false);
const showCurrentPassword = ref(false);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);
const page = usePage();
const branding = computed(() => page.props.branding ?? {});
const logoUrl = computed(() => branding.value?.logo_url || null);
const displayName = computed(() => branding.value?.display_name || "Whisper");

// Submeter formulário
const submit = () => {
    loading.value = true;
    errors.value = {};

    form.post(route("update-password"), {
        onError: (err) => {
            errors.value = err;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>

<style scoped>
.auth-logo {
    max-width: 180px;
    margin: 0 auto;
}

.auth-logo-placeholder {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 180px;
    height: 60px;
    margin: 0 auto;
    border-radius: 12px;
    background-color: rgba(var(--v-theme-warning), 0.12);
    color: rgb(var(--v-theme-warning));
    font-weight: 600;
    letter-spacing: 0.4px;
}
</style>
