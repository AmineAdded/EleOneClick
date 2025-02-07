<template>
  <div>
    <v-dialog v-model="dialog" max-width="900px" persistent>
      <v-card class="rounded-lg elevation-3">
        <!-- En-tête professionnel -->
        <v-card-title class="text-h5 pa-6 bg-primary text-white d-flex align-center">
          <v-icon size="28" class="mr-3">mdi-shield-account</v-icon>
          Évaluation des compétences
        </v-card-title>

        <v-card-text class="pa-6" style="background: #f8fafc">
          <!-- Section employé -->
          <div class="employee-info mb-6">
            <v-card class="elevation-1 pa-4 rounded-lg">
              <div class="d-flex align-center">
                <v-avatar class="mr-4" size="64" color="primary">
                  <v-icon size="32" color="white">mdi-account</v-icon>
                </v-avatar>
                <div>
                  <div class="text-h5 font-weight-medium mb-2">
                    {{ employee.name }} {{ employee.lastname }}
                  </div>
                  <div class="d-flex gap-2">
                    <v-chip color="primary" variant="flat" size="small" class="font-weight-medium">
                      <v-icon start size="16">mdi-identifier</v-icon>
                      UAP: {{ employee.uap_id }}
                    </v-chip>
                    <v-chip color="secondary" variant="flat" size="small" class="font-weight-medium">
                      <v-icon start size="16">mdi-card-account-details</v-icon>
                      ID: {{ employee.salarie_id }}
                    </v-chip>
                  </div>
                </div>
              </div>
            </v-card>
          </div>

          <!-- Liste des tâches -->
          <v-card class="mb-6 rounded-lg elevation-1">
            <v-card-title class="text-subtitle-1 font-weight-medium pa-4">
              <v-icon color="primary" size="24" class="mr-2">mdi-clipboard-check</v-icon>
              Liste des tâches à évaluer
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text class="pa-4">
              <div class="tasks-grid">
                <div v-for="task in filteredTasks" :key="task.tache_id" class="task-evaluation-item">
                  <div class="d-flex align-center justify-space-between">
                    <div class="task-name text-subtitle-1 font-weight-medium">
                      <v-icon size="20" :color="getTaskIconColor(task)" class="mr-2">
                        mdi-checkbox-marked-circle
                      </v-icon>
                      {{ task.name }}
                    </div>
                    <v-select v-model="taskEvaluations[task.tache_id]" :items="evaluationLevels" item-title="label"
                      item-value="value" variant="outlined" density="comfortable" hide-details
                      class="evaluation-select">
                      <template v-slot:selection="{ item }">
                        <v-chip :style="{
                          backgroundColor: getEvaluationColor(item.raw.value?item.raw.value:'D'),
                          color: 'white'
                        }" size="small" class="font-weight-medium" label>
                          <v-icon start size="16" class="mr-1">
                            {{ getEvaluationIcon(item.raw.value?item.raw.value:'D') }}
                          </v-icon>
                          {{ item.raw.label }}
                        </v-chip>
                      </template>
                    </v-select>
                  </div>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-card-text>

        <v-divider></v-divider>

        <!-- Actions -->
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn color="grey-darken-1" variant="outlined" @click="closeDialog" class="mr-3" prepend-icon="mdi-close">
            Annuler
          </v-btn>
          <v-btn color="primary" @click="saveEvaluation" :loading="loading" prepend-icon="mdi-check">
            Enregistrer
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import Evaluation_Serivce from "@/Services/Evaluation/Evaluation";
export default {
  name: "UpdateEmployee",
  props: {
    employee: {
      type: Object,
      required: true,
    },
    tasks: {
      type: Array,
      required: true,
    },
    modelValue: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      dialog: false,
      loading: false,
      taskEvaluations: {},
      evaluations: [],
      evaluationLevels: [
        { value: "A", label: "A - Maîtrise" },
        { value: "B", label: "B - Autonome" },
        { value: "C", label: "C - En cours" },
        { value: "D", label: "D - Débutant" },
      ],
    };
  },
  computed: {
    filteredTasks() {
      // Filtrer les tâches spécifiques à l'UAP du salarié
      return this.tasks.filter((task) => task.uap_id === this.employee.uap_id);
    },
  },
  methods: {
    getEvaluationColor(level) {
      const colors = {
        A: "#2563eb", // Bleu
        B: "#16a34a", // Vert
        C: "#ea580c", // Orange
        D: "#4b5563", // Gris
      };
      return colors[level] || "#4b5563";
    },
    getEvaluationIcon(level) {
      const icons = {
        A: "mdi-star",
        B: "mdi-check-circle",
        C: "mdi-trending-up",
        D: "mdi-school",
      };
      return icons[level] || "mdi-help-circle";
    },
    getTaskIconColor(task) {
      return this.taskEvaluations[task.tache_id]
        ? this.getEvaluationColor(this.taskEvaluations[task.tache_id])
        : "#6B7280";
    },
    closeDialog() {
      this.dialog = false;
      this.$emit("update:modelValue", false);
      this.showEvaluationTask();
    },
    async saveEvaluation() {
      this.loading = true;
      try {
        this.$emit("evaluation-saved", {
          employeeId: this.employee.salarie_id,
          evaluations: this.taskEvaluations,
        });
        this.closeDialog();
      } catch (error) {
        console.error("Erreur lors de la sauvegarde:", error);
      } finally {
        this.loading = false;
      }
    },

async showEvaluationTask() {
  try {
    // Réinitialiser taskEvaluations pour éviter l'accumulation des tâches
    this.taskEvaluations = {};

    const res = await Evaluation_Serivce.showEvaluationTask(this.employee.salarie_id);
    if (Array.isArray(res)) {
      this.evaluations = res;
    } else if (res && res.data) {
      this.evaluations = res.data;
    } else {
      this.evaluations = [];
    }

    // Initialiser taskEvaluations avec les évaluations disponibles
    this.evaluations.forEach((evaluation) => {
      this.taskEvaluations[evaluation.tache_id] = evaluation.noteEvaluation;
    });

    // Ajouter les tâches manquantes avec "D" comme valeur par défaut
    this.filteredTasks.forEach(task => {
      if (!Object.prototype.hasOwnProperty.call(this.taskEvaluations, task.tache_id)) {
        this.taskEvaluations[task.tache_id] = "D";
      }
    });

  } catch (error) {
    console.log("Erreur lors de la récupération des évaluations de l'employé", error);
  }
}

    
  },
  watch: {
    modelValue(val) {
      this.dialog = val;
      if (val) {
        this.showEvaluationTask();
      }
    },
  },
  // mounted() {
  //   if (this.employee) {
  //     this.showEvaluationTask();
  //   }
  // },
};
</script>

<style scoped>
.tasks-grid {
  display: grid;
  gap: 16px;
  grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
}

.task-evaluation-item {
  padding: 16px;
  border-radius: 8px;
  background-color: white;
  border: 1px solid #e2e8f0;
  transition: all 0.2s ease;
}

.task-evaluation-item:hover {
  background-color: #f8fafc;
}

.evaluation-select {
  min-width: 180px;
}

.task-name {
  margin-right: 16px;
  flex: 1;
  color: #1e293b;
}

@media (max-width: 600px) {
  .tasks-grid {
    grid-template-columns: 1fr;
  }

  .task-evaluation-item {
    padding: 12px;
  }

  .evaluation-select {
    min-width: 160px;
  }
}
</style>