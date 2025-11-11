<template>
    <v-dialog
        v-model="dialog"
        max-width="720"
        :fullscreen="$vuetify.display.mobile"
    >
        <v-card rounded="lg" class="message-dialog-card">
            <!-- Header -->
            <v-card-title
                class="dialog-header d-flex justify-space-between align-center py-4"
            >
                <div class="title-container text-medium-emphasis">
                    <span class="dialog-title">Detalhes da Mensagem</span>
                    <v-chip
                        :color="message.message_type?.color"
                        class="ml-2 dialog-chip"
                    >
                        {{ message.message_type?.name }}
                    </v-chip>
                    <v-chip
                        :color="message.is_read ? 'success' : 'warning'"
                        class="ml-2 dialog-chip"
                    >
                        {{ message.is_read ? "Lida" : "Não Lida" }}
                    </v-chip>
                </div>
                <v-btn
                    icon="fi fi-br-x"
                    color="primary"
                    variant="text"
                    size="x-small"
                    @click="dialog = false"
                ></v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="dialog-content pa-6" v-if="message">
                <div class="d-flex flex-column mb-4">
                    <strong>Data de Envio:</strong>
                    <span>
                        {{ formatDate(message.created_at) }}
                    </span>
                </div>

                <div class="mb-4">
                    <strong>Conteúdo:</strong>
                    <v-card variant="outlined" class="mt-2 pa-4">
                        <p style="white-space: pre-wrap">
                            {{ message.content }}
                        </p>
                    </v-card>
                </div>

                <div
                    v-if="message.attachment_path"
                    class="mb-4 attachment-wrapper"
                >
                    <strong>Anexo:</strong>
                    <v-card
                        variant="tonal"
                        class="attachment-card"
                        rounded="lg"
                    >
                        <div class="attachment-content d-flex align-center">
                            <v-avatar color="primary" size="40" class="mr-3">
                                <v-icon
                                    color="white"
                                    size="20"
                                    :icon="attachmentIcon"
                                ></v-icon>
                            </v-avatar>
                            <div class="flex-grow-1">
                                <p class="text-body-2 font-weight-medium mb-0">
                                    {{ attachmentInfo.name }}
                                </p>
                                <p
                                    class="text-caption text-medium-emphasis mb-0"
                                >
                                    {{ attachmentInfo.extension.toUpperCase() }}
                                </p>
                            </div>
                            <v-btn
                                :href="
                                    route('admin.messages.download', message.id)
                                "
                                target="_blank"
                                rel="noopener"
                                color="primary"
                                rounded="lg"
                                class="text-capitalize"
                            >
                                <v-icon
                                    left
                                    class="mr-2"
                                    icon="fi fi-br-download"
                                ></v-icon>
                                <span class="text-capitalize">Baixar</span>
                            </v-btn>
                        </div>
                    </v-card>
                </div>
            </v-card-text>

            <v-divider></v-divider>
            <v-card-actions class="dialog-actions px-6 py-4">
                <v-btn
                    color="error"
                    rounded="lg"
                    variant="outlined"
                    @click="$emit('delete')"
                    :disabled="deleteLoading || !message"
                    :loading="deleteLoading"
                >
                    <v-icon left icon="fi fi-br-trash" class="mr-2" />
                    Excluir
                </v-btn>

                <v-spacer></v-spacer>

                <v-btn
                    color="secondary"
                    variant="outlined"
                    rounded="lg"
                    @click="$emit('toggle-read'), (dialog = false)"
                    :disabled="toggleLoading || !message"
                    :loading="toggleLoading"
                >
                    <v-icon
                        left
                        :icon="
                            message?.is_read
                                ? 'fi fi-br-envelope'
                                : 'fi fi-br-envelope-open'
                        "
                        class="mr-2"
                    />
                    Marcar como {{ message?.is_read ? "Não Lida" : "Lida" }}
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
    message: {
        type: Object,
        default: null,
    },
    toggleLoading: {
        type: Boolean,
        default: false,
    },
    deleteLoading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "toggle-read", "delete"]);

const dialog = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const formatDate = (date) => {
    return new Date(date).toLocaleString("pt-BR");
};

const attachmentInfo = computed(() => {
    const name = props.message?.attachment_name || "";
    const extension = name.includes(".") ? name.split(".").pop() ?? "" : "";
    return {
        name,
        extension,
    };
});

const attachmentIcon = computed(() => {
    const iconMap = {
        pdf: "fi fi-br-file-pdf",
        doc: "fi fi-br-file-word",
        docx: "fi fi-br-file-word",
        xls: "fi fi-br-file-excel",
        xlsx: "fi fi-br-file-excel",
        ppt: "fi fi-br-file-powerpoint",
        pptx: "fi fi-br-file-powerpoint",
        jpg: "fi fi-br-file-image",
        jpeg: "fi fi-br-file-image",
        png: "fi fi-br-file-image",
        gif: "fi fi-br-file-image",
        zip: "fi fi-br-file-zipper",
        rar: "fi fi-br-file-zipper",
        txt: "fi fi-br-file",
    };

    const ext = attachmentInfo.value.extension.toLowerCase();
    return iconMap[ext] || "fi fi-br-file";
});
</script>

<style scoped>
.message-dialog-card {
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

.dialog-header {
    flex-wrap: wrap;
    gap: 12px;
}

.title-container {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
}

.dialog-title {
    font-size: 18px;
    font-weight: 600;
    display: block;
}

.dialog-chip {
    margin-left: 0 !important;
}

.dialog-content {
    overflow-y: auto;
}

.attachment-card {
    background-color: rgba(33, 150, 243, 0.08);
    margin-top: 12px;
    padding: 12px 16px;
}

.attachment-wrapper {
    display: flex;
    flex-direction: column;
}

.attachment-content {
    gap: 12px;
    flex-wrap: wrap;
}

.dialog-actions {
    flex-wrap: wrap;
    gap: 12px;
}

@media (max-width: 600px) {
    .dialog-content {
        padding: 16px !important;
    }

    .dialog-header {
        padding-inline: 16px;
        padding-block: 12px;
    }

    .dialog-title {
        font-size: 16px;
    }

    .attachment-card {
        padding: 12px;
    }

    .dialog-actions {
        padding: 16px !important;
    }

    .dialog-actions .v-btn {
        flex: 1 1 100%;
    }

    .dialog-actions .v-btn + .v-btn {
        margin-left: 0;
    }
}
</style>
