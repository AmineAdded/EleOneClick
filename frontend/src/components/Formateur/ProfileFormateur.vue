<template>
  <div class="profile-container">
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="3000" location="top">
      {{ snackbar.text }}
      <template v-slot:actions>
        <v-btn color="white" variant="text" @click="snackbar.show = false">
          Fermer
        </v-btn>
      </template>
    </v-snackbar>

    <div class="profile-card">

      <div class="profile-header pa-8">
        <div class="header-pattern"></div>
        <div class="header-gradient"></div>

        <div class="header-content">

          <div class="profile-pic-container">
            <template v-if="profilePic">
              <img :src="profilePic" alt="Profile Image" width="165">
            </template>
            <template v-else-if="ProfileImage">
              <img :src="'http://127.0.0.1:8000/storage/' + ProfileImage" alt="Profile Image" width="165">
            </template>
            <template v-else>
              <v-avatar size="160" class="profile-avatar">
                <v-icon size="80" color="white">mdi-account</v-icon>
              </v-avatar>
            </template>

            <label for="photo-upload" class="camera-btn">
              <i class="fas fa-camera"></i>
            </label>

            <input type="file" id="photo-upload" class="d-none" accept="image/*" @change="onPhotoChange" />

            
          </div>
          
          <div class="header-text">
            <h2 class="profile-title">Profil Formateur</h2>
            <div class="profile-role">Expert en Formation</div>
          </div>
        </div>
      </div>

      <v-card-text class="profile-content">
        <!-- Section des informations de base -->
        <div class="section-title mb-6">
          <h3 class="text-h5 font-weight-bold text-primary">
            <v-icon class="mr-2">mdi-card-account-details-outline</v-icon>
            Informations personnelles
          </h3>
        </div>

        <div class="info-grid">
          <div v-for="(field, key) in basicFields" :key="key" class="info-field"
            :class="{ 'pulse': editingFields[key] }">
            <div class="field-header">
              <div class="icon-wrapper" :style="{ background: `${field.color}15` }">
                <v-icon :color="field.color" size="24">{{ field.icon }}</v-icon>
              </div>
              <span class="field-label">{{ field.label }}</span>
            </div>
            <div class="field-content">
              <div v-if="!editingFields[key]" class="value-display">
                <span class="field-value">{{ formateur[key] }}</span>
                <v-btn icon="mdi-pencil" variant="text" :color="field.color" size="small" @click="startEditing(key)"
                  class="edit-btn">
                </v-btn>
              </div>
              <div v-else class="edit-field">
                <v-text-field v-model="editedValues[key]" variant="outlined" density="comfortable" class="edit-input"
                  :color="field.color" :rules="getFieldRules(key)"></v-text-field>
                <div class="edit-actions">
                  <v-btn icon="mdi-check" variant="text" color="success" size="small" @click="saveEdit(key)"></v-btn>
                  <v-btn icon="mdi-close" variant="text" color="error" size="small" @click="cancelEdit(key)"></v-btn>
                </div>
              </div>
            </div>
          </div>

          <!-- Bouton pour sauvegarder les informations de base -->
          <div class="actions-container text-end">
            <v-btn color="primary" variant="elevated" class="action-button save-btn" size="large"
              prepend-icon="mdi-content-save" @click="saveAllChanges" :loading="overlay1">
              Enregistrer les informations personnelles
            </v-btn>
          </div>
        </div>

        <!-- Section changement de mot de passe -->
        <div class="section-divider my-8"></div>
        <div class="section-title mb-6">
          <h3 class="text-h5 font-weight-bold text-primary">
            <v-icon class="mr-2">mdi-lock-reset</v-icon>
            Changement de mot de passe
          </h3>
        </div>

        <div class="password-section">
          <div class="info-field password-change-section">
            <div class="field-content">
              <v-form v-if="!showOtpInput" v-model="passwordFormValid">
                <v-text-field v-model="passwordForm.currentPassword" label="Mot de passe actuel" type="password"
                  variant="outlined" density="comfortable" class="mb-4"
                  :rules="[v => !!v || 'Mot de passe actuel requis']"></v-text-field>

                <v-text-field v-model="passwordForm.newPassword"
                  :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="showPassword ? 'text' : 'password'" @click:append-inner="togglePasswordVisibility"
                  label="Nouveau mot de passe" variant="outlined" density="comfortable" class="mb-4"
                  :rules="passwordRules"></v-text-field>

                <v-text-field v-model="passwordForm.confirmPassword"
                  :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="showPassword ? 'text' : 'password'" @click:append-inner="togglePasswordVisibility"
                  label="Confirmer le nouveau mot de passe" variant="outlined" density="comfortable" class="mb-4"
                  :rules="[
                    v => !!v || 'Confirmation requise',
                    v => v === passwordForm.newPassword || 'Les mots de passe ne correspondent pas'
                  ]"></v-text-field>

                <v-btn color="primary" block :disabled="!passwordFormValid" type="submit" prepend-icon="mdi-email-send" class="save-btn"
                  @click.prevent="sendCode" :loading="passwordFormValid ? overlay : false">
                  Envoyer le code de confirmation
                </v-btn>
              </v-form>

              <div v-else class="otp-container">
                <p class="text-subtitle-1 mb-4">Veuillez entrer le code de confirmation envoyé à votre email</p>
                <div class="otp-input-wrapper">
                  <v-otp-input v-model="otpCode" length="6" type="number" :error-messages="errors.code"
                    @input="validateCode" class="otp-input"></v-otp-input>
                </div>
                <div class="otp-actions mt-6">
                  <v-btn color="primary" block @click="verifyOtp" class="save-btn" :loading="overlay">
                    Valider et changer le mot de passe
                  </v-btn>
                </div>
              </div>
            </div>
          </div>
        </div>
      </v-card-text>
    </div>
  </div>
