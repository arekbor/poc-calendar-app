<script setup lang="ts">
import api from '@/api';
import router from '@/router';
import type { User } from '@/types/user';
import { onMounted, ref } from 'vue';

const user = ref<User | null>(null);

const onDashboard = () => {
    router.push({ name: 'dashboard' });
};

onMounted(async () => {
    const { data } = await api.get<User>('/auth/me');
    user.value = data;
});

</script>

<template>
    <div class="profile-page" v-if="user">
        <div class="profile-card">
            <h2>Profil użytkownika</h2>

            <div class="profile-row">
                <span class="label">Id</span>
                <span class="value">{{ user.id}}</span>
            </div>

            <div class="profile-row">
                <span class="label">Email</span>
                <span class="value">{{ user.email }}</span>
            </div>

            <div class="profile-row">
                <span class="label">Username</span>
                <span class="value">{{ user.username }}</span>
            </div>

            <button class="back-btn" @click="onDashboard">
                ⬅ Powrót do Dashboardu
            </button>
        </div>
    </div>
</template>

<style scoped>
.profile-page {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #f3f4f6;
}

.profile-card {
    background: #ffffff;
    padding: 24px 32px;
    border-radius: 12px;
    width: 360px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.profile-card h2 {
    margin-bottom: 20px;
    text-align: center;
    font-size: 20px;
}

.profile-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    padding: 10px 12px;
    background: #f9fafb;
    border-radius: 8px;
}

.label {
    color: #6b7280;
    font-size: 14px;
}

.value {
    font-weight: 500;
}

.back-btn {
    margin-top: 20px;
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: none;
    background: #2563eb;
    color: white;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s;
}

.back-btn:hover {
    background: #1d4ed8;
}
</style>