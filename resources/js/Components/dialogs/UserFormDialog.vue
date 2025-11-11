<template>
    <v-dialog v-model="dialog" max-width="560">
        <v-card rounded="lg">
            <v-card-title
                class="d-flex justify-space-between align-center py-4"
            >
                <div class="text-h5 text-medium-emphasis ps-2">
                    {{ isEdit ? "Editar Usuário" : "Novo Usuário" }}
                </div>
                <v-btn
                    icon="fi fi-br-x"
                    color="primary"
                    variant="text"
                    size="x-small"
                    @click="close"
                ></v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="pt-8">
                <v-form @submit.prevent="submit">
                    <v-text-field
                        v-model="form.name"
                        label="Nome"
                        prepend-icon="fi fi-br-user"
                        :error-messages="form.errors.name"
                        required
                        rounded="lg"
                        variant="outlined"
                        density="comfortable"
                        class="mb-3"
                    ></v-text-field>

                    <v-text-field
                        v-model="form.email"
                        label="E-mail"
                        type="email"
                        prepend-icon="fi fi-br-envelope"
                        :error-messages="form.errors.email"
                        required
                        rounded="lg"
                        variant="outlined"
                        density="comfortable"
                        class="mb-3"
                    ></v-text-field>

                    <v-text-field
                        v-model="form.password"
                        :label="isEdit ? 'Nova Senha (opcional)' : 'Senha'"
                        :type="showPassword ? 'text' : 'password'"
                        prepend-icon="fi fi-br-lock-alt"
                        :append-inner-icon="
                            showPassword
                                ? 'fi fi-br-eye-crossed'
                                : 'fi fi-br-eye'
                        "
                        @click:append-inner="showPassword = !showPassword"
                        :error-messages="form.errors.password"
                        :required="!isEdit"
                        rounded="lg"
                        variant="outlined"
                        density="comfortable"
                        class="mb-3"
                    ></v-text-field>

                    <v-text-field
                        v-if="form.password"
                        v-model="form.password_confirmation"
                        label="Confirmar Senha"
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
                        rounded="lg"
                        variant="outlined"
                        density="comfortable"
                    ></v-text-field>

                    <v-checkbox
                        v-model="form.force_password_change"
                        label="Forçar alteração de senha no próximo login?"
                        color="warning"
                    ></v-checkbox>
                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions class="px-6 py-4">
                <v-btn
                    variant="text"
                    class="text-capitalize mr-2"
                    @click="close"
                    :disabled="loading"
                >
                    Cancelar
                </v-btn>
                <v-btn
                    color="primary"
                    rounded="lg"
                    class="text-capitalize"
                    type="submit"
                    :loading="loading"
                    @click="submit"
                >
                    {{ isEdit ? "Atualizar" : "Salvar" }}
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
    user: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["update:modelValue", "saved"]);

const dialog = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    is_admin: true,
    force_password_change: true,
});

const loading = ref(false);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const isEdit = computed(() => !!props.user);

const setDefaults = () => {
    if (props.user) {
        form.defaults({
            name: props.user.name || "",
            email: props.user.email || "",
            password: "",
            password_confirmation: "",
            is_admin: !!props.user.is_admin,
            force_password_change: props.user.force_password_change ?? true,
        });
    } else {
        form.defaults({
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
            is_admin: true,
            force_password_change: true,
        });
    }
    form.reset();
    form.clearErrors();
    showPassword.value = false;
    showPasswordConfirmation.value = false;
};

watch(
    () => props.modelValue,
    (value) => {
        if (value) {
            setDefaults();
        }
    }
);

watch(
    () => props.user,
    () => {
        if (props.modelValue) {
            setDefaults();
        }
    }
);

const close = () => {
    if (!loading.value) {
        dialog.value = false;
    }
};

const submit = () => {
    loading.value = true;

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            emit("saved");
            dialog.value = false;
        },
        onFinish: () => {
            loading.value = false;
            form.transform((data) => data);
        },
    };

    if (isEdit.value && props.user) {
        form.transform((data) => {
            const payload = { ...data };
            if (!payload.password) {
                delete payload.password;
                delete payload.password_confirmation;
            }
            return payload;
        }).put(route("admin.users.update", props.user.id), options);
    } else {
        form.transform((data) => data).post(
            route("admin.users.store"),
            options
        );
    }
};
</script>
