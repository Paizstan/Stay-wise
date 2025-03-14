<script setup>
import { ref, onMounted } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const reservas = ref([]);

onMounted(async () => {
    const response = await fetch("/reporte/datos");
    reservas.value = await response.json();
});

function descargarPDF() {
    window.open("/reporte/pdf", "_blank");
}
</script>

<template>
    <Head title="Reportes del Hotel" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Reportes del Hotel
            </h2>
        </template>

        <div class="p-6 bg-white shadow-md rounded-lg">
            <h2 class="text-2xl font-bold mb-4">Vista Previa del Reporte</h2>

            <div v-if="reservas.length > 0">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-4 py-2">Usuario</th>
                            <th class="border px-4 py-2">Fecha Creación</th>
                            <th class="border px-4 py-2">Estado</th>
                            <th class="border px-4 py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="reserva in reservas" :key="reserva.id">
                            <td class="border px-4 py-2">{{ reserva.user?.nombre }}</td> <!-- Ensure user object is properly accessed -->
                            <td class="border px-4 py-2">{{ reserva.fecha_creacion }}</td>
                            <td class="border px-4 py-2">{{ reserva.estado }}</td>
                            <td class="border px-4 py-2">
                                {{ reserva.detalles.reduce((total, det) => total + det.precio, 0) }} USD
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p v-else class="text-gray-500">No hay datos disponibles.</p>

            <!-- Botón para descargar el PDF -->
            <div class="mt-6">
                <button
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700"
                    @click="descargarPDF"
                >
                    Descargar PDF
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
