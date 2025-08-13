<template>
    <div class="flex items-center justify-center" :class="containerClass">
        <div
            class="animate-spin rounded-full border-b-2"
            :class="spinnerClass"
        ></div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    size?: 'sm' | 'md' | 'lg';
    color?: 'primary' | 'white' | 'gray';
}

const props = withDefaults(defineProps<Props>(), {
    size: 'md',
    color: 'primary',
});

const containerClass = computed(() => {
    const sizes = {
        sm: 'h-8',
        md: 'h-12',
        lg: 'h-16',
    };
    return sizes[props.size];
});

const spinnerClass = computed(() => {
    const sizes = {
        sm: 'h-6 w-6',
        md: 'h-8 w-8',
        lg: 'h-12 w-12',
    };

    const colors = {
        primary: 'border-primary-600',
        white: 'border-white',
        gray: 'border-gray-600',
    };

    return `${sizes[props.size]} ${colors[props.color]}`;
});
</script>
