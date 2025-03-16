import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
//imporat primevue
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Swal from 'sweetalert2';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import { ToastService } from 'primevue';
import 'primeicons/primeicons.css';
 
//importanciones de componentes
import { Toast } from 'primevue';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import FileUpload from 'primevue/fileupload';
import Textarea from 'primevue/textarea';
import InputNumber from 'primevue/inputnumber';
import Select from 'primevue/select';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            app.use(PrimeVue, {
                theme: {
                    preset: Aura
                }
            });
            //configuracion global de sweetalert2
            app.config.globalProperties.$swal = Swal;
            app.use(PrimeVue);
            app.use(ToastService);
            app.component('Toast', Toast);
            app.component('Button', Button);
            app.component('Toolbar', Toolbar);
            app.component('DataTable', DataTable);
            app.component('Column', Column);
            app.component('Dialog', Dialog);
            app.component('InputText', InputText);
            app.component('IconField', IconField);
            app.component('InputIcon', InputIcon);
            app.component('FileUpload', FileUpload);
            app.component('Textarea', Textarea);
            app.component('InputNumber', InputNumber);
            app.component('Select', Select);

            app.mount(el);
            return app;
    },
    progress: {
        color: '#4B5563',
    },
});
