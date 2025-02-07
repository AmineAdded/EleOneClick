<template>
     <div v-if="isLoading" class="loader-container">
        <div class="loader-content">
            <v-progress-circular
                indeterminate
                color="primary"
                size="64"
            ></v-progress-circular>
            <span class="loader-text">Chargement en cours...</span>
        </div>
    </div>
    <div v-else>
      <div class="back-button-container">
      <button @click="$router.go(-1)" class="back-button">
        <i class="fas fa-arrow-left"></i>
        <span>Retour</span>
      </button>
    </div>

      <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      :timeout="3000"
      location="top"
    >
      {{ snackbar.text }}
      <template v-slot:actions>
        <v-btn
          color="white"
          variant="text"
          @click="snackbar.show = false"
        >
          Fermer
        </v-btn>
      </template>
    </v-snackbar>

    <div class="container">
      <div class="profile-header">
        <div class="profile-pic-container">
           <img v-if="profilePic" :src="profilePic" alt="Profile Image" width="165">
           <img v-else-if="ProfileImage" :src="'http://127.0.0.1:8000/storage/' + ProfileImage" alt="Profile Image" width="165">
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
        <div>
          <h1 class="h3 fw-bold mb-2">Profil Admin</h1>
          <p class="text-white-75 mb-0">Tableau de bord de gestion</p>
        </div>
      </div>
  
      <div class="info-card">
        <h2 class="card-title">
          <i class="fas fa-user-circle"></i>
          Informations personnelles
        </h2>
        <form @submit.prevent="onProfileSubmit">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Nom</label>
                <v-text-field
                :disabled="!isEditing"
                  v-model="profile.firstName"
                  variant="outlined"
                  density="comfortable"
                  class="edit-input "
                  :rules="firstNameRules"
                ></v-text-field>
                <button type="button" class="btn-edit" @click="toggleEdit">
                  <i class="fas fa-pen"></i>
                </button> 
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Prénom</label>
                <v-text-field
                :disabled="!isEditing"
                  v-model="profile.name"
                  variant="outlined"
                  density="comfortable"
                  class="edit-input "
                  :rules="nameRules"
                ></v-text-field>
                <button type="button" class="btn-edit" @click="toggleEdit">
                  <i class="fas fa-pen"></i>
                </button> 
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">

                <label class="form-label">Email</label>
                <v-text-field
                :disabled="!isEditing"
                  v-model="profile.email"
                  variant="outlined"
                  density="comfortable"
                  class="edit-input "
                  :rules="emailRules"
                ></v-text-field>
                <button type="button" class="btn-edit" @click="toggleEdit">
                  <i class="fas fa-pen"></i>
                </button> 
              </div>
            </div>
          </div>
          <div class="text-end">
            <v-btn
                style="height:50px"
                  type="submit"
                  prepend-icon="mdi-content-save"
                  class="save-btn"
                  :loading="overlay1"
                >
                  Enregistrer les informations
                </v-btn>
          </div>
        </form>
      </div>
  
      <div class="info-card">
        <h2 class="card-title">
          <i class="fas fa-lock"></i>
          Changement de mot de passe
        </h2>
        <v-form v-model="passwordFormValid" v-if="!showConfirmationModal" >

          <div class="row">
            <div class="col-12">
              <v-text-field
                  v-model="password.currentPassword"
                  :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="showPassword ? 'text' : 'password'"
                  @click:append-inner="togglePasswordVisibility"
                  label="Mot de passe actuel"
                  variant="outlined"
                  density="comfortable"
                  class="mb-4"
                  :rules="[v => !!v || 'Mot de passe actuel requis']"
                ></v-text-field>
            </div>
            <div class="col-12">
              <v-text-field
                  v-model="password.newPassword"
                  :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="showPassword ? 'text' : 'password'"
                  @click:append-inner="togglePasswordVisibility"
                  label="Nouveau Mot de passe"
                  variant="outlined"
                  density="comfortable"
                  class="mb-4"
                  :rules="passwordRules"
                ></v-text-field>
            </div>
            <div class="col-12">
              <v-text-field
                  v-model="password.confirmPassword"
                  :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="showPassword ? 'text' : 'password'"
                  @click:append-inner="togglePasswordVisibility"
                  label="Confirmation mot de passe"
                  variant="outlined"
                  density="comfortable"
                  class="mb-4"
                  :rules="[
                    v => !!v || 'Confirmation requise',
                    v => v === password.newPassword || 'Les mots de passe ne correspondent pas'
                  ]"
                ></v-text-field>
            </div>
          </div>
          <div class="text-end ">
            <v-btn
                style="height:50px"
                  :disabled="!passwordFormValid"
                  type="submit"
                @click.prevent="sendCode"
                  prepend-icon="mdi-email-send"
                  class="save-btn"
                  :loading="passwordFormValid ? overlay : false"
                >
                  Envoyer le code de confirmation
                </v-btn>
          </div>
        </v-form>
      </div>
  
      <!-- Modal de confirmation -->
      <div v-if="showConfirmationModal" class="modal-overlay">
        <div class="modal-wrapper">
          <div class="modal-container">
            <button type="button" class="modal-close-btn" @click="closeConfirmationModal">
              <i class="fas fa-times"></i>
            </button>
            <div class="modal-content">
              <h4 class="modal-title">Entrez le code de confirmation</h4>
              <form >
                <div class="otp-input-wrapper ">
                  <v-otp-input
                    v-model="otpCode"
                    length="6"
                    type="number"
                    @input="validateCode"
                    class="otp-input"
                  ></v-otp-input>
                </div>
                <div class="modal-footer">
                  <v-btn
                  style="height:50px"
                    @click="verifyOtp"
                    class="save-btn w-100"
                    :loading="overlay"
                  >
                    Valider et changer le mot de passe
                  </v-btn>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Success Alert -->
      <div v-if="showSuccessAlert" class="alert-success">
        <i class="fas fa-check-circle me-2"></i>
        Modifications enregistrées avec succès !
      </div>
    </div>
