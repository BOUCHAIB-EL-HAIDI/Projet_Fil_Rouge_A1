<template>
  <div>
    <header class="flex justify-between items-end mb-16">
      <div>
        <h1 class="text-5xl font-extrabold glass-gradient-text uppercase tracking-tight">Department Queue</h1>
        <p class="text-slate-500 mt-2 font-medium text-lg tracking-widest">Approvals Pending for Your Department</p>
      </div>
    </header>
    <RequestTable :requests="requests" />
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import RequestTable from '../../components/RequestTable.vue';

const requests = ref([]);
onMounted(async () => {
    const res = await axios.get('/api/purchase-requests');
    requests.value = res.data.data;
});
</script>
