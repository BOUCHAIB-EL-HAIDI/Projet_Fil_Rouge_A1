<template>
  <div class="animate-fade max-w-6xl mx-auto space-y-12 pb-16">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
      <div>
        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-indigo-400 mb-2">Administration</p>
        <h1 class="text-4xl md:text-5xl font-extrabold glass-gradient-text">Organization Control</h1>
        <p class="text-slate-500 mt-2 font-medium max-w-xl">
          Create departments before users can register. Assign roles after onboarding — new accounts always start as <span class="text-slate-300">demandeur</span>.
        </p>
      </div>
        <div class="glass-panel px-6 py-4 flex items-center gap-4 shrink-0">
        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
          <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        </div>
        <div>
          <div class="text-xs font-bold text-white">Directeur Scope</div>
          <div class="text-[10px] text-slate-500 uppercase tracking-widest mt-0.5">Control Panel Restricted</div>
        </div>
      </div>
    </header>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-10">
      <!-- Departments Management -->
      <section class="glass-card relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <h2 class="text-xl font-bold mb-2 flex items-center gap-3">
          <span class="w-1.5 h-6 rounded-full bg-indigo-500"></span>
          Departements
        </h2>
        <p class="text-xs text-slate-500 mb-8 max-w-md">Gerez les departements de l'organisation. Chaque departement peut avoir un seul manager.</p>

        <form @submit.prevent="addDepartment" class="flex flex-col gap-3 mb-10">
          <div class="flex flex-col sm:flex-row gap-3">
            <input v-model="newDeptName" type="text" class="input-field flex-1 !mb-0" placeholder="Nom du departement (ex: Logistique, IT...)" required />
            <select v-model="newDeptManagerId" class="input-field sm:w-64 !mb-0">
              <option :value="null">Choisir un manager (Optionnel)</option>
              <option v-for="m in availableManagers" :key="m.id" :value="m.id">{{ m.name }}</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary w-full" :disabled="deptSaving">
            <svg v-if="!deptSaving" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            {{ deptSaving ? 'Ajout...' : 'Ajouter le departement' }}
          </button>
        </form>

        <ul class="space-y-3">
          <li
            v-for="d in departments"
            :key="d.id"
            class="group flex items-center gap-4 p-5 rounded-2xl bg-white/[0.03] border border-white/5 hover:border-indigo-500/30 transition-all hover:bg-white/[0.05]"
          >
            <template v-if="editingDeptId === d.id">
              <div class="flex-1 flex flex-col gap-2">
                <input v-model="editingDeptName" class="input-field !py-2 !text-sm !mb-0" placeholder="Nom" />
                <select v-model="editingDeptManagerId" class="input-field !py-2 !text-xs !mb-0">
                  <option :value="null">Aucun manager</option>
                  <option v-for="m in availableManagers" :key="m.id" :value="m.id">{{ m.name }}</option>
                </select>
              </div>
              <div class="flex flex-col gap-2">
                <button type="button" class="btn !py-2 !px-3 text-xs bg-emerald-500/10 text-emerald-400 border border-emerald-500/20" @click="saveDept(d)">Sauvegarder</button>
                <button type="button" class="btn !py-2 !px-4 text-xs btn-outline" @click="cancelEditDept">Annuler</button>
              </div>
            </template>
            <template v-else>
              <div class="flex-1 min-w-0">
                <div class="font-bold text-slate-100 text-lg">{{ d.name }}</div>
                <div class="flex items-center gap-2 mt-1">
                  <span class="text-[10px] text-slate-600 font-mono">ID: {{ d.id }}</span>
                  <span v-if="d.manager" class="px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 text-[10px] font-bold">Manager: {{ d.manager.name }}</span>
                  <span v-else class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-bold">Sans manager</span>
                </div>
              </div>
              <div class="flex gap-2">
                <button type="button" class="p-2.5 rounded-xl bg-white/5 text-slate-400 hover:text-indigo-300 hover:bg-indigo-500/10 transition-all" @click="startEditDept(d)" title="Modifier">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
                <button type="button" class="p-2.5 rounded-xl bg-white/5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all" @click="removeDept(d)" title="Supprimer">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </template>
          </li>
        </ul>
      </section>

      <!-- Users & Roles Management -->
      <section class="glass-card relative overflow-hidden">
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-pink-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <h2 class="text-xl font-bold mb-2 flex items-center gap-3">
          <span class="w-1.5 h-6 rounded-full bg-pink-500"></span>
          Utilisateurs & Roles
        </h2>
        <p class="text-xs text-slate-500 mb-8">Attribuez les roles et departements aux membres de votre equipe. Les roles definissent les permissions.</p>

        <div class="glass-panel overflow-x-auto">
          <table class="w-full text-left min-w-[580px]">
            <thead>
              <tr class="border-b border-white/10">
                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Utilisateur</th>
                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Role</th>
                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Departement</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              <tr v-for="u in users" :key="u.id" class="hover:bg-white/[0.02] transition-colors">
                <td class="px-6 py-5">
                  <div class="font-bold text-slate-100 flex items-center gap-2">
                    {{ u.name }}
                    <span v-if="u.role === 'directeur'" class="p-1 bg-indigo-500/20 text-indigo-400 rounded-lg text-[8px] uppercase font-black">Directeur</span>
                  </div>
                  <div class="text-[10px] text-slate-500 font-mono">{{ u.email }}</div>
                </td>
                <td class="px-4 py-5">
                  <select
                    :value="u.role"
                    class="input-field !py-2 !text-xs !mb-0 min-w-[120px]"
                    :disabled="roleSavingId === u.id || u.role === 'directeur'"
                    @change="onRoleChange(u, $event)"
                  >
                    <option value="demandeur">Demandeur</option>
                    <option value="manager">Manager</option>
                    <option value="directeur" disabled>Directeur</option>
                    <option value="acheteur">Acheteur</option>
                  </select>
                </td>
                <td class="px-4 py-5">
                  <select
                    :value="u.department_id"
                    class="input-field !py-2 !text-xs !mb-0 min-w-[140px]"
                    :disabled="roleSavingId === u.id || u.role === 'directeur'"
                    @change="onDeptChange(u, $event)"
                  >
                    <option :value="null">Aucun</option>
                    <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const departments = ref([]);
