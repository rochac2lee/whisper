<template>
    <AdminLayout>
        <v-row>
            <v-col
                cols="12"
                class="d-flex justify-space-between align-center"
                :class="{ 'mt-3': $vuetify.display.mobile }"
            >
                <h1 class="text-h5 font-weight-medium">Tipos de Mensagens</h1>
                <v-btn
                    color="primary"
                    rounded="lg"
                    class="text-capitalize"
                    @click="openCreateDialog"
                >
                    <v-icon left icon="fi fi-br-plus" size="16"></v-icon>
                    <span class="text-capitalize ms-2">Novo Tipo</span>
                </v-btn>
            </v-col>
        </v-row>

        <v-row v-if="messageTypes.length">
            <v-col
                cols="12"
                md="6"
                lg="4"
                v-for="type in messageTypes"
                :key="type.id"
            >
                <v-card rounded="lg" elevation="2" class="type-card pb-2">
                    <v-card-text
                        class="d-flex justify-space-between align-start"
                    >
                        <div class="d-flex">
                            <v-avatar :color="type.color" class="mr-5 mt-1">
                                <v-icon
                                    color="white"
                                    size="20"
                                    icon="fi fi-br-tags"
                                ></v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-subtitle-1 font-weight-bold">
                                    {{ type.name }}
                                </div>
                                <a
                                    href="#"
                                    class="d-flex align-center justify-start mt-3"
                                    @click.prevent="goToMessages(type)"
                                >
                                    <v-icon
                                        icon="fi fi-br-comment-alt"
                                        size="16"
                                        class="mr-2"
                                    ></v-icon>
                                    <span
                                        class="text-body-2 text-medium-emphasis"
                                    >
                                        <strong>
                                            {{
                                                type.messages_count == 1
                                                    ? `${type.messages_count} mensagem`
                                                    : `${type.messages_count} mensagens`
                                            }}
                                        </strong>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <v-menu location="bottom">
                            <template #activator="{ props }">
                                <v-btn
                                    icon
                                    variant="text"
                                    v-bind="props"
                                    size="x-small"
                                >
                                    <v-icon
                                        icon="fi fi-br-menu-dots-vertical"
                                        size="16"
                                    ></v-icon>
                                </v-btn>
                            </template>
                            <v-list density="compact">
                                <v-list-item @click="openEditDialog(type)">
                                    <template #prepend>
                                        <v-icon
                                            icon="fi fi-br-edit"
                                            size="16"
                                        ></v-icon>
                                    </template>
                                    <v-list-item-title>
                                        Editar
                                    </v-list-item-title>
                                </v-list-item>
                                <v-list-item @click="requestDeleteType(type)">
                                    <template #prepend>
                                        <v-icon
                                            icon="fi fi-br-trash"
                                            size="16"
                                        ></v-icon>
                                    </template>
                                    <v-list-item-title class="text-error">
                                        Excluir
                                    </v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-card
            v-else
            rounded="lg"
            elevation="1"
            class="mt-6 text-center py-12"
        >
            <v-card-text>
                <v-icon
                    icon="fi fi-br-tags"
                    size="96"
                    color="primary"
                    class="mb-4"
                ></v-icon>
                <p class="text-h6 text-medium-emphasis mb-0">
                    Nenhum tipo de mensagem cadastrado
                </p>
            </v-card-text>
        </v-card>
        <MessageTypeFormDialog
            v-model="showDialog"
            :message-type="selectedType"
            @saved="handleSaved"
        />
        <ConfirmDialog
            v-model="confirmDeleteDialog"
            title="Confirmar exclusão"
            :message="deleteMessage"
            confirm-text="Excluir"
            confirm-color="error"
            :loading="deleteLoading"
            @confirm="confirmDeleteType"
            @cancel="cancelDelete"
        />
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import MessageTypeFormDialog from "@/Components/dialogs/MessageTypeFormDialog.vue";
import ConfirmDialog from "@/Components/dialogs/ConfirmDialog.vue";

const props = defineProps({
    messageTypes: Array,
});

const showDialog = ref(false);
const confirmDeleteDialog = ref(false);
const selectedType = ref(null);
const typeToDelete = ref(null);
const deleteLoading = ref(false);

const deleteMessage = computed(() =>
    typeToDelete.value
        ? `Tem certeza de que deseja excluir o tipo "${typeToDelete.value.name}"?`
        : "Tem certeza de que deseja excluir este tipo de mensagem?"
);

// Excluir tipo
const requestDeleteType = (type) => {
    typeToDelete.value = { ...type };
    confirmDeleteDialog.value = true;
};

const confirmDeleteType = () => {
    if (!typeToDelete.value) return;
    deleteLoading.value = true;
    router.delete(route("admin.message-types.destroy", typeToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleteLoading.value = false;
            confirmDeleteDialog.value = false;
            typeToDelete.value = null;
        },
    });
};

const cancelDelete = () => {
    if (!deleteLoading.value) {
        confirmDeleteDialog.value = false;
        typeToDelete.value = null;
    }
};

const openCreateDialog = () => {
    selectedType.value = null;
    showDialog.value = true;
};

const openEditDialog = (type) => {
    selectedType.value = { ...type };
    showDialog.value = true;
};

const goToMessages = (type) => {
    if (!type?.id) return;

    router.visit(route("admin.messages.index"), {
        method: "get",
        data: {
            message_type_id: type.id,
        },
        preserveScroll: true,
    });
};

const handleSaved = () => {
    selectedType.value = null;
};
</script>

<style scoped>
.type-card {
    height: 100%;
}

.type-count-btn {
    text-transform: none;
    letter-spacing: normal;
    justify-content: flex-start;
    color: rgba(var(--v-theme-on-surface), 0.7);
    min-width: 0;
}

.type-count-btn:hover {
    background-color: rgba(var(--v-theme-primary), 0.08);
    color: rgb(var(--v-theme-primary));
}
</style>
