<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from 'chart.js/auto';

const reservasChart = ref(null);

onMounted(async () => {
    const response = await fetch('/api/reporte/reservas-mes');
    const data = await response.json();

    const ctx = document.getElementById('reservasChart').getContext('2d');
    reservasChart.value = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: [{
                label: 'Reservas por Mes',
                data: data, // Datos de la API
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<template>
    <Head title="Gestión de Reservas" />

    <AuthenticatedLayout> <!-- Si ya lo usas, no es necesario agregarlo otra vez -->
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestión de Reservas
            </h2>
        </template>

        <div class="container mx-auto p-4">
            <h2 class="text-2xl font-bold text-center mb-6 mt-6">Porcentaje de Reservas por Mes</h2>
            <div class="flex justify-center">
                <canvas id="reservasChart" class="w-full max-w-4xl"></canvas>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
h2 {
    text-align: center;
    margin-bottom: 20px;
}
</style>