const users = ref([]);
const newDeptName = ref('');
const newDeptManagerId = ref(null);
const deptSaving = ref(false);
const editingDeptId = ref(null);
const editingDeptName = ref('');
const editingDeptManagerId = ref(null);
const roleSavingId = ref(null);

const availableManagers = computed(() => {
  // Only users with role 'manager'
  const allManagers = users.value.filter(u => u.role === 'manager');
  
  // List of IDs for managers already assigned to a department
  // (Filter out the current being edited department so we don't hide its own manager)
  const assignedManagerIds = departments.value
    .filter(d => d.id !== editingDeptId.value)
    .filter(d => d.manager_id)
    .map(d => d.manager_id);

  return allManagers.filter(m => !assignedManagerIds.includes(m.id));
});

const loadDepartments = async () => {
  const { data } = await axios.get('/api/departments');
  departments.value = data;
};

const loadUsers = async () => {
  const { data } = await axios.get('/api/admin/users');
  users.value = data;
};

const addDepartment = async () => {
  if (!newDeptName.value.trim()) return;
  deptSaving.value = true;
  try {
    await axios.post('/api/departments', { 
      name: newDeptName.value.trim(),
      manager_id: newDeptManagerId.value
    });
    newDeptName.value = '';
    newDeptManagerId.value = null;
    await loadDepartments();
  } catch (e) {
    alert(e.response?.data?.message || 'Could not create department');
  } finally {
    deptSaving.value = false;
  }
};

const startEditDept = (d) => {
  editingDeptId.value = d.id;
  editingDeptName.value = d.name;
  editingDeptManagerId.value = d.manager_id;
};

const cancelEditDept = () => {
  editingDeptId.value = null;
  editingDeptName.value = '';
  editingDeptManagerId.value = null;
};

const saveDept = async (d) => {
  try {
    await axios.patch(`/api/departments/${d.id}`, { 
      name: editingDeptName.value.trim(),
      manager_id: editingDeptManagerId.value
    });
    cancelEditDept();
    await loadDepartments();
  } catch (e) {
    alert(e.response?.data?.message || 'Update failed');
  }
};

const removeDept = async (d) => {
  if (!confirm(`Delete department “${d.name}”?`)) return;
  try {
    await axios.delete(`/api/departments/${d.id}`);
    await loadDepartments();
  } catch (e) {
    alert(e.response?.data?.message || 'Cannot delete department');
  }
};

const onRoleChange = async (u, event) => {
  const role = event.target.value;
  roleSavingId.value = u.id;
  try {
    await axios.patch(`/api/admin/users/${u.id}/role`, { role, department_id: u.department_id });
    u.role = role;
  } catch (e) {
    alert(e.response?.data?.message || 'Role update failed');
    event.target.value = u.role;
  } finally {
    roleSavingId.value = null;
    loadUsers();
  }
};

const onDeptChange = async (u, event) => {
  const deptId = event.target.value;
  roleSavingId.value = u.id;
  try {
    await axios.patch(`/api/admin/users/${u.id}/role`, { role: u.role, department_id: deptId });
    u.department_id = deptId;
  } catch (e) {
    alert(e.response?.data?.message || 'Department update failed');
    event.target.value = u.department_id;
  } finally {
    roleSavingId.value = null;
    loadUsers();
  }
};

onMounted(async () => {
  try {
    await Promise.all([loadDepartments(), loadUsers()]);
  } catch (e) {
    console.error('Failed to load organization data', e);
  }
});
</script>
