<template>
    <v-dialog v-model="dialog" max-width="420">
        <v-card rounded="lg">
            <v-card-title class="text-h6 font-weight-medium">
                Confirmar saída
            </v-card-title>
            <v-card-text class="pt-2">
                Tem certeza de que deseja encerrar a sessão?
            </v-card-text>
            <v-card-actions class="justify-end">
                <v-btn
                    variant="text"
                    class="text-capitalize"
                    @click="close"
                    :disabled="loading"
                >
                    Cancelar
                </v-btn>
                <v-btn
                    color="error"
                    rounded="lg"
                    class="text-capitalize"
                    @click="confirm"
                    :loading="loading"
                >
                    Sair
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
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "confirm"]);

const dialog = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const close = () => {
    if (!props.loading) {
        dialog.value = false;
    }
};

const confirm = () => {
    emit("confirm");
};
</script>
