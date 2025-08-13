import { apiService } from '@/services/api';
import type { StockHistory, StockQuote } from '@/types';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useStockStore = defineStore('stock', () => {
    const currentQuote = ref<StockQuote | null>(null);
    const history = ref<StockHistory[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const getStockQuote = async (symbol: string) => {
        loading.value = true;
        error.value = null;

        try {
            const quote = await apiService.getStockQuote(symbol);
            currentQuote.value = quote;
            await loadHistory(); // Refresh history after new quote
        } catch (err: any) {
            error.value =
                err.response?.data?.error || 'Failed to fetch stock quote';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const loadHistory = async () => {
        try {
            const historyData = await apiService.getStockHistory();
            history.value = historyData;
        } catch (err: any) {
            console.error('Failed to load history:', err);
        }
    };

    const clearError = () => {
        error.value = null;
    };

    const clearCurrentQuote = () => {
        currentQuote.value = null;
    };

    return {
        currentQuote,
        history,
        loading,
        error,
        getStockQuote,
        loadHistory,
        clearError,
        clearCurrentQuote,
    };
});
