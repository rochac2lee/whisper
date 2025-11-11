<template>
    <AdminLayout>
        <v-row>
            <v-col
                cols="12"
                class="d-flex justify-space-between align-center"
                :class="{ 'mt-3': $vuetify.display.mobile }"
            >
                <h1 class="text-h5 font-weight-medium">Mensagens</h1>
                <div class="d-flex align-center">
                    <v-btn color="primary" rounded="lg" @click="toggleFilters">
                        <v-icon
                            left
                            :icon="
                                showFilters
                                    ? 'fi fi-br-clear-alt'
                                    : 'fi fi-br-filter'
                            "
                        ></v-icon>
                        <span class="text-capitalize ms-2">
                            {{
                                showFilters ? "Ocultar Filtros" : "Ver Filtros"
                            }}
                        </span>
                    </v-btn>
                </div>
            </v-col>
        </v-row>

        <!-- Filtros -->
        <v-card rounded="lg" elevation="1" v-if="showFilters" class="mt-4">
            <v-card-title>Filtros</v-card-title>
            <v-card-text class="pa-4">
                <v-row>
                    <v-col cols="12" md="4">
                        <v-select
                            v-model="filterType"
                            :items="messageTypes"
                            item-title="name"
                            item-value="id"
                            label="Filtrar por Tipo"
                            rounded="lg"
                            variant="outlined"
                            density="compact"
                            hide-details="auto"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-select
                            v-model="filterRead"
                            :items="[
                                { text: 'Não Lidas', value: 0 },
                                { text: 'Lidas', value: 1 },
                            ]"
                            item-title="text"
                            item-value="value"
                            label="Filtrar por Status"
                            rounded="lg"
                            variant="outlined"
                            density="compact"
                            hide-details="auto"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" md="4" v-if="isFiltered">
                        <v-btn
                            color="primary"
                            rounded="lg"
                            variant="outlined"
                            @click="clearFilters"
                        >
                            <v-icon
                                left
                                icon="fi fi-br-clear-alt"
                                size="16"
                                class="me-2"
                            />
                            <span class="text-capitalize">Limpar Filtros</span>
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Lista de Mensagens -->
        <div v-if="messages.data.length > 0" class="mt-4">
            <v-row>
                <v-col cols="12" class="pb-0">
                    <p class="text-body-1 text-medium-emphasis">
                        {{ messages.total }}
                        {{ messages.total === 1 ? "mensagem" : "mensagens" }}
                    </p>
                </v-col>
                <v-col
                    v-for="message in messages.data"
                    :key="message.id"
                    cols="12"
                    class="pb-0"
                >
                    <v-card rounded="lg" elevation="1" class="message-card">
                        <v-card-text class="message-card__content pa-5">
                            <div class="message-card__header">
                                <div class="message-card__chips">
                                    <v-chip
                                        :color="message.message_type.color"
                                        size="small"
                                        class="text-capitalize"
                                    >
                                        {{ message.message_type.name }}
                                    </v-chip>
                                    <v-chip
                                        :color="
                                            message.is_read
                                                ? 'success'
                                                : 'warning'
                                        "
                                        size="small"
                                        class="text-capitalize"
                                    >
                                        {{
                                            message.is_read
                                                ? "Lida"
                                                : "Não Lida"
                                        }}
                                    </v-chip>
                                </div>
                                <span
                                    v-if="!isMobile"
                                    class="text-body-1 text-medium-emphasis message-card__info-text"
                                >
                                    Mensagem recebida em
                                    {{ formatDate(message.created_at) }}
                                </span>
                                <div
                                    class="message-card__actions-wrapper"
                                    :class="{
                                        'message-card__actions-wrapper--desktop':
                                            !isMobile,
                                    }"
                                >
                                    <v-menu location="bottom">
                                        <template #activator="{ props }">
                                            <v-btn
                                                icon
                                                variant="text"
                                                v-bind="props"
                                                size="small"
                                            >
                                                <v-icon
                                                    icon="fi fi-br-menu-dots-vertical"
                                                    size="16"
                                                ></v-icon>
                                            </v-btn>
                                        </template>
                                        <v-list density="compact">
                                            <v-list-item
                                                @click="openDetails(message)"
                                            >
                                                <template #prepend>
                                                    <v-icon
                                                        icon="fi fi-br-eye"
                                                        size="16"
                                                    ></v-icon>
                                                </template>
                                                <v-list-item-title>
                                                    Ver detalhes
                                                </v-list-item-title>
                                            </v-list-item>
                                            <v-list-item
                                                @click="
                                                    requestDeleteMessageById(
                                                        message.id
                                                    )
                                                "
                                            >
                                                <template #prepend>
                                                    <v-icon
                                                        icon="fi fi-br-trash"
                                                        size="16"
                                                    ></v-icon>
                                                </template>
                                                <v-list-item-title
                                                    class="text-error"
                                                >
                                                    Excluir
                                                </v-list-item-title>
                                            </v-list-item>
                                        </v-list>
                                    </v-menu>
                                </div>
                            </div>
                            <div class="message-card__info" v-if="isMobile">
                                <span
                                    class="text-body-1 text-medium-emphasis message-card__info-text"
                                >
                                    Mensagem recebida em
                                    {{ formatDate(message.created_at) }}
                                </span>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Paginação -->
            <div v-if="messages.links.length > 3" class="mt-8">
                <div
                    v-if="isMobile"
                    class="d-flex align-center justify-center gap-2 pagination-mobile"
                >
                    <v-btn
                        variant="flat"
                        size="small"
                        rounded="lg"
                        class="pagination-btn pagination-btn--control"
                        :disabled="!previousLink?.url"
                        @click="goToLink(previousLink)"
                    >
                        <v-icon icon="fi fi-br-angle-left" size="16"></v-icon>
                    </v-btn>

                    <v-select
                        v-model="selectedPage"
                        :items="pageOptions"
                        item-title="label"
                        item-value="value"
                        density="comfortable"
                        rounded="lg"
                        variant="outlined"
                        hide-details
                        class="pagination-select"
                    ></v-select>

                    <span class="pagination-mobile__info text-caption">
                        de {{ lastPage }}
                    </span>

                    <v-btn
                        variant="flat"
                        size="small"
                        rounded="lg"
                        class="pagination-btn pagination-btn--control"
                        :disabled="!nextLink?.url"
                        @click="goToLink(nextLink)"
                    >
                        <v-icon icon="fi fi-br-angle-right" size="16"></v-icon>
                    </v-btn>
                </div>
                <div
                    v-else
                    class="d-flex justify-center flex-wrap pagination-wrapper"
                >
                    <v-btn
                        v-for="link in messages.links"
                        :key="link.label"
                        :disabled="!link.url && !link.active"
                        :href="link.url || undefined"
                        size="small"
                        rounded="lg"
                        variant="flat"
                        :class="[
                            'mx-1',
                            'pagination-btn',
                            { 'pagination-btn--active': link.active },
                        ]"
                    >
                        <v-icon
                            v-if="getPaginationIcon(link.label)"
                            :icon="getPaginationIcon(link.label)"
                            size="16"
                        ></v-icon>
                        <span v-else v-html="link.label"></span>
                    </v-btn>
                </div>
            </div>
        </div>
        <v-card rounded="lg" elevation="1" class="mt-4" v-else>
            <v-card-text
                class="d-flex flex-column align-center justify-center py-12"
            >
                <v-icon
                    size="100"
                    color="primary"
                    icon="fi fi-br-comment-alt"
                    class="mb-4"
                ></v-icon>
                <p class="text-center text-h6">Nenhuma mensagem encontrada</p>
            </v-card-text>
        </v-card>
        <MessageDetailsDialog
            v-model="showDetailsDialog"
            :message="selectedMessage"
            :toggle-loading="toggleLoading"
            :delete-loading="deleteLoading"
            @toggle-read="toggleReadSelected"
            @delete="requestDeleteFromDetails"
        />
        <ConfirmDialog
            v-model="confirmDeleteDialog"
            title="Confirmar exclusão"
            :message="deleteMessageText"
            confirm-text="Excluir"
            confirm-color="error"
            :loading="deleteLoading"
            @confirm="confirmDeleteMessage"
            @cancel="cancelDelete"
        />
    </AdminLayout>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import MessageDetailsDialog from "@/Components/dialogs/MessageDetailsDialog.vue";