</div>
  </template>
  
  <script>
  import { AuthStore } from "@/store/auth";
  import UpdateInfos_Service from "@/Services/ChangerInfoFormateur"
   import Axios from "axios";
  export default {
    name: 'ProfileComponent',
    meta() {
      return {
        title: 'Profil Admin',
        link: [
          {
            rel: 'stylesheet',
            href: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'
          },
          {
            rel: 'stylesheet',
            href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
          }
        ],
        script: [
          {
            src: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
            type: 'text/javascript'
          }
        ]
      };
    },
    data() {
      return {
        profilePic:null,
        isLoading: true, 
        snackbar: {
            show: false,
            text: '',
            color: 'success'
        },
        passwordFormValid:true,
        overlay:false,
        overlay1:false,
        showPassword: false,
        isEditing: false,
        otpCode:'',
        errors: {
            code: [],
        },
        passwordRules: [
        v => !!v || 'Nouveau mot de passe requis',
        v => v.length >= 8 || 'Le mot de passe doit contenir au moins 8 caractères',
        v => /[A-Z]/.test(v) || 'Au moins une majuscule requise',
        v => /[0-9]/.test(v) || 'Au moins un chiffre requis',
        v => /[!@#$%^&*]/.test(v) || 'Au moins un caractère spécial requis',
      ],
      emailRules:[
        v => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(v) || 'email invalide'
      ],
      nameRules:[
        v=> !!v || "nom requis",
        v => v.length >= 3 || "Doit contenir au moins 3 caractères",
      ],
      firstNameRules:[
      v=> !!v || "prénom requis",
      v => v.length >= 3 || "Doit contenir au moins 3 caractères",
      ],
        profile: {
          name: '',
          firstName: '',
          email: '',
          image:null
        },
        password: {
          currentPassword: '',
          newPassword: '',
          confirmPassword: '',
        },
        // codeInputs: ['', '', '', '', '', ''],
        showConfirmationModal: false,
        showSuccessAlert: false,
      };
    },
    created() {
        setTimeout(() => {
            this.isLoading = false;
        }, 800);
        const auth = AuthStore();
        const user = auth.getuser;
        this.profile.name=user?.name || null;
        this.profile.firstName=user?.lastname || null;
        this.profile.email=user?.email || null;
    },
    computed:{
      ProfileImage() {
      const auth = AuthStore();
      const user = auth.getuser;
      
      if (user && user.image) {
        return user.image;
      }
      return null; 
    }
    },
    methods: {
      toggleEdit() {
        this.isEditing = !this.isEditing;
      },
      togglePasswordVisibility() {
        this.showPassword = !this.showPassword;
      },
      onPhotoChange(event) {
        const file = event.target.files[0];
        if (file) {
          this.profile.image=file;
          const reader = new FileReader();
          reader.onload = (e) => {
            this.profilePic = e.target.result;
          };
          reader.readAsDataURL(file);
        }
      },
      async onProfileSubmit() {
            this.overlay1=true;
            const auth = AuthStore();
            const userId = auth.getuser.id;
            const basicInfo = {
            name: this.profile.name,
            lastname: this.profile.firstName,
            email: this.profile.email,
            image: this.profile.image
            };
            try{
            await UpdateInfos_Service.ChangerInfosFormateur(basicInfo, userId);
            setTimeout(()=>{
                this.overlay1=false;
                this.showMessage('Les informations ont été mises à jour avec succès');
            },1000);
            }
            catch (error) {
            this.overlay1=false;
            this.showMessage('Adresse email est déjà utilisée', 'error');
        }
        },
      onPasswordSubmit() {
        this.showConfirmationModal = true;
      },
      closeConfirmationModal() {
        this.showConfirmationModal = false;
      },
      showMessage(text, color = 'success') {
      this.snackbar.text = text;
      this.snackbar.color = color;
      this.snackbar.show = true;
    },
      async sendCode() {
        try {
        this.overlay=true;
        await UpdateInfos_Service.ChangerPassword({email: this.profile.email});
        setTimeout(() => {
            this.overlay=false;
            this.showMessage('Le code de confirmation a été envoyé à votre email');
            this.showConfirmationModal = true;
        }, 1000);
        } catch (error) {
        this.overlay=false;
        this.showMessage('Erreur lors de l\'envoi du code', 'error');
        console.error("Erreur lors de l'envoi du code:", error);
        }
    },
      validateCode(value) {
      this.errors.code = [];
      if (!value) {
        this.errors.code.push('Code requis');
      } else if (value.length !== 6) {
        this.errors.code.push('Le code doit contenir 6 chiffres');
      }
    },
    async verifyOtp() {
        try {
            const response = await Axios.get('resetPassword/exist_code/' + this.otpCode);
            if (response.data.success) {
            const auth = AuthStore();
            const userId = auth.getuser.id;
            const isAdmin = auth.getisadmin;
            this.overlay=true;
            await Axios.post('resetPassword/changer_password', {
                email: this.profile.email,
                password: this.password.newPassword,
                password_token: this.otpCode,
                id:userId,
                isAdmin:isAdmin
            });
            setTimeout(() => {
                this.overlay=false;
                this.showMessage('Mot de passe modifié avec succès');
                this.showOtpInput = false;
                this.resetPasswordForm();
            }, 1000);
            
            } else {
            this.showMessage('Code OTP invalide', 'error');
            }
        } catch (error) {
            this.overlay=false;
            this.showMessage('Erreur lors de la vérification du code', 'error');
            console.error("Erreur lors de la vérification du code OTP :", error);
        }
        },
        resetPasswordForm() {
        this.showConfirmationModal = false;
        this.otpCode = '';
        this.password = {
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
        };
        this.errors.code = [];
        },
        
    },
  };
  </script>
  
  <style>
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


.back-button-container {
  padding: 1rem 2rem;
  max-width: 1100px;
  margin: 0 auto;
  position: relative;
  left:-120px;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.75rem;
  font-weight: 600;
  color: var(--primary-color);
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.back-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.1));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.back-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.15);
}

