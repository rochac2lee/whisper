<template>
    <AdminLayout>
        <v-row>
            <v-col>
                <v-col
                    cols="12"
                    class="d-flex justify-space-between align-center"
                    :class="{ 'mt-3': $vuetify.display.mobile }"
                >
                    <h1 class="text-h5 font-weight-medium">Configurações</h1>
                </v-col>
                <v-card
                    rounded="lg"
                    elevation="1"
                    class="settings-card-wrapper"
                >
                    <v-tabs
                        v-model="currentTab"
                        class="px-4"
                        color="primary"
                        grow
                    >
                        <v-tab value="branding">Identidade Visual</v-tab>
                        <v-tab value="organization">Organização</v-tab>
                        <v-tab value="contact">Contato</v-tab>
                        <v-tab value="address">Endereço</v-tab>
                    </v-tabs>

                    <v-divider></v-divider>

                    <v-window v-model="currentTab">
                        <v-window-item value="branding">
                            <v-card-text class="pt-6">
                                <div class="branding-section">
                                    <div
                                        class="logo-dropzone"
                                        :class="{
                                            'logo-dropzone--active': isDragging,
                                            'logo-dropzone--empty':
                                                !displayedLogo,
                                            'logo-dropzone--remove':
                                                removalPending,
                                        }"
                                        @click="triggerLogoSelect"
                                        @dragenter.prevent="onDragEnter"
                                        @dragover.prevent
                                        @dragleave.prevent="onDragLeave"
                                        @drop.prevent="onDrop"
                                    >
                                        <div class="logo-dropzone__content">
                                            <template v-if="displayedLogo">
                                                <img
                                                    :src="displayedLogo"
                                                    :alt="displayName"
                                                    class="logo-dropzone__thumbnail"
                                                />
                                            </template>
                                            <template v-else>
                                                <v-icon
                                                    icon="fi fi-br-cloud-upload"
                                                    color="primary"
                                                    size="36"
                                                    class="mb-4"
                                                ></v-icon>
                                                <p class="logo-dropzone__title">
                                                    Arraste e solte o logotipo
                                                    aqui
                                                </p>
                                                <p
                                                    class="logo-dropzone__subtitle"
                                                >
                                                    PNG, JPG ou SVG · até
                                                    5&nbsp;MB
                                                </p>
                                            </template>
                                            <div class="logo-dropzone__actions">
                                                <v-btn
                                                    variant="tonal"
                                                    color="primary"
                                                    rounded="lg"
                                                    size="small"
                                                    @click.stop="
                                                        triggerLogoSelect
                                                    "
                                                >
                                                    Selecionar arquivo
                                                </v-btn>
                                                <span
                                                    v-if="logoFileName"
                                                    class="logo-dropzone__filename text-truncate"
                                                >
                                                    {{ logoFileName }}
                                                </span>
                                            </div>
                                            <p
                                                class="logo-dropzone__hint text-medium-emphasis"
                                            >
                                                Você pode arrastar a imagem ou
                                                clicar para selecionar um
                                                arquivo do computador.
                                            </p>
                                        </div>
                                        <input
                                            ref="logoInputRef"
                                            type="file"
                                            class="d-none"
                                            accept="image/*"
                                            @change="onLogoInputChange"
                                        />
                                    </div>
                                    <v-alert
                                        v-if="removalPending"
                                        type="warning"
                                        variant="tonal"
                                        rounded="lg"
                                        class="mt-4"
                                        density="comfortable"
                                    >
                                        O logotipo atual será removido quando
                                        você salvar as alterações.
                                    </v-alert>
                                    <v-alert
                                        v-if="form.errors.branding_logo"
                                        type="error"
                                        variant="tonal"
                                        rounded="lg"
                                        class="mt-4"
                                        density="comfortable"
                                    >
                                        {{ form.errors.branding_logo }}
                                    </v-alert>
                                    <div class="d-flex flex-wrap gap-3 mt-4">
                                        <v-btn
                                            variant="outlined"
                                            color="error"
                                            rounded="lg"
                                            size="small"
                                            @click="removeLogo"
                                            :disabled="
                                                !currentLogoUrl && !logoPreview
                                            "
                                        >
                                            Remover logotipo atual
                                        </v-btn>
                                        <v-btn
                                            v-if="
                                                form.branding_remove_logo ||
                                                logoPreview
                                            "
                                            variant="text"
                                            color="primary"
                                            rounded="lg"
                                            size="small"
                                            @click="resetLogoChanges"
                                        >
                                            Manter logotipo atual
                                        </v-btn>
                                    </div>
                                </div>

                                <v-row class="mt-6" dense>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.branding_display_name"
                                            label="Nome exibido"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-bolt"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors
                                                    .branding_display_name
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.branding_slogan"
                                            label="Slogan (opcional)"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-comment-alt"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors.branding_slogan
                                            "
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-window-item>

                        <v-window-item value="organization">
                            <v-card-text class="pt-6">
                                <v-row dense>
                                    <v-col cols="12">
                                        <v-text-field
                                            v-model="form.organization_cnpj"
                                            label="CNPJ"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-id-badge"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors.organization_cnpj
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field
                                            v-model="
                                                form.organization_legal_name
                                            "
                                            label="Razão social"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-building"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors
                                                    .organization_legal_name
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field
                                            v-model="
                                                form.organization_trade_name
                                            "
                                            label="Nome fantasia"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-star"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors
                                                    .organization_trade_name
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="
                                                form.organization_state_registration
                                            "
                                            label="Inscrição estadual"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-map"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors
                                                    .organization_state_registration
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="
                                                form.organization_municipal_registration
                                            "
                                            label="Inscrição municipal"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-building-columns"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors
                                                    .organization_municipal_registration
                                            "
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-window-item>

                        <v-window-item value="contact">
                            <v-card-text class="pt-6">
                                <v-row dense>
                                    <v-col cols="12">
                                        <v-text-field
                                            v-model="form.contact_email"
                                            label="E-mail principal"
                                            type="email"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-at"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors.contact_email
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.contact_phone"
                                            label="Telefone"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-phone-call"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors.contact_phone
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.contact_portal_url"
                                            label="Portal interno"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-globe"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors.contact_portal_url
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field
                                            v-model="form.contact_website"
                                            label="Site institucional"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-browser"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors.contact_website
                                            "
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-window-item>

                        <v-window-item value="address">
                            <v-card-text class="pt-6">
                                <v-row dense>
                                    <v-col cols="12" md="4">
                                        <v-text-field
                                            v-model="form.address_postal_code"
                                            label="CEP"
                                            variant="outlined"
                                            density="comfortable"
                                            prepend-inner-icon="fi fi-br-marker"
                                            @keyup.enter="lookupPostalCode"
                                            rounded="lg"
                                            :loading="cepLoading"
                                            :error-messages="
                                                form.errors.address_postal_code
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="5">
                                        <v-text-field
                                            v-model="form.address_street"
                                            label="Logradouro"
                                            variant="outlined"
                                            density="comfortable"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors.address_street
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-text-field
                                            v-model="form.address_number"
                                            label="Número"
                                            variant="outlined"
                                            density="comfortable"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors.address_number
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="4">
                                        <v-text-field
                                            v-model="form.address_complement"
                                            label="Complemento"
                                            variant="outlined"
                                            density="comfortable"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors.address_complement
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="4">
                                        <v-text-field
                                            v-model="form.address_neighborhood"
                                            label="Bairro"
                                            variant="outlined"
                                            density="comfortable"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors.address_neighborhood
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-text-field
                                            v-model="form.address_city"
                                            label="Cidade"
                                            variant="outlined"
                                            density="comfortable"
                                            rounded="lg"
                                            :error-messages="
                                                form.errors.address_city
                                            "
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="1">
                                        <v-text-field
                                            v-model="form.address_state"
                                            label="UF"
                                            variant="outlined"
                                            density="comfortable"
                                            rounded="lg"
                                            maxlength="2"
                                            class="text-uppercase"
                                            :error-messages="
                                                form.errors.address_state
                                            "
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-window-item>
                    </v-window>

                    <v-divider></v-divider>

                    <div class="settings-actions">
                        <div class="d-flex flex-wrap gap-3 align-center">
                            <v-btn
                                color="primary"
                                rounded="lg"
                                size="large"
                                :loading="form.processing"
                                @click="saveAll"
                            >
                                Salvar alterações
                            </v-btn>
                            <v-btn
                                variant="text"
                                color="primary"
                                rounded="lg"
                                size="large"
                                @click="resetPendingChanges"
                                :disabled="form.processing || !form.isDirty"
                            >
                                Descartar alterações
                            </v-btn>
                        </div>
                        <p
                            class="settings-actions__hint text-body-2 text-medium-emphasis"
                        >
                            As alterações serão aplicadas em todo o aplicativo.
                        </p>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </AdminLayout>
