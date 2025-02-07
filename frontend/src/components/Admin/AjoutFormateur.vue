<template>
  <v-dialog v-model="dialog" max-width="600px" persistent>
    <template v-slot:activator="{ props }">
      <div class="button-wrapper">
        <v-btn v-bind="props" class="custom-add-btn" elevation="2" color="primary" rounded>
          <div class="btn-content">
            <v-icon icon="mdi-account-plus" class="custom-icon" size="24"></v-icon>
            <span class="btn-text">Ajouter un formateur</span>
          </div>
          <div class="btn-background"></div>
        </v-btn>
      </div>
    </template>

    <v-card class="rounded-lg trainer-form">
      <v-card-title class="d-flex align-center pa-6 bg-primary text-white">
        <v-icon size="28" class="mr-3">mdi-account-plus</v-icon>
        <span class="text-h5 font-weight-medium">Ajouter un nouveau formateur</span>
        <v-spacer></v-spacer>
        <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog" class="close-btn"></v-btn>
      </v-card-title>

      <v-card-text class="pa-6">
        <v-form ref="form" v-model="isFormValid" @submit.prevent="submitForm">
          <div class="form-grid">
            <div class="photo-upload-section">
              <v-avatar size="150" class="profile-avatar" @click="triggerFileInput" style="cursor: pointer;">
                <img v-if="profilePic" :src="profilePic" alt="Profile Image" width="165">
                <v-icon v-else size="64" color="grey">mdi-camera-plus</v-icon>
              </v-avatar>

              <v-file-input ref="fileInput" v-model="formData.photo" accept="image/*" label="Choisir une photo"
                prepend-icon="mdi-camera" @change="handlePhotoUpload" class="mt-4" variant="outlined" hide-details
                style="display: none;"></v-file-input>

            </div>

            <div class="form-fields">
              <v-text-field v-model="formData.name" label="Nom" :rules="nameRules" variant="outlined"
                density="comfortable" placeholder="Entrez le nom" prepend-inner-icon="mdi-account" hide-details="auto"
                class="mb-4" required></v-text-field>

              <v-text-field v-model="formData.lastname" label="Prénom" :rules="lastnameRules" variant="outlined"
                density="comfortable" placeholder="Entrez le prénom" prepend-inner-icon="mdi-account"
                hide-details="auto" class="mb-4" required></v-text-field>

              <v-text-field v-model="formData.email" label="Email" :rules="emailRules" variant="outlined"
                density="comfortable" placeholder="Entrez l'email" prepend-inner-icon="mdi-email" hide-details="auto"
                class="mb-4" required></v-text-field>

              <v-text-field v-model="formData.password" :type="showPassword ? 'text' : 'password'" label="Mot de passe"
                :rules="passwordRules" variant="outlined" density="comfortable" placeholder="Entrez le mot de passe"
                prepend-inner-icon="mdi-lock" :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                @click:append-inner="showPassword = !showPassword" hide-details="auto" class="mb-4"
                required></v-text-field>
            </div>
          </div>
        </v-form>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-actions class="pa-6">
        <v-spacer></v-spacer>
        <v-btn variant="outlined" color="grey-darken-1" class="mr-3 text-none" @click="closeDialog"
          prepend-icon="mdi-close">
          Annuler
        </v-btn>
        <v-btn color="primary" class="text-none" :loading="loading" :disabled="!isFormValid" @click="submitForm"
          prepend-icon="mdi-check" elevation="2">
          Ajouter le formateur
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import Admin_Service from '@/Services/Admin/Admin';
export default {
  name: 'AjoutFormateur',

  data() {
    return {
      dialog: false,
      loading: false,
      isFormValid: false,
      showPassword: false,
      profilePic: null,
      formData: {
        name: '',
        lastname: '',
        email: '',
        password: '',
        image: null
      },
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
      emailRules: [
        v => !!v || 'L\'email est requis',
        v => /.+@.+\..+/.test(v) || 'Veuillez entrer un email valide'
      ],
      passwordRules: [
        v => !!v || 'Le mot de passe est requis',
        v => (v && v.length >= 8) || 'Le mot de passe doit contenir au moins 8 caractères',
        v => /(?=.*\d)(?=.*[A-Z])(?=.*[\W_])/.test(v) || 'Le mot de passe doit contenir des majuscules, des chiffres et des caractères spéciaux'

      ]
    }
  },

  methods: {
    triggerFileInput() {
      this.$refs.fileInput.$el.querySelector('input').click();
    },
    handlePhotoUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.formData.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
          this.profilePic = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },

    async submitForm() {
      if (!this.$refs.form.validate()) return;

      this.loading = true;
      try {
        const basicInfo = {
          name: this.formData.name,
          lastname: this.formData.lastname,
          email: this.formData.email,
          password: this.formData.password,
          image: this.formData.image
        };
        console.log("Hello",basicInfo);
        console.log(this.basicInfo);
        await Admin_Service.createTrainer(this.formData);
        this.$emit('showMessage', 'Formateur ajouté avec succès', 'success');
        this.$emit('refreshData');
        this.closeDialog();
      } catch (error) {
        console.error('Erreur lors de l\'ajout du formateur:', error);
        this.$emit('showMessage', 'Erreur lors de l\'ajout du formateur', 'error');
      } finally {
        this.loading = false;
      }
    },

    closeDialog() {
      this.dialog = false;
      this.$refs.form.reset();
      this.profilePic = null;
      Object.assign(this.formData, {
        name: '',
        lastname: '',
        email: '',
        password: '',
        photo: null
      });
    },
  }
}
</script>

<style scoped>
.form-grid {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 24px;
}

.photo-upload-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.profile-avatar {
  border: 3px solid #1976D2;
  box-shadow: 0 4px 15px rgba(25, 118, 210, 0.2);
}

.trainer-form {
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

@media (max-width: 600px) {
  .form-grid {
    grid-template-columns: 1fr;
  }

  .photo-upload-section {
    margin-bottom: 16px;
  }
}

/* Inherits most styles from the previous component */
</style>