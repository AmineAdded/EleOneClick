<template>
    <Doughnut :data="data" :options="options" />
</template>

<script>
import Admin_Service from "@/Services/Admin/Admin";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
    LinearScale
} from 'chart.js';
import { Doughnut } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, ArcElement, Title, Tooltip, Legend);

export default {
    name: 'ChartJsTaches',
    components: {
        Doughnut
    },
    data() {
        return {
            data: {
                labels: [],
                datasets: [
                    {
                        data: [],
                        label: "Nombre de taches",
                        borderColor: "#fff",
                        backgroundColor: [],
                        hoverOffset: 8
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'left'
                    }
                }
            }
        }
    },
    methods: {
        async fetchUAPData() {
            try {
                const response = await Admin_Service.getTacheData();
                const data = response.data;

                let labels = [];
                let datasetData = [];
                let backgroundColors = [];

                // Liste de couleurs pré-définies
                const colors = [
                    "rgba(255, 99, 132, 0.7)",
                    "rgba(54, 162, 235, 0.7)",
                    "rgba(255, 206, 86, 0.7)",
                    "rgba(75, 192, 192, 0.7)",
                    "rgba(153, 102, 255, 0.7)",
                    "rgba(255, 159, 64, 0.7)",
                    "rgba(231, 233, 237, 0.7)"
                ];

                for (let i = 0; i < data.length; i++) {
                    labels.push(data[i].uap);
                    datasetData.push(data[i].count);
                    backgroundColors.push(colors[i % colors.length]); // Assigner une couleur en boucle
                }

                this.data = {
                    labels: labels,
                    datasets: [
                        {
                            data: datasetData,
                            label: "Nombre de taches",
                            borderColor: "#fff",
                            backgroundColor: backgroundColors,
                            hoverOffset: 8
                        }
                    ]
                };

            } catch (error) {
                console.error("Erreur lors de la récupération des données Taches:", error);
            }
        }
    },
    async mounted() {
        await this.fetchUAPData();
    }
}
</script>
