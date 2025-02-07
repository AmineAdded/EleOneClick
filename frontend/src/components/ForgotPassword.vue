<template>
  <v-dialog max-width="500" v-model="showForgotPasswordModal" class="forgot-password-dialog" >
    <v-card class="modal-card" style="position: relative;left: 0px;">
      <v-btn
        icon
        @click="closeForgotPassword"
        class="close-btn"
        color="white"
        variant="text"
      >
        <v-icon>mdi-close</v-icon>
      </v-btn>

      <div class="modal-header">
        <div class="header-content">
          <v-icon size="36" color="white" class="mb-2">mdi-lock-reset</v-icon>
          <v-card-title class="text-h5 text-white mb-2">
            Réinitialiser le mot de passe
          </v-card-title>
          <div class="subtitle-text">
            Entrez votre email pour recevoir le code de réinitialisation
          </div>
        </div>
      </div>

      <v-card-text class="pt-6">
        <v-form>
          <v-text-field
            label="Email"
            v-model="email"
            :error-messages="emailError"
            @input="validateEmail"
            variant="outlined"
            density="comfortable"
            bg-color="grey-lighten-4"
            prepend-inner-icon="mdi-email-outline"
            placeholder="exemple@email.com"
            class="email-field"
          ></v-text-field>

          <div class="d-flex justify-center mt-6">
            <v-btn
              @click="resetPassword"
              color="primary"
              min-width="200"
              height="44"
              class="submit-btn text-body-1"
              :disabled="!!emailError.length"
              :loading="overlay"
            >
              Réinitialiser
            </v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>

    <!-- Snackbar de succès -->
    <v-snackbar
      v-model="showSnackbar"
      location="top"
      :timeout="3000"
      color="success"
      elevation="24"
    >
      <div class="d-flex align-center">
        <v-icon class="mr-2">mdi-check-circle</v-icon>
        {{ text }}
      </div>

      <template v-slot:actions>
        <v-btn
          variant="text"
          @click="showSnackbar = false"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </template>
    </v-snackbar>
  </v-dialog>
</template>

<script>
import Service_Reset_Pass from "@/Services/reset_password.js";
import axios from "axios";

export default {
  data() {
    return {
      email: "",
      emailError: [],
      message_success: "",
      overlay: false,
      showSnackbar: false,
      text: "Code envoyé avec succès à votre email",
    };
  },

  props: {
    value: { type: Boolean, default: false },
  },

  computed: {
    showForgotPasswordModal: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit("input", val);
      },
    },
  },

  methods: {
    validateEmail() {
      const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (re.test(this.email)) {
        this.emailError = [];
      } else {
        this.emailError = ["Email invalide"];
      }
    },
    async resetPassword() {
      if (this.emailError.length === 0) {
        try {
          this.overlay = true;
          
          const verifyResponse = await axios.get(
            `verifEmail?email=${encodeURIComponent(this.email)}`
          );

          if (!verifyResponse.data.success) {
            this.emailError = ["Cet adresse email n'existe pas."];
            this.overlay = false;
            return;
          }

          const resetResponse = await Service_Reset_Pass.ForgotPassword({
            email: this.email,
          });

          this.message_success = resetResponse.data.message;
          
          setTimeout(() => {
            this.overlay = false;
            this.showSnackbar = true;
            
            setTimeout(() => {
              this.closeForgotPassword();
            }, 2000);
          }, 1000);

        } catch (error) {
          this.emailError = [
            error.response?.data?.message || "Une erreur s'est produite",
          ];
          this.overlay = false;
        }
      } else {
        this.emailError = ["Veuillez entrer un email valide."];
      }
    },

    closeForgotPassword() {
      this.$emit("close");
    },
  },
};
</script>

<style scoped>
.forgot-password-dialog {
  border-radius: 16px;
}

.modal-card {
  border-radius: 16px;
  overflow: hidden;
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
  left: 30px;
}

.subtitle-text {
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.95rem;
  margin-top: 0;
  max-width: 80%;
  margin-left: auto;
  margin-right: auto;
  
}

.email-field {
  margin-top: 8px;
}

.email-field :deep(.v-field__outline) {
  color: black;
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

/* Animation pour le loading */
.v-btn--loading {
  transform: scale(0.98);
  transition: transform 0.2s ease;
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