

<template>
  <v-dialog
    v-model="showChangePasswordModal"
    max-width="500"
    transition="fade-transition"
    class="password-change-modal"
  >
    <v-card class="modal-card" style="margin: 0 auto;position: relative;left: 0px;">
      <v-btn
        icon
        @click="closeChangePasswordModal"
        class="close-btn"
        color="white"
        variant="text"
      >
        <v-icon>mdi-close</v-icon>
      </v-btn>

      <div class="modal-header" style="height: 200px;">
        <div class="header-content">
          <v-icon size="36" color="white" class="mb-2">mdi-lock-reset</v-icon>
          <v-card-title class="text-h5 text-white mb-2">
            Changer le Mot de Passe
          </v-card-title>
          <div class="subtitle-text">
            Remplissez les champs ci-dessous pour changer votre mot de passe
          </div>
        </div>
      </div>

      <v-card-text class="pt-6" >
        <v-form ref="form" v-model="isFormValid" class="password-form">
          <v-text-field
            label="Email"
            v-model="changerPassword.email"
            :error-messages="errors.email"
            @input="validateEmail"
            variant="outlined"
            density="comfortable"
            bg-color="grey-lighten-4"
            prepend-inner-icon="mdi-email-outline"
            placeholder="exemple@email.com"
            class="input-field"
          >
            <template v-slot:append>
              <v-scale-transition>
                <v-icon
                  v-if="isEmailValid"
                  color="success"
                  class="validation-icon"
                >
                  mdi-check-circle
                </v-icon>
              </v-scale-transition>
            </template>
          </v-text-field>

          <v-text-field
            label="Nouveau Mot de Passe"
            v-model="changerPassword.password"
            :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
            :type="showPassword ? 'text' : 'password'"
            variant="outlined"
            density="comfortable"
            bg-color="grey-lighten-4"
            :rules="passwordRules"
            prepend-inner-icon="mdi-lock"
            @click:append-inner="togglePasswordVisibility"
            class="input-field"
          ></v-text-field>

          <v-text-field
            v-model="changerPassword.confirmPassword"
            label="Confirmer le Nouveau Mot de Passe"
            :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
            :type="showPassword ? 'text' : 'password'"
            variant="outlined"
            density="comfortable"
            bg-color="grey-lighten-4"
            :rules="[
              v => !!v || 'Confirmation requise',
              v => v === changerPassword.password || 'Les mots de passe ne correspondent pas'
            ]"
            prepend-inner-icon="mdi-lock-check"
            @click:append-inner="togglePasswordVisibility"
            class="input-field"
          ></v-text-field>

          <div class="code-input-section mt-4">
            <div class="text-subtitle-1 mb-2">Code de vérification</div>
            <v-otp-input
              v-model="changerPassword.code"
              type="number"
              length="6"
              :error-messages="errors.code"
              @input="validateCode"
              class="otp-input"
            ></v-otp-input>
          </div>

          <div class="d-flex justify-center mt-6">
            <v-btn
              @click="submitChangePassword"
              :loading="loading"
              :disabled="!isFormValid"
              color="primary"
              min-width="200"
              height="44"
              class="submit-btn text-body-1"
            >
              <v-icon left class="mr-2">mdi-check</v-icon>
              Changer le mot de passe
            </v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>

    <v-snackbar
      v-model="showSnackBar"
      location="top"
      :timeout="3000"
      :color="snackbarColor"
      elevation="24"
    >
      <div class="d-flex align-center">
        <v-icon class="mr-2">{{ snackbarIcon }}</v-icon>
        {{ text }}
      </div>
      <template v-slot:actions>
        <v-btn
          variant="text"
          @click="showSnackBar = false"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </template>
    </v-snackbar>
  </v-dialog>
</template>

<script>
import reset_password from "@/Services/reset_password.js";
import Axios from "axios";

