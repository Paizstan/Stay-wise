import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";

// Import PrimeVue components
import Toast from "primevue/toast";
import Button from "primevue/button";
import Toolbar from "primevue/toolbar";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import FileUpload from "primevue/fileupload";
import Select from 'primevue/select';
import Textarea from "primevue/textarea";
import InputNumber from "primevue/inputnumber";

//Import PrimeVue CSS
/* import 'primevue/resources/themes/saga-blue/theme.css'; // or any other theme
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css"; */

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.use(ZiggyVue);
        app.use(PrimeVue);
        app.use(ToastService);

        // Register PrimeVue components
        app.component("Toast", Toast);
        app.component("Button", Button);
        app.component("Toolbar", Toolbar);
        app.component("DataTable", DataTable);
        app.component("Column", Column);
        app.component("Dialog", Dialog);
        app.component("InputText", InputText);
        app.component("IconField", IconField);
        app.component("InputIcon", InputIcon);
        app.component("FileUpload", FileUpload);
        app.component('Select', Select);
        app.component("Textarea", Textarea);
        app.component("InputNumber", InputNumber);

        app.mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
