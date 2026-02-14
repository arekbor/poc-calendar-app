<script setup lang="ts">
import { handleApiError } from '@/api';
import router from '@/router';
import { useAuthStore } from '@/stores/auth';
import type { Login } from '@/types/login';
import { ref } from 'vue';
import { useRoute } from 'vue-router';

const authStore = useAuthStore();
const route = useRoute();

const payload = ref<Login>({
    email: '',
    password: ''
});

const errorMessage = ref<string|null>(null);
const loading = ref(false);

const onLogin = async () => {
    loading.value = true;

    try {
        await authStore.login(payload.value);

        const redirect = route.query.redirect as string | undefined;
        router.replace(redirect || { name: 'dashboard' });
    } catch(error) {
        errorMessage.value = handleApiError(error);
    } finally {
        loading.value = false;
    }
};

</script>

<template>
    <form v-on:submit.prevent="onLogin">
        <div class="form-cell">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" v-model="payload.email" />
        </div>

        <div class="form-cell">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" v-model="payload.password" />
        </div>

        <div class="form-cell">
            <button type="submit" :disabled="loading">Login</button>
        </div>

        <div v-if="errorMessage">
            <small>{{ errorMessage }}</small>
        </div>

        <div>
            <RouterLink :to="{ name: 'register' }">
                No account? - register
            </RouterLink>
        </div>
    </form>
</template>

