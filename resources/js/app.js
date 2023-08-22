import './bootstrap';

// import 'admin-lte/plugins/jquery/jquery.min.js'; 
//jquery was installed and initialised in the bootstrap.js

import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';
// import { createApp } from 'vue';
import { createApp } from 'vue/dist/vue.esm-bundler';
import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes.js'
import Login from './pages/auth/Login.vue';





const app = createApp({});

const router = createRouter({
    routes: routes,
    history: createWebHistory(),
});


app.use(router);

app.component('Login', Login);


app.mount('#app');