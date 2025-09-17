import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import PrimeVue from 'primevue/config';
import Material from '@primeuix/themes/material';
import Tooltip from 'primevue/tooltip';

import ToastService from 'primevue/toastservice';


import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(PrimeVue, {
    theme: {
        preset: Material
    },
    pt:{
        button:{
            root:{
                class:'font-lexend'
            }
        },
        select:{
            overlay:{
                class:'font-poppins'
            }
        },
        datepicker:{
            calendar:{
                class:'font-poppins'
            }
        },
    }
});

app.use(ToastService);

app.directive('tooltip', Tooltip);

app.mount('#app')
