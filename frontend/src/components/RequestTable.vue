<template>
  <div class="glass-panel overflow-hidden">
    <table class="w-full text-left">
    <thead>
        <tr class="border-b border-white/5">
        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Operation</th>
        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Phase</th>
        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Impact</th>
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
            {{ req.status.replace(/_/g, ' ') }}
            </div>
        </td>
        <td class="px-8 py-6">
            <div class="text-sm font-medium" :class="req.priority === 'high' ? 'text-red-400' : 'text-slate-300'">
            {{ req.priority }}
            </div>
        </td>
        <td class="px-8 py-6 text-right flex justify-end gap-2">
            <button v-if="req.status === 'brouillon' && req.user_id === authStore.user?.id" @click="$emit('delete', req.id)" class="p-2 rounded-xl text-slate-500 hover:text-white hover:bg-rose-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
            <button @click="$router.push(`/requests/${req.id}`)" class="p-2 rounded-xl bg-white/5 hover:bg-indigo-500 group-hover:shadow-[0_0_20px_rgba(99,102,241,0.3)] transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </td>
        </tr>
        <tr v-if="requests.length === 0">
        <td colspan="4" class="px-8 py-20 text-center text-slate-500 italic">No operations found in current stream.</td>
        </tr>
    </tbody>
    </table>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
const authStore = useAuthStore();
const props = defineProps({
  requests: Array
});

const statusMap = {
  'brouillon': { class: 'bg-slate-400/10 text-slate-400', dotClass: 'bg-slate-400' },
  'en_attente': { class: 'bg-amber-400/10 text-amber-400', dotClass: 'bg-amber-400' },
  'en_attente_directeur': { class: 'bg-violet-400/10 text-violet-300', dotClass: 'bg-violet-400' },
  'approuvee': { class: 'bg-cyan-400/10 text-cyan-400', dotClass: 'bg-cyan-400' },
  'en_cours_achat': { class: 'bg-indigo-400/10 text-indigo-400', dotClass: 'bg-indigo-400' },
  'livree': { class: 'bg-emerald-400/10 text-emerald-400', dotClass: 'bg-emerald-400' },
  'refusee': { class: 'bg-rose-400/10 text-rose-400', dotClass: 'bg-rose-400' }
};
</script>
