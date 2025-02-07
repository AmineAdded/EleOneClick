<template>
    <div class="personnel-evaluation-container">
        <div class="refined-header">
            <div class="header-content">
                <h1 class="compact-title">
                    <span class="title-text">Grille d'Évaluation Personnelle</span>
                </h1>
            </div>
        </div>

        <section class="table-section">
            <v-card class="advanced-search-container" elevation="2" rounded="lg">
                <v-text-field
                    v-model="searchQuery"
                    label="Rechercher personnel"
                    variant="outlined"
                    
                    density="compact"
                    class="search-input"
                    color="primary"
                    style="position: relative; top: 10px; left: -520px;"
                >
                    <template v-slot:prepend-inner>
                        <v-icon color="grey-darken-1">mdi-magnify</v-icon>
                    </template>
                </v-text-field>
            </v-card>

            <v-data-table
                :headers="headers"
                :items="paginatedPersonnels"
                :items-per-page="itemsPerPage"
                class="professional-table"
                elevation="4"
            >

                <template v-slot:item="{ item }">
                    <tr class="hover-row">
                        <td>{{ item.salarie_id }}</td>
                        <td>{{ item.salarie_name }}</td>
                        <td>{{ item.salarie_lastname }}</td>
                        <td>
                            <v-chip 
                                :color="item.formationIPC === 1 ? 'success' : 'warning'"
                                size="small"
                            >
                                {{ item.formationIPC === 1 ? 'Oui':'Non' }}
                            </v-chip>
                        </td>
                        <!-- Dynamically display notes for each task -->
                        <td v-for="(task, index) in taskNames" :key="index">
                            <div :class="getColorClass(item.notes[task])" class="evaluation-cell">
                                {{ item.notes[task] }}
                            </div>
                        </td>
                    </tr>
                </template>

                <template v-slot:bottom>
                    <div class="custom-footer d-flex align-center justify-space-between pa-2">
                        <div class="text-caption text-medium-emphasis">
                            Total: {{ filteredPersonnels.length }} personnels
                        </div>
                        <v-pagination
                            v-model="page"
                            :length="Math.ceil(filteredPersonnels.length / itemsPerPage)"
                            total-visible="5"
                            
                        ></v-pagination>
                    </div>
                </template>
            </v-data-table>
        </section>
    </div>
</template>

<script>
import UAP_Service from "@/Services/UAPServices/UAP";

export default {
    name: 'Tableau2',
    data() {
        return {
            page: 1,
            itemsPerPage: 5,
            searchQuery: "",
            headers: [
                { title: 'ID', key: 'salarie_id', sortable: true },
                { title: 'Nom', key: 'salarie_name', sortable: true },
                { title: 'Prénom', key: 'salarie_lastname', sortable: true },
                { title: 'Formation IPC', key: 'formationIPC', sortable: true },
            ],
            personnels: [], // Contiendra les salariés avec leurs notes
            taskNames: [] // Contiendra les noms des tâches
        };
    },
    props: {
        id: {
            type: Number,
            required: true,
        },
    },
    computed: {
        filteredPersonnels() {
        return this.personnels.filter(personnel => 
            personnel.salarie_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            personnel.salarie_lastname.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
    },
    paginatedPersonnels() {
        const start = (this.page - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        return this.filteredPersonnels.slice(start, end);
    }
    },
    methods: {
        getColorClass(note) {
            const colorMap = {
                'A': 'evaluation-a',
                'B': 'evaluation-b',
                'C': 'evaluation-c',
                'D': 'evaluation-d'
            };
            return colorMap[note] || ''; // Return class or empty string if no match
        },

        async fetchPersonnels() {
            try {
                const response = await UAP_Service.GetAllPersonsUAP(this.id); // Appel API

                // Récupérer toutes les tâches disponibles dans l'UAP
                this.taskNames = response.data.taches;
                
                // Ajouter les tâches dans les headers
                for (let i = 0; i < this.taskNames.length; i++) {
                    this.headers.push({ title: this.taskNames[i], key: this.taskNames[i], sortable: false });
                }

                // Ajouter les salariés et structurer leurs notes
                this.personnels = response.data.salaries;
                
                for (let i = 0; i < this.personnels.length; i++) {
                    let personnel = this.personnels[i];
                    
                    // Initialiser les notes par défaut
                    let notes = {};
                    
                    // Mettre à jour avec les notes réelles
                    for (let key in personnel.notes) {
                        if (this.taskNames.includes(key)) {
                            notes[key] = personnel.notes[key];
                        }
                    }
                    
                    personnel.notes = notes;
                }
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
.personnel-evaluation-container {
    background-color: #f4f6f9;
    padding: 20px;
}

.refined-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.compact-title {
    display: flex;
    justify-content: center;
    margin: 0;
}

.title-text {
    font-size: 1.5em;
    font-weight: 600;
    color: white;
    letter-spacing: 0.5px;
}

.advanced-search-container {
    background-color: white;
    margin-bottom: 15px;
    border-radius: 10px;
}

.search-input {
    
    max-width: 400px;
    margin: 0 auto;
}

.professional-table {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.custom-footer {
    background-color: #f5f5f5;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
}

.hover-row:hover {
    background-color: #f0f4f8 !important;
    transition: background-color 0.3s ease;
}

.evaluation-cell {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 40px;
    height: 40px;
    margin: 0 auto;
    border-radius: 8px;
    font-weight: bold;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    transition: transform 0.3s ease;
    position: relative;
    
}

.evaluation-cell:hover {
    transform: scale(1.1);
}

.evaluation-a { background-color: #4CAF50; }
.evaluation-b { 
    background-color: #FFEB3B;
    color: black;
}
.evaluation-c { background-color: #FF5722; }
.evaluation-d { background-color: #9E9E9E; }
</style>