import api from "@/api";
import type { Login } from "@/types/login";
import type { User } from "@/types/user";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useAuthStore = defineStore("auth", () => {
    const user = ref<User | null>(null);
    const isLoggedIn = ref<boolean>(false);

    const login = async (credentials: Login) => {
        const { data } = await api.post('/auth/login', credentials);
        user.value = data;
        isLoggedIn.value = true;
    }

    const logout = async () => {
        await api.post('/auth/logout');

        user.value = null;
        isLoggedIn.value = false;
    };

    return {
        user,
        isLoggedIn,
        login,
        logout
    };
}, {
    persist: {
        storage: localStorage,
        pick: ['user', 'isLoggedIn']
    }
});