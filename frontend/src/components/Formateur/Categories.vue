<template>
    <div>
        <v-card style="margin-left: 90px;">
            <div class="page-header pa-6">
                <div class="header-content">
                    <div class="title-container">
                        <h1 class="text-h3 font-weight-bold gradient-text mb-2">Gestion des catégories</h1>
                        <div class="subtitle text-subtitle-1 text-medium-emphasis mb-4">
                            Gérez efficacement les catégories des taches
                        </div>
                    </div>
                    <div class="header-decoration"></div>
                </div>
                <v-divider class="custom-divider"></v-divider>
            </div>

            <!-- Dialog de confirmation de suppression -->
            <v-dialog v-model="deleteDialog" max-width="500px" persistent>
                <v-card class="rounded-lg">
                    <v-card-title class="text-h5 pa-6 bg-error text-white d-flex align-center">
                        <v-icon size="32" class="mr-3">mdi-alert-circle</v-icon>
                        Confirmation de suppression
                    </v-card-title>

                    <!-- Contenu du modal -->
                    <v-card-text class="pa-6 pt-8">
                        <div class="d-flex align-center mb-4">
                            <v-avatar color="error" size="48" class="mr-4">
                                <v-icon size="32" color="white">mdi-delete</v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-h6 mb-1">Supprimer la catégorie</div>
                                <div class="text-body-1 text-medium-emphasis">
                                    Vous êtes sur le point de supprimer les informations de la catégorie :
                                    <strong>{{ itemToDelete ? `${itemToDelete.name} ` : ''
                                        }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="text-body-2 text-medium-emphasis mt-4">
                            Cette action est irréversible. Toutes les données associées seront définitivement
                            supprimées.
                        </div>
                    </v-card-text>

                    <v-card-actions class="pa-6 pt-0">
                        <v-spacer></v-spacer>
                        <v-btn color="grey-darken-1" variant="outlined" class="mr-3 px-6" @click="deleteDialog = false">
                            <v-icon start class="mr-1">mdi-close</v-icon>
                            Annuler
                        </v-btn>
                        <v-btn color="error" class="px-6" @click="confirmDelete" elevation="2">
                            <v-icon start class="mr-1">mdi-delete</v-icon>
                            Confirmer la suppression
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>


            <!-- Snackbar -->
            <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="3000" location="top">
                <div class="d-flex align-center">
                    <v-icon :color="snackbar.color === 'success' ? 'light-green-accent-4' : 'red-accent-2'"
                        class="mr-2">
                        {{ snackbar.color === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle' }}
                    </v-icon>
                    {{ snackbar.text }}
                </div>
                <template v-slot:actions>
                    <v-btn color="white" variant="text" @click="snackbar.show = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </template>
            </v-snackbar>

            <v-card-title class="table-header pa-6">
                <div class="d-flex flex-wrap align-center justify-space-between gap-4">
                    <div class=" pa-4 rounded" style="position: relative;top: 10px;">
                        <AjoutCategorie @category-added="refreshCategories" @showMessage="handleMessage" />
                    </div>
                    <v-text-field v-model="search" prepend-icon="mdi-magnify" placeholder="Rechercher une catégorie..."
                        single-line hide-details class="custom-search-field" variant="outlined" density="compact">
                    </v-text-field>
                </div>
            </v-card-title>

            <v-data-table :headers="headers" :items="paginatedCategories" :search="search"
                :items-per-page="itemsPerPage" class="custom-data-table" hover>

                <template v-slot:item="{ item }">
                    <tr>
                        <td>{{ item.id }}</td>
                        <td>
                            <div class="font-weight-medium">{{ item.name }}</div>
                        </td>
                        <td>
                            <v-chip size="small" color="primary" variant="outlined" class="custom-chip">
                                {{ item.uap_id }}
                            </v-chip>
                        </td>
                        <td>
                            <v-btn color="error" size="small" class="action-btn" elevation="2"
                                @click="openDeleteDialog(item)">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>

                <!-- Pagination -->
                <template v-slot:bottom>
                    <div class="d-flex align-center justify-space-between pa-4">
                        <div class="text-caption text-medium-emphasis">
                            {{ filteredCategories.length }} catégories au total
                        </div>
                        <v-pagination v-model="page" :length="pageCount" total-visible="5"
                            class="custom-pagination"></v-pagination>
                    </div>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
import CategoryService from "@/Services/Categories/Categories";
import AjoutCategorie from "@/components/Formateur/AjoutCategorie.vue";
export default {
    name: "Categories",
    components:{
        AjoutCategorie
    },
    data() {
        return {
            page: 1,
            search: '',
            itemsPerPage: 6,
            deleteDialog: false,
            headers: [
                { title: 'ID', key: 'id' },
                { title: 'Nom Catégorie', key: 'name' },
                { title: 'UAP', key: 'uap_id' },
                { title: 'Actions', key: 'actions', sortable: false }
            ],
            categories: [
                {
                    id: '',
                    name: '',
                    uap_id: '',
                }
            ],
            snackbar: {
                show: false,
                text: '',
                color: '',
                icon: ''
            },
            itemToDelete: null,
        }
    },
    computed: {
        paginatedCategories() {
            const start = (this.page - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.categories.slice(start, end);
        },
        pageCount() {
            return Math.ceil(this.categories.length / this.itemsPerPage);
        },
        filteredCategories() {
            return this.categories.filter(category =>
                category.name.toLowerCase().includes(this.search.toLowerCase()) ||
                String(category.uap_id).includes(this.search) ||
                String(category.id).includes(this.search)
            );
        },
    },
    methods: {
        openDeleteDialog(item) {
            this.itemToDelete = item;
            this.deleteDialog = true;
            console.log(this.itemToDelete);
        },

        async confirmDelete() {
            try {
                await CategoryService.deleteCategory(this.itemToDelete.id);
                this.showSnackbar('Tache supprimé avec succès', 'success');
                this.refreshCategories();
            } catch (error) {
                this.showSnackbar('Erreur lors de la suppression', 'error');
                console.error("Erreur lors de la suppression :", error);
            }
            this.deleteDialog = false;
            this.itemToDelete = null;
        },
        async showCategories() {
            try {
                const data = await CategoryService.showCategories();
                this.categories = data.data;
            } catch (error) {
                this.showSnackbar('Erreur lors de la récupération des employés', 'error');
                console.error("Erreur lors de la récupération des employés :", error);
            }
        },
        refreshCategories() {
            this.showCategories();
        },
        showSnackbar(text, color) {
            this.snackbar.text = text;
            this.snackbar.color = color;
            this.snackbar.icon = color === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle';
            this.snackbar.show = true;
        },
        handleMessage(text, color) {
            this.snackbar.text = text;
            this.snackbar.color = color;
            this.snackbar.show = true;
        },
    },
    mounted() {
        return this.showCategories();
    }
}
</script>
<style>
.page-header {
    background: linear-gradient(120deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 16px 16px 0 0;
    position: relative;
    overflow: hidden;
}

.header-content {
    position: relative;
    z-index: 1;
}

.title-container {
    position: relative;
    padding-left: 1rem;
}

.title-container::before {
    content: '';
    position: absolute;
    left: 0;
    top: 8px;
    bottom: 8px;
    width: 4px;
    background: linear-gradient(to bottom, #1976D2, #2196F3);
    border-radius: 4px;
}

.gradient-text {
    background: linear-gradient(135deg, #1976D2, #2196F3);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    font-size: 2.5rem;
    letter-spacing: -0.5px;
    line-height: 1.2;
    font-weight: 700;
    margin-bottom: 0.5rem;
    animation: fadeIn 0.8s ease-out;
}

.subtitle {
    color: #64748b;
    font-size: 1.1rem;
    font-weight: 400;
    animation: slideIn 0.8s ease-out;
    max-width: 600px;
}

.table-header {
    background: linear-gradient(to right, #f8f9fa 0%, #ffffff 100%);
    border-bottom: 1px solid #e0e0e0;
}

.custom-search-field {
    max-width: 400px;
    transition: all 0.3s ease;
}

.custom-search-field:focus-within {
    max-width: 500px;
}

.custom-chip {
    font-weight: 500;
    letter-spacing: 0.5px;
    transition: all 0.2s ease;
}

.custom-chip:hover {
    transform: translateY(-1px);
}

.action-btn {
    transition: all 0.2s ease;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.custom-pagination {
    margin-top: 16px;
}

@media (max-width: 480px) {
    .page-header {
        padding: 1.5rem !important;
    }

    .gradient-text {
        font-size: 1.75rem;
    }

    .custom-search-field {
        max-width: 100%;
    }
}

@media (max-width: 960px) {
    .page-header {
        text-align: center;
    }

    .gradient-text {
        font-size: 2rem;
    }

    .subtitle {
        font-size: 1rem;
    }

    .custom-search-field {
        width: 100%;
        max-width: none;
    }
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>