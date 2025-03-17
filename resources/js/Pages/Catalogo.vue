<script setup>
import { computed, onMounted, ref } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import { Autoplay, Navigation } from "swiper/modules";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faBars,
    faBed,
    faSignInAlt,
    faUserPlus,
    faSignOutAlt,
    faShoppingCart,
} from "@fortawesome/free-solid-svg-icons";
import axios from "axios";
import Swal from "sweetalert2";

const page = usePage();
const user = page.props.auth.user;
const isOpen = ref(false);
const carritoVisible = ref(false);
const reserva = ref(null);
const reservas = ref([]); // Array para almacenar múltiples reservas
/* const total = computed(() => {
    return reservas.value.reduce((sum, reserva) => sum + reserva.precio, 0);
}); */

const destacados = ref([
    {
        id: 1,
        nombre: "Peppermint Tea",
        descripcion: "A refreshing blend of peppermint leaves.",
        precio: 10,
        imagen: "https://www.sofitelbarucalablanca.com/wp-content/uploads/sites/19/2023/04/T3P_1211-HDR-1170x780.jpg",
    },
    {
        id: 2,
        nombre: "Rooibos Tea",
        descripcion: "A rich and flavorful tea from South Africa.",
        precio: 15,
        imagen: "https://cf.bstatic.com/xdata/images/hotel/max1024x768/530900719.jpg?k=93715490f016695d578526be6d55ae829e5d7b392290b1b0fd42fcdfa38c223f&o=&hp=1",
    },
    {
        id: 3,
        nombre: "Ginger Tea",
        descripcion: "A spicy and invigorating tea with ginger root.",
        precio: 12,
        imagen: "https://images.trvl-media.com/lodging/66000000/65730000/65722800/65722721/c13b0293.jpg?impolicy=resizecrop&rw=575&rh=575&ra=fill",
    },
]);

const habitaciones = ref([]);

const eventos = ref([
    {
        id: 1,
        titulo: "Bodas de Ensueño",
        descripcion: "El lugar perfecto para tu día especial",
        imagen: "https://image-tc.galaxy.tf/wijpeg-9gpyzt2id13gkm9tdslsajdiv/boda-kioro-3205x1746.jpg?width=1920",
    },
    {
        id: 2,
        titulo: "Eventos Corporativos",
        descripcion: "Espacios equipados para reuniones exitosas",
        imagen: "https://cache.marriott.com/content/dam/marriott-renditions/SCLWH/sclwh-wedding-5860-hor-feat.jpg?output-quality=70&interpolation=progressive-bilinear&downsize=1920px:*",
    },
]);

const opiniones = ref([
    {
        id: 1,
        nombre: "Carlos Méndez",
        pais: "México",
        comentario: "Excelente servicio y atención. ¡Repetiré sin duda!",
    },
    {
        id: 2,
        nombre: "María García",
        pais: "España",
        comentario:
            "Una experiencia inolvidable. Las instalaciones son de primera.",
    },
    {
        id: 3,
        nombre: "Javier López",
        pais: "Argentina",
        comentario:
            "El mejor lugar para relajarse y disfrutar de unas vacaciones.",
    },
]);

const handleLogout = () => {
    router.post(route("logout"));
};

const reservarHabitacion = (habitacion) => {
    if (!user) {
        Swal.fire({
            title: "Debes iniciar sesión",
            text: "Para realizar reservas necesitas estar autenticado",
            icon: "warning",
            confirmButtonColor: "#7D5A50"
        });
        return;
    }
    // Añadir la habitación al array de reservas
    reservas.value.push({
        ...habitacion,
        fechaReserva: new Date().toLocaleDateString(),
        noches: 1,
        precio: Number(habitacion.precio) // Asegurarse de que el precio sea un número
    });
    carritoVisible.value = true;
};

const confirmarReserva = () => {
    Swal.fire({
        title: "Reserva Confirmada",
        text: `Has reservado la ${reserva.value.nombre}.`,
        icon: "success",
    });
    reserva.value = null;
    carritoVisible.value = false;
};

const urlBase = "http://localhost:8000/api/";

const fetchHabitaciones = async () => {
    try {
        const response = await axios.get(`${urlBase}habitaciones`);
        console.log("Datos de habitaciones:", response.data); // Para verificar la estructura
        habitaciones.value = response.data;
    } catch (err) {
        console.error("Error al obtener las habitaciones:", err);
    }
};

