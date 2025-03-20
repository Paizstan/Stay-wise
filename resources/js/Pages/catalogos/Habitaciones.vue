<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

import { ref, onMounted, computed } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';
import { FileUpload, InputNumber } from 'primevue';
import { Swiper, SwiperSlide } from "swiper/vue"; //para el slide
import "swiper/css";
import "swiper/css/navigation";
import { Navigation } from "swiper/modules";
/* import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import axios from "axios"; */
//import Dropdown from "primevue/dropdown";


//import { ProductService } from '@/service/ProductService';


onMounted(() => {
    fetchHabitaciones();
});
const imagesPreview = ref([]);//array para hacer un preview
const toast = useToast();
const dt = ref();
const habitaciones = ref([]); //arreglo de habitaciones
const dialog = ref(false); //permite mostrar/ocultar el modal para agregar/editar habitaciones
const deleteHabitacionDialog = ref(false);
const habitacion = ref({});
const showImagesDialog = ref(false);
const selectedHabitaciones = ref([]); // Corrected ref name
const imagenes = ref([]); //arreglo de imagenes para las habitaciones
const fileUploadRef = ref(null);
const filters = ref({
    global: { value: null },
});
const submitted = ref(false);
const url = "api/habitaciones";
//Estos creo que seria para ver ls limpieza o si esta disponible
/* const statuses = ref([
        {label: 'INSTOCK', value: 'instock'},
        {label: 'LOWSTOCK', value: 'lowstock'},
        {label: 'OUTOFSTOCK', value: 'outofstock'}
    ]); */

const formatCurrency = (value) => {
    if (value)
        return value.toLocaleString("en-US", {
            style: "currency",
            currency: "USD",
        });
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
    clearFiles();
};
//FUNCIONES PARA HACER PETICIONES A ALA API
const fetchHabitaciones = async () => {
    try {
        const response = await axios.get(url);
        habitaciones.value = response.data;
    } catch (err) {
        console.error("Error al obtener las habitaciones", err);
    }
};

/* formData.append('nombre', habitacion.value.nombre);
        formData.append('tipo', habitacion.value.tipo);
        formData.append('capacidad', habitacion.value.capacidad);
        formData.append('descripcion', habitacion.value.descripcion);
        formData.append('precio', habitacion.value.precio); */

const saveOrUpdate = async () => {
    submitted.value = true;

    if (habitacion?.value.nombre?.trim()) {
        const formData = new FormData();

        // Agregar los datos de la habitación al FormData
        for (const [key, value] of Object.entries(habitacion.value)) {
            if (value !== null && value !== undefined) {
                formData.append(key, value);
                console.log(key, value);
            }
        }

        // Agregar imágenes al FormData
        imagenes.value.forEach((imagen) => {
            formData.append("imagenes[]", imagen.file);
        });

        // Verificar los datos que se están enviando
        console.log([...formData.entries()]);

        try {
            let response;
            if (habitacion.value.id) {
                // Actualizar la habitación usando POST con _method=PUT
                response = await axios.post(`${url}/${habitacion.value.id}?_method=PUT`, formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                });

                if (response.status === 200) {
                    const { habitacion: updatedHabitacion, message } = response.data;
                    const index = findIndexById(updatedHabitacion.id);
                    if (index !== -1) {
                        // Actualizar de manera reactiva
                        habitaciones.value.splice(index, 1, updatedHabitacion);
                    }
                    toast.add({ severity: 'success', summary: 'Actualizado!', detail: message, life: 3000 });
                }
                dialog.value = false;
            } else {
                // Agregar una nueva Habitación
                response = await axios.post(url, formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                });

                if (response.status === 201) {
                    const { habitacion: nuevaHabitacion, message } = response.data;
                    // Agregar la nueva habitación de manera reactiva
                    habitaciones.value.unshift(nuevaHabitacion);
                    toast.add({ severity: 'success', summary: 'Registrado!', detail: message, life: 3000 });
                }
                dialog.value = false;
                habitacion.value = {};
            }

            imagenes.value = [];
            imagesPreview.value = [];
        } catch (err) {
            if (err.response && err.response.status === 409) {
                toast.add({ severity: 'warn', summary: 'Conflicto!', detail: err.response.data.message, life: 3000 });
            } else if (err.response && err.response.status === 500) {
                const { error } = err.response.data;
                toast.add({ severity: 'error', summary: 'Error!', detail: error, life: 3000 });
            } else {
                console.log('Error inesperado', err);
            }
        }
    }
}; 

const onImageSelect = (event) => {
    const files = event.files;
    for (const file of files) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagenes.value.push({
                file: file,
                url: e.target.result
            });
        };
        reader.readAsDataURL(file);
    }
};

