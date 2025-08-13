<template>
    <div class="min-h-screen bg-gray-50">
        <AppHeader />

        <main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1
                    class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl"
                >
                    <span class="block">Track Stock Market</span>
                    <span class="block text-primary-600">Like a Pro</span>
                </h1>
                <p
                    class="mx-auto mt-3 max-w-md text-base text-gray-500 sm:text-lg md:mt-5 md:max-w-3xl md:text-xl"
                >
                    Get real-time stock quotes, track your search history, and
                    make informed investment decisions with our powerful stock
                    tracking platform.
                </p>
                <div
                    class="mx-auto mt-5 max-w-md sm:flex sm:justify-center md:mt-8"
                >
                    <div class="rounded-md shadow">
                        <router-link
                            to="/register"
                            class="flex w-full items-center justify-center rounded-md border border-transparent bg-primary-600 px-8 py-3 text-base font-medium text-white hover:bg-primary-700 md:px-10 md:py-4 md:text-lg"
                        >
                            Get Started
                        </router-link>
                    </div>
                    <div class="mt-3 rounded-md shadow sm:ml-3 sm:mt-0">
                        <router-link
                            to="/login"
                            class="flex w-full items-center justify-center rounded-md border border-transparent bg-white px-8 py-3 text-base font-medium text-primary-600 hover:bg-gray-50 md:px-10 md:py-4 md:text-lg"
                        >
                            Sign In
                        </router-link>
                    </div>
                </div>
            </div>

            <div class="mt-16">
                <div
                    class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div class="pt-6">
                        <div class="flow-root rounded-lg bg-white px-6 pb-8">
                            <div class="-mt-6">
                                <div>
                                    <span
                                        class="inline-flex items-center justify-center rounded-md bg-primary-500 p-3 shadow-lg"
                                    >
                                        <ChartBarIcon
                                            class="h-6 w-6 text-white"
                                        />
                                    </span>
                                </div>
                                <h3
                                    class="mt-8 text-lg font-medium tracking-tight text-gray-900"
                                >
                                    Real-time Data
                                </h3>
                                <p class="mt-5 text-base text-gray-500">
                                    Get up-to-date stock quotes powered by
                                    AlphaVantage API for accurate market
                                    information.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <div class="flow-root rounded-lg bg-white px-6 pb-8">
                            <div class="-mt-6">
                                <div>
                                    <span
                                        class="inline-flex items-center justify-center rounded-md bg-primary-500 p-3 shadow-lg"
                                    >
                                        <ClockIcon class="h-6 w-6 text-white" />
                                    </span>
                                </div>
                                <h3
                                    class="mt-8 text-lg font-medium tracking-tight text-gray-900"
                                >
                                    Search History
                                </h3>
                                <p class="mt-5 text-base text-gray-500">
                                    Keep track of all your stock searches with
                                    detailed history and timestamps.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <div class="flow-root rounded-lg bg-white px-6 pb-8">
                            <div class="-mt-6">
                                <div>
                                    <span
                                        class="inline-flex items-center justify-center rounded-md bg-primary-500 p-3 shadow-lg"
                                    >
                                        <EnvelopeIcon
                                            class="h-6 w-6 text-white"
                                        />
                                    </span>
                                </div>
                                <h3
                                    class="mt-8 text-lg font-medium tracking-tight text-gray-900"
                                >
                                    Email Notifications
                                </h3>
                                <p class="mt-5 text-base text-gray-500">
                                    Receive email notifications with stock quote
                                    information for every search you make.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import AppHeader from '@/components/common/AppHeader.vue';
import {
    ChartBarIcon,
    ClockIcon,
    EnvelopeIcon,
} from '@heroicons/vue/24/outline';
</script>
