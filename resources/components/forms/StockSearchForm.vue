<template>
    <div class="rounded-lg bg-white p-6 shadow">
        <h3 class="mb-4 text-lg font-medium text-gray-900">
            Search Stock Quote
        </h3>

        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div>
                <label
                    for="symbol"
                    class="block text-sm font-medium text-gray-700"
                >
                    Stock Symbol
                </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <input
                        id="symbol"
                        v-model="symbol"
                        type="text"
                        placeholder="e.g., IBM, AAPL, TSLA"
                        class="block w-full flex-1 rounded-none rounded-l-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        :class="{ 'border-red-500': getFieldError('symbol') }"
                    />
                    <button
                        type="submit"
                        :disabled="stockStore.loading"
                        class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <LoadingSpinner
                            v-if="stockStore.loading"
                            size="sm"
                            color="white"
                        />
                        <span v-else>Search</span>
                    </button>
                </div>
                <p
                    v-if="getFieldError('symbol')"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ getFieldError('symbol') }}
                </p>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import LoadingSpinner from '@/components/common/LoadingSpinner.vue';
import { useStockStore } from '@/stores/stock';
import type { ValidationError } from '@/types';
import { validateStockSymbol } from '@/utils/validation';
import { ref } from 'vue';

const stockStore = useStockStore();

const symbol = ref('');
const validationErrors = ref<ValidationError[]>([]);

const getFieldError = (fieldName: string) => {
    const error = validationErrors.value.find((err) => err.field === fieldName);
    return error?.message;
};

const handleSubmit = async () => {
    validationErrors.value = [];
    stockStore.clearError();

    const error = validateStockSymbol(symbol.value);
    if (error) {
        validationErrors.value = [error];
        return;
    }

    try {
        await stockStore.getStockQuote(symbol.value.toUpperCase());
        symbol.value = '';
    } catch (error) {}
};
</script>
