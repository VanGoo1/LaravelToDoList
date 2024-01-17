import Tasks from "@/pages/Tasks.vue";
import Login from "@/pages/Login.vue";
import Register from "@/pages/Register.vue";
import User from "../pages/User.vue";

const routes = [
    {
        path: '/',
        component: Tasks,
        meta: {
            requiresAuth: true,
        },
        name:'Tasks'
    },
    {
        path: '/login',
        component: Login,
        name:'Login'
    },
    {
        path: '/register',
        component: Register,
        name:'Register'
    },
    {
        path: '/user',
        component: User,
        name:'User'
    }
];

export default routes;
