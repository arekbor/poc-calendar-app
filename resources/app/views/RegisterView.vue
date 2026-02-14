<script setup lang="ts">
import api, { handleApiError } from '@/api';
import router from '@/router';
import type { Register } from '@/types/register';
import { ref } from 'vue';

const payload = ref<Register>({
    username: '',
    email: '',
    password: '',
    repeat_password: ''
});

const errorMessage = ref<string | null>(null);
const loading = ref(false);

const onRegister = async () => {
    loading.value = true;

    try {
        await api.post('/auth/register', payload.value);
        router.push({ name: 'login' });
    } catch(error) {
        errorMessage.value = handleApiError(error);
    } finally {
        loading.value = false;
    }
};

</script>

<template>
    <form v-on:submit.prevent="onRegister">
        <div class="form-cell">
            <label for="username">Username</label>
            <input type="text" id="username" v-model="payload.username" />
        </div>

        <div class="form-cell">
            <label for="email">Email</label>
            <input type="text" id="email" v-model="payload.email" />
        </div>

        <div class="form-cell">
            <label for="password">Password</label>
            <input type="password" id="password" v-model="payload.password" />
        </div>

        <div class="form-cell">
            <label for="repeat_password">Repeat password</label>
            <input type="password" id="repeat_password" v-model="payload.repeat_password" />
        </div>

        <div class="form-cell">
            <button type="submit" :disabled="loading">Register</button>
        </div>

        <div v-if="errorMessage">
            <small>{{ errorMessage }}</small>
        </div>

        <div>
            <RouterLink :to="{ name: 'login' }">
                Already have an account? - login
            </RouterLink>
        </div>
    </form>
</template>

<style scoped>

</style>
