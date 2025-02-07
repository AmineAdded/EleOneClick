<template>
    <Bar :data="data" :options="options" />
</template>

<script>
import Admin_Service from "@/Services/Admin/Admin";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js';
import { Bar } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

export default {
    name: 'ChartJsUAP',
    components: {
        Bar
    },
    data() {
        return {
            data: {
                labels: [],
                datasets: [
                    {
                        data: [],
                        label: "Nombre de Salariés",
                        borderColor: "rgba(75, 192, 192, 1)",
                        backgroundColor: [],
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
            }
        }
    },
    methods: {
        async fetchUAPData() {
            try {
                const colors = [
                    "rgba(255, 99, 132, 0.7)",
                    "rgba(54, 162, 235, 0.7)",
                    "rgba(255, 206, 86, 0.7)",
                    "rgba(75, 192, 192, 0.7)",
                    "rgba(153, 102, 255, 0.7)",
                    "rgba(255, 159, 64, 0.7)",
                    "rgba(231, 233, 237, 0.7)"
                ];

                const response = await Admin_Service.getUAPData();
                const data = response.data;

                let labels = [];
                let datasetData = [];
                let backgroundColors = [];

                for (let i = 0; i < data.length; i++) {
                    labels.push(data[i].uap);
                    datasetData.push(data[i].count);
                    backgroundColors.push(colors[i % colors.length]);
                }

                this.data = {
                    labels: labels,
                    datasets: [
                        {
                            data: datasetData,
                            label: "Nombre de Salariés",
                            borderColor: "rgba(75, 192, 192, 1)",
                            backgroundColor: backgroundColors,
                            fill: true,
                        }
                    ]
                };

            } catch (error) {
                console.error("Erreur lors de la récupération des données UAP:", error);
            }
        }

    },

    async mounted() {
        await this.fetchUAPData();
    }
}
</script>