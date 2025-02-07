<template>
    <div>
        <!-- Bouton pour ouvrir le modal avec animation améliorée -->
        <v-btn 
            color="primary" 
            class="add-button px-6"
            prepend-icon="mdi-plus"
            elevation="2"
            rounded
            @click="dialog = true"
        >
            Nouvelle Tâche
        </v-btn>

        <!-- Modal du formulaire avec animations -->
        <v-dialog v-model="dialog" max-width="700px" persistent class="modal-container">
            <v-card class="modal-card rounded-xl">
                <!-- En-tête du modal avec design amélioré -->
                <v-card-title class="modal-header d-flex align-center pa-6">
                    <v-avatar color="white" size="32" class="mr-3 header-avatar">
                        <v-icon color="primary">mdi-clipboard-plus</v-icon>
                    </v-avatar>
                    <span class="text-h5 text-white font-weight-medium">Ajouter une nouvelle tâche</span>
                    <v-spacer></v-spacer>
                    <v-btn icon variant="text" @click="closeDialog" class="close-button">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>

                <!-- Formulaire avec animations et styles améliorés -->
                <v-form ref="form" v-model="valid" @submit.prevent="submitForm">
                    <v-card-text class="pa-6">
                        <v-row>
                            <!-- Nom de la tâche -->
                            <v-col cols="12">
                                <v-text-field
                                    v-model="formData.name"
                                    label="Nom de la tâche"
                                    :rules="nameRules"
                                    variant="outlined"
                                    density="comfortable"
                                    placeholder="Entrez le nom de la tâche"
                                    prepend-inner-icon="mdi-format-title"
                                    class="input-field"
                                    bg-color="grey-lighten-4"
                                    required
                                ></v-text-field>
                            </v-col>

                            <!-- Description avec style amélioré -->
                            <v-col cols="12">
                                <v-textarea
                                    v-model="formData.description"
                                    label="Description"
                                    variant="outlined"
                                    density="comfortable"
                                    placeholder="Décrivez la tâche en détail"
                                    prepend-inner-icon="mdi-text-box-outline"
                                    class="input-field"
                                    bg-color="grey-lighten-4"
                                    auto-grow
                                    rows="3"
                                ></v-textarea>
                            </v-col>

                            <!-- UAP Selection avec design moderne -->
                            <v-col cols="12" md="6">
                                <v-select
                                    v-model="formData.uap_id"
                                    :items="uaps"
                                    item-title="name"
                                    item-value="id"
                                    label="Sélectionner l'UAP"
                                    :rules="uapRules"
                                    variant="outlined"
                                    density="comfortable"
                                    prepend-inner-icon="mdi-office-building"
                                    class="input-field"
                                    bg-color="grey-lighten-4"
                                    required
                                ></v-select>
                            </v-col>

                            <!-- Catégorie Selection avec design moderne -->
                            <v-col cols="12" md="6">
                                <v-select
                                    v-model="formData.categorie_id"
                                    :items="categories"
                                    item-title="name"
                                    item-value="id"
                                    label="Sélectionner la catégorie"
                                    :rules="categoryRules"
                                    variant="outlined"
                                    density="comfortable"
                                    prepend-inner-icon="mdi-shape"
                                    class="input-field"
                                    bg-color="grey-lighten-4"
                                    required
                                ></v-select>
                            </v-col>
                        </v-row>
                    </v-card-text>

                    <!-- Actions du formulaire avec animations -->
                    <v-card-actions class="pa-6 pt-0">
                        <v-spacer></v-spacer>
                        <v-btn
                            variant="outlined"
                            color="grey-darken-1"
                            class="action-button mr-3 px-6"
                            @click="closeDialog"
                        >
                            <v-icon start class="mr-1">mdi-close</v-icon>
                            Annuler
                        </v-btn>
                        <v-btn
                            color="primary"
                            type="submit"
                            :loading="loading"
                            :disabled="!valid || loading"
                            class="action-button px-6"
                            elevation="2"
                        >
                            <v-icon start class="mr-1">mdi-check</v-icon>
                            Enregistrer
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import TaskService from "@/Services/Tasks/Tasks";
import CategoryService from "@/Services/Categories/Categories";

export default {
    name: 'AjoutTache',
    emits: ['task-added', 'showMessage'],

    data() {
        return {
            dialog: false,
            valid: false,
            loading: false,
            formData: {
                name: '',
                description: '',
                uap_id: null,
                categorie_id: null,
            },
            nameRules: [
                v => !!v || 'Le nom est requis',
                v => (v && v.length >= 3) || 'Le nom doit contenir au moins 3 caractères',
            ],
            uapRules: [
                v => !!v || "L'UAP est requise",
            ],
            categoryRules: [
                v => !!v || 'La catégorie est requise',
            ],
            uaps: [],
            categories: [],
        }
    },

    methods: {
        async submitForm() {
            if (!this.$refs.form.validate()) return;

            this.loading = true;
            try {
                await TaskService.addTask(this.formData);
                this.$emit('showMessage', 'Tâche ajoutée avec succès', 'success');
                this.$emit('task-added');
                this.closeDialog();
            } catch (error) {
                console.error('Erreur lors de la création de la tâche:', error);
                this.$emit('showMessage', "Erreur lors de l'ajout de la tâche", 'error');
            } finally {
                this.loading = false;
            }
        },

        closeDialog() {
            this.dialog = false;
            this.$refs.form.reset();
            this.formData = {
                name: '',
                description: '',
                uap_id: null,
                categorie_id: null,
            };
        },

        async fetchUaps() {
            try {
                const response = await CategoryService.showUAPs();
                this.uaps = response.data;
            } catch (error) {
                console.error('Erreur lors de la récupération des UAPs:', error);
                this.$emit('showMessage', 'Erreur lors du chargement des UAPs', 'error');
            }
        },

        async fetchCategories() {
            try {
                const response = await CategoryService.showCategories();
                this.categories = response.data;
            } catch (error) {
                console.error('Erreur lors de la récupération des catégories:', error);
                this.$emit('showMessage', 'Erreur lors du chargement des catégories', 'error');
            }
        },
    },

    mounted() {
        this.fetchUaps();
        this.fetchCategories();
    },
}
</script>

<style scoped>
/* Styles de base améliorés */
.modal-card {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* En-tête du modal */
.modal-header {
    background: linear-gradient(135deg, #1867C0, #2196F3);
    position: relative;
    overflow: hidden;
}

/* Animation de l'avatar */
.header-avatar {
    animation: pulse 2s infinite;
    box-shadow: 0 0 0 rgba(255, 255, 255, 0.4);
}

/* Champs de formulaire */
.input-field {
    transition: all 0.3s ease;
    border-radius: 8px;
}

.input-field:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.input-field:focus-within {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Bouton d'ajout */
.add-button {
    transition: all 0.3s ease;
    text-transform: none !important;
    letter-spacing: 0.5px;
}

.add-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Boutons d'action */
.action-button {
    transition: all 0.3s ease;
    text-transform: none !important;
    letter-spacing: 0.5px;
    border-radius: 8px;
}

.action-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
    }
}

/* Animation du modal */
.modal-container {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.v-dialog-transition-enter-active,
.v-dialog-transition-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.v-dialog-transition-enter-from,
.v-dialog-transition-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>