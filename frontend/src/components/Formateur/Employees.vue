<template>
  <div>
    <v-card style="margin-left: 90px;">
      <div class="page-header pa-6">
        <div class="header-content">
          <div class="title-container">
            <h1 class="text-h3 font-weight-bold gradient-text mb-2">Gestion des Salariés</h1>
            <div class="subtitle text-subtitle-1 text-medium-emphasis mb-4">
              Gérez efficacement les informations de votre équipe
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
                <div class="text-h6 mb-1">Supprimer l'employé</div>
                <div class="text-body-1 text-medium-emphasis">
                  Vous êtes sur le point de supprimer les informations de l'employé :
                  <strong>{{ itemToDelete ? `${itemToDelete.name} ${itemToDelete.lastname}` : '' }}</strong>
                </div>
              </div>
            </div>
            <div class="text-body-2 text-medium-emphasis mt-4">
              Cette action est irréversible. Toutes les données associées seront définitivement supprimées.
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
          <v-icon :color="snackbar.color === 'success' ? 'light-green-accent-4' : 'red-accent-2'" class="mr-2">
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
            <AjoutSalarie @employee-added="refreshEmployees" @showMessage="handleMessage" />
          </div>
          <v-text-field v-model="search" prepend-icon="mdi-magnify" placeholder="Rechercher un employé..." single-line
            hide-details class="custom-search-field" variant="outlined" density="compact"></v-text-field>
        </div>
      </v-card-title>

      <v-data-table :headers="headers" :items="paginatedPersonnels" :search="search" :items-per-page="itemsPerPage"
        class="custom-data-table" hover>
        <template v-slot:item="{ item }">
          <tr :class="{ 'editing-row': editedItem && editedItem.id === item.id }">
            <td>{{ item.id }}</td>
            <td>
              <template v-if="editedItem && editedItem.id === item.id">
                <v-text-field v-model="editedItem.name" density="compact" variant="outlined" hide-details
                  class="edit-field"></v-text-field>
              </template>
              <template v-else>
                <div class="font-weight-medium">{{ item.name }}</div>
              </template>
            </td>
            <td>
              <template v-if="editedItem && editedItem.id === item.id">
                <v-text-field v-model="editedItem.lastname" density="compact" variant="outlined" hide-details
                  class="edit-field"></v-text-field>
              </template>
              <template v-else>
                <div class="font-weight-medium">{{ item.lastname }}</div>
              </template>
            </td>
            <td>
              <template v-if="editedItem && editedItem.id === item.id">
                <v-text-field v-model="editedItem.uap_id" density="compact" variant="outlined" hide-details
                  class="edit-field"></v-text-field>
              </template>
              <template v-else>
                <v-chip size="small" color="primary" variant="outlined" class="custom-chip">
                  {{ item.uap_id }}
                </v-chip>
              </template>
            </td>
            <td>
              <template v-if="editedItem && editedItem.id === item.id">
                <v-text-field v-model="editedItem.formationIPC" density="compact" variant="outlined" hide-details
                  class="edit-field"></v-text-field>
              </template>
              <template v-else>
                <v-chip size="small" :color="getFormationColor(item.formationIPC)" class="custom-chip">
                  {{ item.formationIPC === 1 ? 'Oui' : 'Non' }}
                </v-chip>
              </template>
            </td>
            <td>
              <template v-if="editedItem && editedItem.id === item.id">
                <v-text-field v-model="editedItem.tauxPolyvalence" density="compact" variant="outlined" hide-details
                  class="edit-field"></v-text-field>
              </template>
              <template v-else>
                <v-progress-linear :model-value="parseFloat(item.tauxPolyvalence)" height="20" rounded>
                  <template v-slot:default="{ value }">
                    <span class="progress-text">{{ value.toFixed(2) }}%</span>
                  </template>
                </v-progress-linear>
              </template>
            </td>
            <td class="actions-column">
              <template v-if="editedItem && editedItem.id === item.id">
                <v-btn color="success" size="small" class="mr-2 action-btn" elevation="2" @click="saveEdit(item)">
                  <v-icon start>mdi-check</v-icon>
                  Confirmer
                </v-btn>
                <v-btn color="error" size="small" class="action-btn" elevation="2" @click="cancelEdit">
                  <v-icon start>mdi-close</v-icon>
                  Annuler
                </v-btn>
              </template>
              <template v-else>
                <v-btn color="primary" size="small" class="mr-2 action-btn" elevation="2" @click="editItem(item)">
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn color="error" size="small" class="action-btn" elevation="2" @click="openDeleteDialog(item)">
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
            </td>
          </tr>
        </template>

        <template v-slot:bottom>
          <div class="d-flex align-center justify-space-between pa-4">
            <div class="text-caption text-medium-emphasis">
              {{ filteredPersonnels.length }} employés au total
            </div>

            <v-pagination v-model="page" :length="pageCount" total-visible="5" class="custom-pagination"></v-pagination>
          </div>
        </template>
      </v-data-table>

    </v-card>

  </div>