</template>

<script setup>
import { computed, nextTick, onMounted, ref, watch } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const props = defineProps({
    brandingSettings: Object,
    organizationSettings: Object,
    contactSettings: Object,
    addressSettings: Object,
});

const page = usePage();
const sharedBranding = computed(() => page.props.branding ?? {});
const sharedOrganization = computed(() => page.props.organization ?? {});
const sharedContact = computed(() => page.props.contact ?? {});
const sharedAddress = computed(() => page.props.address ?? {});

const currentTab = ref("branding");
const logoInputRef = ref(null);
const logoPreview = ref(null);
const isDragging = ref(false);
const dragCounter = ref(0);
const cepLoading = ref(false);

const currentLogoUrl = computed(() => sharedBranding.value.logo_url || null);
const displayName = computed(
    () =>
        sharedBranding.value.display_name ||
        props.brandingSettings?.display_name ||
        "Whisper"
);

const buildFormSnapshot = () => ({
    branding_display_name:
        sharedBranding.value.display_name ??
        props.brandingSettings?.display_name ??
        "Whisper",
    branding_slogan:
        sharedBranding.value.slogan ?? props.brandingSettings?.slogan ?? "",
    branding_logo: null,
    branding_remove_logo: false,

    organization_legal_name:
        sharedOrganization.value.legal_name ??
        props.organizationSettings?.legal_name ??
        "",
    organization_trade_name:
        sharedOrganization.value.trade_name ??
        props.organizationSettings?.trade_name ??
        "",
    organization_cnpj: formatCnpj(
        sharedOrganization.value.cnpj ?? props.organizationSettings?.cnpj ?? ""
    ),
    organization_state_registration:
        sharedOrganization.value.state_registration ??
        props.organizationSettings?.state_registration ??
        "",
    organization_municipal_registration:
        sharedOrganization.value.municipal_registration ??
        props.organizationSettings?.municipal_registration ??
        "",

    contact_email:
        sharedContact.value.email ?? props.contactSettings?.email ?? "",
    contact_phone: formatPhone(
        sharedContact.value.phone ?? props.contactSettings?.phone ?? ""
    ),
    contact_website:
        sharedContact.value.website ?? props.contactSettings?.website ?? "",
    contact_portal_url:
        sharedContact.value.portal_url ??
        props.contactSettings?.portal_url ??
        "",

    address_postal_code: formatPostalCode(
        sharedAddress.value.postal_code ??
            props.addressSettings?.postal_code ??
            ""
    ),
    address_street:
        sharedAddress.value.street ?? props.addressSettings?.street ?? "",
    address_number:
        sharedAddress.value.number ?? props.addressSettings?.number ?? "",
    address_complement:
        sharedAddress.value.complement ??
        props.addressSettings?.complement ??
        "",
    address_neighborhood:
        sharedAddress.value.neighborhood ??
        props.addressSettings?.neighborhood ??
        "",
    address_city: sharedAddress.value.city ?? props.addressSettings?.city ?? "",
    address_state: (
        sharedAddress.value.state ??
        props.addressSettings?.state ??
        ""
    )
        .toString()
        .toUpperCase(),
});

