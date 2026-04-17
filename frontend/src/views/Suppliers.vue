<template>
  <div class="animate-fade">
    <header class="flex justify-between items-center mb-16">
      <div>
        <h1 class="text-5xl font-extrabold glass-gradient-text uppercase tracking-tight">Suppliers Directory</h1>
        <p class="text-slate-500 mt-2 font-medium">Manage and review vendor profiles.</p>
      </div>
      <button @click="openAddModal" class="btn btn-primary" v-if="authStore.user?.role === 'acheteur'">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Register Supplier
      </button>
    </header>

    <div v-if="!showAddModal" class="glass-panel overflow-hidden">
      <table class="w-full text-left">
        <thead>
          <tr class="border-b border-white/5">
            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Corporate Entity</th>
            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Contact Details</th>
            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Address Node</th>
            <th class="px-8 py-6 text-right"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          <tr v-for="supplier in suppliers" :key="supplier.id" class="group hover:bg-white/[0.02] transition-colors">
            <td class="px-8 py-6">
              <div class="font-bold text-lg mb-1 group-hover:text-indigo-400 transition-colors">{{ supplier.name }}</div>
              <div class="text-[10px] font-mono text-slate-500 tracking-wider">ID: SUP-{{ String(supplier.id).padStart(4, '0') }}</div>
            </td>
            <td class="px-8 py-6">
              <div class="text-sm text-slate-300 font-medium">{{ supplier.email }}</div>
              <div class="text-xs text-slate-500 mt-1">{{ supplier.phone }}</div>
            </td>
            <td class="px-8 py-6">
              <div class="text-sm font-medium text-slate-400 truncate max-w-[200px]" :title="supplier.address">
                {{ supplier.address || 'N/A' }}
              </div>
            </td>
            <td class="px-8 py-6 text-right flex justify-end gap-2" v-if="authStore.user?.role === 'acheteur'">
              <button @click="editSupplier(supplier)" class="p-2 rounded-xl text-slate-500 hover:text-white hover:bg-amber-500 group-hover:shadow-[0_0_20px_rgba(245,158,11,0.3)] transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </button>
              <button @click="deleteSupplier(supplier.id)" class="p-2 rounded-xl text-slate-500 hover:text-white hover:bg-rose-500 group-hover:shadow-[0_0_20px_rgba(244,63,94,0.3)] transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
            <td v-else class="px-8 py-6"></td>
          </tr>
          <tr v-if="suppliers.length === 0">
            <td colspan="4" class="px-8 py-20 text-center text-slate-500 italic">No vendors located in registry.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Inline Form View -->
    <div v-else class="glass-card w-full max-w-2xl mx-auto shadow-2xl animate-fade">
      <div class="flex justify-between items-start mb-8">
          <div>
            <h2 class="text-2xl font-bold uppercase tracking-tight">{{ isEditing ? 'Edit Vendor' : 'Add Vendor' }}</h2>
            <p class="text-xs text-slate-400 mt-1">Input corporate registry data.</p>
          </div>
          <button @click="showAddModal = false" class="text-slate-500 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        <form @submit.prevent="saveSupplier" class="space-y-6">
          <div>
            <span class="input-label">Corporate Name</span>
            <input v-model="form.name" class="input-field" placeholder="TechCorp Global" required />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <span class="input-label">Email Node</span>
              <input type="email" v-model="form.email" class="input-field" placeholder="contact@techcorp.com" required />
            </div>
            <div>
              <span class="input-label">Telecom Line</span>
              <input v-model="form.phone" class="input-field" placeholder="+1 234 567 8900" required />
            </div>
          </div>
          <div>
            <span class="input-label">Physical Coordinates</span>
            <input v-model="form.address" class="input-field" placeholder="123 Server Lane, Silicon Valley" required />
          </div>
          <div class="pt-6">
            <button type="submit" class="btn btn-primary w-full" :disabled="saving">
              {{ saving ? 'Transmitting...' : 'Register Vendor' }}
            </button>
          </div>
        </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const suppliers = ref([]);
const showAddModal = ref(false);
const saving = ref(false);
const isEditing = ref(false);

const form = ref({
  id: null,
  name: '',
  email: '',
  phone: '',
  address: ''
});

const openAddModal = () => {
  isEditing.value = false;
  form.value = { id: null, name: '', email: '', phone: '', address: '' };
  showAddModal.value = true;
};

const editSupplier = (supplier) => {
  isEditing.value = true;
  form.value = { ...supplier };
  showAddModal.value = true;
};

const fetchSuppliers = async () => {
  try {
    const response = await axios.get('/api/suppliers');
    suppliers.value = response.data;
  } catch (error) {
    console.error('Failed to sync supplier directory', error);
  }
};

const saveSupplier = async () => {
  saving.value = true;
  try {
    if (isEditing.value) {
      await axios.put(`/api/suppliers/${form.value.id}`, form.value);
    } else {
      await axios.post('/api/suppliers', form.value);
    }
    showAddModal.value = false;
    form.value = { id: null, name: '', email: '', phone: '', address: '' };
    await fetchSuppliers();
  } catch (error) {
    alert('Failed to register vendor.');
  } finally {
    saving.value = false;
  }
};

const deleteSupplier = async (id) => {
  if (!confirm('Purge this vendor from the registry?')) return;
  try {
    await axios.delete(`/api/suppliers/${id}`);
    await fetchSuppliers();
  } catch (error) {
    alert('Purge operation failed.');
  }
};

onMounted(() => {
  fetchSuppliers();
});
</script>
