import { apiService } from '@/services/api';
import type { LoginCredentials, RegisterCredentials, User } from '@/types';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null);
    const token = ref<string | null>(localStorage.getItem('authToken'));
    const loading = ref(false);
    const error = ref<string | null>(null);

    const isAuthenticated = computed(() => !!token.value && !!user.value);

    const login = async (credentials: LoginCredentials) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiService.login(credentials);
            token.value = response.token;
            user.value = response.user;
            apiService.setAuthToken(response.token);
        } catch (err: any) {
            error.value = err.response?.data?.error || 'Login failed';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const register = async (credentials: RegisterCredentials) => {
        loading.value = true;
        error.value = null;

        try {
            await apiService.register(credentials);
        } catch (err: any) {
            error.value = err.response?.data?.error || 'Registration failed';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const logout = () => {
        user.value = null;
        token.value = null;
        apiService.removeAuthToken();
    };

    const clearError = () => {
        error.value = null;
    };

    return {
        user,
        token,
        loading,
        error,
        isAuthenticated,
        login,
        register,
        logout,
        clearError,
    };
});
