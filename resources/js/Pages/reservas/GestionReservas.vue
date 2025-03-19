<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';

    import 'primeicons/primeicons.css';
    import { ref, onMounted, computed } from 'vue';
    import { FilterMatchMode } from '@primevue/core/api';
    import { useToast } from 'primevue/usetoast';
    import 'primeicons/primeicons.css';
    import axios from 'axios';
    import Swal from 'sweetalert2';

    const toast = useToast();

    const reservas = ref([]);
    const reserva = ref(null);
    const selectedReserva = ref(null);
    const estado = ref('P');
    const showDetailsDialog = ref(false);

    const estados = {
        'P' :  'Pendientes',
        'A' :  'Anuladas',
        'C' :  'Confirmadas'
    };

    const filters = ref({
        'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
    });

    const filteredReservas = computed(() => {
    return reservas.value?.filter(reserva => {
        if (estado.value === 'P') return reserva.estado === 'Pendiente';
        if (estado.value === 'A') return reserva.estado === 'Anulada';
        if (estado.value === 'C') return reserva.estado === 'Confirmado';
        return true;
    });
});
const fetchReservas = async () => {
    try {
        const response = await axios.get('/api/reservas');
        reservas.value = Array.isArray(response.data) ? response.data : [];
        console.log('Reservas:', response.data); // Log to verify data
    } catch (error) {
        console.error("Error al obtener reservas:", error);
        reservas.value = []; // Asegúrate de que sea un array incluso si hay un error
    }
};

    const hideDialog = () => {
        showDetailsDialog.value = false;
    };

    const viewDetails = (data) => {
        reserva.value = { ...data };
        showDetailsDialog.value = true;
    };

    const changeReserva = async (reserva, nuevoEstado) => {
        const estadoTexto = nuevoEstado === 'C' ? 'Confirmar' : 'Anular';

        const result = await Swal.fire({
            title: `¿Seguro(a) que desea ${estadoTexto} la Reserva No: ${reserva.id}?`,
            text: "Esta acción no se puede revertir",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
        });

        if (result.isConfirmed) {
            try {
                const response = await axios.put(`/api/reservas/${reserva.id}`, { estado: nuevoEstado });

                if (response.status === 202) {
                    const index = reservas.value.findIndex(h => h.id === reserva.id);
                    if (index !== -1) {
                        reservas.value[index].estado = nuevoEstado;

                        // Si se despachó, actualiza la fecha de despacho
                        if (nuevoEstado === 'C') {
                            reservas.value[index].fecha_confirmacion = new Date().toISOString().split('T')[0];
                        }
                    }

                    toast.add({
                        severity: 'success',
                        summary: 'Éxito',
                        detail: `Reserva ${estadoTexto} correctamente`,
                        life: 3000
                    });
                }
            } catch (error) {
                console.error("Error al cambiar estado de la reserva:", error);
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: error.response?.data?.message || "No se pudo cambiar el estado de la reserva",
                    life: 3000
                });
            }
        }
    };


    const formatCurrency = (value) => {
        return value ? value.toLocaleString('en-US', { style: 'currency', currency: 'USD' }) : '';
    };

    onMounted(fetchReservas);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div>
                    <div class="card">
                        <Toolbar class="mb-4">
                            <template #start>
                                <div class="form-check form-check-inline" v-for="(label, key) in estados" :key="key">
                                    <input class="form-check-input mr-2" v-model="estado" type="radio" name="reserva.estado" :id="key" :value="key">
                                    <label class="form-check-label mr-4" :for="key">{{ label }}</label>
                                </div>
                            </template>
                            <template #end>
                                <IconField iconPosition="left">
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                                </IconField>
                            </template>
                        </Toolbar>

                        <DataTable ref="dt" :value="filteredReservas" v-model:selection="selectedReserva" dataKey="id"
                            :paginator="true" :rows="10" :filters="filters"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} reservas"
                            size="small"
                            tableStyle="min-width: 50rem">
                            
                            <Column field="id" header="Reserva No" sortable></Column>
                            <Column field="fecha_creacion" header="Fecha Reserva"></Column>
                            <Column field="estado" header="Estado"></Column>
                            <Column field="user.name" header="Cliente"></Column>
                            <Column :exportable="false">
                                <template #body="{ data }">
                                    <Button icon="pi pi-list" outlined rounded class="mr-2" severity="info"
                                        @click="viewDetails(data)"
                                        v-tooltip="{ value: 'Ver Detalle', showDelay: 1000, hideDelay: 300 }" />
                                    <Button icon="pi pi-check" outlined rounded class="mr-2"
                                        v-if="data.estado === 'Pendiente'" @click="changeReserva(data, 'C')"
                                        v-tooltip="{ value: 'Confirmar Orden', showDelay: 1000, hideDelay: 300 }" />
                                    <Button icon="pi pi-trash" outlined rounded severity="danger"
                                        v-if="data.estado === 'Pendiente'" @click="changeReserva(data, 'A')"
                                        v-tooltip="{ value: 'Anular Reserva', showDelay: 1000, hideDelay: 300 }" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>

                    <!-- Modal para mostrar detalles de la orden -->
                    <Dialog v-model:visible="showDetailsDialog" class="p-fluid" :style="{ width: '650px' }" header="Detalle de Reserva" :modal="true">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4 text-sm font-medium text-gray-700">
                                <div class="inline-flex items-center">
                                    <span class="block text-gray-500">Reserva: </span>
                                    <span class="font-semibold">{{ reserva?.id }}</span>
                                </div>
                                <div class="inline-flex items-center">
                                    <span class="block text-gray-500">Fecha Reserva: </span>
                                    <span class="font-semibold">{{ reserva?.fecha_creacion }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm font-medium text-gray-700">
                                <div class="inline-flex items-center">
                                    <span class="block text-gray-500">Estado: </span>
                                    <span class="font-semibold">{{ reserva?.estado }}</span>
                                </div>
                                  <div class="inline-flex items-center">
                                    <span class="block text-gray-500">Cliente: </span>
                                    <span class="font-semibold">{{ reserva?.user?.name }}</span>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse rounded-lg shadow-md overflow-hidden">
                                    <thead>
                                        <tr class="bg-gray-800 text-white text-left">
                                            <th class="px-4 py-2">Fecha Entrada</th>
                                            <th class="px-4 py-2">Fecha Salida</th>
                                            <th class="px-4 py-2">Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 text-black">
                                        <tr v-for="item in reserva?.detalle_reservas" :key="item.id" class="hover:bg-gray-100">
                                            <td class="px-4 py-2">{{ item.fecha_entrada }}</td>
                                            <td class="px-4 py-2">{{ item.fecha_salida }}</td>
                                            <td class="px-4 py-2">${{ item.precio }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <template #footer>
                            <Button label="Cerrar" icon="pi pi-times" text @click="hideDialog" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg" />
                        </template>
                    </Dialog>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
