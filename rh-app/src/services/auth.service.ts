// src/services/authService.ts
import api from '../types/api';
import type { AuthUser } from '../types/auth'; // à créer

export interface AuthResponse {
  token: string;
  type: string; // "Bearer"
  username: string;
  roles: string[];
  userId: number;
  agentId: number;
}

export const authService = {
  login: async (email: string, password: string): Promise<AuthResponse> => {
    const response = await api.post<AuthResponse>('/auth/login', { email, password });
    return response.data;
  },

  logout: async (): Promise<void> => {
    try {
      const token = localStorage.getItem('token');
      if (token) {
        await api.post('/auth/logout', {}, {
          headers: { Authorization: `Bearer ${token}` }
        });
      }
    } catch (error) {
      console.error('Logout error:', error);
    }
  },

  // Stocke le token ET l'utilisateur complet (AuthUser)
  setSession: (token: string, user: AuthUser): void => {
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
  },

  getToken: (): string | null => {
    return localStorage.getItem('token');
  },

  // Récupère l'utilisateur stocké et le parse en AuthUser
  getUser: (): AuthUser | null => {
    const userStr = localStorage.getItem('user');
    if (userStr) {
      try {
        return JSON.parse(userStr) as AuthUser;
      } catch {
        return null;
      }
    }
    return null;
  },
};