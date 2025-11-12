<template>
    <v-app>
        <v-main>
            <v-container class="fill-height" fluid>
                <v-row justify="center" align="center">
                    <v-col cols="12" sm="8" md="6" lg="4">
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

                        <!-- Card de Login -->
                        <v-card rounded="lg" elevation="8" align="center">
                            <v-card-title
                                class="text-h6 font-weight-bold pa-6 bg-primary text-white"
                            >
                                Acesso Restrito
                            </v-card-title>

                            <v-card-text class="pa-6">
                                <v-form @submit.prevent="submit">
                                    <!-- Email -->
                                    <v-text-field
                                        v-model="form.email"
                                        label="E-mail"
                                        type="email"
                                        prepend-inner-icon="fi fi-br-envelope"
                                        :error-messages="errors.email"
                                        required
                                        rounded="lg"
                                        variant="outlined"
                                        density="comfortable"
                                        class="mb-3"
                                    ></v-text-field>

                                    <!-- Senha -->
                                    <v-text-field
                                        v-model="form.password"
                                        label="Senha"
                                        :type="
                                            showPassword ? 'text' : 'password'
                                        "
                                        prepend-inner-icon="fi fi-br-lock-alt"
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
                                    ></v-text-field>

                                    <!-- Lembrar-me -->
                                    <v-checkbox
                                        v-model="form.remember"
                                        label="Lembrar-me"
                                        color="primary"
                                    ></v-checkbox>

                                    <!-- Botão de Login -->
                                    <v-btn
                                        type="submit"
                                        color="primary"
                                        rounded="lg"
                                        block
                                        :loading="loading"
                                    >
                                        Entrar
                                    </v-btn>
                                </v-form>
                            </v-card-text>
                        </v-card>

                        <!-- Link para Página Pública -->
                        <div class="text-center mt-4">
                            <v-btn
                                :href="route('home')"
                                variant="text"
                                color="primary"
                            >
                                <v-icon
                                    icon="fi fi-br-arrow-left"
                                    size="16"
                                    class="mr-2"
                                ></v-icon>
                                <span>Voltar para Página Inicial</span>
                            </v-btn>
                        </div>
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
    email: "",
    password: "",
    remember: false,
});

const errors = ref({});
const loading = ref(false);
const showPassword = ref(false);
const page = usePage();
const branding = computed(() => page.props.branding ?? {});
const logoUrl = computed(() => branding.value?.logo_url || null);
const displayName = computed(() => branding.value?.display_name || "Whisper");

// Submeter formulário
const submit = () => {
    loading.value = true;
    errors.value = {};

    form.post(route("login"), {
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
    background-color: rgba(var(--v-theme-primary), 0.12);
    color: rgb(var(--v-theme-primary));
    font-weight: 600;
    letter-spacing: 0.4px;
}
</style>
