<template>
    <PublicLayout>
        <v-container :class="$vuetify.display.mobile ? 'py-6 px-4' : 'py-12'">
            <!-- Formulário -->
            <v-row justify="center">
                <v-col cols="12" md="8" lg="6">
                    <v-card elevation="8" rounded="lg" class="overflow-hidden">
                        <!-- Header do Card -->
                        <div :class="$vuetify.display.mobile ? 'pa-4' : 'pa-6'">
                            <div class="d-flex align-center">
                                <v-avatar
                                    color="primary"
                                    :size="$vuetify.display.mobile ? 48 : 56"
                                    :class="
                                        $vuetify.display.mobile
                                            ? 'mr-3'
                                            : 'mr-4'
                                    "
                                >
                                    <v-icon
                                        color="white"
                                        :size="
                                            $vuetify.display.mobile ? 24 : 24
                                        "
                                        icon="fi fi-br-comment-alt"
                                    ></v-icon>
                                </v-avatar>
                                <div>
                                    <h3
                                        :class="
                                            $vuetify.display.mobile
                                                ? 'text-subtitle-1 font-weight-bold text-white'
                                                : 'text-h6 font-weight-bold'
                                        "
                                    >
                                        Fale com a Gente
                                    </h3>
                                    <p
                                        :class="
                                            $vuetify.display.mobile
                                                ? 'text-caption mb-0 opacity-90'
                                                : 'text-body-2 mb-0 opacity-90'
                                        "
                                    >
                                        Este é um espaço aberto e
                                        <strong>100% anônimo</strong> para
                                        compartilhar<br
                                            class="d-none d-sm-block"
                                        />
                                        ideias, preocupações, sugestões ou
                                        elogios com nossa equipe.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <v-divider></v-divider>

                        <!-- Formulário -->
                        <v-card-text
                            :class="$vuetify.display.mobile ? 'pa-4' : 'pa-8'"
                        >
                            <v-form @submit.prevent="submitMessage">
                                <!-- Tipo de Mensagem -->
                                <div class="mb-4">
                                    <label
                                        class="text-body-2 font-weight-medium mb-2 d-block"
                                    >
                                        Tipo de mensagem
                                    </label>
                                    <v-select
                                        v-model="form.message_type_id"
                                        :items="messageTypes"
                                        item-title="name"
                                        item-value="id"
                                        placeholder="Selecione uma opção"
                                        :error-messages="
                                            form.errors.message_type_id
                                        "
                                        variant="outlined"
                                        rounded="lg"
                                        color="primary"
                                        hide-details="auto"
                                    >
                                        <template v-slot:item="{ props, item }">
                                            <v-list-item v-bind="props">
                                                <template v-slot:prepend>
                                                    <v-avatar
                                                        :color="item.raw.color"
                                                        size="12"
                                                    />
                                                </template>
                                            </v-list-item>
                                        </template>
                                    </v-select>
                                </div>

                                <!-- Mensagem -->
                                <div class="mb-4">
                                    <label
                                        class="text-body-2 font-weight-medium mb-2 d-block"
                                    >
                                        Sua mensagem
                                    </label>
                                    <v-textarea
                                        v-model="form.content"
                                        placeholder="Digite sua mensagem de forma clara e objetiva..."
                                        rows="6"
                                        counter="5000"
                                        :error-messages="form.errors.content"
                                        variant="outlined"
                                        rounded="lg"
                                        color="primary"
                                        hide-details
                                        auto-grow
                                    ></v-textarea>
                                </div>

                                <!-- Anexo com Drag and Drop -->
                                <div class="mb-4">
                                    <!-- Toogle para exibir/ocultar a Área de Drop -->
                                    <v-btn
                                        color="primary"
                                        variant="outlined"
                                        size="small"
                                        rounded="lg"
                                        @click="showDropArea = !showDropArea"
                                        v-if="!form.attachment && !showDropArea"
                                    >
                                        <v-icon
                                            icon="fi fi-br-clip"
                                            size="16"
                                            class="mr-2"
                                        ></v-icon>
                                        <span class="text-capitalize">
                                            Adicionar Anexo
                                        </span>
                                    </v-btn>
                                    <label
                                        class="text-body-2 font-weight-medium mb-2 d-block"
                                        v-if="showDropArea"
                                    >
                                        Anexo (opcional)
                                    </label>

                                    <!-- Área de Drop -->
                                    <div
                                        @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="isDragging = false"
                                        @drop.prevent="handleDrop"
                                        v-if="showDropArea"
                                        :class="[
                                            'drag-drop-area',
                                            { dragging: isDragging },
                                            { 'has-file': form.attachment },
                                        ]"
                                    >
                                        <!-- Estado: Sem arquivo -->
                                        <div
                                            v-if="!form.attachment"
                                            class="drop-content"
                                        >
                                            <v-icon
                                                :size="
                                                    $vuetify.display.mobile
                                                        ? 40
                                                        : 48
                                                "
                                                :color="
                                                    isDragging
                                                        ? 'primary'
                                                        : 'grey-lighten-1'
                                                "
                                                class="mb-2"
                                                :icon="
                                                    isDragging
                                                        ? 'fi fi-br-cloud-upload'
                                                        : 'fi fi-br-cloud-upload-alt'
                                                "
                                            ></v-icon>
                                            <p
                                                class="text-body-2 font-weight-medium mb-1"
                                            >
                                                {{
                                                    isDragging
                                                        ? "Solte o arquivo aqui"
                                                        : "Arraste e solte seu arquivo aqui"
                                                }}
                                            </p>
                                            <p
                                                class="text-caption text-medium-emphasis mb-2"
                                            >
                                                ou
                                            </p>
                                            <v-btn
                                                color="primary"
                                                variant="outlined"
                                                rounded="pill"
                                                size="small"
                                                @click="$refs.fileInput.click()"
                                            >
                                                Escolher Arquivo
                                            </v-btn>
                                            <input
                                                ref="fileInput"
                                                type="file"
                                                style="display: none"
                                                @change="handleFileSelect"
                                            />
                                        </div>

                                        <!-- Estado: Com arquivo -->
                                        <div
                                            v-if="
                                                showDropArea && form.attachment
                                            "
                                            class="file-preview"
                                        >
                                            <v-card
                                                rounded="lg"
                                                elevation="0"
                                                color="blue-lighten-5"
                                            >
                                                <v-card-text
                                                    class="d-flex align-center pa-3"
                                                >
                                                    <v-avatar
                                                        color="primary"
                                                        size="40"
                                                        class="mr-3"
                                                    >
                                                        <v-icon
                                                            color="white"
                                                            size="20"
                                                            :icon="
                                                                getFileIcon(
                                                                    form.attachment
                                                                )
                                                            "
                                                        ></v-icon>
                                                    </v-avatar>
                                                    <div class="flex-grow-1">
                                                        <p
                                                            class="text-body-2 font-weight-bold mb-0"
                                                        >
                                                            {{
                                                                form.attachment
                                                                    ?.name
                                                            }}
                                                        </p>
                                                        <p
                                                            class="text-caption text-medium-emphasis mb-0"
                                                        >
                                                            {{
                                                                formatFileSize(
                                                                    form
                                                                        .attachment
                                                                        ?.size
                                                                )
                                                            }}
                                                        </p>
                                                    </div>
                                                    <v-btn
                                                        icon
                                                        size="small"
                                                        variant="text"
                                                        color="error"
                                                        @click="deleteFile"
                                                    >
                                                        <v-icon
                                                            size="20"
                                                            icon="fi fi-br-close"
                                                        ></v-icon>
                                                    </v-btn>
                                                </v-card-text>
                                            </v-card>
                                        </div>
                                    </div>

                                    <p
                                        v-if="
                                            form.errors.attachment &&
                                            showDropArea
                                        "
                                        class="text-caption text-error mt-2"
                                    >
                                        {{ form.errors.attachment }}
                                    </p>
                                </div>

                                <!-- Botão Enviar -->
                                <v-btn
                                    type="submit"
                                    color="primary"
                                    rounded="lg"
                                    block
                                    :loading="loading"
                                    elevation="2"
                                    class="text-none font-weight-bold"
                                >
                                    Enviar Mensagem
                                </v-btn>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </PublicLayout>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import PublicLayout from "@/Layouts/PublicLayout.vue";