import ConfirmDialog from "@/Components/dialogs/ConfirmDialog.vue";
import { useDisplay } from "vuetify";

const props = defineProps({
    messages: Object,
    messageTypes: Array,
    filters: Object,
});

const display = useDisplay();
const isMobile = computed(() => display.smAndDown.value);

const filterType = ref(props.filters?.message_type_id || null);
const filterRead = ref(props.filters?.is_read || null);
const showFilters = ref(false);
const isClearingFilters = ref(false);

const isFiltered = computed(() => {
    return filterType.value !== null || filterRead.value !== null;
});

const clearFilters = () => {
    if (!isFiltered.value) {
        return;
    }

    isClearingFilters.value = true;
    filterType.value = null;
    filterRead.value = null;
    applyFilters();
};

// Formatar data
const formatDate = (date) => {
    return new Date(date).toLocaleString("pt-BR");
};

const truncateContent = (content) => {
    if (!content) return "";
    const text = content.toString();
    return text.length > 70 ? `${text.substring(0, 70)}...` : text;
};

const truncateFilename = (name) => {
    if (!name) return "Anexo";
    return name.length > 24 ? `${name.substring(0, 21)}...` : name;
};

const normalizePaginationLabel = (label) => {
    if (!label) {
        return "";
    }

    return label
        .toString()
        .toLowerCase()
        .replace(/&[a-z]+;/gi, "")
        .trim();
};

