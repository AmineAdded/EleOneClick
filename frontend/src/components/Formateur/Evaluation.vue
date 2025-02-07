<template>
  <div>
    <v-card style="margin-left: 90px;">
      <div class="page-header pa-6">
        <div class="header-content">
          <div class="title-container">
            <h1 class="text-h3 font-weight-bold gradient-text mb-2">Gestion des évaluations</h1>
            <div class="subtitle text-subtitle-1 text-medium-emphasis mb-4">
              Gérez efficacement les évaluations de votre équipe
            </div>
          </div>
          <div class="header-decoration"></div>
        </div>
        <v-divider class="custom-divider"></v-divider>
      </div>

      <!-- Snackbar -->
      <v-snackbar
        v-model="snackbar.show"
        :color="snackbar.color"
        :timeout="3000"
        location="top"
      >
        <div class="d-flex align-center">
          <v-icon
            :color="snackbar.color === 'success' ? 'light-green-accent-4' : 'red-accent-2'"
            class="mr-2"
          >
            {{ snackbar.color === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle' }}
          </v-icon>
          {{ snackbar.text }}
        </div>
        <template v-slot:actions>
          <v-btn
            color="white"
            variant="text"
            @click="snackbar.show = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </template>
      </v-snackbar>

      <!-- Barre de recherche -->
      <v-card-title class="table-header pa-6">
        <div class="d-flex flex-wrap align-center justify-space-between gap-4">
          <v-text-field
            v-model="search"
            prepend-icon="mdi-magnify"
            placeholder="Rechercher un employé..."
            single-line
            hide-details
            class="custom-search-field"
            variant="outlined"
            density="compact"
          ></v-text-field>
        </div>
      </v-card-title>

      <!-- Tableau -->
      <v-data-table
        :headers="headers"
        :items="paginatedPersonnels"
        :search="search"
        :items-per-page="itemsPerPage"
        class="custom-data-table"
        hover
      >
        <template v-slot:item="{ item }">
          <tr>
            <td>{{ item.uap_id }}</td>
            <td>
              <v-chip
                size="small"
                color="primary"
                variant="outlined"
                class="custom-chip"
              >
                {{ item.salarie_id }}
              </v-chip>
            </td>
            <td>
              <div class="font-weight-medium">{{ item.name }}</div>
            </td>
            <td>
              <div class="font-weight-medium">{{ item.lastname }}</div>
            </td>
            <td>
              <v-progress-linear
                :model-value="parseFloat(item.tauxPolyvalence)"
                height="20"
                rounded
              >
                <template v-slot:default="{ value }">
                  <span class="progress-text">{{ value.toFixed(2) }}%</span>
                </template>
              </v-progress-linear>
            </td>
            <td class="actions-column">
              <v-btn
                color="primary"
                size="small"
                class="mr-2 action-btn"
                elevation="2"
                @click="editItem(item)"
              >
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
            </td>
          </tr>
        </template>

        <!-- Pagination -->
        <template v-slot:bottom>
          <div class="d-flex align-center justify-space-between pa-4">
            <div class="text-caption text-medium-emphasis">
              {{ filteredPersonnels.length }} employés au total
            </div>
            <v-pagination
              v-model="page"
              :length="pageCount"
              total-visible="5"
              class="custom-pagination"
            ></v-pagination>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <UpdateEmployee
    v-model="showUpdateDialog"
    :employee="selectedEmployee"
    :tasks="tasks"
    @evaluation-saved="handleEvaluationSaved"
  />
  </div>
</template>

<script>
import Evaluation_Serivce from "@/Services/Evaluation/Evaluation";

import UpdateEmployee from "@/components/Formateur/UpdateEmployee.vue";
export default {
  name: "Evaluation",
  data() {
    return {
      selectedEmployee: null,
      showUpdateDialog: false,
      snackbar: {
        show: false,
        text: '',
        color: '',
      },
      search: '',
      itemsPerPage: 6,
      page: 1,
      headers: [
        { title: 'ID UAP', key: 'uap_id' },
        { title: 'ID Salarié', key: 'salarie_id' },
        { title: 'Nom', key: 'name' },
        { title: 'Prénom', key: 'lastname' },
        { title: 'Taux Polyvalence', key: 'tauxPolyvalence' },
        { title: 'Actions', key: 'actions', sortable: false },
      ],
      employees: [],
      tasks: [],
    };
  },
  components:{
    UpdateEmployee,
  },
  computed: {
    pageCount() {
      return Math.ceil(this.employees.length / this.itemsPerPage);
    },
    paginatedPersonnels() {
      const start = (this.page - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.employees.slice(start, end);
    },
    filteredPersonnels() {
      return this.employees.filter(personnel =>
        personnel.name.toLowerCase().includes(this.search.toLowerCase()) ||
        personnel.lastname.toLowerCase().includes(this.search.toLowerCase())
      );
    },
  },
  methods: {
    async showEvaluation() {
      try {
        const response = await Evaluation_Serivce.showEvaluation();

        this.employees = [];
        this.tasks=[];
        for (const uap of response.data) {
          for (const salarie of uap.salaries) {
            this.employees.push({
              uap_id: uap.id,
              salarie_id: salarie.id,
              name: salarie.name,
              lastname: salarie.lastname,
              tauxPolyvalence: salarie.tauxPolyvalence,
            });
          }
          for(const task of uap.taches){
            this.tasks.push({
              tache_id:task.id,
              name:task.name,
              uap_id:task.uap_id,
              category_id:task.category_id
            });
          }
        }
      } catch (error) {
        console.error("Erreur lors de la récupération des données :", error);
        this.snackbar = {
          show: true,
          text: "Erreur lors de la récupération des données",
          color: "red",
        };
      }
    },
    editItem(item) {
    this.selectedEmployee = item;
    this.showUpdateDialog = true;
  },
  async handleEvaluationSaved(evaluationData) {
    try{
      await Evaluation_Serivce.UpdateEmployee(evaluationData);
      this.showEvaluation();
      this.snackbar = {
          show: true,
          text: "Evaluation modifié avec succé",
          color: "success",
        };

    }catch(error){
      console.log("Une erreur s'est produite lors de la modification des évaluations",error);
      this.snackbar = {
          show: true,
          text: "Erreur lors de la modification des évaluations",
          color: "red",
        };
    } 
  }
  },
  mounted() {
    this.showEvaluation();
  },
};
</script>

<style>
.progress-text {
  color: white;
  font-weight: 500;
  font-size: 0.875rem;
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
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
  .v-dialog > .v-card {
  border-radius: 12px;
  overflow: hidden;
}

.v-card-title {
  letter-spacing: 0.5px;
}
.custom-pagination {
  margin-top: 16px;
}
.custom-data-table-card {
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 24px;
}
.custom-data-table {
  background-color: white;
}

.custom-data-table >>> .v-data-table__tr:hover {
  background-color: #f5f7fa !important;
  transition: background-color 0.2s ease;
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
@media (max-width: 960px) {
  .page-header {
    text-align: center;
    padding: 1.5rem !important;
  }
  .subtitle {
    font-size: 1rem;
  }
  .header-decoration {
    display: none;
  }
  .custom-search-field {
    max-width: 100%;
  }
  .actions-column {
    width: auto;
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
</style>