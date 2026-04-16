<template>
  <div class="animate-fade">
    <header class="flex justify-between items-center mb-16">
      <div>
        <h1 class="text-5xl font-extrabold glass-gradient-text uppercase tracking-tight">Nouvelle Demande</h1>
        <p class="text-slate-500 mt-2 font-medium">Creez une demande d'achat claire et complete.</p>
      </div>
      <button @click="$router.push('/')" class="btn btn-outline">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Retour
      </button>
    </header>

    <div class="glass-card">
      <form @submit.prevent="submitRequest(false)" class="space-y-12">
        <section>
          <h3 class="text-xs font-black uppercase tracking-[0.2em] text-indigo-400 mb-8 flex items-center gap-4">
            <span class="w-8 h-[1px] bg-indigo-500/50"></span>
            Informations principales
            <span class="flex-1 h-[1px] bg-white/5"></span>
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
              <span class="input-label">Titre de la demande</span>
              <input v-model="form.title" class="input-field" placeholder="Ex: Achat de 5 ordinateurs portables" required />
            </div>
            <div>
              <span class="input-label">Niveau de priorite</span>
              <select v-model="form.priority" class="input-field appearance-none">
                <option value="low">Basse</option>
                <option value="medium">Moyenne</option>
                <option value="high">Haute</option>
              </select>
            </div>
          </div>
          <div class="mt-8">
            <span class="input-label">Description</span>
            <textarea v-model="form.description" class="input-field h-32 leading-relaxed" placeholder="Precisez le besoin, le contexte et l'objectif..."></textarea>
          </div>
        </section>

        <section>
          <div class="flex justify-between items-center mb-8">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-purple-400 flex items-center gap-4 w-full">
              <span class="w-8 h-[1px] bg-purple-500/50"></span>
              Articles demandes
              <span class="flex-1 h-[1px] bg-white/5"></span>
            </h3>
            <button type="button" @click="addItem" class="btn btn-secondary !py-2 !px-4 text-xs shrink-0 ml-4 border border-white/10 hover:border-white/30 text-white rounded-xl flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
              Ajouter un article
            </button>
          </div>
          
          <div class="space-y-4">
            <div v-for="(item, index) in form.items" :key="index" class="flex flex-col md:flex-row gap-4 items-end bg-white/[0.02] p-6 rounded-2xl border border-white/5 group hover:bg-white/[0.04] transition-colors">
              <div class="flex-1 w-full">
                <span class="input-label !text-[10px] !mb-2">Nom de l'article</span>
                <input v-model="item.productName" class="input-field !mb-0 !py-3" placeholder="Ex: PC Portable Lenovo ThinkPad" required />
              </div>
              <div class="w-full md:w-64">
                <span class="input-label !text-[10px] !mb-2">Reference (optionnel)</span>
                <input v-model="item.reference" class="input-field !mb-0 !py-3 font-mono text-xs placeholder:font-sans placeholder:text-sm" placeholder="REF-12345" />
              </div>
              <div class="w-full md:w-32">
                <span class="input-label !text-[10px] !mb-2">Quantite</span>
                <input v-model.number="item.quantity" type="number" class="input-field !mb-0 !py-3" placeholder="1" required min="1" />
              </div>
              <button type="button" @click="removeItem(index)" class="btn bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white !p-4 border border-rose-500/20 shadow-[0_0_15px_-5px_rgba(244,63,94,0.3)] opacity-50 group-hover:opacity-100 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
        </section>

        <div class="flex gap-4 justify-end pt-8 mt-12 border-t border-white/5 relative">
          <div class="absolute left-0 top-1/2 -translate-y-1/2 text-xs text-slate-500 font-mono tracking-widest uppercase">
            Pret a envoyer
          </div>
          <button type="button" @click="submitRequest(true)" class="btn btn-outline min-w-[150px]" :disabled="loading">
            Enregistrer brouillon
          </button>
          <button type="submit" class="btn btn-primary min-w-[200px]" :disabled="loading">
            <span v-if="loading" class="flex items-center gap-3">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              Envoi...
            </span>
            <span v-else class="flex items-center justify-center w-full gap-3">
              Envoyer la demande
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);

onMounted(() => {
  if (authStore.user && authStore.user.role !== 'demandeur') {
    router.push('/');
  }
});

const form = ref({
  title: '',
  description: '',
  priority: 'medium',
  items: [{ productName: '', reference: null, quantity: 1 }]
});

const addItem = () => {
  form.value.items.push({ productName: '', reference: null, quantity: 1 });
};

const removeItem = (index) => {
  if (form.value.items.length > 1) {
    form.value.items.splice(index, 1);
  }
};

const submitRequest = async (isDraft = false) => {
  loading.value = true;
  try {
    const payload = {
      ...form.value,
      is_draft: isDraft,
      items: form.value.items.map(item => ({
        ...item,
        reference: item.reference ? item.reference : null
      }))
    };
    await axios.post('/api/purchase-requests', payload);
    router.push('/');
  } catch (error) {
    const apiMessage = error.response?.data?.message;
    const validationErrors = error.response?.data?.errors;
    if (validationErrors) {
      alert(Object.values(validationErrors).flat().join('\n'));
    } else {
      alert(apiMessage || "Echec de creation de la demande.");
    }
  } finally {
    loading.value = false;
  }
};
</script>
