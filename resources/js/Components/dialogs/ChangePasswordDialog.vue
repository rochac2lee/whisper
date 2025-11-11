<template>
    <v-dialog v-model="dialog" max-width="520" persistent>
        <v-card rounded="lg">
            <v-card-title
                class="d-flex justify-space-between align-center py-4"
            >
                <div class="text-h5 text-medium-emphasis ps-2">
                    Alterar Senha
                </div>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pt-4 pb-2 px-6">
                <p class="text-body-2 text-medium-emphasis mb-4">
                    Por segurança, atualize sua senha antes de continuar usando
                    o sistema.
                </p>
                <v-form @submit.prevent="submit">
                    <v-text-field
                        v-model="form.current_password"
                        label="Senha Atual"
                        :type="showCurrentPassword ? 'text' : 'password'"
                        prepend-icon="fi fi-br-lock"
                        :append-inner-icon="
                            showCurrentPassword
                                ? 'fi fi-br-eye-crossed'
                                : 'fi fi-br-eye'
                        "
                        @click:append-inner="
                            showCurrentPassword = !showCurrentPassword
                        "
                        :error-messages="form.errors.current_password"
                        required
                        rounded="lg"
                        variant="outlined"
                        density="comfortable"
                        class="mb-3"
                    ></v-text-field>

                    <v-text-field
                        v-model="form.password"
                        label="Nova Senha"
                        :type="showPassword ? 'text' : 'password'"
                        prepend-icon="fi fi-br-lock-alt"
                        :append-inner-icon="
                            showPassword
                                ? 'fi fi-br-eye-crossed'
                                : 'fi fi-br-eye'
                        "
                        @click:append-inner="showPassword = !showPassword"
                        :error-messages="form.errors.password"
                        required
                        rounded="lg"
                        variant="outlined"
                        density="comfortable"
                        class="mb-3"
                    ></v-text-field>

                    <v-text-field
                        v-model="form.password_confirmation"
                        label="Confirmar Nova Senha"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        prepend-icon="fi fi-br-shield-check"
                        :append-inner-icon="
                            showPasswordConfirmation
                                ? 'fi fi-br-eye-crossed'
                                : 'fi fi-br-eye'
                        "
                        @click:append-inner="
                            showPasswordConfirmation = !showPasswordConfirmation
                        "
                        :error-messages="form.errors.password_confirmation"
                        required
                        rounded="lg"
                        variant="outlined"
                        density="comfortable"
                    ></v-text-field>
                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions class="px-6 py-4">
                <v-btn
                    color="primary"
                    class="text-capitalize"
                    rounded="lg"
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Salvar nova senha
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "success"]);

const dialog = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const showCurrentPassword = ref(false);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

watch(
    () => props.modelValue,
    (value) => {
        if (value) {
            form.reset();
            form.clearErrors();
            showCurrentPassword.value = false;
            showPassword.value = false;
            showPasswordConfirmation.value = false;
        }
    }
);

const submit = () => {
    form.post(route("update-password"), {
        preserveScroll: true,
        onSuccess: () => {
            emit("success");
            dialog.value = false;
        },
    });
};
</script>
