<template>
  <div class="min-h-screen flex items-center justify-center bg-[#0f172a] px-4 selection:bg-indigo-500/30">
    <div class="max-w-md w-full bg-white/[0.02] border border-white/10 rounded-2xl p-8 backdrop-blur-md shadow-2xl">
      <div class="text-center mb-10">
        <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center shadow-[0_0_30px_-5px_rgba(99,102,241,0.5)] mx-auto mb-6 transform rotate-3">
             <span class="text-white font-black text-xl">P</span>
        </div>
        <h1 class="text-3xl font-black text-white tracking-widest uppercase">Connexion</h1>
        <p class="text-slate-500 mt-2 font-medium text-xs tracking-widest uppercase">ProcurePath Access</p>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-6">
        <div class="space-y-2">
          <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Identifiant Email</label>
          <input 
            v-model="email" 
            type="email" 
            placeholder="votre@email.com" 
            class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-slate-200 placeholder:text-slate-700 focus:outline-none focus:border-indigo-500/50 transition-all text-sm"
            required 
          />
        </div>

        <div class="space-y-2">
          <div class="flex justify-between items-center px-1">
            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">Mot de passe</label>
          </div>
          <div class="relative">
            <input 
              v-model="password" 
              :type="showPassword ? 'text' : 'password'" 
              placeholder="••••••••" 
              class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-slate-200 placeholder:text-slate-700 focus:outline-none focus:border-indigo-500/50 transition-all text-sm"
              required 
            />
            <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 hover:text-indigo-400">
              <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
            </button>
          </div>
        </div>

        <div v-if="authStore.loginError" class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs font-bold text-rose-400">
          {{ authStore.loginError }}
        </div>

        <button 
          type="submit" 
          :disabled="loading"
          class="block w-full bg-indigo-500 text-white font-black uppercase tracking-[0.2em] py-4 rounded-xl shadow-[0_15px_40px_-10px_rgba(99,102,241,0.5)] hover:shadow-[0_20px_50px_-8px_rgba(99,102,241,0.6)] hover:-translate-y-1 active:translate-y-0 transition-all text-[10px]"
        >
          {{ loading ? 'Authentification...' : 'Se Connecter' }}
        </button>
      </form>

      <div class="mt-12 text-center pt-8 border-t border-white/5">
        <p class="text-slate-500 text-xs font-medium uppercase tracking-widest">
          Pas de compte ? 
          <router-link to="/register" class="text-indigo-400 font-bold ml-2 hover:text-indigo-300">Créer un compte</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const loading = ref(false);
const showPassword = ref(false);
const authStore = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
  loading.value = true;
  const success = await authStore.login(email.value, password.value);
  loading.value = false;
  if (success) router.push('/');
};
</script>