.back-button:hover::before {
  opacity: 1;
}

.back-button i {
  font-size: 1.1em;
  transition: transform 0.3s ease;
}

.back-button:hover i {
  transform: translateX(-3px);
}

/* Ajout de la media query pour la responsivité */
@media (max-width: 768px) {
  .back-button-container {
    padding: 1rem;
  }
  
  .back-button {
    padding: 0.6rem 1.2rem;
    font-size: 0.9rem;
  }
}
.loader-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background: rgba(25, 118, 210, 0.03);
    z-index: 1001;
    backdrop-filter: blur(8px);
}

.loader-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}

.loader-text {
    color: #1976d2;
    font-size: 1.2em;
    font-weight: 500;
    animation: pulse 2s infinite;
}
.otp-input {
  width: 100%;
}
  :root {
  --primary-color: #3b82f6;
  --secondary-color: #f1f5f9;
  --accent-color: #2563eb;
  --text-primary: #0f172a;
  --text-secondary: #475569;
  --border-color: #e2e8f0;
  --border-radius: 0.75rem;
  --transition-speed: 0.3s;
}

* {
  transition: all var(--transition-speed) ease;
}

body {
  background: #f0f4f8;
  background-image: 
    radial-gradient(#e2e8f0 1px, transparent 1px),
    radial-gradient(#e2e8f0 1px, transparent 1px);
  background-size: 30px 30px;
  background-position: 0 0, 15px 15px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  line-height: 1.6;
  color: var(--text-primary);
}

.container {
  max-width: 1100px;
  padding: 2rem;
  margin: 0 auto;
}

.profile-header {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  border-radius: var(--border-radius);
  padding: 2.5rem;
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  box-shadow: 0 15px 30px rgba(37, 99, 235, 0.1);
  position: relative;
  overflow: hidden;
}

.profile-header::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(
    circle,
    rgba(255, 255, 255, 0.1) 0%,
    transparent 50%
  );
  transform: rotate(45deg);
  animation: shimmer 8s linear infinite;
}

@keyframes shimmer {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.profile-pic-container {
  position: relative;
  width: 160px;
  height: 160px;
  z-index: 2;
}

.profile-pic {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  transition: transform 0.3s ease;
}

.profile-pic:hover {
  transform: scale(1.05);
}

.camera-btn {
  position: absolute;
  bottom: 10px;
  right: 0;
  background: white;
  color: var(--primary-color);
  border-radius: 50%;
  width: 45px;
  height: 45px;
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

.info-card {
  background: white;
  border-radius: var(--border-radius);
  border: 1px solid var(--border-color);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  padding: 2rem;
  margin-bottom: 1.5rem;
  transform: translateY(0);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.info-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.card-title {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding-bottom: 1rem;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid var(--secondary-color);
  color: var(--primary-color);
  font-size: 1.5rem;
  font-weight: 600;
}

.card-title i {
  font-size: 1.5rem;
}

.form-group {
  position: relative;
  margin-bottom: 1.5rem;
}

.form-label {
  font-weight: 500;
  color: var(--text-secondary);
  margin-bottom: 0.5rem;
  display: block;
}

/* .form-control {
  width: 100%;
  border-radius: calc(var(--border-radius) * 0.8);
  padding: 0.75rem 1rem;
  border: 2px solid var(--border-color);
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-control:focus {
  border-color: var(--accent-color);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  outline: none;
} */

.edit:disabled {
  background-color: var(--secondary-color);
  color: var(--text-secondary);
  cursor: not-allowed;
}

.btn-edit {
  position: absolute;
  right: 10px;
  top: 35px;
  background: none;
  border: none;
  color: var(--text-secondary);
  opacity: 0.6;
  cursor: pointer;
  padding: 0.5rem;
  transition: all 0.3s ease;
}

.btn-edit:hover {
  color: var(--primary-color);
  opacity: 1;
  transform: scale(1.1);
}

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

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(5px);
}

.modal-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
}

.modal-container {
  background-color: white;
  width: 90%;
  max-width: 500px;
  border-radius: var(--border-radius);
  padding: 2rem;
  position: relative;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: modalAppear 0.3s ease;
}

@keyframes modalAppear {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.modal-close-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  color: var(--text-secondary);
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.modal-close-btn:hover {
  background-color: var(--secondary-color);
  color: var(--primary-color);
  transform: rotate(90deg);
}

.modal-content {
  text-align: center;
}

.modal-title {
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  font-weight: 600;
  font-size: 1.5rem;
}

.confirmation-code-input {
  display: flex;
  justify-content: center;
  gap: 0.75rem;
  margin: 2rem 0;
}

.confirmation-code-input input {
  width: 50px;
  height: 60px;
  font-size: 1.75rem;
  text-align: center;
  border: 2px solid var(--border-color);
  border-radius: 0.5rem;
  transition: all 0.3s ease;
}

.confirmation-code-input input:focus {
  border-color: var(--accent-color);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  outline: none;
}

.resend-code {
  margin-top: 1.5rem;
}

.resend-code a {
  color: var(--text-secondary);
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.resend-code a:hover {
  color: var(--primary-color);
}

.alert-success {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  padding: 1rem 2rem;
  border-radius: var(--border-radius);
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  animation: slideIn 0.5s ease;
  z-index: 1000;
}

@keyframes slideIn {
  from {
    transform: translateY(100px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .container {
    padding: 1rem;
  }

  .profile-header {
    flex-direction: column;
    text-align: center;
    padding: 1.5rem;
  }

  .profile-pic-container {
    width: 120px;
    height: 120px;
  }

  .confirmation-code-input input {
    width: 40px;
    height: 50px;
    font-size: 1.5rem;
  }
}
    </style>