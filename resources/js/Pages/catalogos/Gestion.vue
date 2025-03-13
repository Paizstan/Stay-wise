<template>
    <div class="container mx-auto p-4">
      <h2 class="text-2xl font-bold text-center mb-4">Porcentaje de Reservas por Mes</h2>
      <div class="flex justify-center">
        <canvas id="reservasChart" class="w-full max-w-4xl"></canvas>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, onMounted } from 'vue';
  import Chart from 'chart.js/auto';
  // import 'primeicons/primeicons.css';
  // import 'primeicons/resource.css';
  import 'tailwindcss/tailwind.css';
  
  export default {
    name: 'ReservasPorMes',
    setup() {
      const reservasChart = ref(null);
  
      onMounted(() => {
        const ctx = document.getElementById('reservasChart').getContext('2d');
        reservasChart.value = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: [{
              label: 'Porcentaje de Reservas',
              data: [12, 19, 3, 5, 2, 3, 10, 15, 8, 6, 7, 9], // Datos simulados
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              borderColor: 'rgba(75, 192, 192, 1)',
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      });
  
      return {
        reservasChart
      };
    }
  };
  </script>
  
  <style scoped>
  h2 {
    text-align: center;
    margin-bottom: 20px;
  }
  
  canvas {
    display: block;
    margin: 0 auto;
  }
  </style>