<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { usePage, Link } from "@inertiajs/vue3";
import Toast from "primevue/toast";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faBars,
    faHome,
    faBox,
    faClipboardList,
    faFileAlt,
    faTimes,
    faUserCircle,
    faSignOutAlt,
    faBed,
    faBook,
} from "@fortawesome/free-solid-svg-icons";
import axios from "axios";

const page = usePage();
const user = page.props.auth.user;
const isSidebarOpen = ref(false);
const anioActual = ref(new Date().getFullYear());

//funciones para la logica de el componente
const logout = async () => {
    try {
        await axios.post("/logout");
        window.location.href = "/login";
    } catch (err) {
        console.error("Error al cerrar la sesion", err);
    }
};
</script>

<template>
    <Toast />
    <div class="h-screen flex flex-col">
        <!--navbar-->
        <header
            class="bg-[#8B5D33] text-white shadow-md fixed top-0 w-full z-50"
        >
            <div class="px-6 py-4 flex justify-between items-center">
                <!--Boton para ocultar/mostrar el sidebar-->
                <button
                    @click="isSidebarOpen = !isSidebarOpen"
                    class="text-white md:hidden"
                >
                    <FontAwesomeIcon :icon="faBars" />
                </button>
                <div class="text-lg font-semibold">Dashboard</div>
                <!--Datos de la sesión-->
                <div class="flex items-center space-x-4">
                    <FontAwesomeIcon :icon="faUserCircle" class="text-2xl" />
                    <div>
                        <p class="text-sm font-semibold">{{ user.name }}</p>
                    </div>
                    <button @click="logout" class="text-white hover:gray-200">
                        <FontAwesomeIcon :icon="faSignOutAlt" />
                    </button>
                </div>
            </div>
        </header>
        <!--inicio div para sidebar y contenido dinámico-->
        <div class="flex flex-1 pt-1">
            <!--sidebar-->
            <aside
                :class="{
                    '-translate-x-full md:translate-x-0': !isSidebarOpen,
                    'translate-x-0': isSidebarOpen,
                }"
                class="fixed md:relative top-8 left-0 h-[calc(100vh-64px)] w-64 bg-[#F1E0C5] text-black transform transition-transform duration-300 ease-in-out shadow-lg z-40"
            >
                <div
                    class="px-4 text-xl font-semibold flex justify-between items-center"
                >
                    <span>MENU</span>
                    <button
                        @click="isSidebarOpen = false"
                        class="md:hidden text-gray-300 hover:text-black"
                    >
                        <FontAwesomeIcon :icon="faTimes" />
                    </button>
                </div>
                <nav class="mt-4">
                    <ul>
                        <li
                            class="px-6 py-3 hover:bg-[#D3BBA6] flex items-center"
                        >
                            <Link
                                :href="route('dashboard')"
                                class="flex items-center w-full"
                            >
                                <FontAwesomeIcon :icon="faHome" class="mr-3" />
                                Inicio
                            </Link>
                        </li>
                        <li
                            class="px-6 py-3 hover:bg-[#D3BBA6] flex items-center"
                        >
                            <Link
                                :href="route('habitaciones')"
                                class="flex items-center w-full"
                            >
                                <FontAwesomeIcon :icon="faBed" class="mr-3" />
                                Habitaciones
                            </Link>
                        </li>
                        <li
                            class="px-6 py-3 hover:bg-[#D3BBA6] flex items-center"
                        >
                            <Link 
                                :href="route('reservas')"
                                class="flex items-center w-full">
                                <FontAwesomeIcon :icon="faClipboardList" class="mr-3" />
                                Reservas
                            </Link>
                        </li>
                        <!-- <li
                            class="px-6 py-3 hover:bg-[#D3BBA6] flex items-center"
                        >
                            <Link
                                :href="route('Habitaciones')"
                                class="flex items-center w-full"
                            >
                                <FontAwesomeIcon :icon="faBed" class="mr-3" />
                                Habitaciones
                            </Link>
                        </li>  -->
                        <li
                            class="px-6 py-3 hover:bg-[#D3BBA6] flex items-center"
                        >
                            <Link :href="route('gestion')" class="flex items-center w-full">
                                <FontAwesomeIcon
                                    :icon="faClipboardList"
                                    class="mr-3"
                                />
                                Sistema de Gestión
                            </Link>
                        </li>
                        <li
                            class="px-6 py-3 hover:bg-[#D3BBA6] flex items-center"
                        >
                            <Link :href="route('reporte.vista')" class="flex items-center w-full">
                                <FontAwesomeIcon
                                    :icon="faFileAlt"
                                    class="mr-3"
                                />
                                Reportes
                            </Link>
                        </li>
                    </ul>
                </nav>
            </aside>
            <!--Div para el contenido dinámico-->
            <main class="flex-1 p-6 overflow-auto bg-white">
                <slot />
            </main>
        </div>
    </div>
    <!--footer de la aplicacion-->
    <footer
        class="bg-[#8B5D33] text-white text-center py-3 shadow-md bottom-0 left-0 w-full"
    >
        &copy;{{ new Date().getFullYear() }} - Todos los derechos reservados
    </footer>
</template>
