<template >
    <div style="width: 240vh;position: relative;left:-240px">
         <header
            class="header"
            :class="{
                'header-scrolled': isScrolled,
                'menu-open': isMenuOpen
            }"
            v-show="!isLoading"
        >
            <div class="header-background">
                <div class="animated-gradient"></div>
                <div class="header-blur"></div>
            </div>

            <section
                class="logo-section"
                @mouseover="logoHover = true"
                @mouseleave="logoHover = false"
                :class="{ 'logo-hover': logoHover }"
                @click="goToHome"
            >
                <div class="logo-container" >
                    <v-img
                        :src="require('@/assets/images/logo1.png')"
                        class="logo-image"
                        :class="{ 'logo-spin': logoHover }"
                        
                    />
                    <div class="logo-glow"></div>
                </div>
                <span class="logo-text" :class="{ 'text-highlight': logoHover }">
                    Company
                </span>
            </section>

            <section class="title-section">
                <div class="title-wrapper">
                    <h1 class="main-title" :class="{ 'title-small': isMobile }">
                        Grille de Polyvalence
                    </h1>
                    <div class="title-underline"></div>
                </div>
                <h5 class="subtitle" :class="{ 'fade-in': !isLoading }">
                    Section/Ligne: UAP {{id}}
                </h5>
            </section>

            <v-btn
                v-if="isMobile"
                icon
                @click="toggleMenu"
                class="menu-button"
                :class="{ 'menu-button-active': isMenuOpen }"
            >
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </v-btn>

            <section
                class="reference-section"
                v-show="!isMobile || !isMenuOpen"
                :class="{ 'slide-in': !isLoading }"
            >
                <div class="reference-container">
                    <div>
                        <h4 class="reference-text">
                            <strong>Référence:</strong>
                            <span class="highlight" v-tooltip="'Code unique'">
                                REF{{ randomRef }}
                            </span>
                        </h4>
                        <div class="reference-divider"></div>
                        <h4 class="reference-text">
                            <strong>Date de création:</strong>
                            <span class="highlight">{{ currentDate }}</span>
                        </h4>
                    </div>
                </div>
            </section>
        </header>

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

        <section class="main-content" :class="{ 'content-loaded': !isLoading }">
        <v-data-table
        v-model:page="page"
        :headers="headers"
        :items="filteredTasks"
        :items-per-page="itemsPerPage"
        class="custom-table"
    >

        <template v-slot:top>
            <v-card class="table-controls pa-4 mb-4" elevation="3" rounded="lg">
                <v-row align="center" justify="space-between" no-gutters class="controls-container">
                    <v-col cols="12" sm="8" class="pr-sm-4">
                        <v-text-field
                            v-model="searchQuery"
                            prepend-inner-icon="mdi-magnify"
                            label="Rechercher par nom de tâche"
                            hide-details
                            
                            variant="outlined"
                            density="comfortable"
                            class="search-field animate-field"
                            bg-color="white"
                        >
                            <template v-slot:prepend>
                                <v-slide-x-transition>
                                    <v-icon v-if="searchQuery" color="primary" class="animate-icon">
                                        mdi-file-search
                                    </v-icon>
                                </v-slide-x-transition>
                            </template>
                        </v-text-field>
                    </v-col>

                    <v-col cols="12" sm="4" class="mt-4 mt-sm-0">
                        <v-select
                            v-model="itemsPerPage"
                            :items="[5, 10, 15, 20]"
                            label="Éléments par page"
                            prepend-inner-icon="mdi-format-list-numbered"
                            hide-details
                            variant="outlined"
                            density="comfortable"
                            class="items-select animate-field"
                            bg-color="white"
                        >
                            <template v-slot:selection="{ item }">
                                <span class="font-weight-medium">{{ item.value }} éléments</span>
                            </template>
                        </v-select>
                    </v-col>
                </v-row>

                <!-- Ajout d'une barre de statistiques rapides -->
                <v-expand-transition>
                    <v-row v-if="searchQuery" class="mt-4 stats-row">
                        <v-col cols="12">
                            <v-chip-group>
                                <v-chip color="primary" label class="mr-2">
                                    <v-icon start>mdi-file-search</v-icon>
                                    {{ filteredTasks.length }} résultats trouvés
                                </v-chip>
                                <v-chip color="info" label>
                                    <v-icon start>mdi-percent</v-icon>
                                    Taux moyen: {{ averageRate }}%
                                </v-chip>
                            </v-chip-group>
                        </v-col>
                    </v-row>
                </v-expand-transition>
            </v-card>
        </template>

        <template v-slot:item="{ item, index }">
            <tr :class="{ 'row-hover': true }">
                <td v-if="index === 0" :rowspan="tasks.length" class="text-center font-weight-bold stats-cell">
                    {{ tauxTotal }}
                </td>
                <td>{{ item.name }}</td>
                <td>
                    <div class="progress-wrapper">
                        <div class="progress-bar" :style="{ width: item.couv }"></div>
                        <span class="progress-text">{{ item.couv }}</span>
                    </div>
                </td>
                <td>
                    <div class="value-display">
                        {{ item.actual }}
                    </div>
                </td>
                <td>
                    <div class="value-display">
                        {{ item.potential }}
                    </div>
                </td>
            </tr>
        </template>

        <template v-slot:bottom>
            <div class="pagination-wrapper">
                <v-pagination v-model="page" :length="pageCount" class="custom-pagination"></v-pagination>
            </div>
        </template>

    </v-data-table>
