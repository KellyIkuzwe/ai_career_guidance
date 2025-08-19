import React, { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';

const AuthContext = createContext();

export function useAuth() {
  return useContext(AuthContext);
}

export function AuthProvider({ children }) {
  const [currentUser, setCurrentUser] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    // Check if user is already logged in
    const fetchUser = async () => {
      try {
        const res = await axios.get('/api/users/me');
        setCurrentUser(res.data);
      } catch (err) {
        setCurrentUser(null);
      } finally {
        setIsLoading(false);
      }
    };

    fetchUser();
  }, []);

  const login = async (email, password) => {
    try {
      const res = await axios.post('/api/users/login', { email, password });
      setCurrentUser(res.data);
      return res.data;
    } catch (err) {
      throw err.response?.data?.msg || 'Login failed';
    }
  };

  const register = async (userData) => {
    try {
      const res = await axios.post('/api/users/register', userData);
      return res.data;
    } catch (err) {
      throw err.response?.data?.msg || 'Registration failed';
    }
  };

  const logout = async () => {
    try {
      await axios.post('/api/users/logout');
      setCurrentUser(null);
    } catch (err) {
      console.error('Logout error:', err);
    }
  };

  const value = {
    currentUser,
    isLoading,
    login,
    register,
    logout,
    isJobSeeker: currentUser?.role === 'jobseeker',
    isCompany: currentUser?.role === 'company',
    isAdmin: currentUser?.role === 'admin',
    isAuthenticated: !!currentUser
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}