</template>

<script>
import { AuthStore } from "@/store/auth";
import UpdateInfos_Service from "@/Services/ChangerInfoFormateur"
import Axios from "axios";

export default {
  name: "ProfileFormateur",
  props: {
    modelValue: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      profilePic: null,
      showPassword: false,
      overlay: false,
      overlay1: false,
      snackbar: {
        show: false,
        text: '',
        color: 'success'
      },

      formateur: {
        name: "",
        lastname: "",
        email: "",
        password: "",
        image: null
      },
      editingFields: {
        name: false,
        lastname: false,
        email: false,
      },
      editedValues: {
        name: "",
        lastname: "",
        email: "",
      },
      basicFields: {
        name: {
          label: "Nom",
          icon: "mdi-account-circle",
          color: "#1E88E5",
        },
        lastname: {
          label: "Prénom",
          icon: "mdi-card-account-details",
          color: "#42A5F5",
        },
        email: {
          label: "Email",
          icon: "mdi-email",
          color: "#64B5F6",
        },
      },
      passwordForm: {
        currentPassword: "",
        newPassword: "",
        confirmPassword: "",
      },
      passwordFormValid: false,
      passwordRules: [
        v => !!v || 'Nouveau mot de passe requis',
        v => v.length >= 8 || 'Le mot de passe doit contenir au moins 8 caractères',
        v => /[A-Z]/.test(v) || 'Au moins une majuscule requise',
        v => /[0-9]/.test(v) || 'Au moins un chiffre requis',
        v => /[!@#$%^&*]/.test(v) || 'Au moins un caractère spécial requis',
      ],
      emailRules: [
        v => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(v) || 'email invalide'
      ],
      showOtpInput: false,
      otpCode: '',
      code: '',
      errors: {
        code: [],
      },
      isOtpValid: false,
    };
  },
  computed: {
    ProfileImage() {
      const auth = AuthStore();
      const user = auth.getuser;

      if (user && user.image) {
        return user.image;
      }
      return null;
    }
  },
  created() {
    const auth = AuthStore();
    const user = auth.getuser;
    if (user) {
      this.formateur = {
        name: user.name || "",
        lastname: user.lastname || "",
        email: user.email || "",
        password: user.password || "",
      };
      this.editedValues = { ...this.formateur };
    }
  },
  methods: {
    onPhotoChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.formateur.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
          this.profilePic = e.target.result;
        };
        reader.readAsDataURL(file);
        console.log(this.formateur.image);
      }
    },
    showMessage(text, color = 'success') {
      this.snackbar.text = text;
      this.snackbar.color = color;
      this.snackbar.show = true;
    },
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    },

    getFieldRules(key) {
      if (key === "name" || key === "lastname") {
        return [
          v => !!v || `${key === "name" ? "Nom" : "Prénom"} requis`,
          v => v.length >= 3 || "Doit contenir au moins 3 caractères",
        ];
      }
      if (key === "email") {
        return [
          v => !!v || "Email requis",
          v => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(v) || "Email invalide",
        ];
      }
      return [];
    },
    startEditing(field) {
      this.editingFields[field] = true;
      this.editedValues[field] = this.formateur[field];
    },
    saveEdit(field) {
      this.formateur[field] = this.editedValues[field];
      this.editingFields[field] = false;
    },
    cancelEdit(field) {
      this.editedValues[field] = this.formateur[field];
      this.editingFields[field] = false;
    },
    async saveAllChanges() {
      this.overlay1 = true;
      const auth = AuthStore();
      const userId = auth.getuser.id;
      const basicInfo = {
        name: this.formateur.name,
        lastname: this.formateur.lastname,
        email: this.formateur.email,
        image: this.formateur.image
      };
      try {
        console.log("Hello", basicInfo, userId);
        await UpdateInfos_Service.ChangerInfosFormateur(basicInfo, userId);
        setTimeout(() => {
          this.overlay1 = false;
          this.showMessage('Les informations ont été mises à jour avec succès');
        }, 1000);
      }
      catch (error) {
        this.overlay1 = false;
        this.showMessage('Adresse email est déjà utilisée', 'error');
      }
    },
    validateCode(value) {
      this.errors.code = [];
      if (!value) {
        this.errors.code.push('Code requis');
        this.isOtpValid = false;
      } else if (value.length !== 6) {
        this.errors.code.push('Le code doit contenir 6 chiffres');
        this.isOtpValid = false;
      } else {
        this.isOtpValid = true;
      }
    },
    async sendCode() {
      try {
        this.overlay = true;
        await UpdateInfos_Service.ChangerPassword({ email: this.formateur.email });
        setTimeout(() => {
          this.overlay = false;
          this.showMessage('Le code de confirmation a été envoyé à votre email');
          this.showOtpInput = true;
        }, 1000);
      } catch (error) {
        this.showMessage('Erreur lors de l\'envoi du code', 'error');
        console.error("Erreur lors de l'envoi du code:", error);
      }
    },

    async verifyOtp() {
      try {
        const response = await Axios.get('resetPassword/exist_code/' + this.otpCode);
        if (response.data.success) {
          const auth = AuthStore();
          const userId = auth.getuser.id;
          const isAdmin = auth.getisadmin;
          this.overlay = true;
          await Axios.post('resetPassword/changer_password', {
            email: this.formateur.email,
            password: this.passwordForm.newPassword,
            password_token: this.otpCode,
            id: userId,
            isAdmin: isAdmin
          });
          setTimeout(() => {
            this.overlay = false;
            this.showMessage('Mot de passe modifié avec succès');
            this.showOtpInput = false;
            this.resetPasswordForm();
          }, 1000);

        } else {
          this.showMessage('Code OTP invalide', 'error');
        }
      } catch (error) {
        this.overlay = false;
        this.showMessage('Erreur lors de la vérification du code', 'error');
        console.error("Erreur lors de la vérification du code OTP :", error);
      }
    },

    resetPasswordForm() {
      this.showOtpInput = false;
      this.otpCode = '';
      this.passwordForm = {
        currentPassword: '',
        newPassword: '',
        confirmPassword: '',
      };
      this.errors.code = [];
    },
  },
};
</script>