const form = useForm(buildFormSnapshot());

const displayedLogo = computed(() => {
    if (logoPreview.value) {
        return logoPreview.value;
    }

    if (form.branding_remove_logo) {
        return null;
    }

    return currentLogoUrl.value;
});

const logoFileName = computed(() => {
    const file = form.branding_logo;
    if (file && typeof file === "object" && "name" in file) {
        return file.name;
    }
    return null;
});

const removalPending = computed(
    () => form.branding_remove_logo && !form.branding_logo
);

const triggerLogoSelect = () => {
    logoInputRef.value?.click();
};

const onLogoInputChange = (event) => {
    const file = event.target.files?.[0] ?? null;
    if (file) {
        handleLogoFile(file);
    }
    event.target.value = "";
};

const onDragEnter = () => {
    dragCounter.value += 1;
    isDragging.value = true;
};

const onDragLeave = () => {
    dragCounter.value = Math.max(0, dragCounter.value - 1);
    if (dragCounter.value === 0) {
        isDragging.value = false;
    }
};

const onDrop = (event) => {
    const file = event.dataTransfer?.files?.[0] ?? null;
    dragCounter.value = 0;
    isDragging.value = false;
    if (file) {
        handleLogoFile(file);
    }
};

const handleLogoFile = (file) => {
    if (!file || !file.type?.startsWith("image/")) {
        return;
    }

    form.branding_logo = file;
    form.branding_remove_logo = false;
    form.clearErrors("branding_logo");

    const reader = new FileReader();
    reader.onload = (event) => {
        logoPreview.value = event.target?.result ?? null;
    };
    reader.readAsDataURL(file);
};

