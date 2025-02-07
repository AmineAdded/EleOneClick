<template>
    <div>
        <!-- Bouton pour ouvrir le modal avec effet de survol amélioré -->
        <v-btn
            color="primary"
            prepend-icon="mdi-plus"
            class="elevation-2 add-button"
            @click="dialog = true"
            rounded
        >
            Ajouter une catégorie
        </v-btn>

        <!-- Modal d'ajout avec design amélioré -->
        <v-dialog v-model="dialog" max-width="600px" persistent>
            <v-card class="modal-card rounded-xl">
                <v-card-title class="modal-header pa-6 d-flex align-center">
                    <div class="d-flex align-center">
                        <v-icon size="32" class="mr-3 header-icon">mdi-shape-plus</v-icon>
                        <span class="text-h5 font-weight-medium">Nouvelle catégorie</span>
                    </div>
                    <v-spacer></v-spacer>
                    <v-btn
                        icon="mdi-close"
                        variant="text"
                        color="black"
                        class="close-button"
                        @click="closeDialog"
                    ></v-btn>
                </v-card-title>

                <v-card-text class="pa-6 pt-8">
                    <v-form ref="form" v-model="valid" @submit.prevent="submitForm">
                        <div class="d-flex flex-column gap-6">
                            <!-- Nom de la catégorie avec design amélioré -->
                            <v-text-field
                                v-model="category.name"
                                label="Nom de la catégorie"
                                :rules="nameRules"
                                required
                                variant="outlined"
                                prepend-inner-icon="mdi-tag"
                                placeholder="Entrez le nom de la catégorie"
                                class="input-field"
                                bg-color="grey-lighten-4"
                            ></v-text-field>

                            <!-- Sélection UAP avec design amélioré -->
                            <v-select
                                v-model="category.uap_id"
                                :items="uaps"
                                label="Unité de Production (UAP)"
                                item-title="name"
                                item-value="id"
                                :rules="uapRules"
                                required
                                variant="outlined"
                                prepend-inner-icon="mdi-factory"
                                placeholder="Sélectionnez une UAP"
                                class="input-field"
                                bg-color="grey-lighten-4"
                            ></v-select>

                            <!-- Message d'erreur amélioré -->
                            <v-fade-transition>
                                <v-alert
                                    v-if="errorMessage"
                                    type="error"
                                    variant="tonal"
                                    class="mb-4 error-alert"
                                    closable
                                    border="start"
                                >
                                    {{ errorMessage }}
                                </v-alert>
                            </v-fade-transition>
                        </div>
                    </v-form>
                </v-card-text>

                <v-card-actions class="pa-6 pt-0">
                    <v-spacer></v-spacer>
                    <v-btn
                        color="grey-darken-1"
                        variant="outlined"
                        class="action-button mr-3"
                        @click="closeDialog"
                        prepend-icon="mdi-close"
                    >
                        Annuler
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="submitForm"
                        :loading="loading"
                        :disabled="!valid"
                        prepend-icon="mdi-check"
                        elevation="2"
                        class="action-button"
                    >
                        Enregistrer
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import CategoryService from "@/Services/Categories/Categories";

export default {
    name: 'AjoutCategorie',
    data() {
        return {
            dialog: false,
            valid: false,
            loading: false,
            errorMessage: '',
            category: {
                name: '',
                uap_id: null
            },
            nameRules: [
                v => !!v || 'Le nom est requis',
                v => (v && v.length >= 3) || 'Le nom doit contenir au moins 3 caractères'
            ],
            uapRules: [
                v => !!v || 'La sélection d\'une UAP est requise'
            ],
            uaps: []
        }
    },
    methods: {
        async fetchUAPs() {
            try {
                const response = await CategoryService.showUAPs();
                this.uaps = response.data;
            } catch (error) {
                console.error('Erreur lors de la récupération des UAPs:', error);
                this.errorMessage = 'Erreur lors du chargement des UAPs';
            }
        },
        async submitForm() {
            if (!this.$refs.form.validate()) return;

            this.loading = true;
            this.errorMessage = '';

            try {
                await CategoryService.addCategory(this.category);
                this.$emit('category-added');
                this.$emit('showMessage', 'Catégorie ajoutée avec succès', 'success');
                this.closeDialog();
            } catch (error) {
                console.error('Erreur lors de l\'ajout de la catégorie:', error);
                this.$emit('showMessage', 'Nom du catégorie existe déjà', 'error');
            } finally {
                this.loading = false;
            }
        },
        closeDialog() {
            this.dialog = false;
            this.$refs.form.reset();
            this.errorMessage = '';
        }
    },
    mounted() {
        this.fetchUAPs();
    }
}
</script>

<style scoped>
/* Styles de base */
.modal-card {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1) !important;
    overflow: hidden;
}

/* En-tête du modal */
.modal-header {
    background: linear-gradient(135deg, #1867C0, #2196F3);
    color: white;
}

.header-icon {
    animation: bounce 1s ease infinite;
}

/* Styles des champs de saisie */
.input-field {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.input-field:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* Bouton d'ajout */
.add-button {
    transition: all 0.3s ease;
    text-transform: none;
    letter-spacing: 0.5px;
}

.add-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Boutons d'action */
.action-button {
    text-transform: none;
    letter-spacing: 0.5px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.action-button:hover {
    transform: translateY(-1px);
}

/* Message d'erreur */
.error-alert {
    border-radius: 8px;
    animation: slideIn 0.3s ease;
}

/* Bouton de fermeture */
.close-button {
    opacity: 0.8;
    transition: all 0.3s ease;
}

.close-button:hover {
    opacity: 1;
    transform: rotate(90deg);
}

/* Animations */
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-2px); }
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

/* Animation du modal */
.v-dialog-transition-enter-active,
.v-dialog-transition-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.v-dialog-transition-enter-from,
.v-dialog-transition-leave-to {
    transform: scale(0.9);
    opacity: 0;
}
</style>