<style scoped>
.save-btn {
  background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
  color: white !important;
  border: none !important;
  padding: 0.75rem 1.5rem !important;
  border-radius: calc(var(--border-radius) * 0.8) !important;
  font-weight: 600 !important;
  letter-spacing: 0.5px !important;
  box-shadow: 0 10px 25px rgba(37, 99, 235, 0.2) !important;
  cursor: pointer !important;
  transition: all 0.3s ease !important;
}

.save-btn:hover {
  transform: translateY(-3px) !important;
  box-shadow: 0 15px 30px rgba(37, 99, 235, 0.3) !important;
  background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
}

.profile-pic-container {
  position: relative;
  width: 160px;
  height: 160px;
  z-index: 2;
}

.profile-container {
  height: 100%;
  width: calc(100vw - 250px);
  display: flex;
  justify-content: center;
  align-items: start;
  background: #f8fafc;
  position: relative;
  left: 50px;
}

.profile-card {
  width: 100%;
  max-width: 1200px;
  border-radius: 24px;
  overflow: hidden;
  background: white;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.profile-header {
  position: relative;
  height: 260px;
  overflow: hidden;
}

.header-pattern {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image:
    linear-gradient(45deg, rgba(255, 255, 255, 0.1) 25%, transparent 25%),
    linear-gradient(-45deg, rgba(255, 255, 255, 0.1) 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, rgba(255, 255, 255, 0.1) 75%),
    linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, 0.1) 75%);
  background-size: 20px 20px;
  opacity: 0.5;
}

