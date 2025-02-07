<template>
  <div v-if="isLoggingOut" time="2000" class="loader-container">
    <div class="loader-content">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
      <span class="loader-text">Déconnexion en cours...</span>
    </div>
  </div>
  <v-app-bar color="white" elevation="0" class="enterprise-navbar px-8 py-2" :class="{ 'navbar-scrolled': isScrolled }">
    <div class="navbar-container d-flex align-center justify-space-between w-100">
      <v-img :src="require('@/assets/images/logo1.png')" contain max-height="45" max-width="140"
        class="enterprise-logo" />

      <v-btn-toggle mandatory group class="enterprise-nav-menu">
        <v-tooltip v-for="(item, index) in menuItemsGuest" :key="index" bottom content-class="custom-tooltip">
          <template v-slot:activator="{ on, attrs }">
            <v-btn text :value="item.value" class="nav-enterprise-item" v-bind="attrs" v-on="on" @click="item.action">
              <v-icon class="nav-icon mr-2">{{ item.icon }}</v-icon>
              <span class="nav-enterprise-text">{{ item.title }}</span>
            </v-btn>
          </template>
          <span>{{ item.tooltip }}</span>
        </v-tooltip>

        <template v-if="isFormateur">
          <v-menu offset-y transition="slide-y-transition">
            <template v-slot:activator="{ props }">
              <v-btn text v-bind="props" class="nav-enterprise-item admin-btn">
                <v-icon class="nav-icon mr-2">mdi-account</v-icon>
                <span>Formateur</span>
                <v-icon class="ml-2">mdi-chevron-down</v-icon>
              </v-btn>
            </template>
            <v-list class="admin-menu" rounded="lg">
              <v-list-item @click="scrollToFormateur" class="admin-menu-item">
                <v-list-item-icon>
                  <v-icon color="primary">mdi-view-dashboard</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Espace Formateur</v-list-item-title>
              </v-list-item>
              <v-divider class="my-2"></v-divider>
              <v-list-item @click="deconnexion" class="admin-menu-item">
                <v-list-item-icon>
                  <v-icon color="error">mdi-logout</v-icon>
                </v-list-item-icon>
                <v-list-item-title class="error--text">Déconnexion</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>

        <template v-if="isAdmin">
          <v-menu offset-y transition="slide-y-transition">
            <template v-slot:activator="{ props }">
              <v-btn text v-bind="props" class="nav-enterprise-item admin-btn">
                <v-icon class="nav-icon mr-2">mdi-account</v-icon>
                <span>Admin</span>
                <v-icon class="ml-2">mdi-chevron-down</v-icon>
              </v-btn>
            </template>
            <v-list class="admin-menu" rounded="lg">
              <v-list-item @click="scrollToAdmin" class="admin-menu-item">
                <v-list-item-icon>
                  <v-icon color="primary">mdi-view-dashboard</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Espace Admin</v-list-item-title>
              </v-list-item>
              <v-divider class="my-2"></v-divider>
              <v-list-item @click="deconnexion" class="admin-menu-item">
                <v-list-item-icon>
                  <v-icon color="error">mdi-logout</v-icon>
                </v-list-item-icon>
                <v-list-item-title class="error--text">Déconnexion</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </v-btn-toggle>

      <div class="action-enterprise-zone" v-if="!isFormateur && !isAdmin">
        <v-btn class="mr-4 login-enterprise-cta" color="primary" elevation="0" @click="openLoginModal">
          <v-icon left>mdi-login</v-icon>
          Connexion
        </v-btn>
        <v-btn outlined color="primary" class="signup-enterprise-cta" @click="openSignUpModal">
          <v-icon left>mdi-account-plus</v-icon>
          S'inscrire
        </v-btn>
      </div>
    </div>

    <!-- Login Modal -->
    <v-dialog v-model="showLoginModal" max-width="500">
      <v-card class="login-modal">
        <v-card-title class="header">
          <div class="d-flex align-center">
            <v-icon color="white" class="mr-2">mdi-login</v-icon>
            <span class="text-h5 font-weight-medium text-white">Connexion</span>
            <v-icon @click="closeLoginModal" class="close-btn">mdi-close</v-icon>
          </div>

        </v-card-title>

        <v-card-text class="pt-6 px-6">
          <v-form @submit.prevent="submitLogin">
            <v-text-field v-model="login.Email" label="Email" outlined dense prepend-inner-icon="mdi-account"
              class="mb-4 rounded-lg" :error-messages="errors.email" @input="validateEmail"
              placeholder="exemple@email.com"></v-text-field>

            <v-text-field v-model="login.Password" :append-inner-icon="showEye ? 'mdi-eye' : 'mdi-eye-off'"
              :type="showEye ? 'text' : 'password'" label="Mot de passe" outlined dense prepend-inner-icon="mdi-lock"
              class="mb-4 rounded-lg" :error-messages="errors.password" @click:append-inner="togglePasswordVisibility"
              @input="validatePassword" placeholder="Votre mot de passe"></v-text-field>

            <div class="d-flex align-center justify-space-between mt-4">
              <v-btn color="primary" class="px-8 text-capitalize rounded-lg" @click="submitLogin" elevation="2"
                :loading="loading">
                <v-icon left>mdi-login</v-icon>
                Connexion
              </v-btn>
              <a @click.prevent="showForgotPassword" class="text-decoration-none text-caption primary--text hover-link">
                Mot de passe oublié ?
              </a>
            </div>
          </v-form>
        </v-card-text>

        <v-divider class="mt-4"></v-divider>

        <v-card-actions class="justify-center py-4">
          <span class="text-body-2">N'avez pas de compte?&nbsp;</span>
          <a @click.prevent="openSignUpModal" class="text-decoration-none primary--text font-weight-medium hover-link">
            S'inscrire
          </a>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Modal Forgot Password -->
    <ForgotPassword v-model="showForgotPasswordModal" v-if="showForgotPasswordModal" @close="closeForgotPassword" />

    <!-- Modal sign up -->
    <SignUp v-model="showSignUpModal" v-if="showSignUpModal" @close="closeSignUpModal" @show-login="openLoginModal"
      @show-snackbar="afficheSnackBar" />

    <!-- Changer Password modal -->
    <ChangerPassword v-model="showChangePasswordModal" v-if="showChangePasswordModal"
      @close="closeChangePasswordModal" />

    <!-- Enhanced Snackbar -->
    <v-snackbar v-model="showSnackBar" :color="snackbarColor" top timeout="3000">
      <div class="d-flex align-center">
        <v-icon class="mr-2" color="white">
          {{ snackbarIcon }}
        </v-icon>
        {{ text }}
      </div>
      <template v-slot:actions>
        <v-btn icon @click="showSnackBar = false">
          <v-icon color="white">mdi-close</v-icon>
        </v-btn>
      </template>
    </v-snackbar>

  </v-app-bar>