</template>

<script>
import AjoutSalarie from "./AjoutSalarie.vue";

import EmployeeService from "@/Services/Employees/EmployeesService";
export default {
  name: "Employees",
  components: {
    AjoutSalarie,
  },
  data() {
    return {
      search: '',
      itemsPerPage: 6,
      editedItem: {
        id: '',
        name: '',
        lastname: '',
        uap_id: '',
        formationIPC: '',
        tauxPolyvalence: ''
      },
      page: 1,
      deleteDialog: false,
      itemToDelete: null,
      snackbar: {
        show: false,
        text: '',
        color: '',
        icon: ''
      },
      employees: [
        {
          id: '',
          name: '',
          lastname: '',
          uap_id: '',
          formationIPC: '',
          tauxPolyvalence: '',
        },
      ],
      headers: [
        { title: 'ID', key: 'id' },
        { title: 'Nom', key: 'name' },
        { title: 'Prénom', key: 'lastname' },
        { title: 'ID UAP', key: 'uap_id' },
        { title: 'Formation IPC', key: 'formationIPC' },
        { title: 'Taux Polyvalence', key: 'tauxPolyvalence' },
        { title: 'Actions', key: 'actions', sortable: false }
      ]
    }
  },
  computed: {
    filteredPersonnels() {
      return this.employees.filter(personnel =>
        personnel.name.toLowerCase().includes(this.search.toLowerCase()) ||
        personnel.lastname.toLowerCase().includes(this.search.toLowerCase())
      );
    },
    pageCount() {
      return Math.ceil(this.employees.length / this.itemsPerPage);
    },
    paginatedPersonnels() {
      const start = (this.page - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.employees.slice(start, end);
    }
  },
  methods: {
    handleMessage(text, color) {
      this.snackbar.text = text;
      this.snackbar.color = color;
      this.snackbar.show = true;
    },
    refreshEmployees() {
      this.showEmployees();
    },
    editItem(item) {
      this.editedItem = item;
    },
    async saveEdit(item) {
      try {
        this.editedItem.formationIPC = Number(this.editedItem.formationIPC);
        await EmployeeService.updateEmployee(item.id, this.editedItem);
        this.editedItem = null;
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
      console.log(this.itemToDelete);
    },
    async confirmDelete() {
      try {
        await EmployeeService.deleteEmployee(this.itemToDelete.id);
        this.showEmployees();
        this.showSnackbar('Employé supprimé avec succès', 'success');
      } catch (error) {
        this.showSnackbar('Erreur lors de la suppression', 'error');
        console.error("Erreur lors de la suppression :", error);
      }
      this.deleteDialog = false;
      this.itemToDelete = null;
    },
    showSnackbar(text, color) {
      this.snackbar.text = text;
      this.snackbar.color = color;
      this.snackbar.icon = color === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle';
      this.snackbar.show = true;
    },
    async showEmployees() {
      try {
        const data = await EmployeeService.afficheEmployees();
        this.employees = data;
      } catch (error) {
        this.showSnackbar('Erreur lors de la récupération des employés', 'error');
        console.error("Erreur lors de la récupération des employés :", error);
      }
    },
    getFormationColor(formation) {
      const colors = {
        '1': 'success',
        '0': 'warning',
      };
      return colors[formation] || 'grey';
    },
  },
  mounted() {
    return this.showEmployees();
  }
}
</script>

<style scoped>
.v-pagination>>>.v-pagination__item--active {
  background-color: #e0e0e0 !important;
  color: rgba(0, 0, 0, 0.87) !important;
}

.v-dialog>.v-card {
  border-radius: 12px;
  overflow: hidden;
}

.v-card-title {
  letter-spacing: 0.5px;
}

.v-btn {
  text-transform: none;
  font-weight: 500;
}

.v-snackbar {
  font-weight: 500;
}

.modal-transition-enter-active,
.modal-transition-leave-active {
  transition: opacity 0.3s ease;
}

.modal-transition-enter-from,
.modal-transition-leave-to {
  opacity: 0;
}

.custom-data-table-card {
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 24px;
}


.table-header {
  background: linear-gradient(to right, #f8f9fa 0%, #ffffff 100%);
  border-bottom: 1px solid #e0e0e0;
}

.v-data-table__th {
  font-weight: 600 !important;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background-color: #f8f9fa !important;
  color: #2c3e50 !important;
  padding: 12px 16px !important;
}

.v-data-table__tr:hover {
  background-color: #f8f9fa !important;
  transition: background-color 0.2s ease;
}

/* .page-header {
  background: linear-gradient(135deg, #f6f7f9 0%, #ffffff 100%);
} */
.stats-card {
  min-width: 200px;
  border: 1px solid #e0e0e0;
  transition: all 0.3s ease;
}

.stats-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.custom-search-field {
  max-width: 400px;
  transition: all 0.3s ease;
}

.custom-search-field:focus-within {
  max-width: 500px;
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


.custom-chip {
  font-weight: 500;
  letter-spacing: 0.5px;
  transition: all 0.2s ease;
}

.custom-chip:hover {
  transform: translateY(-1px);
}

.progress-text {
  color: white;
  font-weight: 500;
  font-size: 0.875rem;
}

.actions-column {
  width: 160px;
  white-space: nowrap;
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

.pagination-btn {
  transition: background-color 0.2s ease;
}

.v-data-table__th {
  font-weight: 600 !important;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background-color: #f5f5f5;
}

/* Animation pour les changements d'état */
.v-enter-active,
.v-leave-active {
  transition: opacity 0.3s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}

/* Style pour la barre de progression */
.v-progress-linear {
  border-radius: 10px !important;
  overflow: hidden;
}

.v-progress-linear__background {
  opacity: 0.2 !important;
}

/* Responsive design */
@media (max-width: 600px) {
  .custom-search-field {
    max-width: 100%;
  }

  .actions-column {
    width: auto;
  }
}

/* Responsive design amélioré */
@media (max-width: 960px) {
  .page-header {
    text-align: center;
  }

  .stats-card {
    width: 100%;
    margin-bottom: 16px;
  }

  .custom-search-field {
    width: 100%;
    max-width: none;
  }
}

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

.header-decoration {
  position: absolute;
  top: -50px;
  right: -50px;
  width: 200px;
  height: 200px;
  background: linear-gradient(135deg, rgba(25, 118, 210, 0.1), rgba(33, 150, 243, 0.1));
  border-radius: 50%;
  transform: rotate(-45deg);
  opacity: 0.6;
}

.custom-divider {
  border-color: rgba(25, 118, 210, 0.1) !important;
  margin-top: 1.5rem !important;
  margin-bottom: 1rem !important;
  position: relative;
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

/* Responsive design */
@media (max-width: 768px) {
  .gradient-text {
    font-size: 2rem;
  }

  .subtitle {
    font-size: 1rem;
  }

  .header-decoration {
    display: none;
  }
}

@media (max-width: 480px) {
  .gradient-text {
    font-size: 1.75rem;
  }

  .page-header {
    padding: 1.5rem !important;
  }
}
</style>