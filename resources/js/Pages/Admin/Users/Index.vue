<template>
    <AdminLayout>
        <v-row>
            <v-col
                cols="12"
                class="d-flex justify-space-between align-center"
                :class="{ 'mt-3': $vuetify.display.mobile }"
            >
                <h1 class="text-h5 font-weight-medium">Usuários</h1>
                <v-btn
                    color="primary"
                    rounded="lg"
                    class="text-capitalize"
                    @click="openCreateDialog"
                >
                    <v-icon left icon="fi fi-br-plus" size="16"></v-icon>
                    <span class="text-capitalize ms-2">Novo Usuário</span>
                </v-btn>
            </v-col>
        </v-row>

        <v-row v-if="users.length">
            <v-col cols="12" md="6" lg="4" v-for="user in users" :key="user.id">
                <v-card rounded="lg" elevation="2" class="user-card pb-2">
                    <v-card-text class="d-flex justify-space-between">
                        <div class="d-flex">
                            <v-avatar color="primary" class="mr-5 mt-1">
                                <v-icon
                                    color="white"
                                    size="20"
                                    icon="fi fi-br-user"
                                ></v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-subtitle-1 font-weight-bold">
                                    {{ user.name }}
                                </div>
                                <v-icon
                                    icon="fi fi-br-envelope"
                                    size="16"
                                    class="mr-2"
                                ></v-icon>
                                <span class="text-body-2 text-medium-emphasis">
                                    {{ user.email }}
                                </span>
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
                                <v-list-item @click="openEditDialog(user)">
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
                                <v-list-item @click="requestDeleteUser(user)">
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
                    icon="fi fi-br-user"
                    size="96"
                    color="primary"
                    class="mb-4"
                ></v-icon>
                <p class="text-h6 text-medium-emphasis mb-0">
                    Nenhum usuário cadastrado
                </p>
            </v-card-text>
        </v-card>
        <UserFormDialog
            v-model="showDialog"
            :user="selectedUser"
            @saved="handleSaved"
        />
        <ConfirmDialog
            v-model="confirmDeleteDialog"
            title="Confirmar exclusão"
            :message="deleteMessage"
            confirm-text="Excluir"
            confirm-color="error"
            :loading="deleteLoading"
            @confirm="confirmDeleteUser"
            @cancel="cancelDelete"
        />
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import UserFormDialog from "@/Components/dialogs/UserFormDialog.vue";
import ConfirmDialog from "@/Components/dialogs/ConfirmDialog.vue";

const props = defineProps({
    users: Array,
});

const showDialog = ref(false);
const selectedUser = ref(null);
const confirmDeleteDialog = ref(false);
const userToDelete = ref(null);
const deleteLoading = ref(false);

const deleteMessage = computed(() =>
    userToDelete.value
        ? `Tem certeza de que deseja excluir "${userToDelete.value.name}"?`
        : "Tem certeza de que deseja excluir este usuário?"
);

const openCreateDialog = () => {
    selectedUser.value = null;
    showDialog.value = true;
};

const openEditDialog = (user) => {
    selectedUser.value = { ...user };
    showDialog.value = true;
};

const handleSaved = () => {
    selectedUser.value = null;
    showDialog.value = false;
};

const requestDeleteUser = (user) => {
    userToDelete.value = { ...user };
    confirmDeleteDialog.value = true;
};

const confirmDeleteUser = () => {
    if (!userToDelete.value) return;
    deleteLoading.value = true;
    router.delete(route("admin.users.destroy", userToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleteLoading.value = false;
            confirmDeleteDialog.value = false;
            userToDelete.value = null;
        },
    });
};

const cancelDelete = () => {
    if (!deleteLoading.value) {
        confirmDeleteDialog.value = false;
        userToDelete.value = null;
    }
};
</script>

<style scoped>
.user-card {
    height: 100%;
}
</style>
