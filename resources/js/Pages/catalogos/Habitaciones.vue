<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';

    import { ref, onMounted, computed } from 'vue';
    import { FilterMatchMode } from '@primevue/core/api';
    import { useToast } from 'primevue/usetoast';
    import axios from 'axios';

    //import { ProductService } from '@/service/ProductService';

    onMounted(() => {
        fetchHabitaciones();
    });

    const toast = useToast();
    const dt = ref();
    const habitaciones = ref([]);//arreglo de habitaciones
    const dialog = ref(false);//permite mostrar/ocultar el modal para agregar/editar habitaciones
    const deleteHabitacionDialog = ref(false);
    const habitacion = ref({});

    const selectedHabitaciones = ref([]); // Corrected ref name
    const imagenes = ref([]);//arreglo de imagenes para las habitaciones
    const fileUploadRef = ref(null);
    const filters = ref({
        'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
    });
    const submitted = ref(false);
    const url = 'http://127.0.0.1:8000/api/habitaciones';
//Estos creo que seria para ver ls limpieza o si esta disponible
    /* const statuses = ref([
        {label: 'INSTOCK', value: 'instock'},
        {label: 'LOWSTOCK', value: 'lowstock'},
        {label: 'OUTOFSTOCK', value: 'outofstock'}
    ]); */

    const formatCurrency = (value) => {
        if(value)
            return value.toLocaleString('en-US', {style: 'currency', currency: 'USD'});
        return;
    };
    const openNew = () => {
        habitacion.value = {};
        submitted.value = false;
        dialog.value = true;
    };
    const hideDialog = () => {
        dialog.value = false;
        submitted.value = false;
    };
    //FUNCIONES PARA HACER PETICIONES A ALA API
    const fetchHabitaciones = async()=>{
            try{
                const response =await axios.get(url);
                habitaciones.value = response.data;
            }catch(err){
                console.error("Error al obtener las habitaciones", err);
            }

        };

    const saveOrUpdate = () => {
        submitted.value = true;

        if (habitacion?.value.nombre?.trim()) {
            if (habitacion.value.id) {
                //se va actualizar la habitacion
                toast.add({severity:'success', summary: 'Successful', detail: 'Habitacion Updated', life: 3000});
            }
            else {
                //se va agregar un nuevo producto
            toast.add({severity:'success', summary: 'Successful', detail: 'Habitación Created', life: 3000});
            }

            dialog.value = false;
            habitacion.value = {};
        }
    };
    const editHabitacion = (prod) => {
        habitacion.value = {...prod};
        dialog.value = true;
    };
    const confirmDeleteHabitacion = (prod) => {
        habitacion.value = {...prod};
        deleteHabitacionDialog.value = true;
    };
    const deleteHabitacion = () => {
        habitaciones.value = habitaciones.value.filter(val => val.id !== habitacion.value.id);
        deleteHabitacionDialog.value = false;
        habitacion.value = {};
        toast.add({severity:'success', summary: 'Successful', detail: 'Habitacion Deleted', life: 3000});
    };
    const findIndexById = (id) => {
        let index = -1;
        for (let i = 0; i < habitaciones.value.length; i++) {
            if (habitaciones.value[i].id === id) {
                index = i;
                break;
            }
        }

        return index;
    };

    const exportCSV = () => {
        dt.value.exportCSV();
    };

    const getStatusLabel = (status) => {
        switch (status) {
            case 'INSTOCK':
                return 'success';

            case 'LOWSTOCK':
                return 'warn';

            case 'OUTOFSTOCK':
                return 'danger';

            default:
                return null;
        }
    };

    const onImageSelect =(event)=>{
        const files = event.files;
        for(const file of files){;
            const reader = new FileReader();
            reader.onload = (e) => {
                imagenes.value.push(e.target.result);
            };
            reader.readAsDataURL(file);
        }

    }; 
    //funcion para eliminar una imagen
    const removeImage = (index) => {
        imagenes.value.splice(index, 1);

        
    };
    //Funciones para setear las imagenes del arreglo
    const onFileClear = () => {
        imagenes.value = [];
    };
    const clearFiles = () => {
        fileUploadRef.value.clear();
        imagenes.value = [];
    };

    const dialogTitle = computed(() =>
            habitacion.value.id ? "Edición de Habitacion" : "Registro de Habitaciones"
        );
    const btnTitle = computed(() => (habitacion.value.id ? "Actualizar" : "Guardar"));
</script>