const props = defineProps({
    messageTypes: Array,
});

const showDropArea = ref(false);

const form = useForm({
    message_type_id: null,
    content: "",
    attachment: null,
});

const loading = ref(false);
const isDragging = ref(false);

// Manipular drop
const handleDrop = (e) => {
    isDragging.value = false;
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        form.attachment = files[0];
        showDropArea.value = true;
    }
};

// Manipular seleção de arquivo
const handleFileSelect = (e) => {
    const files = e.target.files;
    if (files.length > 0) {
        form.attachment = files[0];
        showDropArea.value = true;
    }
};

// Excluir arquivo
const deleteFile = () => {
    form.attachment = null;
};

// Obter ícone baseado no tipo de arquivo
const getFileIcon = (file) => {
    const fileName = file?.name || "";
    const ext = fileName.split(".").pop().toLowerCase();

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

    return iconMap[ext] || "fi fi-br-file";
};

// Formatar tamanho do arquivo
const formatFileSize = (bytes) => {
    if (!bytes) return "0 Bytes";

    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
};

const submitMessage = () => {
    loading.value = true;

    form.post("/messages", {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            showDropArea.value = false;
            isDragging.value = false;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>

<style scoped>
.bg-gradient-primary {
    background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
}

.opacity-90 {
    opacity: 0.9;
}

/* Drag and Drop Area */
.drag-drop-area {
    border: 2px dashed #bdbdbd;
    border-radius: 12px;
    padding: 24px 16px;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: #fafafa;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.drag-drop-area:hover {
    border-color: #1976d2;
    background: #f5f5f5;
}

.drag-drop-area.dragging {
    border-color: #1976d2;
    background: linear-gradient(
        135deg,
        rgba(25, 118, 210, 0.05) 0%,
        rgba(33, 150, 243, 0.05) 100%
    );
    transform: scale(1.02);
    box-shadow: 0 8px 24px rgba(25, 118, 210, 0.15);
}

.drag-drop-area.dragging::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg,
        rgba(25, 118, 210, 0.03) 0%,
        rgba(33, 150, 243, 0.03) 100%
    );
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.drag-drop-area.has-file {
    border-color: #4caf50;
    background: transparent;
    padding: 16px;
}

.drop-content {
    transition: all 0.3s ease;
}

.file-preview {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile adjustments */
@media (max-width: 600px) {
    .drag-drop-area {
        padding: 20px 12px;
    }
}
</style>
