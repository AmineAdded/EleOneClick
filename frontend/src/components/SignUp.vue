<template>
  <v-dialog v-model="showSignUpModal" max-width="500" persistent>
    <v-card class="signup-modal" style="position: relative;left: 20px;">
      <v-card-title class="header">
        <div class="d-flex align-center">
          <v-icon color="white" class="mr-2">mdi-account-plus</v-icon>
          <span class="text-h5 font-weight-medium text-white">S'inscrire</span>
        </div>
        <v-icon @click="closeSignUpModal" class="close-btn" color="white">mdi-close</v-icon>
      </v-card-title>

      <v-card-text class="pt-6 px-6">
        <v-form ref="registerForm">
          <!-- Upload photo section -->
          <div class="text-center mb-4">
            <div class="profile-upload mb-2">
              <v-avatar size="100" color="grey lighten-3" class="mb-2">
                <img v-if="imagePreview" :src="imagePreview" alt="Profile Preview">
                <v-icon v-else size="48" color="grey darken-2">mdi-account</v-icon>
              </v-avatar>
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                @change="handleFileUpload"
                style="display: none"
              >
              <v-btn
                color="primary"
                text
                small
                @click="$refs.fileInput.click()"
                class="mt-2"
              >
                <v-icon left>mdi-camera</v-icon>
                {{ imagePreview ? 'Changer la photo' : 'Ajouter une photo' }}
              </v-btn>
              <div v-if="errors.photo" class="error--text text-caption mt-1">
                {{ errors.photo[0] }}
              </div>
            </div>
          </div>

          <div class="d-flex gap-4">
            <v-text-field
              v-model="register.name"
              label="Prénom"
              outlined
              dense
              prepend-inner-icon="mdi-account-circle"
              :error-messages="errors.name"
              @input="validatename"
              class="rounded-lg"
              placeholder="Votre prénom"
            ></v-text-field>

            <v-text-field
              v-model="register.lastname"
              label="Nom"
              outlined
              dense
              prepend-inner-icon="mdi-account-details"
              :error-messages="errors.lastname"
              @input="validateLastname"
              class="rounded-lg"
              placeholder="Votre nom"
            ></v-text-field>
          </div>

          <v-text-field
            v-model="register.email"
            label="Email"
            outlined
            dense
            prepend-inner-icon="mdi-email"
            :error-messages="errors.email"
            @input="validateEmail"
            class="rounded-lg mb-2"
            placeholder="exemple@email.com"
          ></v-text-field>
          
          <v-text-field
            v-model="register.password"
            :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
            :type="showPassword ? 'text' : 'password'"
            label="Mot de passe"
            outlined
            dense
            prepend-inner-icon="mdi-lock"
            :error-messages="errors.password"
            @click:append-inner="togglePasswordVisibility"
            @input="validatePassword"
            class="rounded-lg"
            placeholder="Votre mot de passe"
          ></v-text-field>

          <div class="password-requirements mt-2 mb-4">
            <div class="text-caption text-grey">Le mot de passe doit contenir:</div>
            <div class="d-flex flex-wrap gap-2 mt-1">
              <v-chip
                x-small
                :color="register.password.length >= 8 ? 'success' : 'grey'"
                text-color="white"
              >8 caractères min.</v-chip>
              <v-chip
                x-small
                :color="/[A-Z]/.test(register.password) ? 'success' : 'grey'"
                text-color="white"
              >Majuscule</v-chip>
              <v-chip
                x-small
                :color="/[0-9]/.test(register.password) ? 'success' : 'grey'"
                text-color="white"
              >Chiffre</v-chip>
              <v-chip
                x-small
                :color="/[!@#$%^&*]/.test(register.password) ? 'success' : 'grey'"
                text-color="white"
              >Caractère spécial</v-chip>
            </div>
          </div>

          <v-btn
            color="primary"
            block
            large
            class="mt-4 rounded-lg"
            @click="submitRegister"
            :loading="loading"
            elevation="2"
          >
            <v-icon left>mdi-account-plus</v-icon>
            S'inscrire
          </v-btn>
        </v-form>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-actions class="justify-center py-4">
        <span class="text-body-2">Vous avez déjà un compte?&nbsp;</span>
        <a
          @click.prevent="showLoginModal"
          class="text-decoration-none primary--text font-weight-medium hover-link"
        >
          Se connecter
        </a>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import AuthService from "@/Services/Auth/auth";
