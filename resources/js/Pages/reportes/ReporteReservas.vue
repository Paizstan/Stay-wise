<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

const fechaInicio = ref("");
const fechaFinal = ref("");
const estado = ref("Confirmado");
const cargando = ref(false); // Mensaje de generndo reporte
const mensajeError = ref(""); // Estado para mostrar mensajes de error

const generarReporte = () => {
    if (!fechaInicio.value || !fechaFinal.value) {
        mensajeError.value = "Por favor, ingrese ambas fechas para generar el reporte.";
        return;
    }

    mensajeError.value = "";
    cargando.value = true;
    const params = new URLSearchParams({
        fechainicio: fechaInicio.value,
        fechaFinal: fechaFinal.value,
        estado: estado.value,
    });

    setTimeout(() => {
        window.open(`/reportes/reservas?${params.toString()}`, "_blank");
        cargando.value = false;
        fechaInicio.value = "";
        fechaFinal.value = "";
    }, 1000); // simulacion de tiempo
};
</script>

<template>
    <Head title="Reporte de Reservas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-[#5E3023]">
                Reporte de Reservas
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold mb-6 text-[#5E3023]">
                        Generar Reporte de Reservas
                    </h2>

                    <div v-if="mensajeError" class="text-red-600 mb-4">{{ mensajeError }}</div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block font-medium text-gray-700 mb-2"
                                >Fecha Inicio</label
                            >
                            <input
                                type="date"
                                v-model="fechaInicio"
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-[#7D5A50] focus:border-[#7D5A50]"
                            />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-2"
                                >Fecha Final</label
                            >
                            <input
                                type="date"
                                v-model="fechaFinal"
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-[#7D5A50] focus:border-[#7D5A50]"
                            />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-2"
                                >Estado de Reserva</label
                            >
                            <select
                                v-model="estado"
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-[#7D5A50] focus:border-[#7D5A50]"
                            >
                                <option value="Confirmado">Confirmadas</option>
                                <option value="Pendiente">Pendientes</option>
                                <option value="Cancelada">Canceladas</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button
                            @click="generarReporte"
                            class="px-6 py-2 bg-[#7D5A50] text-white rounded-md hover:bg-[#5E3023] transition-colors duration-200 flex items-center space-x-2"
                            :disabled="cargando"
                        >
                            <svg
                                v-if="!cargando"
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                />
                            </svg>
                            <span v-if="cargando">Generando reporte...</span>
                            <span v-else>Generar PDF</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