const removeLogo = () => {
    if (!currentLogoUrl.value && !logoPreview.value) {
        return;
    }

    form.branding_logo = null;
    logoPreview.value = null;
    form.branding_remove_logo = true;
};

const resetLogoChanges = () => {
    form.branding_logo = null;
    logoPreview.value = null;
    form.branding_remove_logo = false;
};

const resolveTabByField = (fieldName) => {
    if (!fieldName) {
        return currentTab.value;
    }

    if (fieldName.startsWith("branding_")) {
        return "branding";
    }

    if (fieldName.startsWith("organization_")) {
        return "organization";
    }

    if (fieldName.startsWith("contact_")) {
        return "contact";
    }

    if (fieldName.startsWith("address_")) {
        return "address";
    }

    return currentTab.value;
};

const saveAll = () => {
    form.post(route("admin.settings.update"), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            router.reload({
                only: ["branding", "organization", "contact", "address"],
                onSuccess: () => {
                    syncFormWithProps();
                },
            });
        },
        onError: (errors) => {
            const firstField = Object.keys(errors ?? {})[0] ?? null;
            currentTab.value = resolveTabByField(firstField);
        },
    });
};

const resetPendingChanges = () => {
    syncFormWithProps();
};

const syncFormWithProps = () => {
    const snapshot = buildFormSnapshot();
    form.defaults(snapshot);
    form.reset();
    form.branding_logo = null;
    form.branding_remove_logo = false;
    logoPreview.value = currentLogoUrl.value;
    dragCounter.value = 0;
    isDragging.value = false;
};

const lookupPostalCode = async () => {
    const digits = (form.address_postal_code || "").replace(/\D/g, "");

    if (digits.length !== 8 || cepLoading.value) {
        return;
    }

    cepLoading.value = true;
    form.clearErrors("address_postal_code");

    try {
        const response = await fetch(
            `https://viacep.com.br/ws/${digits}/json/`
        );
        const data = await response.json();

        if (data.erro) {
            form.setError("address_postal_code", "CEP não encontrado.");
            return;
        }

        form.address_postal_code = formatPostalCode(digits);
        form.address_street = data.logradouro || form.address_street;
        form.address_neighborhood = data.bairro || form.address_neighborhood;
        form.address_city = data.localidade || form.address_city;
        form.address_state = (data.uf || form.address_state || "")
            .toString()
            .toUpperCase();
    } catch (error) {
        form.setError(
            "address_postal_code",
            "Não foi possível buscar o CEP. Tente novamente."
        );
    } finally {
        cepLoading.value = false;
    }
};

watch(
    () => form.branding_logo,
    (file) => {
        if (!file) {
            if (!form.branding_remove_logo) {
                logoPreview.value = null;
            }
            return;
        }

        if (Array.isArray(file)) {
            file = file[0] ?? null;
        }

        if (!(file instanceof File)) {
            return;
        }

        handleLogoFile(file);
    }
);

watch(
    () => form.organization_cnpj,
    (value) => {
        const formatted = formatCnpj(value);
        if (value !== formatted) {
            form.organization_cnpj = formatted;
        }
    }
);

watch(
    () => form.contact_phone,
    (value) => {
        const formatted = formatPhone(value);
        if (value !== formatted) {
            form.contact_phone = formatted;
        }
    }
);

watch(
    () => form.address_postal_code,
    (value, previous) => {
        const digits = (value ?? "").toString().replace(/\D/g, "");
        const previousDigits = (previous ?? "").toString().replace(/\D/g, "");
        const formatted = formatPostalCode(value);
        const shouldLookup = digits.length === 8 && digits !== previousDigits;

        if (value !== formatted) {
            form.address_postal_code = formatted;

            if (shouldLookup) {
                nextTick(() => {
                    lookupPostalCode();
                });
            }

            return;
        }

        if (shouldLookup) {
            lookupPostalCode();
        }
    }
);

watch(
    () => form.address_state,
    (value) => {
        if (value === undefined || value === null) {
            return;
        }

        const upper = value
            .toString()
            .replace(/[^a-zA-Z]/g, "")
            .toUpperCase()
            .slice(0, 2);

        if (value !== upper) {
            form.address_state = upper;
        }
    }
);