const editHabitacion = (habit) => {
    habitacion.value = { ...habit };
    imagesPreview.value = habit.imagenes.map(img => `images/habitacions/${img.nombre}`);
    dialog.value = true;
};
const confirmDeleteHabitacion = (habit) => {
    habitacion.value = { ...habit };
    deleteHabitacionDialog.value = true;
};
const viewImages = (habit) => {
    habitacion.value = {...habit};        
    showImagesDialog.value = true;
};
const deleteHabitacion = async () => {
    try {
        const response = await axios.delete(`${url}/${habitacion.value.id}`);
        if (response.status === 200 || response.status === 204 || response.status === 205) {
            const { message } = response.data;
            habitaciones.value = habitaciones.value.filter(val => val.id !== habitacion.value.id);
            toast.add({ severity: 'success', summary: 'Habitación eliminada', detail: message || 'La habitación ha sido eliminada con éxito', life: 3000 });
        }
    } catch (err) {
        if (err.response && err.response.status === 409) {
            const { error } = err.response.data;
            toast.add({ severity: 'warn', summary: 'No se puede eliminar', detail: error, life: 3000 });
        } else if (err.response && err.response.status === 500) {
            const { error } = err.response.data;
            toast.add({ severity: 'error', summary: 'Error', detail: error, life: 3000 });
        } else {
            console.error("Error al eliminar una habitacion ", err);
        }
    }
    deleteHabitacionDialog.value = false;
    habitacion.value = {};
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
        case "INSTOCK":
            return "success";

        case "LOWSTOCK":
            return "warn";

        case "OUTOFSTOCK":
            return "danger";

        default:
            return null;
    }
};

/* const onImageSelect = (event) => {
    const files = event.files;
    for (const file of files) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagenes.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    }
}; */
//funcion para eliminar una imagen
const removeImage = (index) => {
    imagenes.value.splice(index, 1);
};
//Funciones para setear las imagenes del arreglo
const onFileClear = () => {
    imagenes.value = [];
};
const clearFiles = () => {
    if(fileUploadRef.value) {
            fileUploadRef.value.clear();
        }
        imagenes.value = [];
};

