<template>
    <div class="min-h-screen bg-gray-50">
        <AppHeader />

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Welcome back, {{ authStore.user?.email }}
                </p>
            </div>

            <div class="space-y-8">
                <!-- Stock Search Form -->
                <StockSearchForm />

                <!-- Error Alert -->
                <ErrorAlert
                    :message="stockStore.error"
                    @close="stockStore.clearError"
                />

                <!-- Current Quote -->
                <StockQuoteCard :quote="stockStore.currentQuote" />

                <!-- History Table -->
                <HistoryTable :history="stockStore.history" />
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import AppHeader from '@/components/common/AppHeader.vue';
import ErrorAlert from '@/components/common/ErrorAlert.vue';
import StockSearchForm from '@/components/forms/StockSearchForm.vue';
import HistoryTable from '@/components/stock/HistoryTable.vue';
import StockQuoteCard from '@/components/stock/StockQuoteCard.vue';
import { useAuthStore } from '@/stores/auth';
import { useStockStore } from '@/stores/stock';
import { onMounted } from 'vue';

const authStore = useAuthStore();
const stockStore = useStockStore();

onMounted(() => {
    // Load history when component mounts
    stockStore.loadHistory();
});
</script>
