<template>
    <div>
        <v-card class="trainer-management-card elevation-3">
            <div class="page-header pa-6">
                <div class="header-content">
                    <div class="title-container">
                        <h1 class="text-h3 font-weight-bold gradient-text mb-2">Gestion des Formateurs</h1>
                        <div class="subtitle text-subtitle-1 text-medium-emphasis mb-4">
                            Gérez efficacement vos ressources de formation
                        </div>
                    </div>
                    <div class="header-decoration"></div>
                </div>
                <v-divider class="custom-divider"></v-divider>
            </div>

            <!-- Delete Confirmation Dialog -->
            <v-dialog v-model="deleteDialog" max-width="500px" persistent>
                <v-card class="rounded-lg">
                    <v-card-title class="text-h5 pa-6 bg-error text-white d-flex align-center">
                        <v-icon size="32" class="mr-3">mdi-alert-circle</v-icon>
                        Confirmation de suppression
                    </v-card-title>

                    <v-card-text class="pa-6 pt-8">
                        <div class="d-flex align-center mb-4">
                            <v-avatar color="error" size="48" class="mr-4">
                                <v-icon size="32" color="white">mdi-delete</v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-h6 mb-1">Supprimer le formateur</div>
                                <div class="text-body-1 text-medium-emphasis">
                                    Vous êtes sur le point de supprimer les informations du formateur :
                                    <strong>{{ itemToDelete ? `${itemToDelete.name} ${itemToDelete.lastname}` : ''
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
                    <AjoutFormateur @showMessage="showSnackbar" @refreshData="showFormateurs"/>
                    <v-text-field v-model="search" prepend-icon="mdi-magnify" placeholder="Rechercher un formateur..."
                        single-line hide-details class="custom-search-field" variant="outlined"
                        density="compact"></v-text-field>
                </div>
            </v-card-title>

            <v-data-table :headers="headers" :items="paginatedTrainers" :search="search" :items-per-page="itemsPerPage"
                class="custom-data-table" hover>
                <template v-slot:item="{ item }">
                    <tr :class="{ 'editing-row': editedItem && editedItem.id === item.id }">
                        <td>{{ item.id }}</td>
                        <td>
                            <template v-if="editedItem && editedItem.id === item.id">
                                <div v-if="profilePic">
                                    <img :src="profilePic" alt="Profile Image" width="40">
                                </div>
                                <div v-else-if="item.image">
                                    <v-avatar size="40" class="mr-3">
                                        <v-img :src="'http://127.0.0.1:8000/storage/' + item.image"
                                            alt="Trainer Image"></v-img>
                                    </v-avatar>
                                </div>
                                <div v-else>
                                    <v-avatar size="40" class="profile-avatar">
                                        <v-icon size="30" color="white">mdi-account</v-icon>
                                    </v-avatar>
                                </div>

                                <label for="photo-upload" class="camera-btn">
                                    <i class="fas fa-camera"></i>
                                </label>

                                <input type="file" id="photo-upload" class="d-none" accept="image/*"
                                    @change="onPhotoChange" />
                            </template>
                            <template v-else-if="item.image">
                                <v-avatar size="40" class="mr-3">
                                    <v-img :src="'http://127.0.0.1:8000/storage/' + item.image"
                                        alt="Trainer Image"></v-img>
                                </v-avatar>

                            </template>
                            <template v-else>
                                <v-avatar size="40" class="profile-avatar">
                                    <v-icon size="30" color="white">mdi-account</v-icon>
                                </v-avatar>
                            </template>
                        </td>
                        <td>
                            <template v-if="editedItem && editedItem.id === item.id">
                                <v-text-field v-model="editedItem.name" density="compact" variant="outlined"
                                    hide-details class="edit-field"></v-text-field>
                            </template>
                            <template v-else>
                                <div class="font-weight-medium">{{ item.name }}</div>
                            </template>
                        </td>
                        <td>
                            <template v-if="editedItem && editedItem.id === item.id">
                                <v-text-field v-model="editedItem.lastname" density="compact" variant="outlined"
                                    hide-details class="edit-field"></v-text-field>
                            </template>
                            <template v-else>
                                <div class="font-weight-medium">{{ item.lastname }}</div>
                            </template>
                        </td>
                        <td>
                            <template v-if="editedItem && editedItem.id === item.id">
                                <v-text-field v-model="editedItem.email" density="compact" variant="outlined"
                                    hide-details class="edit-field"></v-text-field>
                            </template>
                            <template v-else>
                                <div class="font-weight-medium">{{ item.email }}</div>
                            </template>
                        </td>
                        <td class="actions-column">
                            <template v-if="editedItem && editedItem.id === item.id">
                                <v-btn color="success" size="small" class="mr-2 action-btn" elevation="2"
                                    @click="saveEdit(item)">
                                    <v-icon start>mdi-check</v-icon>
                                    Confirmer
                                </v-btn>
                                <v-btn color="error" size="small" class="action-btn" elevation="2" @click="cancelEdit">
                                    <v-icon start>mdi-close</v-icon>
                                    Annuler
                                </v-btn>
                            </template>
                            <template v-else>
                                <v-btn color="primary" size="small" class="mr-2 action-btn" elevation="2"
                                    @click="editItem(item)">
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn color="error" size="small" class="action-btn" elevation="2"
                                    @click="openDeleteDialog(item)">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </template>
                        </td>
                    </tr>
                </template>

                <template v-slot:bottom>
                    <div class="d-flex align-center justify-space-between pa-4">
                        <div class="text-caption text-medium-emphasis">
                            {{ filteredTrainers.length }} formateurs au total
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
import Admin_Service from '@/Services/Admin/Admin';
import AjoutFormateur from "./AjoutFormateur.vue";

import { defineComponent } from "vue";
export default defineComponent ({
    name: "Gestion Formateur",
    components:{
        AjoutFormateur
    },
    data() {
        return {
            profilePic:null,
            search: '',
            itemsPerPage: 6,
            page: 1,
            deleteDialog: false,
            itemToDelete: null,
            editedItem: {
                id: '',
                name: '',
                lastname: '',
                email: '',
                image: null
            },
            snackbar: {
                show: false,
                text: '',
                color: '',
                icon: ''
            },
            trainers: [
                {
                    id: '',
                    image: '',
                    name: '',
                    lastname: '',
                    email: ''
                },
            ],
            headers: [
                { title: 'ID', key: 'id' },
                { title: 'Image', key: 'image' },
                { title: 'Nom', key: 'name' },
                { title: 'Prénom', key: 'lastname' },
                { title: 'Email', key: 'email' },
                { title: 'Actions', key: 'actions', sortable: false }
            ]
        }
    },
    computed: {
        filteredTrainers() {
            return this.trainers.filter(trainer =>
                trainer.name.toLowerCase().includes(this.search.toLowerCase()) ||
                trainer.lastname.toLowerCase().includes(this.search.toLowerCase()) ||
                trainer.email.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        pageCount() {
            return Math.ceil(this.trainers.length / this.itemsPerPage);
        },
        paginatedTrainers() {
            const start = (this.page - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.trainers.slice(start, end);
        }
    },
    methods: {
        onPhotoChange(event) {
        const file = event.target.files[0];
        if (file) {
          this.trainers.image=file;
        //   this.editItem.image=file;
          const reader = new FileReader();
          reader.onload = (e) => {
            this.profilePic = e.target.result;
          };
          reader.readAsDataURL(file);
        }
      },
        showSnackbar(text, color) {
            this.snackbar.text = text;
            this.snackbar.color = color;
            this.snackbar.show = true;
        },
        editItem(item) {
            this.editedItem = item;
        },
        async saveEdit(item) {
            const basicInfo = {
            name: item.name,
            lastname: item.lastname,
            email: item.email,
            image: this.trainers.image
            };
            try {
                await Admin_Service.updateFormateur(item.id, basicInfo);
                this.showFormateurs();
                this.editedItem = null;
                this.profilePic=null;
                this.showSnackbar('Informations modifiés avec succé', 'success');
            } catch (error) {
                this.showSnackbar('Erreur lors de la modification', 'error');
                console.log("Une erreur lors de l'update", error);
            }
        },
        cancelEdit() {
            this.editedItem = null;
        },
        openDeleteDialog(item) {
            this.itemToDelete = item;
            this.deleteDialog = true;
        },
        async confirmDelete() {
            try {
                await Admin_Service.deleteFormateur(this.itemToDelete.id);
                this.showFormateurs();
                this.showSnackbar('Formateur supprimé avec succès', 'success');
            } catch (error) {
                this.showSnackbar('Erreur lors de la suppression', 'error');
                console.error("Erreur lors de la suppression :", error);
            }
            this.deleteDialog = false;
            this.itemToDelete = null;
        },
        async showFormateurs() {
            try {
                const data = await Admin_Service.showFormateurs();
                this.trainers = data.data;
            } catch (error) {
                this.showSnackbar('Erreur lors de la récupération des formateurs', 'error');
                console.error("Erreur lors de la récupération des formateurs :", error);
            }
        },
    },
    mounted() {
        return this.showFormateurs();
    }
})
</script>

<style scoped>
/* Most styles are kept from the previous component */
.trainer-management-card {
    background-color: #f8f9fa;
    border-radius: 16px;
    margin-left: 250px;
    padding: 1.5rem;
    height: 100vh;
}

.add-trainer-btn {
    text-transform: none;
    letter-spacing: 0.5px;
    font-weight: 500;
}

/* Responsive image avatars */
.v-avatar {
    border: 2px solid #e0e0e0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Additional responsive design enhancements */
@media (max-width: 600px) {
    .table-header {
        flex-direction: column;
        align-items: stretch !important;
    }

    .add-trainer-btn {
        margin-bottom: 16px;
    }
}

.custom-data-table-card {
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 24px;
}

.custom-data-table {
    background-color: white;
}

.custom-data-table>>>.v-data-table__tr:hover {
    background-color: #f5f7fa !important;
    transition: background-color 0.2s ease;
}

.editing-row {
    background-color: #e3f2fd !important;
}

.edit-field {
    min-width: 120px;
}

.actions-column {
    width: 160px;
    white-space: nowrap;
}

.camera-btn {
    position: absolute;
    bottom: 7px;
    right: 54%;
    background: white;
    color: var(--primary-color);
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    z-index: 3;
    cursor: pointer;
    transition: all 0.3s ease;
}

.camera-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}
</style>