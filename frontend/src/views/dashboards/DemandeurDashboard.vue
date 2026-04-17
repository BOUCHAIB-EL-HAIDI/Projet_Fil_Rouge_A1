<template>
  <div>
    <header class="flex justify-between items-end mb-16">
      <div>
        <h1 class="text-5xl font-extrabold glass-gradient-text">Demandeur Dashboard</h1>
        <p class="text-slate-500 mt-2 font-medium text-lg uppercase tracking-widest">My Purchase Requests</p>
      </div>
      <router-link to="/requests/create" class="btn btn-primary">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        New Request
      </router-link>
    </header>
    <RequestTable :requests="requests" @delete="deleteRequest" />
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import RequestTable from '../../components/RequestTable.vue';

const requests = ref([]);

const fetchRequests = async () => {
    const res = await axios.get('/api/purchase-requests');
    requests.value = res.data.data;
};

const deleteRequest = async (id) => {
    if(!confirm('Delete this draft permanently?')) return;
    try {
        await axios.delete(`/api/purchase-requests/${id}`);
        await fetchRequests();
    } catch(err) {
        alert('Failed to delete draft.');
    }
};

onMounted(async () => {
    await fetchRequests();
});
</script>