onMounted(() => {
    syncFormWithProps();
});

watch(
    () => ({
        branding: sharedBranding.value,
        organization: sharedOrganization.value,
        contact: sharedContact.value,
        address: sharedAddress.value,
    }),
    () => {
        if (form.processing) {
            return;
        }

        nextTick(() => {
            syncFormWithProps();
        });
    },
    { deep: true }
);

function formatCnpj(value) {
    if (!value) return "";
    const digits = value.toString().replace(/\D/g, "").slice(0, 14);
    let result = digits;

    if (digits.length > 12) {
        result = digits.replace(
            /(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/,
            (_, a, b, c, d, e) => `${a}.${b}.${c}/${d}${e ? `-${e}` : ""}`
        );
    } else if (digits.length > 8) {
        result = digits.replace(
            /(\d{2})(\d{3})(\d{3})(\d{0,4})/,
            (_, a, b, c, d) => `${a}.${b}.${c}/${d}`
        );
    } else if (digits.length > 5) {
        result = digits.replace(
            /(\d{2})(\d{3})(\d{0,3})/,
            (_, a, b, c) => `${a}.${b}.${c}`
        );
    } else if (digits.length > 2) {
        result = digits.replace(/(\d{2})(\d{0,3})/, (_, a, b) => `${a}.${b}`);
    }

    return result;
}

function formatPhone(value) {
    if (!value) return "";

    const digits = value.toString().replace(/\D/g, "").slice(0, 11);

    if (digits.length <= 10) {
        return digits.replace(/(\d{0,2})(\d{0,4})(\d{0,4})/, (_, a, b, c) =>
            [
                a ? `(${a}` : "",
                a ? (a.length === 2 ? ") " : "") : "",
                b,
                c ? `-${c}` : "",
            ].join("")
        );
    }

    return digits.replace(/(\d{0,2})(\d{0,5})(\d{0,4})/, (_, a, b, c) =>
        [
            a ? `(${a}` : "",
            a ? (a.length === 2 ? ") " : "") : "",
            b,
            c ? `-${c}` : "",
        ].join("")
    );
}

function formatPostalCode(value) {
    if (!value) return "";

    const digits = value.toString().replace(/\D/g, "").slice(0, 8);

    if (digits.length <= 5) {
        return digits;
    }

    return `${digits.slice(0, 5)}-${digits.slice(5)}`;
}
</script>

<style scoped>
.settings-card-wrapper {
    overflow: hidden;
}

.settings-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.branding-section {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.logo-dropzone {
    position: relative;
    border: 1.5px dashed rgba(var(--v-theme-primary), 0.4);
    border-radius: 20px;
    padding: 32px;
    text-align: center;
    background: rgba(var(--v-theme-primary), 0.04);
    transition: all 0.2s ease;
    cursor: pointer;
}

.logo-dropzone--active {
    background: rgba(var(--v-theme-primary), 0.12);
    border-color: rgb(var(--v-theme-primary));
}

.logo-dropzone--empty {
    min-height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-dropzone--remove {
    border-color: rgba(var(--v-theme-error), 0.6);
    background: rgba(var(--v-theme-error), 0.08);
}

.logo-dropzone__content {
    display: flex;
    flex-direction: column;
    gap: 12px;
    align-items: center;
}

.logo-dropzone__thumbnail {
    max-width: min(360px, 100%);
    max-height: 140px;
    object-fit: contain;
}

.logo-dropzone__title {
    font-size: 18px;
    font-weight: 600;
    color: rgb(var(--v-theme-primary));
    margin-bottom: 4px;
}

.logo-dropzone__subtitle {
    font-size: 14px;
    color: rgba(var(--v-theme-on-surface), 0.7);
}

.logo-dropzone__hint {
    font-size: 13px;
    margin-top: 4px;
}

.logo-dropzone__actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.logo-dropzone__filename {
    max-width: 260px;
}

.settings-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 20px 24px;
    background-color: rgba(var(--v-theme-surface), 0.94);
    border-top: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    position: sticky;
    bottom: 0;
    left: 0;
    right: 0;
    backdrop-filter: blur(6px);
}

.settings-actions__hint {
    margin: 0;
}

.text-uppercase input {
    text-transform: uppercase;
}

@media (min-width: 960px) {
    .settings-actions {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}
</style>
