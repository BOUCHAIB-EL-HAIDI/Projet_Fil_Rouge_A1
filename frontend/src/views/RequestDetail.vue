<template>
  <div v-if="request" class="animate-fade pb-12">
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-16">
      <div>
        <div class="flex items-center gap-4 mb-2">
          <h1 class="text-4xl font-extrabold uppercase tracking-tight glass-gradient-text">{{ request.title }}</h1>
          <div :class="[
            'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border',
            statusMap[request.status]?.class || 'bg-slate-400/10 text-slate-400 border-slate-400/20'
          ]">
            {{ statusLabel(request.status) }}
          </div>
        </div>
        <p class="text-slate-500 font-mono text-sm tracking-wide">
          ORIGIN: <span class="text-slate-300">{{ request.user.name }}</span> // TIMESTAMP: <span class="text-slate-300">{{ new Date(request.created_at).toLocaleString() }}</span>
        </p>
      </div>
      
      <div class="flex gap-4">
        <button v-if="isDemandeurAndDraft" @click="updateStatus('pending_manager')" class="btn bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500 hover:text-white">
          Soumettre la demande
        </button>

        <template v-if="isAcheteur">
          <button v-if="request.status === 'approved'" @click="updateStatus('in_progress')" class="btn bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 hover:bg-indigo-500 hover:text-white">
            Commencer le traitement
          </button>
          <button v-if="request.status === 'ordered'" @click="updateStatus('consultation')" class="btn bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500 hover:text-white shadow-lg shadow-amber-500/20">
            Lancer la consultation
          </button>
          <button v-if="request.status === 'consultation'" @click="updateStatus('delivered')" class="btn bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500 hover:text-white shadow-lg shadow-emerald-500/20">
            Confirmer la livraison
          </button>
        </template>
      </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 space-y-8">
        <section class="glass-card">
          <h3 class="text-xs font-black uppercase tracking-[0.2em] text-indigo-400 mb-6 flex items-center gap-4">
            <span class="w-8 h-[1px] bg-indigo-500/50"></span>
            Description
            <span class="flex-1 h-[1px] bg-white/5"></span>
          </h3>
          <p class="text-slate-300 leading-relaxed">{{ request.description || 'Pas de description.' }}</p>
          
          <div class="mt-8 pt-8 border-t border-white/5">
            <h4 class="text-[10px] font-black text-slate-500 uppercase mb-4 tracking-[0.2em]">Articles</h4>
            <div class="space-y-3">
              <div v-for="item in request.items" :key="item.id" class="flex justify-between items-center bg-white/[0.02] p-4 rounded-2xl border border-white/5 hover:bg-white/[0.05] transition-colors">
                <div>
                  <div class="font-bold text-slate-200 text-lg">{{ item.productName }}</div>
                  <div class="text-[10px] text-slate-500 font-mono mt-1">REF: {{ item.reference || 'N/A' }}</div>
                </div>
                <div class="text-2xl font-black text-indigo-400">×{{ item.quantity }}</div>
              </div>
            </div>
          </div>
        </section>


        <section v-if="chatActive" class="glass-card flex flex-col h-[550px]">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-purple-400 flex items-center gap-4 flex-1">
              <span class="w-8 h-[1px] bg-purple-500/50"></span>
              Discussion Securisee
              <span class="flex-1 h-[1px] bg-white/5"></span>
            </h3>
          </div>
          
          <div class="flex-1 overflow-y-auto space-y-4 pr-4 mb-6 custom-scrollbar" ref="messageBox">
            <div v-for="msg in messages" :key="msg.id" :class="['flex', msg.user_id === authStore.user?.id ? 'justify-end' : 'justify-start']">
              <div :class="['max-w-[80%] p-5 rounded-2xl border transition-all', msg.user_id === authStore.user?.id ? 'bg-indigo-500/10 border-indigo-500/20 text-slate-200 rounded-tr-none' : 'bg-white/[0.02] border-white/5 text-slate-300 rounded-tl-none']">
                <div class="flex items-baseline gap-2 mb-2">
                  <div class="text-[10px] font-black uppercase tracking-widest" :class="msg.user_id === authStore.user?.id ? 'text-indigo-400' : 'text-slate-500'">
                    {{ msg.user.name }} • {{ msg.user.role }}
                  </div>
                  <div class="text-[8px] text-slate-600 font-mono">{{ new Date(msg.created_at).toLocaleTimeString() }}</div>
                </div>
                <p class="text-sm leading-relaxed">{{ msg.content }}</p>
              </div>
            </div>
          </div>
          <div class="flex gap-4 p-2 bg-white/5 rounded-2xl">
            <input v-model="newMessage" @keyup.enter="sendMessage" class="input-field !bg-transparent !border-0 !mb-0 font-mono text-sm focus:ring-0" placeholder="Tapez votre message..." />
            <button @click="sendMessage" class="p-3 rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition-all shadow-lg shadow-indigo-500/20">
              <svg class="w-5 h-5 -rotate-45 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
          </div>
        </section>
        
        <section v-else class="glass-card flex items-center justify-center p-20 text-center border-dashed border-2 border-white/5">
          <div>
            <div class="w-16 h-16 rounded-3xl bg-white/5 flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h3 class="text-slate-400 font-bold tracking-widest uppercase text-sm mb-2">Canal Verrouille</h3>
            <p class="text-xs text-slate-500 max-w-xs mx-auto">La discussion s'active une fois que la demande est approuvee par le directeur.</p>
          </div>
        </section>

      </div>

      <div class="space-y-8">
        <section v-if="canApprove" class="glass-card !border-indigo-500/30 bg-indigo-500/5 shadow-[0_0_50px_-12px_rgba(99,102,241,0.2)]">
           <h3 class="text-xs font-black uppercase tracking-[0.2em] text-indigo-400 mb-6 flex items-center gap-4">
            <span class="w-8 h-[1px] bg-indigo-500/50"></span>
            Décision Requise
            <span class="flex-1 h-[1px] bg-white/5"></span>
          </h3>
          <div class="space-y-4">
            <div>
              <span class="text-[9px] font-black uppercase tracking-widest text-slate-500 block mb-2 px-1">Commentaire (Facultatif)</span>
              <textarea v-model="approvalComment" class="input-field !mb-0 h-24 text-xs py-3 placeholder:text-slate-600" placeholder="Motif de l'approbation ou du refus..."></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <button @click="submitApproval('approved')" class="btn bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500 hover:text-white shadow-lg shadow-emerald-500/10 transition-all font-bold uppercase text-[10px] tracking-widest">Approuver</button>
              <button @click="submitApproval('rejected')" class="btn bg-rose-500/10 text-rose-400 border border-rose-500/20 hover:bg-rose-500 hover:text-white shadow-lg shadow-rose-500/10 transition-all font-bold uppercase text-[10px] tracking-widest">Refuser</button>
            </div>
          </div>
        </section>

        <section class="glass-card">
           <h3 class="text-xs font-black uppercase tracking-[0.2em] text-pink-400 mb-6 flex items-center gap-4">
            <span class="w-8 h-[1px] bg-pink-500/50"></span>
            Pieces Jointes
            <span class="flex-1 h-[1px] bg-white/5"></span>
          </h3>
          
          <div class="space-y-3 mb-6">
            <div v-for="file in request.attachments" :key="file.id" class="flex justify-between items-center p-3.5 rounded-xl bg-white/[0.02] border border-white/5 hover:bg-white/5 transition-all group">
              <div class="flex items-center gap-3 overflow-hidden cursor-pointer flex-1" @click="downloadFile(file)">
                <svg class="w-5 h-5 text-pink-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <div class="truncate text-xs text-slate-300 font-mono">{{ file.original_name || 'Document attaché' }}</div>
              </div>
              <div class="flex items-center gap-2">
                <button @click="downloadFile(file)" class="p-1.5 text-slate-500 hover:text-pink-400 transition-colors" title="Télécharger">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </button>
                <button v-if="authStore.user?.id === file.user_id || authStore.user?.role === 'directeur'" @click="deleteAttachment(file.id)" class="p-1.5 text-slate-500 hover:text-rose-500 transition-colors" title="Supprimer">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </div>
            <div v-if="request.attachments.length === 0" class="text-xs text-slate-500 italic p-6 text-center border border-dashed border-white/10 rounded-xl">Aucun fichier.</div>
          </div>

          <div v-if="chatActive" class="relative">
            <input type="file" @change="uploadFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" :disabled="uploading" />
            <button class="btn btn-outline w-full !py-3 text-xs flex justify-center items-center gap-3" :class="{'opacity-50 cursor-not-allowed': uploading}">
               <svg v-if="uploading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
               <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
               {{ uploading ? 'Envoi...' : 'Ajouter un document' }}
            </button>
          </div>
        </section>

        <section v-if="chatActive" class="glass-card">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-amber-400 flex items-center gap-4 flex-1">
              <span class="w-8 h-[1px] bg-amber-500/50"></span>
              Offres Fournisseurs (Devis)
              <span class="flex-1 h-[1px] bg-white/5"></span>
            </h3>
            <button v-if="isAcheteur && ['approved', 'in_progress'].includes(request.status)" @click="openQuoteModal" class="px-3 py-1.5 rounded-lg bg-amber-500/10 text-amber-500 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 hover:text-white transition-all">Nouveau Devis</button>
          </div>
          
          <div class="space-y-4">
            <div v-for="quote in request.devis" :key="quote.id" 
                 :class="['p-5 rounded-2xl border transition-all', quote.status === 'selected' ? 'bg-indigo-500/10 border-indigo-500/30' : 'bg-white/[0.02] border-white/5']">
              <div class="flex justify-between items-start mb-4">
                <span class="font-bold text-slate-100 text-base">{{ quote.supplier.name }}</span>
                <span :class="['px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest', 
                  quote.status === 'selected' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20' : 
                  quote.status === 'rejected' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/20' :
                  'bg-amber-500/20 text-amber-400 border border-amber-500/20']">
                  {{ quote.status === 'selected' ? 'Selectionne' : quote.status === 'rejected' ? 'Refuse' : 'Recu' }}
                </span>
              </div>
              <div class="grid grid-cols-2 gap-4 text-xs">
                <div class="text-slate-400">Montant Tot: <span class="text-white font-mono font-bold">{{ quote.price }} MAD</span></div>
                <div class="text-slate-400">Livraison: <span class="text-white font-bold">{{ quote.deliveryTime || 'N/A' }}</span></div>
                <div class="text-slate-400 col-span-2">Paiement: <span class="text-white font-bold">{{ quote.paymentMethod || 'N/A' }}</span></div>
              </div>
              
              <div v-if="authStore.user?.role === 'directeur' && quote.status === 'received' && request.status !== 'delivered'" class="mt-5 space-y-2">
                <button @click="selectDevis(quote.id)" class="w-full py-2 rounded-xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-500 hover:text-white transition-all">
                  Confirmer et Commander
                </button>
              </div>

              <div v-if="isAcheteur && quote.status === 'received'" class="mt-4 flex gap-2">
                <button @click="editQuote(quote)" class="flex-1 py-1.5 rounded-lg bg-white/5 text-slate-400 border border-white/10 text-[9px] font-bold uppercase hover:bg-white/10 transition-all">Editer</button>
                <button @click="deleteQuote(quote.id)" class="flex-1 py-1.5 rounded-lg bg-rose-500/10 text-rose-500 border border-rose-500/20 text-[9px] font-bold uppercase hover:bg-rose-500 hover:text-white transition-all">Supprimer</button>
              </div>
            </div>
            <div v-if="!request.devis || request.devis.length === 0" class="text-xs text-slate-500 italic text-center p-8 border border-dashed border-white/10 rounded-2xl">Aucun devis enregistre.</div>
          </div>
        </section>

        <section v-if="request.approvals && request.approvals.length > 0" class="glass-card">
          <h3 class="text-xs font-black uppercase tracking-[0.2em] text-cyan-400 mb-6 flex items-center gap-4">
            <span class="w-8 h-[1px] bg-cyan-500/50"></span>
            Décisions & Commentaires
            <span class="flex-1 h-[1px] bg-white/5"></span>
          </h3>
          <div class="space-y-4">
            <div v-for="approval in request.approvals" :key="approval.id" class="p-4 rounded-2xl bg-white/[0.02] border border-white/5">
              <div class="flex justify-between items-start mb-3">
                <div class="flex items-center gap-3">
                  <div :class="['w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs', 
                    approval.decision === 'approved' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400']">
                    {{ approval.decision === 'approved' ? 'A' : 'R' }}
                  </div>
                  <div>
                    <div class="text-xs font-bold text-slate-200">{{ approval.user.name }}</div>
                    <div class="text-[9px] text-slate-500 uppercase font-black">Etape {{ approval.step }} • {{ approval.user.role }}</div>
                  </div>
                </div>
                <div class="text-[9px] font-mono text-slate-600">{{ new Date(approval.decidedAt).toLocaleString() }}</div>
              </div>
              <div v-if="approval.comment" class="bg-black/20 p-3 rounded-xl border border-white/5 italic text-xs text-slate-400 leading-relaxed shadow-inner">
                "{{ approval.comment }}"
              </div>
            </div>
          </div>
        </section>

        <section class="glass-card">
          <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-6 flex items-center gap-4">
            <span class="w-8 h-[1px] bg-white/10"></span>
            Historique des Etats
            <span class="flex-1 h-[1px] bg-white/5"></span>
          </h3>
          <div class="space-y-6">
            <div v-for="history in request.status_histories" :key="history.id" class="flex gap-4">
              <div class="w-px bg-white/10 relative">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-slate-700 border border-slate-600"></div>
              </div>
              <div class="pb-2">
                <div class="font-bold text-xs tracking-wide text-slate-200 uppercase">{{ statusLabel(history.newStatus) }}</div>
                <div class="text-[9px] font-mono text-slate-600 tracking-wider mt-1">{{ new Date(history.changedAt).toLocaleString() }}</div>
                <div class="text-[9px] uppercase font-bold text-slate-500 mt-2">PAR: <span class="text-slate-400">{{ history.user.name }}</span></div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>

    <div v-if="showQuoteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-fade">
      <div class="glass-card w-full max-w-md shadow-2xl">
        <div class="flex justify-between items-start mb-8">
          <h2 class="text-2xl font-bold uppercase tracking-tight glass-gradient-text">{{ isEditingQuote ? 'Editer le Devis' : 'Enregistrer un Devis' }}</h2>
          <button @click="showQuoteModal = false" class="text-slate-500 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        <form @submit.prevent="submitQuote" class="space-y-6">
          <div>
            <span class="input-label">Fournisseur</span>
            <select v-model="quoteForm.supplier_id" class="input-field appearance-none" required>
              <option value="" disabled>Choisir un fournisseur...</option>
              <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">{{ sup.name }}</option>
            </select>
          </div>
          <div class="space-y-3 max-h-48 overflow-y-auto custom-scrollbar pr-2">
            <div v-for="(item, index) in quoteForm.items_prices" :key="index" class="bg-white/5 p-3 rounded-xl border border-white/10">
              <div class="flex justify-between text-xs mb-2 text-slate-300">
                  <span class="font-bold">{{ item.productName }}</span>
                  <span>Qte: {{ item.quantity }}</span>
              </div>
              <div>
                <span class="input-label !text-[9px]">Prix Unitaire (MAD)</span>
                <input v-model.number="item.price" type="number" step="0.01" class="input-field !py-2" required />
              </div>
            </div>
          </div>
          
          <div class="flex justify-between items-center px-2 py-2 bg-indigo-500/10 rounded-lg border border-indigo-500/20">
            <span class="text-xs font-bold text-slate-400">Total Provisoire:</span>
            <span class="text-lg font-black text-indigo-400">{{ quoteForm.items_prices.reduce((sum, item) => sum + ((item.price || 0) * item.quantity), 0) }} MAD</span>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <span class="input-label">Delai de Livraison</span>
              <input v-model="quoteForm.deliveryTime" class="input-field" placeholder="ex: 3 jours" required />
            </div>
            <div>
              <span class="input-label">Methode de Paiement</span>
              <input v-model="quoteForm.paymentMethod" class="input-field" placeholder="ex: 50% en avance" required />
            </div>
          </div>
          <div class="pt-4">
            <button type="submit" class="btn btn-primary w-full shadow-lg shadow-indigo-500/30">
              {{ isEditingQuote ? 'Mettre a jour' : 'Valider le Devis' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';
import echo from '../echo';

const route = useRoute();
const authStore = useAuthStore();
const request = ref(null);
const messages = ref([]);
const newMessage = ref('');
const messageBox = ref(null);
const uploading = ref(false);
const approvalComment = ref('');

const isEditingQuote = ref(false);
const showQuoteModal = ref(false);
const suppliers = ref([]);

const quoteForm = ref({
  id: null,
  supplier_id: '',
  deliveryTime: '',
  paymentMethod: '',
  items_prices: [],
});

const openQuoteModal = () => {
  isEditingQuote.value = false;
  quoteForm.value = { 
    id: null, 
    supplier_id: '', 
    deliveryTime: '',
    paymentMethod: '',
    items_prices: request.value.items.map(item => ({
      item_id: item.id,
      productName: item.productName,
      quantity: item.quantity,
      price: null
    }))
  };
  showQuoteModal.value = true;
};

const submitApproval = async (decision) => {
  try {
    await axios.post(`/api/purchase-requests/${route.params.id}/approvals`, {
      decision: decision,
      comment: approvalComment.value
    });
    approvalComment.value = '';
    fetchRequest();
  } catch (error) {
    alert(error.response?.data?.message || 'Echec de l\'operation.');
  }
};

const statusLabel = (status) => {
  const labels = {
    draft: 'Brouillon',
    pending_manager: 'En attente Manager',
    pending_directeur: 'En attente Directeur',
    approved: 'Approuvee',
    in_progress: 'En traitement',
    ordered: 'Commande passee',
    delivered: 'Livree',
    consultation: 'En Consultation',
    rejected: 'Refusee',
    cancelled: 'Annulee'
  };
  return labels[status] || String(status).replace(/_/g, ' ');
};

const statusMap = {
  'draft': { class: 'bg-slate-400/10 text-slate-400 border-slate-400/20' },
  'pending_manager': { class: 'bg-amber-400/10 text-amber-400 border-amber-400/20' },
  'pending_directeur': { class: 'bg-violet-400/10 text-violet-300 border-violet-400/20' },
  'approved': { class: 'bg-cyan-400/10 text-cyan-400 border-cyan-400/20' },
  'in_progress': { class: 'bg-indigo-400/10 text-indigo-400 border-indigo-400/20' },
  'ordered': { class: 'bg-blue-400/10 text-blue-400 border-blue-400/20' },
  'consultation': { class: 'bg-amber-500/10 text-amber-400 border-amber-500/20' },
  'delivered': { class: 'bg-emerald-400/10 text-emerald-400 border-emerald-400/20' },
  'rejected': { class: 'bg-rose-400/10 text-rose-400 border-rose-400/20' },
  'cancelled': { class: 'bg-slate-600/10 text-slate-500 border-slate-600/20' }
};

const chatActive = computed(() => {
  return request.value && ['approved', 'in_progress', 'ordered', 'consultation', 'delivered'].includes(request.value.status);
});

const fetchRequest = async () => {
  const res = await axios.get(`/api/purchase-requests/${route.params.id}`);
  request.value = res.data;
  messages.value = res.data.messages || [];
  await nextTick();
  scrollToBottom();
};

const scrollToBottom = () => {
  if (messageBox.value) {
    messageBox.value.scrollTop = messageBox.value.scrollHeight;
  }
};

const uploadFile = async (e) => {
  const file = e.target.files[0];
  if (!file) return;
  
  const formData = new FormData();
  formData.append('file', file);
  formData.append('originalName', file.name);

  uploading.value = true;
  try {
    await axios.post(`/api/purchase-requests/${route.params.id}/attachments`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    await fetchRequest();
  } catch (err) {
    alert('Echec de l\'envoi du fichier.');
  } finally {
    uploading.value = false;
  }
};

const downloadFile = async (file) => {
  try {
    const response = await axios.get(`/api/attachments/${file.id}/download`, {
      responseType: 'blob'
    });
    
    if (response.data.type === 'application/json') {
      const text = await response.data.text();
      const error = JSON.parse(text);
      throw new Error(error.message || 'Server returned an error');
    }

    const url = window.URL.createObjectURL(response.data);
    const link = document.createElement('a');
    link.href = url;
    
    let fileName = file.original_name || `document-${file.id}`;
    const contentDisposition = response.headers['content-disposition'];
    if (contentDisposition) {
      const fileNameMatch = contentDisposition.match(/filename="?(.+)"?/i);
      if (fileNameMatch) fileName = fileNameMatch[1];
    }
    
    link.setAttribute('download', fileName);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    console.error('Download error:', err);
    alert('Echec du téléchargement: ' + (err.message || 'Veuillez vérifier votre connexion.'));
  }
};

const deleteAttachment = async (id) => {
  if (!confirm('Supprimer cette pièce jointe ?')) return;
  try {
    await axios.delete(`/api/attachments/${id}`);
    await fetchRequest();
  } catch (err) {
    alert('Echec de suppression du fichier.');
  }
};

const editQuote = (quote) => {
  isEditingQuote.value = true;
  quoteForm.value = {
    id: quote.id,
    supplier_id: quote.supplier_id,
    deliveryTime: quote.deliveryTime || '',
    paymentMethod: quote.paymentMethod || '',
    items_prices: quote.items_prices || request.value.items.map(item => ({
      item_id: item.id,
      productName: item.productName,
      quantity: item.quantity,
      price: null
    }))
  };
  showQuoteModal.value = true;
};

const deleteQuote = async (id) => {
  if (!confirm('Supprimer ce devis ?')) return;
  try {
    await axios.delete(`/api/purchase-requests/${route.params.id}/devis/${id}`);
    await fetchRequest();
  } catch (err) {
    alert('Echec de suppression.');
  }
};

const submitQuote = async () => {
  try {
    if (isEditingQuote.value) {
      await axios.put(`/api/purchase-requests/${route.params.id}/devis/${quoteForm.value.id}`, quoteForm.value);
    } else {
      await axios.post(`/api/purchase-requests/${route.params.id}/devis`, quoteForm.value);
    }
    
    showQuoteModal.value = false;
    isEditingQuote.value = false;
    quoteForm.value = {
      id: null,
      supplier_id: '',
      deliveryTime: '',
      paymentMethod: '',
      items_prices: [],
    };
    await fetchRequest();
  } catch (error) {
    const errorMsg = error.response?.data?.message || error.response?.statusText || error.message;
    alert('Echec de l\'enregistrement du devis: ' + errorMsg);
  }
};

const selectDevis = async (quoteId) => {
  try {
    await axios.post(`/api/purchase-requests/${route.params.id}/devis/${quoteId}/select`);
    await fetchRequest();
  } catch (error) {
    const errorMsg = error.response?.data?.message || error.message;
    alert('Echec de la selection: ' + errorMsg);
  }
};

const fetchSuppliers = async () => {
  if (authStore.user?.role === 'acheteur' || authStore.user?.role === 'directeur') {
    try {
      const response = await axios.get('/api/suppliers');
      suppliers.value = response.data;
    } catch(err) {}
  }
};

onMounted(async () => {
  await fetchRequest();
  await fetchSuppliers();
  
  echo.channel(`purchase-request.${route.params.id}`)
    .listen('.message.sent', (e) => {
      messages.value.push(e.message);
      nextTick(scrollToBottom);
    })
    .listen('.status.updated', (e) => {
      fetchRequest();
    });
});

onUnmounted(() => {
  echo.leave(`purchase-request.${route.params.id}`);
});

const sendMessage = async () => {
  if (!newMessage.value.trim() || !chatActive.value) return;
  const messageContent = newMessage.value;
  newMessage.value = '';
  
  try {
    const res = await axios.post(`/api/purchase-requests/${route.params.id}/messages`, {
      content: messageContent
    });
    
    messages.value.push(res.data);
    nextTick(scrollToBottom);
  } catch (error) {
    console.error('Failed to send message', error);
    alert('Echec de l\'envoi du message.');
  }
};

const isAcheteur = computed(() => authStore.user?.role === 'acheteur');
const isDemandeurAndDraft = computed(() => request.value?.status === 'draft' && request.value?.user_id === authStore.user?.id);

const canApprove = computed(() => {
  if (!request.value) return false;
  const status = request.value.status;
  const role = authStore.user?.role;

  if (status === 'pending_manager' && role === 'manager') return true;
  if (status === 'pending_directeur' && role === 'directeur') return true;

  return false;
});

const updateStatus = async (status) => {
  try {
    await axios.put(`/api/purchase-requests/${route.params.id}`, { status });
    fetchRequest();
  } catch (error) {
    alert('Echec de la mise a jour.');
  }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
}
</style>
