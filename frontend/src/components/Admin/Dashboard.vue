<template>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="admin-profile p-4">
            <div class="d-flex align-items-center">
                <template v-if="ProfileImage">
                    <img :src="'http://127.0.0.1:8000/storage/' + ProfileImage" class="rounded-circle" alt="Admin"
                        width="60" height="60">
                </template>
                <div class="profile-info" style="margin-left: 10px;">
                    <h5 class="text-white mb-1">{{ userName }} {{ lastName }}</h5>
                    <div class="online-status">
                        <span class="status-dot"></span>
                        <span class="status-text">En ligne</span>
                    </div>
                </div>
            </div>
        </div>
        <nav class="nav flex-column mt-3">
            <a class="nav-link active" href="#"><i class="fas fa-fw fa-tachometer-alt"></i> Dashboard</a>
            <hr class="sidebar-divider my-2">
            <a href="/" class="text-decoration-none">
                <div class="sidebar-heading px-3 py-2 text-uppercase text-white-50"><small>Acceuil</small></div>
            </a>
            <hr class="sidebar-divider my-2">
            <a href="#" class="text-decoration-none" @click.prevent="setActivView('statistics')">
                <div class="sidebar-heading px-3 py-2 text-uppercase text-white-50"><small>Statistiques</small></div>
            </a>
            <hr class="sidebar-divider my-2">
            <a href="#" class="text-decoration-none" @click.prevent="setActivView('gestionFormateurs')">
                <div class="sidebar-heading px-3 py-2 text-uppercase text-white-50"><small>Gestion formateurs</small></div>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
     <div v-if="activViewAdmin==='gestionFormateurs'">
        <GestionFormateur />
     </div>
    <div v-if="activViewAdmin==='statistics'" class="main-content">
        <!-- Topbar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control border-0 bg-light" placeholder="Rechercher...">
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown me-3">
                    <button class="btn position-relative" data-bs-toggle="dropdown"
                        @click.prevent="this.countNotReadNotifications = 0">
                        <i class="fas fa-bell fa-fw"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            v-if="this.countNotReadNotifications">
                            {{ this.countNotReadNotifications + "+" }}
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown scrollable-dropdown">
                        <h6 class="dropdown-header">Centre de notifications</h6>
                        <div v-for="(Notification, index) in Notifications" :key="index" class="notification-item"
                            :class="{ 'bg-gray': !this.isRead[Notification.id]?.isRead, 'bg-white': this.isRead[Notification.id]?.isRead }">
                            <button class="dropdown-item" @click.stop.prevent="notificatioIsRead(Notification.id)">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-sign-in-alt fa-fw me-2 text-primary"
                                        style="position: relative;top:10px" v-if="Notification.typeMessage === 0"></i>

                                    <i class="fas fa-user-plus fa-fw me-2 text-success"
                                        style="position: relative;top:10px" v-if="Notification.typeMessage === 1"></i>

                                    <i class="fas fa-user-edit fa-fw me-2 text-warning"
                                        style="position: relative; top: 10px" v-if="Notification.typeMessage === 2"></i>


                                    <div class="notification-content">
                                        <span>{{ Notification.message }}</span>
                                        <div class="text-muted notification-details">
                                            <strong>{{ Notification.nomUser + " " + Notification.lastname }}</strong>
                                            (id: {{ Notification.userId }}) à <strong>{{ Notification.sendAt }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>


                </div>
                <div class="dropdown">
                    <button class="btn d-flex align-items-center" data-bs-toggle="dropdown">
                        <v-icon class="mr-2">mdi-account</v-icon>
                        <span>Admin</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="/ProfileAdmin"><i class="fas fa-user fa-fw me-2"></i> Profile</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" @click.prevent="logout"><i
                                class="fas fa-sign-out-alt fa-fw me-2"></i>
                            Déconnexion</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="container-fluid px-0">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card primary p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-primary text-uppercase mb-1"><small class="fw-bold">Salariés
                                    </small></div>
                                <div class="h5 mb-0 font-weight-bold">{{ nombreSalaries }}</div>
                            </div>
                            <i class="fas fa-users icon text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card success p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-success text-uppercase mb-1"><small class="fw-bold">UAP</small>
                                </div>
                                <div class="h5 mb-0 font-weight-bold">{{ nombreUap }}</div>
                            </div>
                            <i class="fas fa-check-circle icon text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card warning p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-warning text-uppercase mb-1"><small class="fw-bold">taches
                                        Actifs</small></div>
                                <div class="h5 mb-0 font-weight-bold">{{ nombreTaches }}</div>
                            </div>
                            <i class="fas fa-book icon text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card danger p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-danger text-uppercase mb-1"><small class="fw-bold">Formateurs</small>
                                </div>
                                <div class="h5 mb-0 font-weight-bold">{{ nombreFormateurs }}</div>
                            </div>
                            <i class="fas fa-chalkboard-teacher icon text-danger"></i>
                        </div>
                    </div>
                </div>
                <!-- Other stat cards remain the same -->
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="chart-container">
                        <h6 class="mb-3">Salariés par UAP</h6>
                        <ChartJsUAP style="position: relative;bottom: 30px;left: 90px;" />
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5">
                    <div class="chart-container">
                        <h6 class="mb-3">Taches par UAP</h6>
                        <ChartJsTaches style="position: relative;bottom: 30px;left: 30px;" />
                    </div>
                </div>
            </div>

            <!-- Employee Management Section -->
            <div class="row">
                <div class="col-xl-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Aperçu des UAP</h6>
                        </div>
                        <div class="card-body" style="max-height: 500px;overflow-y: scroll;">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>UAP</th>
                                            <th>Responsable</th>
                                            <th>Taux Polyvalence</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(uap, index) in UAP" :key="index">
                                            <td>
                                                <div class="fw-bold">{{ uap.name }}</div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{ uap.Responsable }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2">
                                                        <div class="progress-bar bg-success"
                                                            :style="{ width: uap.couv }">
                                                        </div>
                                                    </div>
                                                    {{ uap.couv }}
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Couverture des taches</h6>
                        </div>
                        <div class="card-body" style="max-height: 300px;overflow-y: scroll;">
                            <div class="task-item" v-for="(TacheEvaluation, index) in TachesEvaluation" :key="index">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">{{ TacheEvaluation.name }}</h6>
                                        <small class="text-muted">{{ TacheEvaluation.UAPname }}</small>
                                    </div>
                                    <span class="badge bg-warning">{{ TacheEvaluation.couv + '%' }}</span>
                                </div>
                                <div class="progress mt-2 task-progress">
                                    <div class="progress-bar bg-warning" :style="{ width: TacheEvaluation.couv + '%' }">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Évaluations Récentes</h6>
                    </div>
                    <div class="card-body" style="max-height: 500px;">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Employé</th>
                                        <th>Tache</th>
                                        <th>Note</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(Evaluation, index) in Evaluations" :key="index">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="fw-bold">{{ Evaluation.nomSalarie }}</div>
                                                    <small class="text-muted">ID: {{ Evaluation.id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary">{{ Evaluation.nomTache }}</span></td>
                                        <td>
                                            <div class="evaluation-badge"
                                                :style="{ backgroundColor: getBackgroundColor(Evaluation.note) }">
                                                {{ Evaluation.note }}
                                            </div>

                                        </td>
                                        <td>{{ Evaluation.dateEvaluation }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Catégories de Compétences</h6>
                    </div>
                    <div class="card-body" style="overflow-y: scroll; max-height: 400px;">
                        <div class="row" v-for="(chunk, rowIndex) in chunkedCategories" :key="rowIndex">
                            <div class="col-md-6 mb-3" v-for="(categorie, index) in chunk" :key="index">
                                <div class="card uap-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ categorie.name }}</h6>
                                                <small class="text-muted">{{ categorie.nbreTaches }} taches</small>
                                            </div>
                                            <div class="category-pill bg-primary bg-opacity-10 text-white">
                                                {{ categorie.pourcentage }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ChartJsUAP from "@/components/Admin/ChartJsUAP.vue";
import ChartJsTaches from "@/components/Admin/ChartJsTaches.vue"
import GestionFormateur from "./GestionFormateur.vue";
import Admin_Service from "@/Services/Admin/Admin";
import { AuthStore } from "@/store/auth.js";
import dayjs from "dayjs";


import { defineComponent } from "vue";
// import auth from "@/Services/Auth/auth";
export default defineComponent({
    name: "Dashboard",
    components: {
        ChartJsUAP,
        ChartJsTaches,
        GestionFormateur
    },
    data() {
        return {
            // showStatistics:true,
            // showGestionFormateurs:false,
            nombreSalaries: null,
            nombreUap: null,
            nombreTaches: null,
            nombreFormateurs: null,
            tasksNames: [],
            UAP: [],
            Evaluations: [],
            Categories: [],
            TachesEvaluation: [],
            Notifications: [],
            countNotReadNotifications: 0,
            isRead: {}
        }
    },
    computed: {
        activViewAdmin(){
            const auth = AuthStore();
            return auth.getActiveViewAdmin;
        },
        userName() {
            const auth = AuthStore();
            return auth.getuser?.name || "Utilisateur";
        },
        lastName() {
            const auth = AuthStore();
            return auth.getuser?.lastname || "Utilisateur";
        },
        ProfileImage() {
            const auth = AuthStore();
            return auth.getuser?.image || null;
        },
        chunkedCategories() {
            let result = [];
            for (let i = 0; i < this.Categories.length; i += 2) {
                result.push(this.Categories.slice(i, i + 2));
            }
            return result;
        }
    },
    methods: {
        setActivView(view) {
            const auth=AuthStore();
            auth.setActiveViewAdmin(view);
        },
        async count() {
            try {
                const res = await Admin_Service.count();
                return res.data;
            } catch (err) {
                console.log("erreur lors de la récupération des données", err);
            }
        },
        async logout() {
            const auth = AuthStore();
            try {
                await auth.logout();
                auth.setStatusMessage("Déconnexion en cours ...");
                console.log(auth.getStatusMessage);
                await this.$router.push('/');
            } catch (error) {
                console.error("Erreur lors de la déconnexion:", error);
            }
        },
        async fetchPersonnels() {
            try {
                const UapInfos = await Admin_Service.getUapInfos();
                const data = UapInfos.data;

                data.forEach(uap => {
                    this.UAP.push({
                        name: uap.name,
                        Responsable: uap.responsable,
                        couv: (uap.couv).toFixed(2)+"%"
                    });
                });

            } catch (err) {
                console.error("Erreur lors de la récupération des données :", err);
            }
        },
        getBackgroundColor(note) {
            switch (note) {
                case 'A':
                    return '#1976d2 ';
                case 'B':
                    return 'yellow';
                case 'C':
                    return 'orange';
                case 'D':
                    return 'gray';
                default:
                    return 'transparent';
            }
        },
        async fetchCategories() {
            try {
                const res = await Admin_Service.getCategoriesData();
                const data = res.data;
                for (let i = 0; i < data.length; i++) {
                    this.Categories.push({
                        name: data[i].categorie,
                        nbreTaches: data[i].count,
                        pourcentage: (data[i].count / data[i].countTotal).toFixed(2)
                    });
                }
            } catch (err) {
                console.log("Erreur lors de la récupération des catégories", err);
            }
        },
        async fetchTacheEvaluation() {
            try {
                const res = await Admin_Service.fetchTacheEvaluation();
                const data = res.data;
                for (let i = 0; i < data.length; i++) {
                    this.TachesEvaluation.push({
                        name: data[i].name,
                        couv: (data[i].couv).toFixed(2),
                        UAPname: data[i].nomUAP
                    });
                }
            } catch (err) {
                console.log("Erreur lors de la récupération des données", err);
            }
        },
        async fetchNotifications() {
            try {
                const res = await Admin_Service.fetchNotifications();
                const data = res.data;

                this.countNotReadNotifications = data[1];
                this.Notifications = [];

                for (let i = 0; i < data[0].length; i++) {
                    this.Notifications.push({
                        nomUser: data[0][i].name,
                        lastname: data[0][i].lastname,
                        message: data[0][i].notification.message,
                        isRead: data[0][i].notification.isRead,
                        sendAt: dayjs(data[0][i].notification.created_at).format("YYYY-MM-DD HH:mm"),
                        id: data[0][i].notification.id,
                        typeMessage: data[0][i].notification.typeMessage,
                        userId: data[0][i].userId
                    });
                    this.isRead[data[0][i].notification.id] = { isRead: data[0][i].notification.isRead };
                }
            } catch (err) {
                console.log("Erreur lors de la récupération des notifications", err);
            }
        },
        async notificatioIsRead(id) {
            if (!id) {
                console.error("L'ID de la notification est invalide :", id);
                return;
            }
            try {
                this.isRead[id].isRead = true;
                await Admin_Service.notificatioIsRead(id);
                console.log("Notification mise à jour avec succès !");
            } catch (err) {
                console.log("Erreur lors de la modification de la notification", err);
            }
        }

    },
    async mounted() {
        await this.fetchNotifications();
        const counts = await this.count();
        if (counts) {
            for (let i = 0; i < counts.latestEvaluations.length; i++) {
                this.Evaluations.push({
                    nomSalarie: counts.latestEvaluations[i].salarie.name,
                    id: counts.latestEvaluations[i].salarie_id,
                    nomTache: counts.latestEvaluations[i].tache.name,
                    note: counts.latestEvaluations[i].noteEvaluation,
                    dateEvaluation: counts.latestEvaluations[i].dateEvaluation
                });
            }
            this.nombreSalaries = counts.salaries;
            this.nombreUap = counts.uaps;
            this.nombreTaches = counts.taches;
            this.nombreFormateurs = counts.formateurs;
        }
        await this.fetchCategories();
        await this.fetchTacheEvaluation();
        await this.fetchPersonnels();
    }

})
</script>
<style>
.bg-gray {
    background-color: #f0f0f0 !important;
}

.bg-white {
    background-color: white !important;
}

.notification-item {
    padding: 10px 15px;
    border-bottom: 1px solid #ddd;
    /* Séparation entre les notifications */
}

.notification-content {
    display: flex;
    flex-direction: column;
}

.notification-details {
    font-size: 12px;
    margin-top: 2px;
    color: gray;
}

.scrollable-dropdown {
    max-height: 300px;
    overflow-y: auto;
}

.logo-background {
    background: linear-gradient(135deg, #1976d2 0%, #1565c0 50%, #ff9800 150%) !important;
    box-shadow: 0 6px 20px rgba(25, 118, 210, 0.25);
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
}

.logo-glow {
    position: absolute;
    width: 52px;
    height: 52px;
    background: radial-gradient(circle, rgba(25, 118, 210, 0.15) 0%, transparent 70%);
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

:root {
    --primary-color: #4e73df;
    --secondary-color: #1cc88a;
    --warning-color: #f6c23e;
    --danger-color: #e74a3b;
    --dark-color: #1a2942;
    --light-color: #f8f9fc;
}

body {
    background: #f8f9fc;
}

/* Sidebar Styles */
.sidebar {
    background: linear-gradient(180deg, var(--dark-color) 10%, #224abe 100%);
    min-height: 100vh;
    position: fixed;
    z-index: 100;
    padding: 0;
    width: 250px;
}

.sidebar-brand {
    height: 70px;
    background: rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    font-weight: bold;
}

.sidebar .nav-link {
    color: rgba(255, 255, 255, .8);
    padding: 1rem;
    margin: 0.2rem 1rem;
    border-radius: 5px;
    display: flex;
    align-items: center;
}

.sidebar .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.sidebar .nav-link i {
    margin-right: 0.5rem;
    width: 1.5rem;
    text-align: center;
}

.sidebar .nav-link.active {
    background: rgba(255, 255, 255, 0.15);
    color: white;
}

/* Main Content Styles */
.main-content {
    margin-left: 250px;
    padding: 1.5rem;
}

/* Topbar Styles */
.topbar {
    background: white;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, .15);
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-radius: 0.35rem;
}

.search-box {
    position: relative;
}

.search-box input {
    padding-left: 2.5rem;
    border-radius: 20px;
}

.search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #d1d3e2;
}

/* Card Styles */
.stat-card {
    border-left: 4px solid;
    background: white;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, .15);
    margin-bottom: 1.5rem;
}

.stat-card.primary {
    border-left-color: var(--primary-color);
}

.stat-card.success {
    border-left-color: var(--secondary-color);
}

.stat-card.warning {
    border-left-color: var(--warning-color);
}

.stat-card.danger {
    border-left-color: var(--danger-color);
}

.stat-card .icon {
    font-size: 2rem;
    opacity: 0.3;
}

/* Table Styles */
.table-card {
    background: white;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, .15);
    margin-bottom: 1.5rem;
}

.table-card .card-header {
    background: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table th {
    background: #f8f9fc;
    border-top: none;
}

/* Employee Management Styles */
.employee-stats-card {
    background: white;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, .15);
    margin-bottom: 1.5rem;
    border-left: 4px solid var(--primary-color);
}

.task-progress {
    height: 8px;
    border-radius: 4px;
}

.evaluation-badge {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
}

.uap-card {
    border: none;
    transition: transform 0.2s;
}

.uap-card:hover {
    transform: translateY(-5px);
}

.category-pill {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.task-item {
    padding: 1rem;
    border-left: 3px solid transparent;
    transition: all 0.2s;
}

.task-item:hover {
    background: #f8f9fc;
    border-left-color: var(--primary-color);
}

/* Chart Styles */
.chart-container {
    height: 300px;
    background: white;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, .15);
    padding: 1rem;
    margin-bottom: 1.5rem;
}

/* Notification Styles */
.notification-dropdown {
    min-width: 300px;
}

.notification-item {
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #e3e6f0;
}

.notification-item:last-child {
    border-bottom: none;
}

.admin-profile {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.profile-image img {
    border: 2px solid #ffffff;
}

.online-status {
    display: flex;
    align-items: center;
}

.status-dot {
    width: 8px;
    height: 8px;
    background-color: #28a745;
    border-radius: 50%;
    margin-right: 6px;
}

.status-text {
    color: #28a745;
    font-size: 14px;
}
</style>