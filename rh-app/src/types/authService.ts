// src/types/authService.ts
import api from '../types/api';

// Définition des types utilisateur et réponse
export interface AuthUser {
  username: string;
  roles: string[];
  droits: string[];
  userId: number;
  agentId: number;
}

export interface AuthResponse {
  token: string;
  type: 'Bearer';
  username: string;
  roles: string[];
  droits: string[];
  userId: number;
  agentId: number;
}

// Service
export const authService = {
  login: async (email: string, password: string): Promise<AuthResponse> => {
    // L'endpoint backend utilise 'username' pour l'identifiant, mais on envoie email
    // (le backend attend probablement 'email' dans le LoginRequest)
    const response = await api.post('/auth/login', { email, password });
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

  setSession: (token: string, user: AuthUser): void => {
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
  },

  getToken: (): string | null => {
    return localStorage.getItem('token');
  },

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