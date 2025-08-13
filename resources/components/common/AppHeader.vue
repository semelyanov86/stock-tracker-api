<template>
    <header class="border-b bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <router-link
                        to="/"
                        class="text-2xl font-bold text-primary-600"
                    >
                        📈 Stock Tracker
                    </router-link>
                </div>

                <nav class="flex items-center space-x-4">
                    <template v-if="!authStore.isAuthenticated">
                        <router-link
                            to="/login"
                            class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-600"
                        >
                            Login
                        </router-link>
                        <router-link
                            to="/register"
                            class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700"
                        >
                            Register
                        </router-link>
                    </template>

                    <template v-else>
                        <router-link
                            to="/dashboard"
                            class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-600"
                        >
                            Dashboard
                        </router-link>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-600">{{
                                authStore.user?.email
                            }}</span>
                            <button
                                @click="handleLogout"
                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-red-600"
                            >
                                Logout
                            </button>
                        </div>
                    </template>
                </nav>
            </div>
        </div>
    </header>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = () => {
    authStore.logout();
    router.push('/');
};
</script>
