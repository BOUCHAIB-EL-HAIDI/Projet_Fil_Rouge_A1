<template>
  <div class="min-h-screen flex selection:bg-indigo-500/30 bg-bg-dark text-slate-100 font-inter">
    
    <!-- Mobile Hamburger -->
    <button 
      v-if="authStore.isAuthenticated"
      @click="isMobileMenuOpen = !isMobileMenuOpen" 
      class="lg:hidden fixed top-6 right-6 z-[110] p-3 rounded-2xl bg-white/5 border border-white/10 text-white shadow-2xl backdrop-blur-md"
    >
      <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
      <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>

    <!-- Sidebar Wrapper -->
    <div 
      v-if="authStore.isAuthenticated"
      :class="['fixed lg:sticky top-0 z-[100] h-screen transition-all duration-500 ease-in-out', 
              isMobileMenuOpen ? 'left-0' : '-left-full lg:left-0']"
    >
      <!-- Premium Sidebar -->
      <aside class="w-80 h-full bg-[#0a0f1d] border-r border-white/5 p-8 flex flex-col shadow-2xl">
        <div class="flex items-center justify-between mb-12 px-2">
          <div class="flex items-center gap-3 cursor-pointer group" @click="router.push('/')">
            <div class="w-10 h-10 bg-indigo-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform duration-500">
              <span class="text-white font-black text-xl">P</span>
            </div>
            <h1 class="text-2xl font-black text-white tracking-widest uppercase">Procure<span class="text-indigo-400">Path</span></h1>
          </div>
          <NotificationBell />
        </div>

        <nav class="flex-1 space-y-2">
          <router-link to="/" class="nav-item" active-class="nav-item-active" @click="isMobileMenuOpen = false">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            Dashboard
          </router-link>
          <router-link v-if="authStore.user?.role === 'demandeur'" to="/requests/create" class="nav-item" active-class="nav-item-active" @click="isMobileMenuOpen = false">
            <svg class="w-5 h-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nouvelle demande
          </router-link>
          <router-link v-if="authStore.user?.role === 'directeur'" to="/management" class="nav-item" active-class="nav-item-active" @click="isMobileMenuOpen = false">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Gestion Orga
          </router-link>
          <router-link v-if="authStore.user?.role === 'acheteur' || authStore.user?.role === 'directeur'" to="/suppliers" class="nav-item" active-class="nav-item-active" @click="isMobileMenuOpen = false">
            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m4-4h4"></path></svg>
            Fournisseurs
          </router-link>
        </nav>

        <div class="mt-auto space-y-6">
          <div class="glass-panel !p-6 flex items-center gap-4 bg-white/[0.03]">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white shadow-lg">
              {{ authStore.user?.name?.charAt(0) }}
            </div>
            <div class="flex-1 overflow-hidden">
              <div class="font-bold truncate text-slate-100 text-sm">{{ authStore.user?.name }}</div>
              <div class="text-[9px] text-slate-500 uppercase tracking-widest font-black mt-1">{{ authStore.user?.role }}</div>
            </div>
          </div>
          
          <button @click="handleLogout" :disabled="loggingOut" class="nav-item w-full text-red-400 hover:text-red-300 hover:bg-red-400/5 group disabled:opacity-60 border border-transparent hover:border-red-400/10 transition-all">
            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span>{{ loggingOut ? 'Deconnexion...' : 'Se deconnecter' }}</span>
          </button>
        </div>
      </aside>
    </div>

    <!-- Mobile Overlay -->
    <div 
      v-if="isMobileMenuOpen" 
      @click="isMobileMenuOpen = false" 
      class="fixed inset-0 z-[90] bg-black/60 backdrop-blur-sm lg:hidden animate-fade"
    ></div>

    <!-- Main Content Area -->
    <main class="flex-1 p-6 lg:p-12 max-w-7xl mx-auto w-full min-h-screen">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth';
import NotificationBell from './components/NotificationBell.vue';

const authStore = useAuthStore();
const router = useRouter();
const loggingOut = ref(false);
const isMobileMenuOpen = ref(false);

onMounted(async () => {
  if (authStore.isAuthenticated) {
    await authStore.fetchUser();
  }
});

const handleLogout = async () => {
  loggingOut.value = true;
  await authStore.logout();
  loggingOut.value = false;
};
</script>
