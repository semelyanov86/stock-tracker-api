<template>
    <div class="min-h-screen bg-gray-50">
        <AppHeader />

        <div class="flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                    Create your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Or
                    <router-link
                        to="/login"
                        class="font-medium text-primary-600 hover:text-primary-500"
                    >
                        sign in to your existing account
                    </router-link>
                </p>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white px-4 py-8 shadow sm:rounded-lg sm:px-10">
                    <ErrorAlert
                        :message="authStore.error"
                        @close="authStore.clearError"
                    />

                    <RegistrationForm />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import AppHeader from '@/components/common/AppHeader.vue';
import ErrorAlert from '@/components/common/ErrorAlert.vue';
import RegistrationForm from '@/components/forms/RegistrationForm.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
</script>