const dialogTitle = computed(() =>
    habitacion.value.id ? "Edición de Habitacion" : "Registro de Habitaciones"
);
const btnTitle = computed(() =>
    habitacion.value.id ? "Actualizar" : "Guardar"
);
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
                                <Button
                                    label="Nueva Habitacion"
                                    icon="pi pi-plus"
                                    class="mr-2"
                                    @click="openNew"
                                />
                            </template>

                            <template #end>
                                <FileUpload
                                    mode="basic"
                                    accept="image/*"
                                    :maxFileSize="1000000"
                                    label="Import"
                                    customUpload
                                    chooseLabel="Import"
                                    class="mr-2"
                                    auto
                                    :chooseButtonProps="{
                                        severity: 'secondary',
                                    }"
                                />
                                <Button
                                    label="Export"
                                    icon="pi pi-upload"
                                    severity="secondary"
                                    @click="exportCSV($event)"
                                />
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
                                <div
                                    class="flex flex-wrap gap-2 items-center justify-between"
                                >
                                    <h4 class="m-0">Gestion de Habitaciones</h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText
                                            v-model="filters['global'].value"
                                            placeholder="Search..."
                                        />
                                    </IconField>
                                </div>
                            </template>

                            <Column
                                field="nombre"
                                header="Habitacion"
                                sortable
                                style="min-width: 16rem"
                            ></Column>
                            <Column
                                field="tipo"
                                header="Tipo"
                                sortable
                                style="min-width: 16rem"
                            ></Column>
                            <Column
                                field="capacidad"
                                header="Capacidad"
                                sortable
                                style="min-width: 16rem"
                            ></Column>
                            <Column
                                field="descripcion"
                                header="Descripcion"
                                sortable
                                style="min-width: 16rem"
                            ></Column>
                            <Column
                                field="precio"
                                header="Precio"
                                sortable
                                style="min-width: 8rem"
                            >
                                <template #body="slotProps">
                                    ${{ formatCurrency(slotProps.data.precio) }}
                                </template>
                            </Column>
                            <Column
                                :exportable="false"
                                style="min-width: 12rem"
                            >
                                <template #body="slotProps">
                                    <Button icon="pi pi-images" outlined rounded class="mr-2" severity="info" @click="viewImages(slotProps.data)" />
                                    <Button
                                        icon="pi pi-pencil"
                                        outlined
                                        rounded
                                        class="mr-2"
                                        @click="editHabitacion(slotProps.data)"
                                    />
                                    <Button
                                        icon="pi pi-trash"
                                        outlined
                                        rounded
                                        severity="danger"
                                        @click="
                                            confirmDeleteHabitacion(
                                                slotProps.data
                                            )
                                        "
                                    />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                    <!--Modal para guadar o actualizar una habitacion-->
                    <Dialog
                        v-model:visible="dialog"
                        :style="{ width: '450px' }"
                        :header="dialogTitle"
                        :modal="true"
                    >
                        <div class="flex flex-col gap-6">
                            <div>
                                <label for="nombre" class="block font-bold mb-3"
                                    >Nombre Habitacion</label
                                >
                                <InputText
                                    id="nombre"
                                    v-model.trim="habitacion.nombre"
                                    required="true"
                                    autofocus
                                    :invalid="submitted && !habitacion.nombre"
                                    fluid
                                />
                                <small
                                    v-if="submitted && !habitacion.nombre"
                                    class="text-red-500"
                                    >Nombre es requerido.</small
                                >
                            </div>
                            <div>
                                <label for="tipo" class="block font-bold mb-2">Tipo</label>
                                <InputText id="tipo" v-model="habitacion.tipo" required="true" class="w-full" />
                                <small class="p-error text-red-500" v-if="submitted && !habitacion.tipo">Tipo es requerido.</small>
                            </div>
                            <div>
                                    <label for="capacidad" class="block font-bold mb-2">Capacidad</label>
                                    <InputText id="capacidad" v-model="habitacion.capacidad" required="true" class="w-full" />
                                    <small class="p-error text-red-500" v-if="submitted && !habitacion.capacidad">Capacidad es requerida.</small>
                            </div>
                            <div>
                                <label
                                    for="descripcion"
                                    class="block font-bold mb-3"
                                    >Descripcion de la Habitacion</label
                                >
                                <Textarea
                                    id="descripcion"
                                    v-model="habitacion.descripcion"
                                    required="true"
                                    rows="2"
                                    cols="20"
                                    fluid
                                />
                                <small
                                    class="p-error text-red-500"
                                    v-if="submitted && !habitacion.descripcion"
                                    >Descripcion es requerida.</small
                                >
                            </div>
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-6">
                                    <label for="precio">Precio</label>
                                    <InputNumber
                                        id="precio"
                                        v-model="habitacion.precio"
                                        mode="currency"
                                        currency="USD"
                                        locale="en-US"
                                        fluid
                                        :class="{
                                            'p-invalid':
                                                submitted && !habitacion.precio,
                                        }"
                                    />
                                    <small
                                        class="p-error text-red-500"
                                        v-if="submitted && !habitacion.precio"
                                        >Precio es requerido.</small
                                    >
                                </div>
                            </div>
                            <!--Carga de imagenes-->
                            <div>
                                <label class="block font-bold mb-3"
                                    >Imágenes</label
                                >
                                <FileUpload
                                    mode="basic"
                                    accept="image/*"
                                    customUpload
                                    multiple
                                    @select="onImageSelect"
                                    choose-label="Seleccionar Imágenes"
                                />
                            </div>
                            <!--Vista previa de las imagenes-->
                            <div
                                v-if="imagenes.length"
                                class="grid grid-cols-3 gap-3 mt-3"
                            >
                                <div
                                    v-for="(img, index) in imagenes"
                                    :key="index"
                                    class="relative group"
                                >
                                    <img
                                        :src="img.url"
                                        class="w-full h-24 object-cover rounded-md shadow"
                                    />
                                    <Button
                                        icon="pi pi-times"
                                        text
                                        class="absolute top-0 right-0 p-1 text-white rounded-full bg-red-500 opacity-0 group-hover:opacity-100"
                                        @click="removeImage(index)"
                                    />
                                </div>
                            </div>
                        </div>
                        <template #footer>
                            <Button
                                label="Cancelar"
                                icon="pi pi-times"
                                text
                                @click="hideDialog"
                            />
                            <Button
                                :label="btnTitle"
                                icon="pi pi-check"
                                @click="saveOrUpdate"
                            />
                        </template>
                    </Dialog>
                    <Dialog v-model:visible="showImagesDialog" header="Imagenes de la Habitación" :style="{ width: '550px' }" class="p-fluid">
                        <Swiper :modules="[Navigation]" navigation class="h-40">
                            <SwiperSlide v-for="img in habitacion.imagenes" :key="img">
                                <img :src="`images/habitacions/${img.nombre}`" class="w-full h-40 object-contain" />
                            </SwiperSlide>
                        </Swiper>
                    </Dialog>
                    <Dialog v-model:visible="deleteHabitacionDialog" :style="{ width: '450px' }" header="Confirmacion" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="habitacion"
                            >Segur@ que quieres eliminar la Habitacion <b>{{ habitacion.nombre }}</b
                            >?</span
                        >
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteHabitacionDialog = false" />
                        <Button label="Si" icon="pi pi-check" @click="deleteHabitacion" />
                    </template>
                    </Dialog>                    
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