</template>


<script>
import AuthService from "@/Services/Auth/auth.js";
import { AuthStore } from "@/store/auth.js";
import ForgotPassword from "@/components/ForgotPassword.vue";
import SignUp from "@/components/SignUp.vue";
import ChangerPassword from "@/components/ChangerPassword.vue";

export default {
  name: "NavBar",
  components: {
    SignUp,
    ForgotPassword,
    ChangerPassword
  },
  data() {
    return {
      isLoggingOut: false,
      showSnackBar: false,
      showLoginModal: false,
      showSignUpModal: false,
      showForgotPasswordModal: false,
      showChangePasswordModal: false,
      isScrolled: false,
      showEye: false,
      loading: false,
      snackbarColor: 'error',
      snackbarIcon: 'mdi-alert-circle',
      forgotPasswordLoading: false,
      login: {
        Email: "",
        Password: "",
      },
      forgotPassword: {
        Email: ""
      },
      errors: {
        email: [],
        password: []
      },
      forgotPasswordErrors: {
        email: []
      },
      menuItemsGuest: [
        {
          icon: 'mdi-home-city',
          title: 'Accueil',
          value: 'home',
          action: this.scrollToHome,
          tooltip: 'Page d\'accueil principale'
        },
        {
          icon: 'mdi-view-grid',
          title: 'Grille',
          value: 'account',
          action: this.scrollToGrille,
          tooltip: 'Grille de compétences'
        },
        {
          icon: 'mdi-phone',
          title: 'Contact',
          value: 'users',
          action: this.scrollToContact,
          tooltip: 'Informations de contact'
        }
      ],
    }
  },
  computed: {
    isFormateur() {
      const auth = AuthStore();
      return auth.getisadmin === 0 && auth.getisauth != null;
    },
    isAdmin(){
      const auth = AuthStore();
      return auth.getisadmin === 1 && auth.getisauth != null;
    }
  },
  created() {
    if (this.$route.path === '/ChangerPasswordView') {
      this.showChangePasswordModal = true;
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll);
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
  },
  methods: {
    handleScroll() {
      this.isScrolled = window.scrollY > 20;
    },
    scrollToHome() {
      const homeSection = document.getElementById("home");
      if (homeSection) homeSection.scrollIntoView({ behavior: "smooth" });
    },
    scrollToGrille() {
      const grilleSection = document.getElementById("grillePoly");
      if (grilleSection) grilleSection.scrollIntoView({ behavior: "smooth" });
    },
    scrollToContact() {
      const contactSection = document.getElementById("contact");
      if (contactSection) contactSection.scrollIntoView({ behavior: "smooth" });
    },
    scrollToFormateur() {
      this.$router.push("/FormateurView");
    },
    scrollToAdmin(){
      this.$router.push("/AdminView");
    },
    async deconnexion() {
      this.isLoggingOut = true;
      const auth = AuthStore();
      try {
        await auth.logout();
        await new Promise(resolve => setTimeout(resolve, 1000));
        await this.$router.push('/');
      } catch (error) {
        console.error("Erreur lors de la déconnexion:", error);
      } finally {
        this.isLoggingOut = false;
      }
    },
    openLoginModal() {
      this.showLoginModal = true;
    },
    closeLoginModal() {
      this.showLoginModal = false;

    },
    openSignUpModal() {
      this.showSignUpModal = true;
      this.showLoginModal = false;
    },
    closeSignUpModal() {
      this.showSignUpModal = false;
    },
    showForgotPassword() {
      this.showLoginModal = false;
      this.showForgotPasswordModal = true;
    },
    closeForgotPassword() {
      this.showForgotPasswordModal = false;
      this.showLoginModal = true;
    },
    closeChangePasswordModal() {
      this.showChangePasswordModal = false;
    },
    togglePasswordVisibility() {
      this.showEye = !this.showEye;
    },
    validateEmail() {
      if (this.isValidEmail(this.login.Email)) {
        this.errors.email = [];
      } else {
        this.errors.email = ['L\'email est invalide.'];
      }
    },
    validateForgotPasswordEmail() {
      if (this.isValidEmail(this.forgotPassword.Email)) {
        this.forgotPasswordErrors.email = [];
      } else {
        this.forgotPasswordErrors.email = ['L\'email est invalide.'];
      }
    },
    validatePassword() {
      if (this.login.Password.length >= 6) {
        this.errors.password = [];
      } else {
        this.errors.password = ['Le mot de passe doit contenir au moins 8 caractères.'];
      }
    },
    isValidEmail(email) {
      const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      return re.test(email);
    },
    async submitLogin() {
      if (this.login.Email !== "" && this.login.Password !== "") {
        this.loading = true;
        try {
          await AuthService.login(this.login.Email, this.login.Password);
          const auth = AuthStore();
          this.showSnackBar = true;
          this.snackbarColor = 'success';
          this.snackbarIcon = 'mdi-check-circle';
          this.text = 'Connexion réussie!';

          if (auth.getisadmin == 1) {
            this.$router.push('/AdminView');
          } else if (!auth.getisadmin) {
            this.$router.push('/FormateurView');
          }
        } catch (err) {
          this.showSnackBar=true;
          this.errors.email = '';
          this.errors.password = '';
          this.text = 'Connexion échouée: vérifiez vos informations.';
        } finally {
          this.loading = false;
        }
      }
      else{
        this.loading=false;
        this.errors.email="Champ email est requis";
        this.errors.password="Champ password est requis";
      }
    },
    async submitForgotPassword() {
      this.forgotPasswordLoading = true;
      try {
        await AuthService.forgotPassword(this.forgotPassword.Email);
        this.$toast.success('Un email de réinitialisation a été envoyé.');
        this.closeForgotPassword();
      } catch (err) {
        this.forgotPasswordErrors.email = ['Erreur lors de la réinitialisation du mot de passe'];
      } finally {
        this.forgotPasswordLoading = false;
      }
    },
    afficheSnackBar(data) {
      this.showSnackBar = true;
      this.snackbarColor = data.color;
      this.snackbarIcon = data.icon;
      this.text = data.message;
    }
  }
}
</script>

