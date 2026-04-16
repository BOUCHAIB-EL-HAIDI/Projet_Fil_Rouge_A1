<template>
  <div class="min-h-screen flex items-center justify-center bg-[#0f172a] px-4 py-16 selection:bg-indigo-500/30">
    <div class="max-w-xl w-full bg-white/[0.02] border border-white/10 rounded-2xl p-8 backdrop-blur-md shadow-2xl">
      <div class="text-center mb-10">
        <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center shadow-[0_0_30px_-5px_rgba(99,102,241,0.5)] mx-auto mb-6 transform rotate-3">
             <span class="text-white font-black text-xl">P</span>
        </div>
        <h1 class="text-3xl font-black text-white tracking-widest uppercase">Inscription</h1>
        <p class="text-slate-500 mt-2 font-medium text-xs tracking-widest uppercase">Créez votre accès ProcurePath</p>
      </div>

      <div v-if="departments.length === 0 && !deptLoadError" class="flex justify-center py-20">
         <div class="w-8 h-8 border-4 border-indigo-500/20 border-t-indigo-500 rounded-full animate-spin"></div>
      </div>

      <div v-else-if="deptLoadError" class="p-4 rounded-xl bg-orange-500/10 border border-orange-500/20 text-xs font-bold text-orange-400 text-center mb-8">
        Erreur de chargement. Veuillez rafraîchir.
      </div>

      <form v-else @submit.prevent="handleRegister" class="space-y-6">
        <div class="space-y-2 group">
          <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Nom complet</label>
          <input v-model="form.name" type="text" placeholder="John Doe" class="reg-input" required />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-2 group">
            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Email</label>
            <input v-model="form.email" type="email" placeholder="john@example.com" class="reg-input" required />
          </div>
          <div class="space-y-2 group">
            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Département</label>
            <select v-model="form.department_id" class="reg-input appearance-none" required>
              <option disabled value="">Choisir...</option>
              <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
            </select>
          </div>
        </div>

        <div class="space-y-2 group">
          <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Poste / Fonction</label>
          <input v-model="form.post" type="text" placeholder="ex: Acheteur" class="reg-input" required />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-2 group">
            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Mot de passe</label>
            <input v-model="form.password" type="password" placeholder="••••••••" class="reg-input" required minlength="8" />
          </div>
          <div class="space-y-2 group">
            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Confirmation</label>
            <input v-model="form.password_confirmation" type="password" placeholder="••••••••" class="reg-input" required />
          </div>
        </div>

        <button
          type="submit"
          class="w-full bg-indigo-500 text-white font-black uppercase tracking-[0.2em] py-4 rounded-xl shadow-[0_15px_40px_-10px_rgba(99,102,241,0.5)] hover:shadow-[0_20px_50px_-8px_rgba(99,102,241,0.6)] hover:-translate-y-1 active:translate-y-0 transition-all text-[10px] mt-4"
        >
          Créer mon compte
        </button>

        <div class="mt-8 text-center pt-8 border-t border-white/5">
          <p class="text-slate-500 text-xs font-medium uppercase tracking-widest">
            Déjà inscrit ? 
            <router-link to="/login" class="text-indigo-400 font-bold ml-2 hover:text-indigo-300">Se connecter</router-link>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import axios from 'axios';

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  post: '',
  department_id: '',
});

const departments = ref([]);
const deptLoadError = ref(false);
const authStore = useAuthStore();
const router = useRouter();

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/departments');
    departments.value = data;
  } catch {
    deptLoadError.value = true;
  }
});

const handleRegister = async () => {
  if (departments.value.length === 0) return;
  try {
    const payload = {
      ...form.value,
      department_id: Number(form.value.department_id),
    };
    const response = await axios.post('/api/register', payload);
    authStore.token = response.data.token;
    authStore.user = response.data.user;
    localStorage.setItem('token', authStore.token);
    authStore.setAuthHeader();
    router.push('/');
  } catch (error) {
    const msg = error.response?.data?.message;
    const errs = error.response?.data?.errors;
    if (errs) {
      alert(Object.values(errs).flat().join('\n'));
    } else {
      alert(msg || 'Inscription échouée');
    }
  }
};
</script>

<style scoped>
@reference "../style.css";
.reg-input {
  @apply w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-slate-200 placeholder:text-slate-700 focus:outline-none focus:border-indigo-500/50 transition-all text-sm;
}
</style>
