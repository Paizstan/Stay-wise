<script setup>
import { ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faTrash, faEdit, faPlus } from "@fortawesome/free-solid-svg-icons";

const page = usePage();
const habitaciones = ref(page.props.habitaciones);

const form = useForm({
    nombre: "",
    precio: "",
    descripcion: "",
});

const submit = () => {
    form.post(route("habitaciones"), {
        onSuccess: () => {
            form.reset();
        },
    });
};

const eliminar = (id) => {
    if (confirm("¿Estás seguro de eliminar esta habitación?")) {
        form.delete(route("habitaciones", id), {
            onSuccess: () => {
                habitaciones.value = habitaciones.value.filter(h => h.id !== id);
            }
        });
    }
};
</script>

<template>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Habitaciones</h1>

        <!-- Formulario -->
        <form @submit.prevent="submit" class="mb-4">
            <input v-model="form.nombre" placeholder="Nombre" class="border p-2 mr-2" />
            <input v-model="form.precio" placeholder="Precio" type="number" class="border p-2 mr-2" />
            <input v-model="form.descripcion" placeholder="Descripción" class="border p-2" />
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 ml-2">
                <FontAwesomeIcon :icon="faPlus" />
                Agregar
            </button>
        </form>

        <!-- Lista de Habitaciones -->
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Nombre</th>
                    <th class="border p-2">Precio</th>
                    <th class="border p-2">Descripción</th>
                    <th class="border p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="habitacion in habitaciones" :key="habitacion.id" class="text-center">
                    <td class="border p-2">{{ habitacion.nombre }}</td>
                    <td class="border p-2">${{ habitacion.precio }}</td>
                    <td class="border p-2">{{ habitacion.descripcion }}</td>
                    <td class="border p-2">
                        <button class="text-red-500 mr-2" @click="eliminar(habitacion.id)">
                            <FontAwesomeIcon :icon="faTrash" />
                        </button>
                        <button class="text-blue-500">
                            <FontAwesomeIcon :icon="faEdit" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table> 
    </div>
</template>
