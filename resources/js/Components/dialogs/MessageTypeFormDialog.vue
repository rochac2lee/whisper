<template>
    <v-dialog v-model="dialog" max-width="520">
        <v-card rounded="lg">
            <v-card-title
                class="d-flex justify-space-between align-center py-4"
            >
                <div class="text-h5 text-medium-emphasis ps-2">
                    {{
                        isEdit
                            ? "Editar Tipo de Mensagem"
                            : "Novo Tipo de Mensagem"
                    }}
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
            <v-card-text class="pt-4 pb-2 px-6">
                <v-form @submit.prevent="submit">
                    <v-text-field
                        v-model="form.name"
                        label="Nome"
                        :error-messages="form.errors.name"
                        required
                        rounded="lg"
                        variant="outlined"
                        density="comfortable"
                        class="mb-3"
                    ></v-text-field>

                    <v-text-field
                        v-model="form.color"
                        label="Cor (Hex)"
                        type="text"
                        placeholder="#000000"
                        maxlength="7"
                        :error-messages="form.errors.color"
                        required
                        rounded="lg"
                        variant="outlined"
                        density="comfortable"
                        class="mb-3"
                    >
                        <template #append-inner>
                            <span
                                v-if="colorPreview"
                                class="color-preview-chip"
                                :style="{ backgroundColor: colorPreview }"
                                @click.stop
                            >
                                <input
                                    type="color"
                                    class="color-picker-input"
                                    :value="colorPreview"
                                    @input="
                                        (event) =>
                                            (form.color = event.target.value)
                                    "
                                />
                            </span>
                        </template>
                    </v-text-field>

                    <v-checkbox
                        v-model="form.active"
                        label="Ativo"
                        color="primary"
                        class="mb-3"
                    ></v-checkbox>

                    <div class="d-flex justify-end mt-6"></div>
                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions class="px-6 py-4">
                <v-spacer></v-spacer>
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
                    @click="submit"
                    :loading="loading"
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
    messageType: {
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
    color: "#1976D2",
    active: true,
});

const loading = ref(false);

const isValidHexColor = (value) => /^#([0-9A-Fa-f]{6})$/.test(value?.trim());

const colorPreview = computed(() =>
    isValidHexColor(form.color) ? form.color : null
);

const isEdit = computed(() => !!props.messageType);

const setFormValues = () => {
    if (props.messageType) {
        form.defaults({
            name: props.messageType.name || "",
            color: props.messageType.color || "#1976D2",
            active: props.messageType.active ?? true,
        });
    } else {
        form.defaults({
            name: "",
            color: "#1976D2",
            active: true,
        });
    }

    form.reset();
    form.clearErrors();
};

watch(
    () => props.modelValue,
    (value) => {
        if (value) {
            setFormValues();
        }
    }
);

watch(
    () => props.messageType,
    () => {
        if (props.modelValue) {
            setFormValues();
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
        },
    };

    if (isEdit.value && props.messageType) {
        form.put(
            route("admin.message-types.update", props.messageType.id),
            options
        );
    } else {
        form.post(route("admin.message-types.store"), options);
    }
};
</script>

<style scoped>
.color-preview-chip {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 6px;
    border: 1px solid rgba(0, 0, 0, 0.12);
    position: relative;
    cursor: pointer;
    overflow: hidden;
}

.color-picker-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    border: none;
    padding: 0;
    background: transparent;
    -webkit-appearance: none;
    appearance: none;
}

.color-picker-input::-webkit-color-swatch-wrapper {
    padding: 0;
}

.color-picker-input::-webkit-color-swatch {
    border: none;
}

.color-picker-input::-moz-color-swatch {
    border: none;
}
</style>
