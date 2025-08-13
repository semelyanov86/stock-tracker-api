<template>
    <div class="rounded-lg bg-white shadow">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900">Query History</h3>
        </div>

        <div v-if="history.length === 0" class="p-6 text-center">
            <p class="text-gray-500">
                No stock queries yet. Search for a stock to see your history.
            </p>
        </div>

        <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Date
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Company
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Symbol
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Open
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            High
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Low
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            Close
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr
                        v-for="item in history"
                        :key="`${item.symbol}-${item.date}`"
                        class="hover:bg-gray-50"
                    >
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm text-gray-900"
                        >
                            {{ formatDate(item.date) }}
                        </td>
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900"
                        >
                            {{ item.name }}
                        </td>
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                        >
                            {{ item.symbol }}
                        </td>
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm text-gray-900"
                        >
                            ${{ item.open.toFixed(2) }}
                        </td>
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm font-medium text-green-600"
                        >
                            ${{ item.high.toFixed(2) }}
                        </td>
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm font-medium text-red-600"
                        >
                            ${{ item.low.toFixed(2) }}
                        </td>
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm text-gray-900"
                        >
                            ${{ item.close.toFixed(2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { StockHistory } from '@/types';

interface Props {
    history: StockHistory[];
}

defineProps<Props>();

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
