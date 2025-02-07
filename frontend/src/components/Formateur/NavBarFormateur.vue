<template>
    <!-- Overlay de déconnexion -->
    <v-overlay v-model="isLoggingOut" class="align-center justify-center" persistent>
        <v-progress-circular indeterminate color="primary" size="64" style="margin-left: 100px;"/>
        <div class="text-h6 mt-4 white--text">Déconnexion en cours...</div>
    </v-overlay>

        <section>
            <v-navigation-drawer
                style="z-index: 1;"
                v-show="!isLoading"
                permanent
                class="custom-sidebar d-flex flex-column"
                width="280"
            >
                <!-- Logo et Nom -->
                <div class="brand-section pa-4">
                    <a href="/" class="text-decoration-none">
                        <div class="brand-container d-flex align-center mb-6">
                        <div class="logo-wrapper">
                            <v-avatar size="52" class="logo-background elevation-3">
                                <span class="text-h5 font-weight-bold white--text">E1T</span>
                            </v-avatar>
                            <div class="logo-glow"></div>
                        </div>
                        <div class="brand-text ms-4">
                            <span class="company-name">EleOneTech</span>
                            <span class="company-slogan">Innovation & Excellence</span>
                        </div>
                        </div>
                    </a>
                   

                    <!-- Section Title -->
                    <div class="section-title-container mb-6">
                        <h1 class="text-h5 font-weight-bold section-title">Grille de Polyvalence</h1>
                        <div class="title-underline"></div>
                    </div>
                </div>

                <v-divider class="mb-4"></v-divider>

                <!-- User Profile Section -->
                <div class="user-section px-4 mb-6">
                    <v-btn
                        class="user-profile-btn"
                        variant="elevated"
                        elevation="2"
                        block
                    >
                        <v-avatar size="36" class="user-avatar me-2">
                            <v-icon size="22">mdi-account</v-icon>
                        </v-avatar>
                        <span class="user-name">{{ userName }}</span>
                        <v-icon end size="20" class="ms-2">mdi-chevron-down</v-icon>
                    </v-btn>
                </div>

                <!-- Navigation Menu -->
                <v-list class="navigation-list px-2">
                    <v-list-item
                        v-for="(item, index) in menuItems"
                        :key="index"
                        :value="item"
                        @click="handleMenuClick(item.action)"
                        class="menu-item mb-2"
                        rounded="lg"
                    >
                        <template v-slot:prepend>
                            <v-icon :icon="item.icon" size="22" class="me-3"></v-icon>
                        </template>
                        <v-list-item-title>{{ item.title }}</v-list-item-title>
                    </v-list-item>
                </v-list>

                <!-- Footer copyright élégant -->
                <div class="mt-auto">
                    <v-divider></v-divider>
                    <div class="footer-elegant pa-4">
                        <div class="footer-content d-flex flex-column align-center">
                            <div class="copyright-logo mb-2">
                                <v-icon color="primary" size="20">mdi-copyright</v-icon>
                            </div>
                            <div class="copyright-text text-center">
                                <div class="text-body-2 font-weight-medium primary--text mb-1">
                                    EleOneTech
                                </div>
                                <div class="text-caption text-medium-emphasis">
                                    Tous droits réservés {{ new Date().getFullYear() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </v-navigation-drawer>
        </section>
</template>

<script>
import { AuthStore } from "@/store/auth";

export default {
    name: "FormateurView",
    data() {
        return {
            isLoggingOut: false,
            menuItems: [
                { title: "Mon Profil", icon: "mdi-account-circle", action: "profile" },
                { title: "Gestion des Salariés", icon: "mdi-account-group", action: "employees" },
                { title: "Gestion des Tâches", icon: "mdi-clipboard-list", action: "tasks" },
                { title: "Évaluation", icon: "mdi-star", action: "evaluation" },
                { title: "Déconnexion", icon: "mdi-logout", action: "logout" },
            ],
        };
    },

    computed: {
        userName() {
            const auth = AuthStore();
            return auth.getuser?.name || "Utilisateur";
        },
    },
    methods: {
        handleMenuClick(action) {
            switch (action) {
                case "logout":
                    this.logout();
                    break;
                case "profile":
                    this.goToProfile();
                    break;
                case "employees":
                    this.goToEmployees();
                    break;
                case "tasks":
                    this.goToTasks();
                    break;
                case "evaluation":
                    this.goToEvaluation();
                    break;
            }
        },

        async logout() {
            const auth = AuthStore();
            
            try {
                await auth.logout();
                auth.setStatusMessage("Déconnexion en cours ...");
                await this.$router.push('/');
            } catch (error) {
                console.error("Erreur lors de la déconnexion:", error);
            }
        },
        goToProfile() {
            this.$emit("changeView", "ProfileFormateur");
        },

        goToEmployees() {
            this.$emit("changeView", "Employees");
        },

        goToTasks() {
            this.$emit('changeView','TachesCategories');
        },

        goToEvaluation() {
            this.$emit("changeView", "Evaluation");
        },
    },
};
</script>

<style scoped>
.custom-sidebar {
    background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(255,255,255,0.95)) !important;
    backdrop-filter: blur(15px);
    border-right: 1px solid rgba(25, 118, 210, 0.12);
    box-shadow: 4px 0 30px rgba(0, 0, 0, 0.03);
}

.logo-background {
    background: linear-gradient(135deg, #1976d2 0%, #1565c0 50%, #ff9800 150%) !important;
    box-shadow: 0 6px 20px rgba(25, 118, 210, 0.25);
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
}

.logo-background:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(25, 118, 210, 0.3);
}

.logo-glow {
    position: absolute;
    width: 52px;
    height: 52px;
    background: radial-gradient(circle, rgba(25,118,210,0.15) 0%, transparent 70%);
    animation: pulse 3s infinite;
}

.company-name {
    font-size: 1.4rem;
    font-weight: 700;
    background: linear-gradient(135deg, #1976d2 20%, #ff9800);
    -webkit-background-clip: text;
    color: transparent;
    display: block;
    letter-spacing: 0.5px;
}

.company-slogan {
    font-size: 0.85rem;
    color: #555;
    display: block;
    margin-top: -2px;
    letter-spacing: 0.3px;
}

.section-title-container {
    position: relative;
    text-align: center;
}

.section-title {
    background: linear-gradient(135deg, #1976d2, #1565c0);
    -webkit-background-clip: text;
    color: transparent;
    position: relative;
    z-index: 1;
    letter-spacing: 0.5px;
}

.title-underline {
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 40%;
    height: 2.5px;
    background: linear-gradient(to right, #1976d2, #ff9800);
    border-radius: 4px;
    opacity: 0.8;
}

.user-profile-btn {
    background: linear-gradient(to right, rgba(25,118,210,0.08), rgba(255,152,0,0.08)) !important;
    border: 1px solid rgba(25,118,210,0.15);
    transition: all 0.3s ease;
    height: 48px;
}

.user-profile-btn:hover {
    background: linear-gradient(to right, rgba(25,118,210,0.12), rgba(255,152,0,0.12)) !important;
    transform: translateY(-2px);
    border: 1px solid rgba(25,118,210,0.2);
}

.user-avatar {
    background: linear-gradient(135deg, #1976d2, #ff9800);
    border: 2px solid rgba(255,255,255,0.9);
}

.navigation-list {
    background: transparent;
}

.menu-item {
    transition: all 0.2s ease;
    border-radius: 12px;
    margin-bottom: 8px;
}

.menu-item:hover {
    background: linear-gradient(to right, rgba(25,118,210,0.08), rgba(255,152,0,0.08));
    transform: translateX(4px);
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.15);
        opacity: 0.3;
    }
    100% {
        transform: scale(1);
        opacity: 0.5;
    }
}

</style>