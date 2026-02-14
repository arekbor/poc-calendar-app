<script setup lang="ts">
import router from '@/router';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const onLogout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
};

const onProfile = async () => {
    router.push({ name: 'profile' });
};
</script>

<template>
    <header class="app-header">
        <h2 class="logo">Calendar</h2>

        <nav class="actions">
            <button @click="onProfile">Profile</button>
            <button class="logout" @click="onLogout">Logout</button>
        </nav>
    </header>

    <RouterView/>
</template>

<style scoped>
.app-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 24px;
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}

.logo {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}

.actions {
    display: flex;
    gap: 12px;
}

button {
    padding: 8px 14px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.2s;
}

button:hover {
    background: #e5e7eb;
}

.logout {
    background: #ef4444;
    border-color: #ef4444;
    color: white;
}

.logout:hover {
    background: #dc2626;
}
</style>