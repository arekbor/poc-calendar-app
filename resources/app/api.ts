import axios, { HttpStatusCode } from "axios";
import router from "@/router";
import { useAuthStore } from "@/stores/auth";
import type { Csrf } from "@/types/csrf";

const api = axios.create({
    baseURL: '/api'
});

api.interceptors.request.use(
    async (config) => {
        if (config.method && ['post', 'put', 'patch', 'delete'].includes(config.method)) {

            try {
                const { data } = await api.get<Csrf>(`/csrf-token`);
            
                config.headers['csrf_name'] = data.csrf_name;
                config.headers['csrf_value'] = data.csrf_value;
            } catch(err) {
                Promise.reject(err);
            }
        }

        return config;
    },
    (error) => {
        Promise.reject(error);
    }
);

api.interceptors.response.use(
    (response) => {
        return response;
    },
    async (error) => {
        const authStore = useAuthStore();

        if (axios.isAxiosError(error)) {
            if (error.response?.status === HttpStatusCode.Unauthorized) {
                await authStore.logout();
                router.push({ name: 'login' })
            }

            if (error.response?.status === HttpStatusCode.NotFound) {
                router.push({ name: 'not-found' });
            }

            if(error.response?.status === HttpStatusCode.InternalServerError) {
                router.push({ name: 'internal-server-error' });
            }
        }

        return Promise.reject(error);
    }
);

export function handleApiError(error: unknown, fallbackMessage = 'Internal server error'): string {
    if (axios.isAxiosError(error)) {
        const errData = error.response?.data as { message?: string } | undefined;
        if (errData?.message) {
            return errData.message;
        }
    }

    return fallbackMessage;
};

export default api;