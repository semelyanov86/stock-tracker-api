<template>
    <div v-if="quote" class="rounded-lg bg-white p-6 shadow">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900">
                    {{ quote.name }}
                </h3>
                <p class="text-sm text-gray-500">{{ quote.symbol }}</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">
                    ${{ quote.close.toFixed(2) }}
                </p>
                <p class="text-sm text-gray-500">Current Price</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-lg bg-gray-50 p-3 text-center">
                <p class="text-sm font-medium text-gray-500">Open</p>
                <p class="text-lg font-semibold text-gray-900">
                    ${{ quote.open.toFixed(2) }}
                </p>
            </div>
            <div class="rounded-lg bg-gray-50 p-3 text-center">
                <p class="text-sm font-medium text-gray-500">High</p>
                <p class="text-lg font-semibold text-green-600">
                    ${{ quote.high.toFixed(2) }}
                </p>
            </div>
            <div class="rounded-lg bg-gray-50 p-3 text-center">
                <p class="text-sm font-medium text-gray-500">Low</p>
                <p class="text-lg font-semibold text-red-600">
                    ${{ quote.low.toFixed(2) }}
                </p>
            </div>
            <div class="rounded-lg bg-gray-50 p-3 text-center">
                <p class="text-sm font-medium text-gray-500">Close</p>
                <p class="text-lg font-semibold text-gray-900">
                    ${{ quote.close.toFixed(2) }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { StockQuote } from '@/types';

interface Props {
    quote: StockQuote | null;
}

defineProps<Props>();
</script>
