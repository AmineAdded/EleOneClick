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
    <div v-else style="display: grid; grid-template-columns: 1fr 6fr">

        <NavBarFormateur @changeView="setActiveView" />
        <ProfileFormateur v-if="activeView === 'ProfileFormateur'" />
        <Employees v-if="activeView === 'Employees'"/>
        <Evaluation v-if="activeView === 'Evaluation'"/>
        <div v-if="activeView==='TachesCategories'">
            <Taches />
            <Categories/>
            <Categores/>
        </div>

        <div v-if="statusBienvenue" class="welcome-box">
            <h1>Bienvenue {{ userName }}</h1>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import { AuthStore } from "@/store/auth.js"; 

import NavBarFormateur from "@/components/Formateur/NavBarFormateur.vue";
import ProfileFormateur from "@/components/Formateur/ProfileFormateur.vue";
import Employees from "@/components/Formateur/Employees.vue";
import Evaluation from "@/components/Formateur/Evaluation.vue";
import Taches from "@/components/Formateur/Taches.vue";
import Categories from "@/components/Formateur/Categories.vue";

export default defineComponent({
    name: "FormateurView",
    components: {
        NavBarFormateur,
        ProfileFormateur,
        Employees,
        Evaluation,
        Taches,
        Categories
    },
    data() {
        return {
            isLoading: true, 
        };
    },
    created() {
        setTimeout(() => {
            this.isLoading = false;
        }, 800);
    },
    methods: {
        setActiveView(viewName) {
            const authStore = AuthStore(); 
            authStore.setActiveView(viewName);
        },
    },
    computed: {
        activeView() {
            const authStore = AuthStore(); 
            return authStore.getActiveView;   
        },
        statusBienvenue(){
            const authStore = AuthStore(); 
            return authStore.getStatusBienvenue;
        },
        userName(){
            const authStore = AuthStore(); 
            return authStore.getuser?.name || null;
        }
    },
});
</script>

<style>
.welcome-box {
    position: fixed;
    top: 30px;
    right: -400px; /* Commence hors écran */
    background: linear-gradient(135deg, #2196F3, #4CAF50);
    color: white;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    animation: slideIn 0.6s ease forwards, fadeOut 0.6s ease 2.4s forwards;
    z-index: 1000;
    min-width: 300px;
}

.welcome-box h1 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 500;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 10px;
}

.welcome-box h1::before {
    content: '👋';
    font-size: 1.4rem;
    animation: wave 1s ease-in-out infinite;
}

@keyframes slideIn {
    from {
        right: -400px;
        opacity: 0;
    }
    to {
        right: 20px;
        opacity: 1;
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-20px);
    }
}

@keyframes wave {
    0%, 100% {
        transform: rotate(0deg);
    }
    25% {
        transform: rotate(-20deg);
    }
    75% {
        transform: rotate(20deg);
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
</style>
