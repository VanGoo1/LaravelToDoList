<template>
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <div
            class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    Sign in to your account
                </h1>
                <div class="space-y-4 md:space-y-6">
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your
                            Login</label>
                        <input v-model="loginData.name" type="text"
                               class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               required>
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your
                            email</label>
                        <input v-model="loginData.email" type="email"
                               class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               required>
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <input v-model="loginData.password" type="password"
                               class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               required>
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Repeat
                            password</label>
                        <input v-model="loginData.repeat" type="password"
                               class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               required>
                    </div>

                    <button @click="submitForm(loginData)"
                            class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        Sign in
                    </button>
                    <div v-for="error_message in loginData.error">
                        <p class="text-red-500 text-base">{{ error_message }}</p>
                    </div>

                    <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                        Have an account?
                        <router-link to="/login"
                                     class="font-medium text-primary-600 hover:underline dark:text-primary-500">Login
                        </router-link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import {ref} from "vue";
import axios from "axios";
import router from "../router/router.js";

const loginData = ref({
    email: '',
    password: '',
    repeat: '',
    name: '',
    error: []
})

function submitForm(loginData) {
    loginData.error  = [];
    if (validate(loginData)) {
        axios.post('/api/register', {
            name: loginData.name,
            password: loginData.password,
            email: loginData.email
        }).then(response => {
            if (response.data.success === false)
                loginData.error.push(response.data.message);
        }).catch(error => {
            if (error.response.data.errors.name) {
               loginData.error.push(error.response.data.errors.name[0]);
            }
            if (error.response.data.errors.password) {
                loginData.error.push(error.response.data.errors.password[0]);
            }
            if (error.response.data.errors.email) {
                loginData.error.push(error.response.data.errors.email[0]);
            }
        });
    } else {
        loginData.error.push('password inputs mismatch or fields are empty');
    }
}

function validate(loginData) {
    if (loginData.password !== loginData.repeat)
        return false;
    if (!loginData.password || !loginData.repeat || !loginData.email || !loginData.name)
        return false
    return true;
}
</script>

<style scoped lang="sass">

</style>
