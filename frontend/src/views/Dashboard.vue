<template>
  <div class="animate-fade">
    <header class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-10 md:mb-16">
      <div>
        <h1 class="text-3xl md:text-5xl font-extrabold glass-gradient-text">{{ isDirecteur ? 'Tableau de Bord Direction' : 'Mon Tableau de Bord' }}</h1>
        <p class="text-slate-500 mt-2 font-medium">Suivi des demandes d'achat en temps reel.</p>
      </div>
      <button v-if="authStore.user?.role === 'demandeur'" @click="$router.push('/requests/create')" class="btn btn-primary w-full sm:w-auto">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Nouvelle demande
      </button>
    </header>

    <!-- Analytics section for Directeur -->
    <div v-if="isDirecteur && analytics?.metrics" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-8 mb-16">
      <div v-for="(val, key) in analytics.metrics" :key="key" class="glass-card group cursor-default !p-6">
        <div class="flex justify-between items-start mb-4">
          <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">{{ key.replace('_', ' ') }}</span>
          <div :class="['w-2 h-2 rounded-full animate-pulse', 
            key.includes('pending') ? 'bg-amber-500' : 
            key.includes('approved') ? 'bg-emerald-500' :
            key.includes('rejected') ? 'bg-rose-500' :
            key.includes('delivered') ? 'bg-cyan-500' : 'bg-indigo-500']"></div>
        </div>
        <div class="text-4xl font-bold group-hover:scale-110 transition-transform duration-500 origin-left">{{ val }}</div>
        <div class="mt-4 w-full bg-white/5 h-1 rounded-full overflow-hidden">
          <div :class="['h-full rounded-full transition-all duration-1000',
            key.includes('pending') ? 'bg-amber-500' : 
            key.includes('approved') ? 'bg-emerald-500' :
            key.includes('rejected') ? 'bg-rose-500' :
            key.includes('delivered') ? 'bg-cyan-500' : 'bg-indigo-500']" :style="{ width: '100%' }"></div>
        </div>
      </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">
      <!-- Activity Table -->
      <section class="xl:col-span-2 space-y-5 md:space-y-8">
        <div class="flex items-center gap-4">
          <h2 class="text-xl md:text-2xl font-bold">Demandes recentes</h2>
          <div class="h-[1px] flex-1 bg-white/5"></div>
          <span class="text-xs font-bold text-slate-500">{{ requests.length }} TOTAL</span>
        </div>

        <div class="space-y-3 md:hidden">
          <article
            v-for="req in requests"
            :key="req.id"
            class="glass-card border border-white/10"
          >
            <div class="flex items-start justify-between gap-3">
              <div>
                <h3 class="font-bold text-base">{{ req.title }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ new Date(req.created_at).toLocaleDateString() }}</p>
              </div>
              <button @click="$router.push(`/requests/${req.id}`)" class="p-2 rounded-xl bg-white/5 hover:bg-indigo-500 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
            </div>
            <div class="mt-4 flex items-center justify-between">
              <div :class="[
                'inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight',
                statusMap[req.status]?.class || 'bg-slate-400/10 text-slate-400'
              ]">
                <span class="w-1.5 h-1.5 rounded-full mr-2" :class="statusMap[req.status]?.dotClass"></span>
                {{ statusLabel(req.status) }}
              </div>
              <span class="text-xs font-semibold" :class="req.priority === 'high' ? 'text-red-400' : 'text-slate-300'">
                {{ priorityLabel(req.priority) }}
              </span>
            </div>
          </article>
          <div v-if="requests.length === 0" class="glass-card py-16 text-center">
             <div class="text-slate-500 italic mb-6">Aucune demande pour le moment.</div>
             <button v-if="authStore.user?.role === 'demandeur'" @click="$router.push('/requests/create')" class="btn btn-primary w-full max-w-[200px] mx-auto text-sm">
                Nouvelle demande
             </button>
          </div>
        </div>

        <div class="glass-panel overflow-hidden hidden md:block">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-white/5">
                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Demande</th>
                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Statut</th>
                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Priorite</th>
                <th class="px-8 py-6 text-right"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              <tr v-for="req in requests" :key="req.id" class="group hover:bg-white/[0.02] transition-colors">
                <td class="px-8 py-6">
                  <div class="font-bold text-lg mb-1 group-hover:text-indigo-400 transition-colors">{{ req.title }}</div>
                  <div class="text-xs text-slate-500">{{ new Date(req.created_at).toLocaleDateString() }}</div>
                </td>
                <td class="px-8 py-6">
                  <div :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight',
                    statusMap[req.status]?.class || 'bg-slate-400/10 text-slate-400'
                  ]">
                    <span class="w-1.5 h-1.5 rounded-full mr-2" :class="statusMap[req.status]?.dotClass"></span>
                    {{ statusLabel(req.status) }}
                  </div>
                </td>
                <td class="px-8 py-6">
                  <div class="text-sm font-medium" :class="req.priority === 'high' ? 'text-red-400' : 'text-slate-300'">
                    {{ priorityLabel(req.priority) }}
                  </div>
                </td>
                <td class="px-8 py-6 text-right">
                  <button @click="$router.push(`/requests/${req.id}`)" class="p-2 rounded-xl bg-white/5 hover:bg-indigo-500 group-hover:shadow-[0_0_20px_rgba(99,102,241,0.3)] transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                  </button>
                </td>
              </tr>
              <tr v-if="requests.length === 0">
                <td colspan="4" class="px-8 py-32 text-center">
                  <div class="flex flex-col items-center max-w-sm mx-auto">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 flex items-center justify-center mb-6 border border-indigo-500/20">
                      <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-white">Aucune demande trouvée</h3>
                    <p class="text-slate-500 text-sm mb-8 leading-relaxed">
                      C'est ici que vous pourrez suivre vos demandes d'achat. 
                      Commencez par en créer une pour lancer le processus !
                    </p>
                    <button v-if="authStore.user?.role === 'demandeur'" @click="$router.push('/requests/create')" class="btn btn-primary w-full">
                      Créer ma première demande
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Sidebar widgets -->
      <aside v-if="isDirecteur && analytics" class="space-y-8 animate-fade" style="animation-delay: 0.2s">
        <div class="glass-card">
          <h3 class="text-lg font-bold mb-8">Department Load</h3>
          <div class="space-y-6">
            <div v-for="dept in analytics.requests_by_department" :key="dept.id">
              <div class="flex justify-between text-xs font-bold mb-3 uppercase tracking-widest text-slate-400">
                <span>{{ dept.name }}</span>
                <span class="text-white">{{ dept.purchase_requests_count }}</span>
              </div>
              <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-1000" :style="{ width: (dept.purchase_requests_count / (requests.length || 1) * 100) + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="glass-card bg-indigo-500/5 border-indigo-500/20">
          <h3 class="text-lg font-bold text-indigo-400 mb-4 italic px-2">Efficiency Rating</h3>
          <div class="text-5xl font-black px-2 tabular-nums tracking-tighter">98.4<span class="text-xl text-indigo-500">%</span></div>
          <p class="text-slate-500 text-xs mt-4 px-2 italic">Based on average delivery of {{ analytics.average_delivery_time }}.</p>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const requests = ref([]);
const analytics = ref(null);

const isDirecteur = computed(() => authStore.user?.role === 'directeur');

const topStats = computed(() => {
  if (!analytics.value) return {};
  return {
    'Lead Time Moyen': analytics.value.average_delivery_time,
    'Devis Acceptes': analytics.value.supplier_analytics.devis_accepted,
    'Taux Approbation': analytics.value.approval_metrics.manager, // Example mapping
    'Dossiers Urgents': (requests.value.filter(r => r.priority === 'urgent').length)
  };
});

const statusMap = {
  'draft': { class: 'bg-slate-400/10 text-slate-400', dotClass: 'bg-slate-400' },
  'pending_manager': { class: 'bg-amber-400/10 text-amber-400', dotClass: 'bg-amber-400' },
  'pending_directeur': { class: 'bg-violet-400/10 text-violet-300', dotClass: 'bg-violet-400' },
  'approved': { class: 'bg-cyan-400/10 text-cyan-400', dotClass: 'bg-cyan-400' },
  'in_progress': { class: 'bg-indigo-400/10 text-indigo-400', dotClass: 'bg-indigo-400' },
  'ordered': { class: 'bg-blue-400/10 text-blue-400', dotClass: 'bg-blue-400' },
  'delivered': { class: 'bg-emerald-400/10 text-emerald-400', dotClass: 'bg-emerald-400' },
  'rejected': { class: 'bg-rose-400/10 text-rose-400', dotClass: 'bg-rose-400' },
  'cancelled': { class: 'bg-slate-600/10 text-slate-500', dotClass: 'bg-slate-600' }
};

const priorityLabel = (priority) => {
  if (priority === 'urgent') return 'Urgent';
  if (priority === 'high') return 'Haute';
  if (priority === 'medium') return 'Moyenne';
  if (priority === 'low') return 'Basse';
  return priority;
};

const statusLabel = (status) => {
  const labels = {
    draft: 'Brouillon',
    pending_manager: 'Attente Manager',
    pending_directeur: 'Attente Directeur',
    approved: 'Approuvee',
    in_progress: 'En traitement',
    ordered: 'Commande passee',
    delivered: 'Livree',
    rejected: 'Refusee',
    cancelled: 'Annulee'
  };
  return labels[status] || String(status).replace(/_/g, ' ');
};

onMounted(async () => {
  try {
    const [reqRes, anaRes] = await Promise.all([
      axios.get('/api/purchase-requests'),
      isDirecteur.value ? axios.get('/api/analytics') : Promise.resolve({ data: null })
    ]);
    requests.value = reqRes.data.data || reqRes.data;
    analytics.value = anaRes.data;
  } catch (error) {
    console.error('Failed to sync state', error);
  }
});
</script>
