// src/contexts/AuthProvider.tsx
import React, { useState, useEffect } from 'react';
import { AuthContext } from './AuthContext';
import { authService } from '../types/authService';

import { AxiosError } from 'axios';
import { isTokenExpired } from '../utils/token';
import type { AuthUser } from '../types/auth';


export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<AuthUser | null>(null); // <-- Utiliser AuthUser
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const loadStorage = () => {
      try {
        const token = authService.getToken();
        const storedUser = authService.getUser(); // doit retourner AuthUser | null

        if (storedUser && token && !isTokenExpired(token)) {
          setUser(storedUser);
        } else {
          localStorage.removeItem('token');
          localStorage.removeItem('user');
        }
      } catch (error) {
        console.error('Erreur de chargement', error);
      } finally {
        setIsLoading(false);
      }
    };
    loadStorage();

    const handleSessionExpired = () => {
      setUser(null);
    };
    window.addEventListener('auth:logout', handleSessionExpired);
    return () => window.removeEventListener('auth:logout', handleSessionExpired);
  }, []);

  const signIn = async (email: string, password: string) => {
    try {
      const data = await authService.login(email, password);
      // data doit contenir token, username, roles, userId, agentId
      const userData: AuthUser = {
        username: data.username,
        roles: data.roles,
        droits: data.droits || [],
        userId: data.userId,
        agentId: data.agentId,
      };
      authService.setSession(data.token, userData);
      setUser(userData);
      return { success: true };
    } catch (err) {
      const error = err as AxiosError;
      console.error('Erreur de connexion:', error);
      let errorMessage = 'Email ou mot de passe incorrect';

      if (error.response?.data && typeof error.response.data === 'object' && 'message' in error.response.data) {
        errorMessage = (error.response.data as { message: string }).message;
      } else if (error.message) {
        errorMessage = error.message;
      }

      return { success: false, error: errorMessage };
    }
  };

  const signOut = async () => {
    try {
      await authService.logout();
    } catch (error) {
      console.error('Erreur lors de la déconnexion', error);
    } finally {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      setUser(null);
    }
  };

  return (
    <AuthContext.Provider value={{ user, isLoading, signIn, signOut }}>
      {children}
    </AuthContext.Provider>
  );
};