<template>
    <Head title="Habitaciones" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Habitaciones
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!--Inicio template de habitaciones - primevue -->
                <div>
                    <div class="card">
                        <Toolbar class="mb-6">
                            <template #start>
                                <Button label="Nueva Habitacion" icon="pi pi-plus" class="mr-2" @click="openNew" />
                            </template>

                            <template #end>
                                <FileUpload mode="basic" accept="image/*" :maxFileSize="1000000" label="Import" customUpload chooseLabel="Import" class="mr-2" auto :chooseButtonProps="{ severity: 'secondary' }" />
                                <Button label="Export" icon="pi pi-upload" severity="secondary" @click="exportCSV($event)" />
                            </template>
                        </Toolbar>

                        <DataTable
                            ref="dt"
                            v-model:selection="selectedHabitaciones"
                            :value="habitaciones"
                            dataKey="id"
                            :paginator="true"
                            :rows="10"
                            :filters="filters"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                            currentPageReportTemplate="Mostrando del {first} al {last} de {totalRecords} habitaciones"
                        >
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="m-0">Gestion de Habitaciones</h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Search..." />
                                    </IconField>
                                </div>
                            </template>

                            <Column field="nombre" header="Habitacion" sortable style="min-width: 16rem"></Column>
                            <Column field="tipo" header="Tipo" sortable style="min-width: 16rem"></Column>
                            <Column field="capacidad" header="Capacidad" sortable style="min-width: 16rem"></Column>
                            <Column field="descripcion" header="Descripcion" sortable style="min-width: 16rem"></Column>
                            <Column field="precio" header="Precio" sortable style="min-width: 8rem">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.precio) }}
                                </template>
                            </Column>
                            <Column :exportable="false" style="min-width: 12rem">
                                <template #body="slotProps">
                                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editHabitacion(slotProps.data)" />
                                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteHabitacion(slotProps.data)" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                    <!--Modal para guadar o actualizar una habitacion-->
                    <Dialog v-model:visible="dialog" :style="{ width: '450px' }" :header="dialogTitle" :modal="true">
                        <div class="flex flex-col gap-6">
                            <div>
                                <label for="nombre" class="block font-bold mb-3">Nombre Habitacion</label>
                                <InputText id="nombre" v-model.trim="habitacion.nombre" required="true" autofocus :invalid="submitted && !habitacion.nombre" fluid />
                                <small v-if="submitted && !habitacion.nombre" class="text-red-500">Nombre es requerido.</small>
                            </div>
                            <div class="col-span-6">
                                <label for="tipo">Tipo</label>
                                <Select v-model="habitacion.tipo" :options="['Individual', 'Doble', 'Doble estándar', 'Apartamento', 'Suite', 'Suite ejecutiva', 'Suite presidencial']" class="w-full" />
                                <small v-if="submitted && !habitacion.tipo" class="text-red-500">Seleccione un Tipo</small>
                            </div>
                            <div class="col-span-6">
                                <label for="capacidad">Capacidad</label>
                                <Select v-model="habitacion.capacidad" :options="['1', '2', '4', '6']" class="w-full" />
                                <small v-if="submitted && !habitacion.capacidad" class="text-red-500">Seleccione la capacidad de la Habitacion</small>
                            </div>
                            <div>
                                <label for="descripcion" class="block font-bold mb-3">Descripcion</label>
                                <Textarea id="descripcion" v-model="habitacion.descripcion" required="true" rows="2" cols="20" fluid />
                                <small class="p-error text-red-500" v-if="submitted && !habitacion.descripcion">Descripcion es requerida.</small>
                            </div>
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-6">
                                    <label for="precio">Precio</label>
                                    <InputNumber id="precio" v-model="habitacion.precio" mode="currency" currency="USD" locale="en-US" fluid :class="{ 'p-invalid': submitted && !habitacion.precio }" />
                                    <small class="p-error text-red-500" v-if="submitted && !habitacion.precio">Precio es requerido.</small>
                                </div>
                            </div>
                            <!--Carga de imagenes-->
                            <div>
                                <label class="block font-bold mb-3">Imágenes</label>
                                <FileUpload mode="basic" accept="image/*" customUpload multiple @select="onImageSelect" choose-label="Seleccionar Imágenes" />
                            </div>
                            <!--Vista previa de las imagenes-->
                            <div v-if="imagenes.length" class="grid grid-cols-3 gap-3 mt-3">
                                <div v-for="(img, index) in imagenes" :key="index" class="relative group">
                                    <img :src="img" class="w-full h-24 object-cover rounded-md shadow">
                                    <Button icon="pi pi-times" text class="absolute top-0 right-0 p-1 text-white rounded-full bg-red-500 opacity-0 group-hover:opacity-100" @click="removeImage(index)" />
                                </div>
                            </div>
                        </div>
                        <template #footer>
                            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                            <Button :label="btnTitle" icon="pi pi-check" @click="saveOrUpdate" />
                        </template>
                    </Dialog>
                    <!--
                    <Dialog v-model:visible="deleteProductDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                        <div class="flex items-center gap-4">
                            <i class="pi pi-exclamation-triangle !text-3xl" />
                            <span v-if="product">Are you sure you want to delete <b>{{ product.name }}</b>?</span>
                        </div>
                        <template #footer>
                            <Button label="No" icon="pi pi-times" text @click="deleteProductDialog = false" />
                            <Button label="Yes" icon="pi pi-check" @click="deleteProduct" />
                        </template>
                    </Dialog>

                    <Dialog v-model:visible="deleteProductsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                        <div class="flex items-center gap-4">
                            <i class="pi pi-exclamation-triangle !text-3xl" />
                            <span v-if="product">Are you sure you want to delete the selected products?</span>
                        </div>
                        <template #footer>
                            <Button label="No" icon="pi pi-times" text @click="deleteProductsDialog = false" />
                            <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedProducts" />
                        </template>
                    </Dialog>-->
                </div>
                <!--Fin template de productos - primevue -->
            </div>
        </div>
    </AuthenticatedLayout>
</template>