const getPaginationIcon = (label) => {
    const normalized = normalizePaginationLabel(label);

    if (
        normalized.includes("previous") ||
        normalized.includes("previews") ||
        normalized.includes("anterior")
    ) {
        return "fi fi-br-angle-left";
    }

    if (
        normalized.includes("next") ||
        normalized.includes("próximo") ||
        normalized.includes("proximo")
    ) {
        return "fi fi-br-angle-right";
    }

    return null;
};

const paginationLinks = computed(() => props.messages?.links ?? []);

const previousLink = computed(() =>
    paginationLinks.value.find((link) =>
        normalizePaginationLabel(link.label).includes("previous")
    )
);

const nextLink = computed(() =>
    paginationLinks.value.find((link) =>
        normalizePaginationLabel(link.label).includes("next")
    )
);

const numericLinks = computed(() =>
    paginationLinks.value.filter((link) => /^\d+$/.test(link.label))
);

const currentPage = computed(
    () =>
        props.messages?.current_page ??
        parseInt(
            numericLinks.value.find((link) => link.active)?.label || "1",
            10
        )
);

const lastPage = computed(
    () =>
        props.messages?.last_page ??
        parseInt(
            numericLinks.value[numericLinks.value.length - 1]?.label || "1",
            10
        )
);

const pageOptions = computed(() =>
    numericLinks.value.map((link) => ({
        label: link.label,
        value: parseInt(link.label, 10),
        url: link.url,
    }))
);

const selectedPage = ref(currentPage.value);

watch(currentPage, (value) => {
    selectedPage.value = value;
});

const goToLink = (link) => {
    if (!link?.url || link.active) {
        return;
    }

    router.visit(link.url, { preserveScroll: true });
};

watch(
    selectedPage,
    (value, prev) => {
        if (prev === undefined || value === prev) {
            return;
        }

        const link = numericLinks.value.find(
            (item) => parseInt(item.label, 10) === value
        );

        if (link) {
            goToLink(link);
        }
    },
    { flush: "post" }
);