const scrollToHabitaciones = () => {
    const habitacionesSection = document.querySelector('#habitacionesSection');
    if (habitacionesSection) {
        habitacionesSection.scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
        isOpen.value = false;
    }
};

const eliminarReserva = (index) => {
    reservas.value.splice(index, 1);
    if (reservas.value.length === 0) {
        carritoVisible.value = false;
    }
};

const limpiarCarrito = () => {
    reservas.value = [];
    carritoVisible.value = false;
};

const confirmarReservas = () => {
    Swal.fire({
        title: "Reservas Confirmadas",
        text: `Has confirmado ${reservas.value.length} reservas por un total de $${total.value}`,
        icon: "success",
        confirmButtonColor: "#7D5A50"
    }).then(() => {
        limpiarCarrito();
    });
};

// Reemplaza el computed total actual
const total = computed(() => {
    return reservas.value.reduce((sum, reserva) => {
        return sum + (reserva.precio * (reserva.noches || 1));
    }, 0);
});

/* const calcularTotal = () => {
    total.value = reservas.value.reduce((sum, reserva) => {
        return sum + (reserva.precio * reserva.noches);
    }, 0);
}; */

onMounted(() => {
    fetchHabitaciones();
});
</script>

<template>
    <div class="container mx-auto p-6 bg-[#F5E3C3]">
        <nav
            class="fixed top-0 left-0 w-full bg-[#7D5A50] text-white shadow-md z-50 p-4"
        >
            <div class="container mx-auto flex justify-between items-center">
                <!-- Logo a la izquierda -->
                <h1 class="text-xl font-bold">Staywise</h1>

                <!-- Botones a la derecha -->
                <div class="hidden lg:flex items-center space-x-4">
                    <button
                        @click="scrollToHabitaciones"
                        class="text-white hover:text-[#E1C699] flex items-center space-x-2">
                        <FontAwesomeIcon :icon="faBed" class="w-5 h-5" />
                        <span>Habitaciones</span>
                    </button>

                    <!-- Botones para usuario no autenticado -->
                    <template v-if="!user">
                        <button
                            @click="router.visit(route('login'))"
                            class="text-white hover:text-[#E1C699] flex items-center space-x-2"
                        >
                            <FontAwesomeIcon
                                :icon="faSignInAlt"
                                class="w-5 h-5"
                            />
                            <span>Iniciar Sesión</span>
                        </button>
                        <button
                            @click="router.visit(route('register'))"
                            class="bg-[#5E3023] text-white px-4 py-2 rounded hover:bg-[#4A261C] flex items-center space-x-2"
                        >
                            <FontAwesomeIcon
                                :icon="faUserPlus"
                                class="w-5 h-5"
                            />
                            <span>Registrarse</span>
                        </button>
                    </template>

                    <!-- Botones para usuario autenticado -->
                    <template v-else>
                        <span class="text-white">{{ user.name }}</span>
                        <button
                            @click="handleLogout"
                            class="bg-[#5E3023] text-white px-4 py-2 rounded hover:bg-[#4A261C] flex items-center space-x-2"
                        >
                            <FontAwesomeIcon
                                :icon="faSignOutAlt"
                                class="w-5 h-5"
                            />
                            <span>Cerrar Sesión</span>
                        </button>
                        <button
                            @click="carritoVisible = !carritoVisible"
                            class="text-white hover:text-[#E1C699] flex items-center space-x-2"
                        >
                            <FontAwesomeIcon
                                :icon="faShoppingCart"
                                class="w-5 h-5"
                            />
                            <span>Carrito</span>
                        </button>
                    </template>
                </div>

            

                <!-- Botón menú móvil -->
                <button @click="isOpen = !isOpen" class="lg:hidden">
                    <FontAwesomeIcon :icon="faBars" class="w-6 h-6" />
                </button>
            </div>

            <!-- Menú móvil -->
            <div v-if="isOpen" class="lg:hidden mt-4">
                <div class="flex flex-col space-y-2">
                    <button
                        @click="scrollToHabitaciones"
                        class="text-white hover:text-[#E1C699] flex items-center space-x-2"
                    >
                        <FontAwesomeIcon :icon="faBed" class="w-5 h-5" />
                        <span>Habitaciones</span>
                    </button>

                    <!-- Botones móviles para usuario no autenticado -->
                    <template v-if="!user">
                        <button
                            @click="router.visit(route('login'))"
                            class="text-white hover:text-[#E1C699] flex items-center space-x-2"
                        >
                            <FontAwesomeIcon
                                :icon="faSignInAlt"
                                class="w-5 h-5"
                            />
                            <span>Iniciar Sesión</span>
                        </button>
                        <button
                            @click="router.visit(route('register'))"
                            class="text-white hover:text-[#E1C699] flex items-center space-x-2"
                        >
                            <FontAwesomeIcon
                                :icon="faUserPlus"
                                class="w-5 h-5"
                            />
                            <span>Registrarse</span>
                        </button>
                    </template>

                    <!-- Botones móviles para usuario autenticado -->
                    <template v-else>
                        <span class="text-white">{{ user.name }}</span>
                        <button
                            @click="handleLogout"
                            class="text-white hover:text-[#E1C699] flex items-center space-x-2"
                        >
                            <FontAwesomeIcon
                                :icon="faSignOutAlt"
                                class="w-5 h-5"
                            />
                            <span>Cerrar Sesión</span>
                        </button>
                        <button
                            @click="carritoVisible = !carritoVisible"
                            class="text-white hover:text-[#E1C699] flex items-center space-x-2"
                        >
                            <FontAwesomeIcon
                                :icon="faShoppingCart"
                                class="w-5 h-5"
                            />
                            <span>Carrito</span>
                        </button>
                    </template>
                </div>
            </div>
        </nav>

        <div class="pt-20">
            <Swiper
                :modules="[Navigation, Autoplay]"
                :navigation="flase"
                :autoplay="{ delay: 3000 }"
                loop
                class="my-6"
            >
                <SwiperSlide
                    v-for="destacado in destacados"
                    :key="destacado.id"
                    class="p-4"
                >
                    <div
                        class="bg-[#5E3023] text-white rounded-lg shadow-lg overflow-hidden"
                    >
                        <img
                            :src="destacado.imagen"
                            class="w-full h-56 object-cover"
                        />
                        <div class="p-4">
                            <h2 class="text-xl font-semibold">
                                {{ destacado.nombre }}
                            </h2>
                            <p class="text-[#E1C699] text-lg font-bold">
                                ${{ destacado.precio }}
                            </p>
                            <p>{{ destacado.descripcion }}</p>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>

            <!-- Ofertas y Paquetes -->
            <div class="my-12">
                <h2 class="text-3xl font-bold text-[#5E3023] text-center mb-8">
                    Ofertas y Paquetes
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="habitacion in habitacionesOfertas"
                        :key="habitacion.id"
                        class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform hover:scale-105"
                    >
                        <img
                            :src="habitacion.imagen"
                            class="w-full h-48 object-cover"
                        />
                        <div class="p-4">
                            <h3 class="text-xl font-semibold text-[#5E3023]">
                                {{ habitacion.nombre }}
                            </h3>
                            <p class="text-[#7D5A50]">
                                {{ habitacion.descripcion }}
                            </p>
                            <p class="text-2xl font-bold text-[#5E3023] mt-2">
                                ${{ habitacion.precio }}/noche
                            </p>
                            <button
                                @click="reservarHabitacion(habitacion)"
                                class="w-full bg-[#7D5A50] text-white py-2 rounded mt-4 hover:bg-[#5E3023]"
                            >
                                Reservar Ahora
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Habitaciones -->
            <div class="my-12" ref="habitacionesSection" id="habitacionesSection">
                <h2 class="text-3xl font-bold text-[#5E3023] text-center mb-8">
                    Habitaciones
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="habitacion in habitaciones"
                        :key="habitacion.id"
                        class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform hover:scale-105"
                    >
                        <Swiper :modules="[Navigation]" navigation class="h-48">
                            <SwiperSlide
                                v-for="imagen in habitacion.imagenes"
                                :key="imagen.id"
                            >
                                <img
                                    :src="`/images/habitacions/${imagen.nombre}`"
                                    :alt="habitacion.nombre"
                                    class="w-full h-48 object-cover"
                                />
                            </SwiperSlide>
                            <!-- Imagen por defecto si no hay imágenes -->
                            <SwiperSlide v-if="!habitacion.imagenes?.length">
                                <img
                                    src="/images/default-room.jpg"
                                    :alt="habitacion.nombre"
                                    class="w-full h-48 object-cover"
                                />
                            </SwiperSlide>
                        </Swiper>
                        <div class="p-4">
                            <h3 class="text-xl font-semibold text-[#5E3023]">
                                {{ habitacion.nombre }}
                            </h3>
                            <p class="text-[#7D5A50]">
                                {{ habitacion.descripcion }}
                            </p>
                            <p class="text-2xl font-bold text-[#5E3023] mt-2">
                                ${{ habitacion.precio }}/noche
                            </p>
                            <button
                                @click="reservarHabitacion(habitacion)"
                                class="w-full bg-[#7D5A50] text-white py-2 rounded mt-4 hover:bg-[#5E3023]"
                            >
                                Reservar Ahora
                            </button>
                        </div>
                    </div>
                </div>
            </div>

           <!-- Carrito de Reservas -->
