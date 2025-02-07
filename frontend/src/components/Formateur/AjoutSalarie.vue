<template>
    <v-dialog v-model="dialog" max-width="600px" persistent>
      <template v-slot:activator="{ props }">
  <div class="button-wrapper">
    <v-btn
      v-bind="props"
      class="custom-add-btn"
      elevation="2"
      color="primary"
    >
      <div class="btn-content">
        <v-icon
          icon="mdi-account-plus"
          class="custom-icon"
          size="24"
        ></v-icon>
        <span class="btn-text">Ajouter un salarié</span>
      </div>
      <div class="btn-background"></div>
    </v-btn>
  </div>
</template>
  
      <v-card class="rounded-lg">
        <v-card-title class="d-flex align-center pa-6 bg-primary text-white">
          <v-icon size="28" class="mr-3">mdi-account-plus</v-icon>
          <span class="text-h5 font-weight-medium">Ajouter un nouveau salarié</span>
          <v-spacer></v-spacer>
          <v-btn
            icon="mdi-close"
            variant="text"
            color="white"
            @click="closeDialog"
            class="close-btn"
          ></v-btn>
        </v-card-title>
  
        <v-card-text class="pa-6">
          <v-form ref="form" v-model="isFormValid" @submit.prevent="submitForm">
            <div class="form-grid">
              <v-text-field
                v-model="formData.lastname"
                label="Nom"
                :rules="nameRules"
                variant="outlined"
                density="comfortable"
                placeholder="Entrez le nom"
                prepend-inner-icon="mdi-account"
                hide-details="auto"
                class="mb-4"
                required
              ></v-text-field>
  
              <v-text-field
                v-model="formData.name"
                label="Prénom"
                :rules="lastnameRules"
                variant="outlined"
                density="comfortable"
                placeholder="Entrez le prénom"
                prepend-inner-icon="mdi-account"
                hide-details="auto"
                class="mb-4"
                required
              ></v-text-field>
  
              <v-select
                v-model="formData.uap_id"
                label="ID UAP"
                :items="uapOptions"
                :rules="uapRules"
                variant="outlined"
                density="comfortable"
                placeholder="Sélectionnez l'UAP"
                prepend-inner-icon="mdi-office-building"
                hide-details="auto"
                class="mb-4"
                required
              ></v-select>
  
              <v-radio-group
                v-model="formData.formationIPC"
                label="Formation IPC"
                :rules="formationRules"
                hide-details="auto"
                class="mb-4"
                required
              >
                <v-radio
                  label="Oui"
                  :value="1"
                  color="success"
                ></v-radio>
                <v-radio
                  label="Non"
                  :value="0"
                  color="error"
                ></v-radio>
              </v-radio-group>
            </div>
          </v-form>
        </v-card-text>
  
        <v-divider></v-divider>
  
        <v-card-actions class="pa-6">
          <v-spacer></v-spacer>
          <v-btn
            variant="outlined"
            color="grey-darken-1"
            class="mr-3 text-none"
            @click="closeDialog"
            prepend-icon="mdi-close"
          >
            Annuler
          </v-btn>
          <v-btn
            color="primary"
            class="text-none"
            :loading="loading"
            :disabled="!isFormValid"
            @click="submitForm"
            prepend-icon="mdi-check"
            elevation="2"
          >
            Ajouter le salarié
          </v-btn>
        </v-card-actions>
      </v-card>

    </v-dialog>
  </template>
  
  <script>
  import EmployeeService from "@/Services/Employees/EmployeesService";
  
  export default {
    name: 'AjoutSalarie',
    
    data() {
      return {
        dialog: false,
        loading: false,
        isFormValid: false,
        formData: {
          name: '',
          lastname: '',
          uap_id: null,
          formationIPC: null,
          tauxPolyvalence: 0,
          tauxPolyvalencePotentiel: 0,
        },
        uapOptions: [
          { title: 'UAP 1', value: 1 },
          { title: 'UAP 2', value: 2 },
          { title: 'UAP 3', value: 3 },
          { title: 'UAP 4', value: 4 },
          { title: 'UAP 5', value: 5 },
        ],
        nameRules: [
          v => !!v || 'Le nom est requis',
          v => (v && v.length >= 2) || 'Le nom doit contenir au moins 2 caractères',
          v => /^[a-zA-ZÀ-ÿ\s-]+$/.test(v) || 'Le nom ne doit contenir que des lettres'
        ],
        lastnameRules: [
          v => !!v || 'Le prénom est requis',
          v => (v && v.length >= 2) || 'Le prénom doit contenir au moins 2 caractères',
          v => /^[a-zA-ZÀ-ÿ\s-]+$/.test(v) || 'Le prénom ne doit contenir que des lettres'
        ],
        uapRules: [
          v => !!v || 'L\'UAP est requise',
        ],
        formationRules: [
          v => v !== null || 'La formation IPC est requise',
        ],
      }
    },
  
    methods: {
      async submitForm() {
        if (!this.$refs.form.validate()) return;
  
        this.loading = true;
        try {
          await EmployeeService.createEmployee(this.formData);
          this.$emit('showMessage','Salarié ajouté avec succès','success');
          this.$emit('employee-added');
          this.closeDialog();
        } catch (error) {
          console.error('Erreur lors de l\'ajout du salarié:', error);
          this.$emit('showMessage', 'Erreur lors de l\'ajout du salarié', 'error');
        } finally {
          this.loading = false;
        }
      },
  
      closeDialog() {
        this.dialog = false;
        this.$refs.form.reset();
        Object.assign(this.formData, {
          name: '',
          lastname: '',
          uap_id: null,
          formationIPC: null,
          tauxPolyvalence: 0
        });
      },
    }
  }
  </script>
  
  <style scoped>
  .form-grid {
    display: grid;
    gap: 16px;
    max-width: 100%;
  }
  
  .close-btn {
    opacity: 0.8;
    transition: opacity 0.2s ease;
  }
  
  .close-btn:hover {
    opacity: 1;
  }
  
  .v-btn {
    text-transform: none !important;
    letter-spacing: 0.5px;
  }
  
  /* Animation d'entrée pour le dialog */
  .v-dialog-transition-enter-active {
    transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  .v-dialog-transition-leave-active {
    transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  /* Style pour les champs de formulaire */
  :deep(.v-field) {
    border-radius: 8px;
  }
  
  :deep(.v-field__input) {
    padding: 12px 16px;
  }
  
  :deep(.v-field__prepend-inner) {
    padding-inline-start: 12px;
  }
  
  /* Style pour le radio group */
  :deep(.v-radio) {
    margin-bottom: 8px;
  }
  
  /* Personnalisation des couleurs et ombres */
  .v-card {
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
  }
  
  .bg-primary {
    background: linear-gradient(135deg, #1976D2, #2196F3);
  }

  .button-wrapper {
  padding: 16px 24px;
}

.custom-add-btn {
  position: relative;
  padding: 12px 24px !important;
  min-height: 48px !important;
  border-radius: 12px !important;
  text-transform: none !important;
  font-weight: 600 !important;
  font-size: 0.95rem !important;
  letter-spacing: 0.3px !important;
  overflow: hidden !important;
  background: linear-gradient(135deg, #1976D2, #2196F3) !important;
  transition: all 0.3s ease !important;
}

.btn-content {
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
  z-index: 2;
}

.custom-icon {
  transition: transform 0.3s ease;
}

.btn-text {
  white-space: nowrap;
}

.btn-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #1E88E5, #42A5F5);
  opacity: 0;
  transition: opacity 0.3s ease;
}

/* Hover effects */
.custom-add-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(33, 150, 243, 0.3) !important;
}


.custom-add-btn:hover .btn-background {
  opacity: 1;
}

/* Active state */
.custom-add-btn:active {
  transform: translateY(1px);
  box-shadow: 0 2px 8px rgba(33, 150, 243, 0.2) !important;
}

/* Media queries */
@media (max-width: 600px) {
  .button-wrapper {
    padding: 12px 16px;
  }

  .custom-add-btn {
    width: 100% !important;
    padding: 10px 20px !important;
    min-height: 44px !important;
  }

  .btn-text {
    font-size: 0.9rem !important;
  }

  .custom-icon {
    font-size: 20px !important;
  }
}
  </style>