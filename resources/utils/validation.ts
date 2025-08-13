import type { ValidationError } from '@/types';

export const validateEmail = (email: string): ValidationError | null => {
    if (!email) {
        return { field: 'email', message: 'Email is required' };
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        return {
            field: 'email',
            message: 'Please enter a valid email address',
        };
    }

    return null;
};

export const validatePassword = (password: string): ValidationError | null => {
    if (!password) {
        return { field: 'password', message: 'Password is required' };
    }

    if (password.length < 6) {
        return {
            field: 'password',
            message: 'Password must be at least 6 characters long',
        };
    }

    return null;
};

export const validateStockSymbol = (symbol: string): ValidationError | null => {
    if (!symbol) {
        return { field: 'symbol', message: 'Stock symbol is required' };
    }

    if (symbol.length < 1 || symbol.length > 10) {
        return {
            field: 'symbol',
            message: 'Stock symbol must be between 1 and 10 characters',
        };
    }

    return null;
};

export const validateForm = (
    fields: Record<string, string>,
    validators: Record<string, (value: string) => ValidationError | null>,
): ValidationError[] => {
    const errors: ValidationError[] = [];

    for (const [fieldName, value] of Object.entries(fields)) {
        const validator = validators[fieldName];
        if (validator) {
            const error = validator(value);
            if (error) {
                errors.push(error);
            }
        }
    }

    return errors;
};
