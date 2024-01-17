<script setup>
import {ref, onMounted} from "vue";
import axios from "axios";
import router from "../router/router.js";
import moment from "moment"

let newTask = ref({
    title: '',
    description: '',
    deadline: '',
    isEditing: false,
    isReady: false
});
const error = ref('');
const originalTask = ref({});
const tasks = ref([]);
onMounted(() => {
    axios.get('/api/tasks').then(
        response => {
            response.data.forEach(task => {
                tasks.value.push({
                    title: task.title,
                    description: task.description,
                    deadline: moment(task.deadline).format('YYYY-MM-DDThh:mm'),
                    id: task.id,
                    isEditing: false,
                    is_ready: task.is_ready
                });
            });
            sortTasks();
        }
    ).catch(error => {
        if (error.response.status === 401) {
            router.push('/login');
            localStorage.removeItem('token');
        }
    });
});

function save(task) {
    axios.put(`api/tasks/${task.id}`, task)
        .catch(error => {
            console.log(error);
        });
}

function sortTasks() {
    tasks.value.sort((a, b) => new Date(a.deadline) - new Date(b.deadline));
}

function addTask(task) {
    axios.post("/api/tasks/add", task).then(response => {
        let addedTask = response.data.task;
        addedTask.isEditing = false;
        tasks.value.push(addedTask);
        sortTasks();
    }).catch(error => {
        console.log(error);
    });
    newTask.value.deadline = '';
    newTask.value.title = '';
    newTask.value.description = '';
}

function CancelEdit(task) {
    task.title = originalTask.value.title;
    task.description = originalTask.value.description;
    task.deadline = originalTask.value.deadline;
    task.id = originalTask.value.id;
    task.isEditing = false;
}

function ValidateInput(task) {
    return !(!task.deadline || !task.title || !moment(task.deadline).isValid());
}

function EditClick(task) {
    error.value = '';
    let prevTask = tasks.value.find(task => task.id === originalTask.value.id);
    if (prevTask !== undefined) {
        CancelEdit(prevTask);
    }
    originalTask.value = {...task};
    task.isEditing = true;
}

function OnCheckedHandler(event, task) {
    save(task);
}

function SaveEdited(task) {
    if (ValidateInput(task)) {
        task.isEditing = false;
        save(task);
        sortTasks();
    } else {
        error.value = "fulfill title or deadline";
    }
}

let a = true

function deleteTask(task) {
    axios.delete(`/api/tasks/${task.id}`).catch(error => console.log(error));
    let index = tasks.value.findIndex(obj => obj.id === task.id);
    tasks.value.splice(index, 1);
}
</script>

<template>
    <div class="flex justify-center mx-40 mt-2.5 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 ">
        <input v-model="newTask.deadline" type="datetime-local"
               class="block mx-4 p-2.5  text-sm text-gray-400  bg-gray-800 border border-gray-600 rounded-lg">
        <input v-model="newTask.title"
               class="dark:text-white block mx-4 p-2.5 text-sm placeholder-gray-400 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600"
               placeholder="title"/>
        <textarea v-model="newTask.description" rows="1"
                  class="resize-none block mx-4 p-2.5  text-sm w-1/2  bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder="description"></textarea>
        <button @click="addTask(newTask)"
                class="inline-flex justify-center p-2 text-blue-600 rounded-full cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600">
            Add
        </button>
    </div>
    <ul class="text-xl divide-y divide-gray-100 container mx-auto px-40">
        <li v-for="task in tasks" :key="task.id">
            <div v-if="task.isEditing === false" class="flex justify-end gap-x-6 py-5 bg-transparent">
                <div class="flex min-w-0 gap-x-4 w-1/3">
                    <div class="min-w-0 flex-auto">
                        <p class="leading-6 text-white">{{ task.title }}</p>
                        <p class="mt-1 truncate leading-5 text-gray-500">{{ task.description }}</p>
                    </div>
                </div>
                <div class="w-2/3 hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                    <p class="leading-6 text-gray-400">{{ moment(task.deadline).format('MMMM Do YYYY, H:mm:ss') }}</p>
                </div>
                <div class="hidden shrink-0 justify-center sm:flex sm:flex-col sm:items-end">
                    <div class="inline-flex items-center">
                        <label class="relative flex items-center p-3 rounded-full cursor-pointer">
                            <input type="checkbox"
                                   v-model="task.is_ready"
                                   @change="OnCheckedHandler($event, task)"
                                   class="bg-gray-600 before:content[''] peer relative w-6 h-6 cursor-pointer appearance-none rounded-md  transition-all before:absolute before:top-2/4 before:left-2/4 before:block before:h-12 before:w-12 before:-translate-y-2/4 before:-translate-x-2/4 before:rounded-full before:bg-blue-gray-500 before:opacity-0 before:transition-opacity checked:border-blue-500 checked:bg-blue-500 checked:before:bg-blue-500 hover:before:opacity-10"
                            />
                            <span
                                class="absolute text-white transition-opacity opacity-0 pointer-events-none top-2/4 left-2/4 -translate-y-2/4 -translate-x-2/4 peer-checked:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20"
                                     fill="currentColor"
                                     stroke="currentColor" stroke-width="1">
                                    <path fill-rule="evenodd"
                                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                          clip-rule="evenodd">
                                    </path>
                                </svg>
                            </span>
                        </label>
                    </div>
                </div>
                <div class=" flex justify-end space-x-2">
                    <button
                        class="p-0.5 w-16 bg-transparent border-2 border-rose-900 rounded hover:bg-rose-900 text-white"
                        @click="deleteTask(task)"
                    >
                        Delete
                    </button>
                    <button
                        class="p-0.5 w-16 bg-transparent border-2 border-purple-800 rounded hover:bg-purple-800 text-white"
                        @click='EditClick(task)'>
                        Edit
                    </button>
                </div>
            </div>
            <div v-else class="flex justify-end gap-x-6 py-5 bg-transparent">
                <div class="flex gap-x-4 w-2/3">
                    <div class="min-w-0 flex-auto">
                        <input type="text" v-model="task.title"
                               class="dark:text-white block p-1 text-sm placeholder-gray-400 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600">
                        <textarea rows="1" v-model="task.description"
                                  class="mt-1 resize-none block  p-1  text-sm w-1/2  bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        />
                    </div>
                </div>
                <div v-show="error" class="w-1/3">
                    <p class="text-red-500">{{ error }}</p>
                </div>
                <div class="w-1/4 hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                    <input
                        class="block mr-9 p-2.5  text-sm text-gray-400  bg-gray-800 border border-gray-600 rounded-lg"
                        type="datetime-local" v-model="task.deadline">
                </div>
                <div class="w-1/3 flex justify-end space-x-2">
                    <button
                        class="p-0.5 w-16 bg-transparent border-2 border-amber-700 rounded hover:bg-amber-700 text-white"
                        @click="CancelEdit(task)"
                    >
                        Cancel
                    </button>
                    <button
                        class="p-0.5 w-16 bg-transparent border-2 border-green-800 rounded hover:bg-green-800 text-white"
                        @click='SaveEdited(task)'>
                        Save
                    </button>
                </div>
            </div>
        </li>
    </ul>
</template>

<style scoped lang="sass">
</style>