<div v-if="carritoVisible" 
     class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl mx-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-[#5E3023]">Carrito de Reservas</h2>
            <button @click="carritoVisible = false" 
                    class="text-gray-500 hover:text-gray-700">
                <span class="text-2xl">&times;</span>
            </button>
        </div>

        <!-- Lista de reservas -->
        <div v-if="reservas.length > 0" class="space-y-4">
            <div v-for="(reserva, index) in reservas" 
                 :key="index"
                 class="flex items-center justify-between border-b pb-4">
                <div class="flex items-center space-x-4">
                    <img :src="reserva.imagen" 
                         class="w-20 h-20 object-cover rounded"
                         alt="Imagen habitación">
                    <div>
                        <h3 class="font-semibold text-[#5E3023]">{{ reserva.nombre }}</h3>
                        <p class="text-sm text-gray-600">{{ reserva.descripcion }}</p>
                        <div class="flex items-center space-x-2 mt-2">
                            <label class="text-sm">Noches:</label>
                            <input type="number" 
                                   v-model="reserva.noches" 
                                   min="1" 
                                   class="w-16 px-2 py-1 border rounded"
                                   @change="calcularTotal">
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-[#5E3023]">${{ reserva.precio * reserva.noches }}</p>
                    <button @click="eliminarReserva(index)"
                            class="text-red-500 hover:text-red-700 text-sm">
                        Eliminar
                    </button>
                </div>
            </div>

            <!-- Total y botones -->
            <div class="border-t pt-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-bold text-lg">Total:</span>
                    <span class="font-bold text-lg text-[#5E3023]">
                        ${{ total.toFixed(2) }}
                    </span>
                </div>
                <div class="flex space-x-4">
                    <button @click="confirmarReservas"
                            class="flex-1 bg-[#7D5A50] text-white py-2 rounded hover:bg-[#5E3023]">
                        Confirmar Reservas
                    </button>
                    <button @click="limpiarCarrito"
                            class="flex-1 bg-red-500 text-white py-2 rounded hover:bg-red-700">
                        Vaciar Carrito
                    </button>
                </div>
            </div>
        </div>

        <!-- Carrito vacío -->
        <div v-else class="text-center py-8">
            <p class="text-gray-500">No hay reservas en el carrito</p>
        </div>
    </div>
</div>
            <!-- Eventos y Celebraciones -->
            <div class="my-12">
                <h2 class="text-3xl font-bold text-[#5E3023] text-center mb-8">
                    Eventos & Celebraciones
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div
                        v-for="evento in eventos"
                        :key="evento.id"
                        class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform hover:scale-105"
                    >
                        <img
                            :src="evento.imagen"
                            class="w-full h-64 object-cover"
                        />
                        <div class="p-4">
                            <h3 class="text-xl font-semibold text-[#5E3023]">
                                {{ evento.titulo }}
                            </h3>
                            <p class="text-[#7D5A50]">
                                {{ evento.descripcion }}
                            </p>
                            <button
                                class="bg-[#7D5A50] text-white px-6 py-2 rounded mt-4 hover:bg-[#5E3023]"
                            >
                                Más Información
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opiniones -->
            <div class="my-12">
                <h2 class="text-3xl font-bold text-[#5E3023] text-center mb-8">
                    Opiniones de Nuestros Huéspedes
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        v-for="opinion in opiniones"
                        :key="opinion.id"
                        class="bg-white p-6 rounded-lg shadow-lg"
                    >
                        <p class="text-[#7D5A50] italic mb-4">
                            "{{ opinion.comentario }}"
                        </p>
                        <div class="flex items-center">
                            <div>
                                <p class="font-semibold text-[#5E3023]">
                                    {{ opinion.nombre }}
                                </p>
                                <p class="text-sm text-[#7D5A50]">
                                    {{ opinion.pais }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="bg-[#7D5A50] text-white p-6 mt-6 text-center">
            <p>© 2023 Staywise. Todos los derechos reservados.</p>
        </footer>
    </div>
</template>