.header-gradient {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, #1976d2 0%, #1565c0 50%, #0d47a1 100%);
  opacity: 0.95;
}

.header-content {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  gap: 32px;
  padding: 32px;
}

.profile-avatar {
  background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
  border: 6px solid rgba(255, 255, 255, 0.9);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
}

.profile-avatar:hover {
  transform: scale(1.05);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
}

.profile-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: white;
  margin-bottom: 8px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.profile-role {
  font-size: 1.25rem;
  color: rgba(255, 255, 255, 0.9);
  font-weight: 500;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
  padding: 24px;
}

.info-field {
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.info-field:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

.info-field::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #1976d2, #2196f3);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.info-field:hover::after {
  opacity: 1;
}

.field-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
}

.icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 12px;
  transition: transform 0.3s ease;
}

.info-field:hover .icon-wrapper {
  transform: scale(1.1);
}

.field-label {
  font-size: 1.1rem;
  font-weight: 600;
  color: #334155;
}

.field-content {
  padding-left: 64px;
}

.value-display {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.field-value {
  font-size: 1.15rem;
  color: #1e293b;
  font-weight: 500;
}

.edit-btn {
  opacity: 0;
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.value-display:hover .edit-btn {
  opacity: 1;
}

.edit-btn:hover {
  transform: scale(1.1);
}

.edit-field {
  display: flex;
  gap: 12px;
  align-items: center;
}

.save-button {
  background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%) !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  position: relative;
  left: 30px;
}

.save-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(25, 118, 210, 0.25);
}

/* Nouvelles classes pour l'OTP */
.otp-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
}

.otp-input-wrapper {
  width: 100%;
  max-width: 360px;
  margin: 20px 0;
}

.otp-input {
  width: 100%;
}

.otp-actions {
  width: 100%;
  max-width: 300px;
  text-align: center;
}


.password-change-section {
  grid-column: span 2;
}

/* Animation pour le pulse */
@keyframes pulse {
  0% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.02);
  }

  100% {
    transform: scale(1);
  }
}

.pulse {
  animation: pulse 2s infinite;
}

/* Media Queries */
@media (max-width: 1024px) {
  .info-grid {
    grid-template-columns: 1fr;
  }

  .password-change-section {
    grid-column: span 1;
  }
}

@media (max-width: 768px) {
  .profile-container {
    width: 100%;
    left: 0;
    padding: 16px;
  }

  .header-content {
    flex-direction: column;
    text-align: center;
  }

  .profile-title {
    font-size: 2rem;
  }

  .field-content {
    padding-left: 0;
  }

  .otp-input-wrapper {
    max-width: 100%;
  }
}

@media (max-width: 480px) {
  .profile-container {
    padding: 10px;
  }

  .profile-header {
    height: auto;
    padding: 20px;
  }

  .profile-avatar {
    width: 120px !important;
    height: 120px !important;
  }

  .field-header {
    flex-direction: column;
    text-align: center;
  }

  .icon-wrapper {
    margin: 0 auto;
  }

  .otp-actions {
    max-width: 100%;
  }
}

.section-title {
  padding: 0 24px;
}

.section-title h3 {
  color: #1976d2;
  display: flex;
  align-items: center;
}

.section-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(25, 118, 210, 0.2), transparent);
  margin: 40px 24px;
}

.password-section {
  background: rgba(25, 118, 210, 0.02);
  border-radius: 16px;
  margin: 0 24px;
  padding: 24px;
}

/* Ajustement du style du bouton de sauvegarde */
.actions-container {
  grid-column: span 2;
  margin-top: 24px;
}

.save-button {
  min-width: 300px;
  float: right;
}

@media (max-width: 768px) {
  .section-title {
    padding: 0 16px;
  }

  .password-section {
    margin: 0 16px;
    padding: 16px;
  }

  .save-button {
    min-width: 100%;
  }
}
</style>