import { AuthStore } from "@/store/auth";

export default {
  name: "SignUp",
  data() {
    return {
      loading: false,
      showSignUpModal: true,
      showPassword: false,
      imagePreview: null,
      register: {
        name: "",
        lastname: "",
        email: "",
        password: "",
        photo: null,
      },
      errors: {
        name: [],
        lastname: [],
        email: [],
        photo:[]
      },
    };
  },
  methods: {
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        // Validation du type de fichier
        if (!file.type.match('image.*')) {
          this.errors.photo = ['Veuillez sélectionner une image valide'];
          return;
        }
        
        // Validation de la taille (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
          this.errors.photo = ['L\'image ne doit pas dépasser 5MB'];
          return;
        }

        this.register.photo = file;
        this.errors.photo = [];

        // Création de l'aperçu
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    },
    validatename() {
      if (this.register.name.length < 3) {
        this.errors.name = ["Le prénom d'utilisateur doit contenir au moins 3 caractères."];
      } else {
        this.errors.name = [];
      }
    },
    validateLastname() {
      if (this.register.lastname.length < 3) {
        this.errors.lastname = ["Le nom d'utilisateur doit contenir au moins 3 caractères."];
      } else {
        this.errors.lastname = [];
      }
    },
    validateEmail() {
      const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!re.test(this.register.email)) {
        this.errors.email = ["L'email est invalide."];
      } else {
        this.errors.email = [];
      }
    },
    validatePassword(){
      const alpha =/[A-Z]/;
      const num= /[0-9]/;
      const special=/[!@#$%^&*]/;
      if(this.register.password.length<8){
        this.errors.password=["Le mot de passe doit contenir au moins 8 caractères"];
      }
      else if(!alpha.test(this.register.password)){
        this.errors.password=["Au moins une majuscule requise"];
      }
      else if(!num.test(this.register.password)){
        this.errors.password=["Au moins un chiffre requis"];
      }
      else if(!special.test(this.register.password)){
        this.errors.password=["Au moins un caractére spécial est requis"];
      }
      else{
        this.errors.password=[];
      }
    },
    async submitRegister() {
      this.loading = true;
        this.validatename();
        this.validateLastname();
        this.validateEmail();
        this.validatePassword();

        if (
          this.errors.name.length === 0 &&
          this.errors.lastname.length === 0 &&
          this.errors.email.length === 0 &&
          this.errors.password.length === 0
        ) {
          try{
            const res=await AuthService.SignUp(this.register);
            console.log(res);
            this.closeSignUpModal();
            const auth=AuthStore();
            if(auth.getisauth==true && auth.getisadmin==0){
              this.$router.push('/FormateurView');
              // await AuthService.sendEmailSignUp({email:this.register.email,name:this.register.name});
            }
          }catch(err){
            this.$emit("show-snackbar", {
              message: "Adresse email déjà utilisée.",
              color: "error",
              icon: "mdi-alert-circle"
            });
          }
          finally {
            this.loading = false;
            this.register = {
              name: "",
              lastname: "",
              email: "",
              password: "",
              photo: null
            };
            this.imagePreview = null;
          }
          
        }
        else{
          this.loading = false;
        }
    },
    closeSignUpModal() {
      this.$emit("close");
    },
    showLoginModal() {
      this.closeSignUpModal();
      this.$emit("show-login");
    },
  },
};
</script>

<style scoped>
.router-link {
  text-decoration: none;
  display: block;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.router-link .v-btn {
  width: 80%;
}

.signup-modal {
  border-radius: 8px;
  overflow: hidden;
}

.header {
  background-color: #1976D2;
  color: white;
  padding: 20px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.close-btn {
  position: absolute;
  right: 17px;
  top: 24px;
}

.gap-4 {
  gap: 16px;
}

.gap-2 {
  gap: 8px;
}

.hover-link {
  transition: opacity 0.2s;
}

.hover-link:hover {
  opacity: 0.8;
  cursor: pointer;
}

.v-text-field ::v-deep .v-input__slot {
  border-radius: 8px !important;
}

.password-requirements {
  background-color: #f5f5f5;
  padding: 12px;
  border-radius: 8px;
}

.profile-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.profile-upload .v-avatar {
  border: 2px solid #e0e0e0;
  background-color: white;
  transition: all 0.2s ease;
}

.profile-upload .v-avatar:hover {
  border-color: #1976D2;
  cursor: pointer;
}
</style>