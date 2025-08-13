<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                Email address
            </label>
            <div class="mt-1">
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    required
                    class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm"
                    :class="{ 'border-red-500': getFieldError('email') }"
                />
                <p
                    v-if="getFieldError('email')"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ getFieldError('email') }}
                </p>
            </div>
        </div>

        <div>
            <label
                for="password"
                class="block text-sm font-medium text-gray-700"
            >
                Password
            </label>
            <div class="mt-1">
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-primary-500 sm:text-sm"
                    :class="{ 'border-red-500': getFieldError('password') }"
                />
                <p
                    v-if="getFieldError('password')"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ getFieldError('password') }}
                </p>
            </div>
        </div>

        <div>
            <button
                type="submit"
                :disabled="authStore.loading"
                class="flex w-full justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <LoadingSpinner
                    v-if="authStore.loading"
                    size="sm"
                    color="white"
                />
                <span v-else>Create Account</span>
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import LoadingSpinner from '@/components/common/LoadingSpinner.vue';
import { useAuthStore } from '@/stores/auth';
import type { ValidationError } from '@/types';
import {
    validateEmail,
    validateForm,
    validatePassword,
} from '@/utils/validation';
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const form = reactive({
    email: '',
    password: '',
});

const validationErrors = ref<ValidationError[]>([]);

const getFieldError = (fieldName: string) => {
    const error = validationErrors.value.find((err) => err.field === fieldName);
    return error?.message;
};

const handleSubmit = async () => {
    validationErrors.value = [];
    authStore.clearError();

    const errors = validateForm(form, {
        email: validateEmail,
        password: validatePassword,
    });

    if (errors.length > 0) {
        validationErrors.value = errors;
        return;
    }

    try {
        await authStore.register(form);
        router.push('/login');
    } catch (error) {
        // Error is handled by the store
    }
};
</script>
