import { defineStore } from 'pinia';
import axios from 'axios';
import router from '../router';
import echo from '../echo';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loginError: null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    hasRole: (state) => (role) => state.user?.role === role,
  },
  actions: {
    async login(email, password) {
      this.loginError = null;
      try {
        const response = await axios.post('/api/login', { email, password });
        this.token = response.data.token;
        this.user = response.data.user;
        localStorage.setItem('token', this.token);
        this.setAuthHeader();
        this.updateEchoToken();
        return true;
      } catch (error) {
        const errors = error.response?.data?.errors;
        if (errors) {
          this.loginError = Object.values(errors).flat()[0];
        } else {
          this.loginError = error.response?.data?.message || 'Login failed. Please try again.';
        }
        return false;
      }
    },
    _clearState() {
      this.user = null;
      this.token = null;
      this.loginError = null;
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
      this.updateEchoToken();
    },
    async fetchUser() {
      if (!this.token) return;
      try {
        this.setAuthHeader();
        this.updateEchoToken();
        const response = await axios.get('/api/me');
        this.user = response.data;
      } catch (error) {
        this._clearState();
      }
    },
    async logout() {
      if (this.token) {
        try {
          this.setAuthHeader();
          await axios.post('/api/logout');
        } catch (_) {
        }
      }
      this._clearState();
      router.push('/login');
    },
    setAuthHeader() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
      }
    },
    updateEchoToken() {
      if (echo.connector.options.auth) {
        echo.connector.options.auth.headers.Authorization = `Bearer ${this.token}`;
      }
    }
  },
});