export default {
  name: "ChangerPassword",
  data() {
    return {
      text: '',
      showSnackBar: false,
      snackbarColor: 'success',
      snackbarIcon: 'mdi-check-circle',
      showPassword: false,
      showChangePasswordModal: true,
      loading: false,
      isFormValid: false,
      isEmailValid: false,
      changerPassword: {
        email: '',
        password: '',
        confirmPassword: '',
        code: '',
      },
      errors: {
        email: [],
        code: []
      },
      passwordRules: [
        v => !!v || 'Nouveau mot de passe requis',
        v => v.length >= 8 || 'Le mot de passe doit contenir au moins 8 caractères',
        v => /[A-Z]/.test(v) || 'Au moins une majuscule requise',
        v => /[0-9]/.test(v) || 'Au moins un chiffre requis',
        v => /[!@#$%^&*]/.test(v) || 'Au moins un caractère spécial requis',
      ],
    }
  },
  methods: {
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    },

    closeChangePasswordModal() {
      this.$emit('close');
    },

    validateEmail() {
      const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      this.isEmailValid = re.test(this.changerPassword.email);
      if (!this.isEmailValid) {
        this.errors.email = ["L'email est invalide."];
      } else {
        this.errors.email = [];
      }
    },

    isValidEmail(email) {
      const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      return re.test(email);
    },

    validateCode() {
      if (!this.changerPassword.code) {
        this.errors.code = ["Le code est requis."];
      } else if (this.changerPassword.code.length !== 6) {
        this.errors.code = ["Le code doit contenir exactement 6 chiffres."];
      } else {
        this.errors.code = [];
      }
    },

    async submitChangePassword() {
      if (!this.$refs.form.validate()) {
        return;
      }

      this.loading = true;
      this.errors.email = [];
      this.errors.code = [];

      try {
        // Vérifier si l'email existe
        const verifyEmailResponse = await Axios.get(
          `verifEmail?email=${encodeURIComponent(this.changerPassword.email)}`
        );

        if (!verifyEmailResponse.data.success) {
          this.text = "Cet email n'existe pas dans notre base.";
          this.snackbarColor = 'error';
          this.snackbarIcon = 'mdi-alert';
          this.showSnackBar = true;
          return;
        }

        // Vérifier le code
        const verifyCodeResponse = await Axios.get(
          `resetPassword/exist_code/${this.changerPassword.code}`
        );

        if (!verifyCodeResponse.data.success) {
          this.text = "Le code de réinitialisation est invalide.";
          this.snackbarColor = 'error';
          this.snackbarIcon = 'mdi-alert';
          this.showSnackBar = true;
          return;
        }

        // Changer le mot de passe
        await reset_password.ChangerPassword({
          email: this.changerPassword.email,
          password: this.changerPassword.password,
          password_token: this.changerPassword.code,
        });

        this.text = "Mot de passe réinitialisé avec succès.";
        this.snackbarColor = 'success';
        this.snackbarIcon = 'mdi-check-circle';
        this.showSnackBar = true;

        // Fermer le modal après un délai
        setTimeout(() => {
          this.closeChangePasswordModal();
        }, 2000);

      } catch (error) {
        console.error('Erreur:', error);
        this.text = error.response?.data?.message || "Une erreur s'est produite lors de la réinitialisation.";
        this.snackbarColor = 'error';
        this.snackbarIcon = 'mdi-alert';
        this.showSnackBar = true;
      } finally {
        this.loading = false;
      }
    },
  }
}
</script>

<style scoped>
.password-change-modal {
  border-radius: 16px;
}

.modal-card {
  border-radius: 16px;
  overflow: hidden;
  width: 550px;
}

.close-btn {
  position: absolute;
  right: 8px;
  top: 8px;
  z-index: 1;
}

.modal-header {
  background: linear-gradient(135deg, #1976d2, #1565c0);
  padding: 32px 24px;
  text-align: center;
  position: relative;
}

.header-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  position: relative;
  left: 25px;
}

.subtitle-text {
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.95rem;
  margin-top: 0;
  max-width: 80%;
  margin-left: auto;
  margin-right: auto;
}

.input-field {
  margin-top: 6px;
}

.input-field :deep(.v-field__outline) {
  color: black;
}

.password-form {
  animation: slideUp 0.5s ease-out;
}

.validation-icon {
  animation: scaleIn 0.3s ease-out;
}

.submit-btn {
  text-transform: none;
  letter-spacing: 0.5px;
  font-weight: 500;
  border-radius: 8px;
}

.submit-btn:not(:disabled) {
  background: linear-gradient(135deg, #1976d2, #1565c0);
}

.otp-input {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-top: 8px;
}

 
/* Animations */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes scaleIn {
  from {
    transform: scale(0);
  }
  to {
    transform: scale(1);
  }
}

/* Animation pour le snackbar */
.v-snackbar-enter-active,
.v-snackbar-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.v-snackbar-enter-from,
.v-snackbar-leave-to {
  transform: translateY(-100%);
}

</style>
