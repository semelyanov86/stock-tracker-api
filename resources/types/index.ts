export interface User {
    id: number;
    email: string;
}

export interface LoginCredentials {
    email: string;
    password: string;
}

export interface RegisterCredentials {
    email: string;
    password: string;
}

export interface AuthResponse {
    token: string;
    user: User;
}

export interface StockQuote {
    name: string;
    symbol: string;
    open: number;
    high: number;
    low: number;
    close: number;
}

export interface StockHistory {
    date: string;
    name: string;
    symbol: string;
    open: number;
    high: number;
    low: number;
    close: number;
}

export interface ApiError {
    error: string;
    details?: string;
}

export interface ValidationError {
    field: string;
    message: string;
}