<style scoped>
.enterprise-navbar {
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.95) !important;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.navbar-scrolled {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
  background: rgba(255, 255, 255, 0.98) !important;
}

.navbar-container {
  max-width: 1440px;
  margin: 0 auto;
}

.enterprise-logo {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.enterprise-logo:hover {
  transform: scale(1.05);
}

.enterprise-nav-menu {
  background: transparent;
  border: none;
}

.nav-enterprise-item {
  margin: 0 4px;
  padding: 0 16px;
  height: 40px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 8px;
  color: #37474F;
  font-weight: 500;
  letter-spacing: 0.3px;
}

.nav-enterprise-item:hover {
  background: rgba(25, 118, 210, 0.08);
  color: #1976D2;
  transform: translateY(-1px);
}

.nav-icon {
  transition: transform 0.3s ease;
}

.nav-enterprise-item:hover .nav-icon {
  transform: scale(1.1);
}

.nav-enterprise-text {
  font-size: 0.95rem;
  font-weight: 500;
  text-transform: none;
}

.login-enterprise-cta,
.signup-enterprise-cta {
  height: 40px;
  padding: 0 24px;
  border-radius: 8px;
  text-transform: none;
  font-weight: 600;
  letter-spacing: 0.3px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.login-enterprise-cta {
  background: linear-gradient(135deg, #1976D2, #2196F3);
}

.login-enterprise-cta:hover,
.signup-enterprise-cta:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(25, 118, 210, 0.2);
}

.signup-enterprise-cta {
  border: 2px solid #1976D2;
}

.admin-btn {
  background: rgba(25, 118, 210, 0.05);
}

.admin-menu {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

.admin-menu-item {
  transition: background-color 0.2s ease;
  border-radius: 6px;
  margin: 4px;
}

.admin-menu-item:hover {
  background-color: rgba(25, 118, 210, 0.08);
}

.custom-tooltip {
  background: rgba(33, 33, 33, 0.95);
  border-radius: 6px;
  font-size: 0.85rem;
  padding: 6px 12px;
}

/* Modals styling */
.v-dialog {
  border-radius: 16px;
  overflow: hidden;
}

.header {
  background: #1976D2;
  padding: 24px;
}

.close-btn {
  opacity: 0.8;
  transition: all 0.2s ease;
  position: relative;
  left: 270px;
  /* */
  color: white;
}

.close-btn:hover {
  opacity: 1;
  transform: scale(1.1);
}

.hover-link {
  cursor: pointer;
  transition: opacity 0.2s ease;
}

.hover-link:hover {
  opacity: 0.8;
}
</style>