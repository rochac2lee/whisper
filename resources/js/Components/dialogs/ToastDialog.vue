<template>
    <transition name="slide-up">
        <div v-if="visible" class="toast-dialog-container">
            <v-card :class="['toast-dialog-card', typeClass]" rounded="lg">
                <v-card-title
                    class="d-flex justify-space-between align-center py-3"
                >
                    <div class="text-body-2 text-white">
                        {{ title }}
                    </div>
                    <v-btn
                        icon="fi fi-br-x"
                        color="white"
                        variant="text"
                        size="x-small"
                        @click="close"
                    ></v-btn>
                </v-card-title>
                <v-divider
                    class="toast-divider"
                    :style="{
                        opacity: 0.25,
                        borderColor: 'rgba(255,255,255,0.2)',
                    }"
                ></v-divider>
                <v-card-text class="py-3 px-4">
                    <div class="d-flex align-center">
                        <v-icon
                            :icon="icon"
                            size="20"
                            class="mr-2"
                            color="white"
                        ></v-icon>
                        <span class="text-body-3 text-white">
                            {{ message }}
                        </span>
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </transition>
</template>

<script setup>
import { computed, watch, ref } from "vue";

const props = defineProps({
    type: {
        type: String,
        default: "success", // success | error
    },
    message: {
        type: String,
        default: "",
    },
    title: {
        type: String,
        default: "Notificação",
    },
    modelValue: {
        type: Boolean,
        default: false,
    },
    timeout: {
        type: Number,
        default: 5000,
    },
});

const emit = defineEmits(["update:modelValue"]);

const visible = ref(props.modelValue);

const icon = computed(() =>
    props.type === "error"
        ? "fi fi-br-circle-exclamation"
        : "fi fi-br-check-circle"
);

const typeClass = computed(() =>
    props.type === "error" ? "toast-dialog-error" : "toast-dialog-success"
);

let timer = null;

const clearTimer = () => {
    if (timer) {
        clearTimeout(timer);
        timer = null;
    }
};

watch(
    () => props.modelValue,
    (value) => {
        visible.value = value;
        clearTimer();
        if (value) {
            timer = setTimeout(() => {
                visible.value = false;
                emit("update:modelValue", false);
            }, props.timeout);
        }
    },
    { immediate: true }
);

watch(visible, (value) => {
    if (!value) {
        emit("update:modelValue", false);
    }
});

const close = () => {
    visible.value = false;
};

defineExpose({ close });
</script>

<style scoped>
.toast-dialog-container {
    position: fixed;
    bottom: 32px;
    right: 32px;
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast-dialog-card {
    pointer-events: auto;
    min-width: 300px;
    max-width: 360px;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: linear-gradient(
        135deg,
        rgba(18, 18, 18, 0.92),
        rgba(33, 33, 33, 0.92)
    );
}

.toast-dialog-success {
    background: linear-gradient(135deg, #2e7d32, #1b5e20);
}

.toast-dialog-error {
    background: linear-gradient(135deg, #c62828, #8e0000);
}

.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.2s ease;
}

.slide-up-enter-from,
.slide-up-leave-to {
    transform: translateY(16px);
    opacity: 0;
}
</style>
