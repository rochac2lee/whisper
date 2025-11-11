<template>
    <v-dialog v-model="internalVisible" max-width="420">
        <v-card rounded="lg">
            <v-card-title
                class="d-flex justify-space-between align-center py-4"
            >
                <div class="text-h5 text-medium-emphasis ps-2">
                    {{ title }}
                </div>
                <v-btn
                    icon="fi fi-br-x"
                    color="primary"
                    variant="text"
                    size="x-small"
                    @click="onCancel"
                ></v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="text-body-2 text-medium-emphasis pa-6">
                {{ message }}
            </v-card-text>
            <v-card-actions class="justify-end pa-4 pt-0">
                <v-btn
                    variant="text"
                    class="text-capitalize"
                    @click="onCancel"
                    :disabled="loading"
                >
                    {{ cancelText }}
                </v-btn>
                <v-btn
                    :color="confirmColor"
                    rounded="lg"
                    class="text-capitalize"
                    @click="onConfirm"
                    :loading="loading"
                >
                    {{ confirmText }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "Confirmação",
    },
    message: {
        type: String,
        default: "",
    },
    confirmText: {
        type: String,
        default: "Confirmar",
    },
    cancelText: {
        type: String,
        default: "Cancelar",
    },
    confirmColor: {
        type: String,
        default: "error",
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "confirm", "cancel"]);

const internalVisible = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const onCancel = () => {
    if (!props.loading) {
        emit("cancel");
        internalVisible.value = false;
    }
};

const onConfirm = () => {
    emit("confirm");
};
</script>
