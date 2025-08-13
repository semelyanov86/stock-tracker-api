import type {
    ApiError,
    AuthResponse,
    LoginCredentials,
    RegisterCredentials,
    StockHistory,
    StockQuote,
} from '@/types';
import axios, { AxiosError, AxiosResponse } from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://web.test/api';

class ApiService {
    private axiosInstance = axios.create({
        baseURL: API_BASE_URL,
        headers: {
            'Content-Type': 'application/json',
        },
    });

    constructor() {
        // Request interceptor to add auth token
        this.axiosInstance.interceptors.request.use(
            (config) => {
                const token = localStorage.getItem('authToken');
                if (token) {
                    config.headers.Authorization = `Bearer ${token}`;
                }
                return config;
            },
            (error) => {
                return Promise.reject(error);
            },
        );

        // Response interceptor for error handling
        this.axiosInstance.interceptors.response.use(
            (response) => response,
            (error: AxiosError<ApiError>) => {
                if (error.response?.status === 401) {
                    localStorage.removeItem('authToken');
                    window.location.href = '/login';
                }
                return Promise.reject(error);
            },
        );
    }

    async register(
        credentials: RegisterCredentials,
    ): Promise<{ message: string; user_id: number }> {
        const response: AxiosResponse = await this.axiosInstance.post(
            '/register',
            credentials,
        );
        return response.data;
    }

    async login(credentials: LoginCredentials): Promise<AuthResponse> {
        const response: AxiosResponse<AuthResponse> =
            await this.axiosInstance.post('/auth/login', credentials);
        return response.data;
    }

    async getStockQuote(symbol: string): Promise<StockQuote> {
        const response: AxiosResponse<StockQuote> =
            await this.axiosInstance.get(`/stock?q=${symbol}`);
        return response.data;
    }

    async getStockHistory(): Promise<StockHistory[]> {
        const response: AxiosResponse<StockHistory[]> =
            await this.axiosInstance.get('/history');
        return response.data;
    }

    setAuthToken(token: string): void {
        localStorage.setItem('authToken', token);
    }

    removeAuthToken(): void {
        localStorage.removeItem('authToken');
    }

    getAuthToken(): string | null {
        return localStorage.getItem('authToken');
    }
}

export const apiService = new ApiService();
export default apiService;