const getAttachmentIcon = (message) => {
    const name = message?.attachment_name ?? "";
    const ext = name.includes(".") ? name.split(".").pop()?.toLowerCase() : "";
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

// Aplicar filtros
const buildFilterParams = () => {
    const params = {};

    if (filterType.value !== null && filterType.value !== undefined) {
        params.message_type_id = filterType.value;
    }

    if (filterRead.value !== null && filterRead.value !== undefined) {
        params.is_read = filterRead.value;
    }

    return params;
};

const applyFilters = () => {
    router.get(route("admin.messages.index"), buildFilterParams(), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const showDetailsDialog = ref(false);
const selectedMessage = ref(null);
const toggleLoading = ref(false);
const deleteLoading = ref(false);
const confirmDeleteDialog = ref(false);
const messageToDelete = ref(null);

const deleteMessageText = computed(() =>
    messageToDelete.value
        ? `Tem certeza de que deseja excluir a mensagem #${messageToDelete.value.id}?`
        : "Tem certeza de que deseja excluir esta mensagem?"
);

const openDetails = (message) => {
    selectedMessage.value = { ...message };
    showDetailsDialog.value = true;
};

const toggleReadSelected = () => {
    if (!selectedMessage.value) return;
    const id = selectedMessage.value.id;
    toggleLoading.value = true;
    router.post(
        route("admin.messages.toggle-read", id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                const newStatus = !selectedMessage.value.is_read;
                selectedMessage.value = {
                    ...selectedMessage.value,
                    is_read: newStatus,
                };
                router.reload({ only: ["messages"] });
            },
            onFinish: () => {
                toggleLoading.value = false;
            },
        }
    );
};

const requestDeleteFromDetails = () => {
    if (selectedMessage.value) {
        messageToDelete.value = { ...selectedMessage.value };
    }
    confirmDeleteDialog.value = true;
};

const requestDeleteMessageById = (id) => {
    messageToDelete.value = props.messages.data.find(
        (message) => message.id === id
    );
    confirmDeleteDialog.value = true;
};

const confirmDeleteMessage = () => {
    if (!messageToDelete.value) return;
    deleteLoading.value = true;
    router.delete(route("admin.messages.destroy", messageToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDetailsDialog.value = false;
            router.reload({ only: ["messages"] });
        },
        onFinish: () => {
            deleteLoading.value = false;
            confirmDeleteDialog.value = false;
            messageToDelete.value = null;
        },
    });
};

const cancelDelete = () => {
    if (!deleteLoading.value) {
        confirmDeleteDialog.value = false;
        messageToDelete.value = null;
    }
};

// Toggle filtros
const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

watch([filterType, filterRead], () => {
    if (isClearingFilters.value) {
        isClearingFilters.value = false;
        return;
    }

    applyFilters();
});
</script>

<style scoped>
.message-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.message-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(33, 150, 243, 0.12);
}

.message-card__content {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
}

.message-card__header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    width: 100%;
    gap: 8px;
}

.message-card__chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    flex: 0 1 auto;
}

.message-card__info {
    display: flex;
    align-items: center;
    gap: 12px;
    color: rgba(var(--v-theme-on-surface), 0.72);
    width: 100%;
}

.message-card__info-text {
    flex: 1;
}

.message-card__actions-wrapper {
    display: flex;
    align-items: center;
    margin-left: auto;
}

.message-card__actions-wrapper--desktop {
    padding-left: 16px;
}

.message-card__actions {
    margin-left: auto;
}

@media (min-width: 960px) {
    .message-card__content {
        flex-direction: row;
        align-items: center;
        gap: 16px;
    }

    .message-card__header {
        flex-direction: row;
        gap: 16px;
        align-items: center;
    }

    .message-card__chips {
        flex: 0 0 auto;
    }

    .message-card__info {
        flex: 1;
        align-items: center;
        gap: 16px;
    }
}

.attachment-banner {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    background-color: rgba(33, 150, 243, 0.08);
    border-radius: 10px;
}

.attachment-banner .v-icon {
    flex-shrink: 0;
}

.attachment-banner .v-btn {
    flex-shrink: 0;
}
.attachment-banner span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 160px;
}

.pagination-btn {
    background-color: rgb(var(--v-theme-surface));
    color: rgba(
        var(--v-theme-on-surface),
        var(--v-high-emphasis-opacity, 0.87)
    );
    box-shadow: none;
    transition: background-color 0.2s ease, color 0.2s ease,
        box-shadow 0.2s ease;
}

.pagination-btn .v-icon {
    color: currentColor;
}

.pagination-btn:hover:not(.pagination-btn--active):not(:disabled) {
    background-color: rgba(var(--v-theme-primary), 0.12);
    color: rgb(var(--v-theme-primary));
    box-shadow: 0 6px 18px rgba(var(--v-theme-primary), 0.15);
}

.pagination-btn--active {
    background-color: rgb(var(--v-theme-primary));
    color: rgb(var(--v-theme-on-primary));
    box-shadow: 0 10px 24px rgba(var(--v-theme-primary), 0.25);
    pointer-events: none;
}

.pagination-btn--active .v-icon {
    color: currentColor;
}

.pagination-wrapper {
    row-gap: 8px;
}

.pagination-mobile {
    gap: 8px;
}

.pagination-btn--control {
    width: 44px;
    min-width: 44px;
}

.pagination-select {
    max-width: 110px;
}

.pagination-mobile__info {
    color: rgba(
        var(--v-theme-on-surface),
        var(--v-medium-emphasis-opacity, 0.6)
    );
}
</style>