</section>
</div>
</template>

<script>
import UAP_Service from "@/Services/UAPServices/UAP";
export default {
    name: 'Tableau1',

    data() {
        return {
            randomRef: Math.floor(100 + Math.random() * 900),
            currentDate: null,
            logoHover: false,
            isScrolled: false,
            isMenuOpen: false,
            isLoading: true,
            isMobile: false,
            searchQuery: "",
            tauxTotal: 0,

            page: 1,
            itemsPerPage: 5,
            headers: [
                { title: 'Taux de polyvalence', key: 'taux'},
                { title: 'Nom de la tâche', key: 'name' },
                { title: 'Couverture de l activité', key: 'couv' },
                { title: 'Actuel', key: 'actual' },
                { title: 'Potentiel', key: 'potential' },
            ],
            tasksNames: [],
            tasks: [],
        };
    },
    props: {
        id: {
            type: Number,
            required: true,
        },
    },
    computed: {
        pageCount() {
            return Math.ceil(this.filteredTasks.length / this.itemsPerPage);
        },
        filteredTasks() {
      // Filtrer les taches par le nom de la tâche
      return this.tasks.filter(task =>
        task.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
        averageRate() {
        if (!this.filteredTasks.length) return 0;
        const sum = this.filteredTasks.reduce((acc, item) => {
        return acc + parseFloat(item.couv.replace('%', ''));
        }, 0);
        return (sum / this.filteredTasks.length).toFixed(1);
    },
    },

    created() {
        this.checkScreenSize();
        this.initDateTime();
        window.addEventListener('resize', this.checkScreenSize);
        window.addEventListener('scroll', this.handleScroll);

        // Simuler un temps de chargement
        setTimeout(() => {
            this.isLoading = false;
        }, 800);
    },

    beforeUnmount() {
        window.removeEventListener('resize', this.checkScreenSize);
        window.removeEventListener('scroll', this.handleScroll);
    },

    methods: {
        initDateTime() {
            const now = new Date();
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            };
            this.currentDate = new Intl.DateTimeFormat('fr-FR', options).format(now);
        },

        handleScroll() {
            this.isScrolled = window.scrollY > 50;
        },

        checkScreenSize() {
            this.isMobile = window.innerWidth <= 768;
            if (!this.isMobile) {
                this.isMenuOpen = false;
            }
        },

        toggleMenu() {
            this.isMenuOpen = !this.isMenuOpen;
        },

        goToHome() {
            this.$router.push('/');
        },
        async fetchPersonnels() {
            try {
            const response = await UAP_Service.GetAllPersonsUAP(this.id);
            const personnels = response.data.salaries; 
            this.tasks = []; 

            // Récupérer les noms de toutes les tâches
            this.tasksNames = response.data.taches; 
            let tauxTotal=0;

            // Calcul des valeurs pour chaque tâche
            for (let i = 0; i < this.tasksNames.length; i++) {
                let taskName = this.tasksNames[i];
                let actuel = 0;
                let potentiel = 0;

                // Parcourir chaque salarié pour compter ceux ayant A, B ou C pour cette tâche
                for (let j = 0; j < personnels.length; j++) {
                    let personnel = personnels[j];
                    let note = personnel.notes[taskName] || "D"; 

                    if (note === "A" || note === "B") {
                        actuel++;
                    }
                    if (note === "A" || note === "B" || note === "C") {
                        potentiel++;
                    }
                }

                let couverture = (potentiel > 0) ? ((actuel / potentiel) * 100).toFixed(1) : "0";
                tauxTotal += parseFloat(couverture);
                

                // Ajouter la tâche dans la liste des tâches
                this.tasks.push({
                    name: taskName,      
                    couv: couverture + "%",   
                    actual: actuel,      
                    potential: potentiel, 
                    
                });
            }
           
            this.tauxTotal = (tauxTotal / this.tasksNames.length).toFixed(1) + "%";
            this.isLoading = false; 

        } catch (error) {
            console.error("Erreur lors de la récupération des données :", error);
        }
    }
    },
    mounted() {
            this.fetchPersonnels();
    }
};
</script>

<style scoped>
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(135deg, #1a237e, #0d47a1, #01579b);
    color: #fff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    padding: 15px 30px;
    z-index: 1000;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100px;
}



.logo-section {
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.logo-hover {
    transform: scale(1.08);
}

.logo-image {
    height: 60px;
    width: 90px;
    transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.logo-spin {
    transform: rotate(360deg);
}

.logo-text {
    margin-left: 15px;
    font-size: 1.3em;
    font-weight: 600;
    transition: all 0.3s ease;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.text-highlight {
    color: #ffd54f;
    transform: scale(1.05);
}

.title-section {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.main-title {
    margin: 0;
    font-size: 2em;
    transition: all 0.3s ease;
    font-weight: 400;
    letter-spacing: 1px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.subtitle {
    margin-top: 8px;
    color: #e3f2fd;
    font-weight: 400;
}

.title-small {
    font-size: 1.5em;
}

.reference-section {
    background: rgba(255, 255, 255, 0.1);
    padding: 10px 15px;
    border-radius: 8px;
    backdrop-filter: blur(5px);
    transition: all 0.3s ease;
}

.reference-section:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

.reference-text {
    margin: 0;
    font-size: 0.9em;
    white-space: nowrap;
}

.highlight {
    font-weight: 600;
    color: #ffd54f;
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
    background: rgba(25, 118, 210, 0.05);
    z-index: 1001;
    backdrop-filter: blur(5px);
}

.loader-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.loader-text {
    color: #1976d2;
    font-size: 1.1em;
    animation: pulse 1.5s infinite;
}

.main-content {
    margin-top: 120px;
    padding: 20px;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.5s ease;
}

.content-loaded {
    opacity: 1;
    transform: translateY(0);
}

.custom-table {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.table-controls {
    padding: 16px;
    background: #f5f5f5;
}

.items-per-page {
    max-width: 200px;
}

.row-hover {
    transition: background-color 0.3s ease;
}

.row-hover:hover {
    background-color: #f5f5f5;
}

.stats-cell {
    background: #e3f2fd;
    font-weight: 600;
    color: #1976d2;
}

.progress-wrapper {
    position: relative;
    height: 24px;
    background: #f5f5f5;
    border-radius: 12px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #2196f3, #1976d2);
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.progress-actual {
    background: linear-gradient(90deg, #66bb6a, #43a047);
}

.progress-potential {
    background: linear-gradient(90deg, #ffa726, #f57c00);
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    font-weight: 500;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.pagination-wrapper {
    padding: 16px;
    background: #f5f5f5;
}

.custom-pagination {
    justify-content: center;
}

@keyframes pulse {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}

@media (max-width: 768px) {
    .header {
        padding: 10px 15px;
    }

    .logo-image {
        height: 40px;
        width: 60px;
    }

    .logo-text {
        display: none;
    }

    .reference-section {
        display: none;
    }

    .main-title {
        font-size: 1.4em;
    }
}



.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 90px;
    padding: 0 40px;
    z-index: 1000;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.header-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: -1;
}

.animated-gradient {
    position: absolute;
    width: 200%;
    height: 200%;
    top: -50%;
    left: -50%;
    background: linear-gradient(
        45deg,
        #0a2463,
        #1a3b8c,
        #2653b5,
        #1a3b8c,
        #0a2463
    );
    animation: gradientMove 15s ease infinite;
    opacity: 0.95;
}

.header-blur {
    position: absolute;
    inset: 0;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.1);
}

.header-scrolled {
    height: 70px;
    padding: 0 30px;
    backdrop-filter: blur(15px);
}

.logo-container {
    position: relative;
    overflow: hidden;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 15px;
    cursor: pointer;
    padding: 8px;
    border-radius: 12px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(255, 255, 255, 0.1);
}

.logo-hover {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.logo-image {
    height: 50px;
    width: 75px;
    transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    filter: brightness(1.1);
}

.logo-spin {
    transform: rotate(360deg) scale(1.1);
}

.logo-glow {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0.2), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.logo-hover .logo-glow {
    opacity: 1;
}

.logo-text {
    font-size: 1.4em;
    font-weight: 600;
    background: linear-gradient(90deg, #fff, #e3f2fd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.text-highlight {
    background: linear-gradient(90deg, #ffd54f, #ffecb3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    transform: scale(1.05);
}

.title-wrapper {
    position: relative;
    padding-bottom: 8px;
}

.main-title {
    font-size: 2.2em;
    font-weight: 500;
    letter-spacing: 1px;
    margin: 0;
    background: linear-gradient(90deg, #ffffff, #e3f2fd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.title-underline {
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ffffff, transparent);
}

.subtitle {
    margin-top: 8px;
    color: #e3f2fd;
    font-weight: 400;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.5s ease;
}

.subtitle.fade-in {
    opacity: 1;
    transform: translateY(0);
}

.reference-section {
    transform: translateX(50px);
    opacity: 0;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.reference-section.slide-in {
    transform: translateX(0);
    opacity: 1;
}

.reference-card {
    background: rgba(255, 255, 255, 0.1);
    padding: 12px 20px;
    border-radius: 12px;
    backdrop-filter: blur(5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.reference-card:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.reference-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    margin: 8px 0;
}

.reference-text {
    margin: 0;
    font-size: 0.95em;
    color: #e3f2fd;
}

.highlight {
    color: #ffd54f;
    font-weight: 600;
    text-shadow: 0 0 10px rgba(255, 213, 79, 0.3);
}

.hamburger {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 8px;
}

.hamburger span {
    display: block;
    width: 24px;
    height: 2px;
    background-color: #fff;
    transition: all 0.3s ease;
}

.menu-button-active .hamburger span:nth-child(1) {
    transform: translateY(6px) rotate(45deg);
}

.menu-button-active .hamburger span:nth-child(2) {
    opacity: 0;
}

.menu-button-active .hamburger span:nth-child(3) {
    transform: translateY(-6px) rotate(-45deg);
}

@keyframes gradientMove {
    0% {
        transform: rotate(0deg);
    }
    50% {
        transform: rotate(180deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

@media (max-width: 768px) {
    .header {
        padding: 0 20px;
        height: 70px;
    }

    .logo-section {
        padding: 6px;
    }

    .logo-image {
        height: 40px;
        width: 60px;
    }

    .logo-text {
        display: none;
    }

    .main-title {
        font-size: 1.6em;
    }

    .reference-section {
        position: absolute;
        top: 70px;
        left: 0;
        width: 100%;
        background: rgba(10, 36, 99, 0.95);
        padding: 15px;
        transform: translateY(-100%);
        transition: transform 0.3s ease;
    }

    .menu-open .reference-section {
        transform: translateY(0);
    }
}

.table-controls {
  background: linear-gradient(145deg, #ffffff, #f8f9fa);
  transition: all 0.3s ease;
}

.table-controls:hover {
  transform: translateY(-2px);
}

.animate-field {
  transition: all 0.3s ease;
}

.animate-field:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.animate-icon {
  transition: all 0.3s ease;
}

.stats-row {
  border-top: 1px solid rgba(0, 0, 0, 0.05);
  padding-top: 16px;
}

.v-chip {
  transition: all 0.3s ease;
}

.v-chip:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

@media (max-width: 600px) {
  .controls-container {
    flex-direction: column;
  }
  
  .items-select {
    margin-top: 16px;
  }
}

.progress-wrapper {
    position: relative;
    height: 24px;
    background: #f5f5f5;
    border-radius: 12px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #2196f3, #1976d2);
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Styles modifiés pour les valeurs numériques */
.progress-actual {
    background: none;
    width: auto !important;
}

.progress-potential {
    background: none;
    width: auto !important;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #333; /* Changé pour une meilleure lisibilité */
    font-weight: 500;
}

/* Nouveau style pour les valeurs numériques */
.value-display {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    font-weight: 500;
    color: #333;
    position: relative;
    left:-60px;
}
</style>