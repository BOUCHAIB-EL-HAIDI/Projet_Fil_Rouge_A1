<template>
  <div class="relative">
    <button @click="toggle" class="relative p-2.5 rounded-xl bg-white/5 text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 transition-all group border border-white/5 hover:border-indigo-500/20">
      <svg class="w-6 h-6 group-hover:shake" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
      </svg>
      <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-rose-500 text-[10px] font-black text-white flex items-center justify-center rounded-full border-2 border-[#0a0f1d] shadow-[0_0_10px_rgba(244,63,94,0.4)]">
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <div v-if="show" class="absolute right-0 mt-4 w-80 glass-card !p-0 z-[100] shadow-2xl animate-fade-in-down border border-white/10 overflow-hidden backdrop-blur-xl bg-[#0f172a]/95">
      <div class="p-4 border-b border-white/5 flex justify-between items-center bg-white/[0.03]">
        <div class="flex items-center gap-2">
          <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Notifications</h3>
          <span v-if="unreadCount > 0" class="px-1.5 py-0.5 rounded-md bg-indigo-500/10 text-indigo-400 text-[8px] font-black uppercase">{{ unreadCount }} nouvelles</span>
        </div>
        <button v-if="notifications.length > 0" @click="markAllAsRead" class="text-[9px] text-indigo-400 hover:text-indigo-300 font-black uppercase tracking-tighter transition-colors">Tout marquer lu</button>
      </div>

      <div class="max-h-[380px] overflow-y-auto custom-scrollbar">
        <div v-for="n in notifications" :key="n.id" 
             @click="handleNotificationClick(n)"
             :class="['p-4 border-b border-white/5 cursor-pointer hover:bg-white/[0.04] transition-all flex gap-3 group/item', !n.read_at ? 'bg-indigo-500/[0.03]' : 'opacity-50 hover:opacity-100']">
          <div :class="['w-1.5 h-1.5 rounded-full mt-1.5 shrink-0 transition-transform group-hover/item:scale-125', !n.read_at ? 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]' : 'bg-slate-600']"></div>
          <div class="flex-1 min-w-0">
            <div class="text-[13px] text-slate-200 font-bold mb-0.5 leading-tight group-hover/item:text-indigo-300 transition-colors truncate">{{ n.data.title }}</div>
            <div class="text-[11px] text-slate-500 leading-snug line-clamp-2">{{ n.data.message }}</div>
            <div class="flex items-center gap-2 mt-2">
              <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <div class="text-[9px] text-slate-600 font-mono tracking-tighter">{{ n.data.time || 'A l\'instant' }}</div>
            </div>
          </div>
        </div>

        <div v-if="notifications.length === 0" class="p-12 text-center">
          <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/5">
             <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
          </div>
          <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Le vide intersidéral</p>
          <p class="text-[9px] text-slate-600 mt-1 italic font-mono">Aucune notification signalée.</p>
        </div>
      </div>
      
      <div v-if="notifications.length > 0" class="p-3 bg-white/[0.01] border-t border-white/5 text-center">
        <button class="text-[9px] text-slate-600 hover:text-slate-400 font-black uppercase tracking-[0.2em] transition-colors">Afficher l'historique complet</button>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';
const authStore = useAuthStore();
const router = useRouter();
const show = ref(false);
const notifications = ref([]);
let pollInterval = null;

const unreadCount = computed(() => notifications.value.filter(n => !n.read_at).length);

const toggle = () => {
  show.value = !show.value;
  if (show.value) fetchNotifications();
};

const fetchNotifications = async () => {
  try {
    const res = await axios.get('/api/notifications');
    notifications.value = res.data;
  } catch (err) {
  }
};

const markAllAsRead = async () => {
  try {
    await axios.post('/api/notifications/read-all');
    notifications.value.forEach(n => n.read_at = new Date());
  } catch (err) {}
};

const handleNotificationClick = async (n) => {
  if (!n.read_at) {
    try {
      await axios.post(`/api/notifications/${n.id}/read`);
      n.read_at = new Date();
    } catch (err) {}
  }
  
  show.value = false;
  if (n.data.purchase_request_id) {
    router.push(`/requests/${n.data.purchase_request_id}`);
  }
};

import echo from '../echo';

onMounted(() => {
  fetchNotifications();
  
  if (authStore.user) {
    echo.private(`App.Models.User.${authStore.user.id}`)
      .notification((notification) => {
        notifications.value.unshift({
          id: Math.random().toString(36).substr(2, 9),
          data: notification,
          read_at: null,
          created_at: new Date().toISOString()
        });
      });
  }
  
  pollInterval = setInterval(fetchNotifications, 60000);
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
  if (authStore.user) {
    echo.leave(`App.Models.User.${authStore.user.id}`);
  }
});

window.addEventListener('click', (e) => {
  if (show.value && !e.target.closest('.relative')) {
    show.value = false;
  }
});
</script>

<style scoped>
@keyframes shake {
  0% { transform: rotate(0deg); }
  25% { transform: rotate(10deg); }
  50% { transform: rotate(-10deg); }
  75% { transform: rotate(5deg); }
  100% { transform: rotate(0deg); }
}
.group-hover\:shake {
  animation: shake 0.5s ease-in-out;
}
.animate-fade-in-down {
  animation: fadeInDown 0.3s ease-out;
}
@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
