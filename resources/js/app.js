import './bootstrap';
import {createApp} from "vue";
import App from './app.vue';
import router from "@/router/router.js";
import "../css/app.css";
import axios from "axios";

if (localStorage.getItem('token')) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
}

async function isValidToken() {
    let response = await axios.get('api/validate-token');
    return response.data.isValid;
}

router.beforeEach(async (to, from, next) => {
        let isValid = await isValidToken();
        if (to.meta.requiresAuth && !isValid)
            next({name: 'Login'});
        else if (isValid && (to.name === 'Login' || to.name === "Register"))
            next({name: 'User'});
        else
            next();
    }
);

createApp(App)
    .use(router)
    .mount('#app');
