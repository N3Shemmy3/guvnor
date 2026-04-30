<script setup lang="ts">
import axios from "axios";

definePageMeta({ layout: "user" });

const config = useRuntimeConfig();
const isLoading = ref(true);
const applications = ref([]);
const searchQuery = ref("");
const statusFilter = ref("all");

const fetchApplications = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(
      `${config.public.api}/admin/applications.php`,
      {
        params: { action: "list-applications" },
        withCredentials: true,
      },
    );
    applications.value = response.data.data || [];
  } catch (error) {
    console.error("Fetch error:", error);
  } finally {
    isLoading.value = false;
  }
};

const filteredApplications = computed(() => {
  return applications.value.filter((app: any) => {
    const matchesSearch =
      app.service_name
        .toLowerCase()
        .includes(searchQuery.value.toLowerCase()) ||
      app.user_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      app.reference_no.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus =
      statusFilter.value === "all" || app.status === statusFilter.value;
    return matchesSearch && matchesStatus;
  });
});

const getStatusClass = (status: string) => {
  switch (status.toLowerCase()) {
    case "approved":
      return "bg-emerald-50 text-emerald-700 border-emerald-100";
    case "rejected":
      return "bg-rose-50 text-rose-700 border-rose-100";
    case "pending":
      return "bg-amber-50 text-amber-700 border-amber-100";
    default:
      return "bg-slate-50 text-slate-700 border-slate-100";
  }
};

onMounted(fetchApplications);
</script>

<template>
  <div
    class="w-full max-w-7xl mx-auto flex flex-col gap-6 px-4 md:px-8 py-6 md:py-10"
  >
    <div
      class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-6"
    >
      <div class="flex flex-col gap-1">
        <h1
          class="text-3xl font-black font-bricolage tracking-tighter italic text-slate-900 uppercase"
        >
          Application Registry
        </h1>
        <p
          class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]"
        >
          Citizen Request Management System
        </p>
      </div>

      <div class="flex flex-col sm:flex-row gap-2">
        <div class="relative">
          <Icon
            name="ic:outline-search"
            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
            size="18"
          />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search Reference, Name..."
            class="pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-md text-xs font-bold focus:border-primary outline-none w-full sm:w-64 transition-all"
          />
        </div>
        <select
          v-model="statusFilter"
          class="px-4 py-2 bg-white border border-slate-200 rounded-md text-xs font-bold focus:border-primary outline-none transition-all appearance-none"
        >
          <option value="all">All Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-md overflow-hidden">
      <div v-if="isLoading" class="p-20 flex justify-center">
        <Icon name="svg-spinners:180-ring" class="text-primary" size="32" />
      </div>

      <div
        v-else-if="filteredApplications.length === 0"
        class="p-20 text-center"
      >
        <Icon
          name="ic:outline-assignment-late"
          class="text-slate-200 mb-2"
          size="48"
        />
        <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">
          No applications found
        </p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-slate-50 border-b border-slate-200 text-[10px] font-black uppercase text-slate-500 tracking-widest"
            >
              <th class="px-6 py-4">Reference & Service</th>
              <th class="px-6 py-4">Citizen</th>
              <th class="px-6 py-4">Submitted</th>
              <th class="px-6 py-4 text-center">Status</th>
              <th class="px-6 py-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="app in filteredApplications"
              :key="app.id"
              class="hover:bg-slate-50/50 transition-colors group"
            >
              <td class="px-6 py-4">
                <div class="flex flex-col">
                  <span
                    class="text-sm font-black text-slate-900 leading-tight"
                    >{{ app.service_name }}</span
                  >
                  <span
                    class="text-[10px] font-mono font-bold text-primary mt-1"
                    >#{{ app.reference_no }}</span
                  >
                </div>
              </td>
              <td class="px-6 py-4">
                <span
                  class="text-xs font-bold text-slate-600 uppercase tracking-tight"
                  >{{ app.user_name }}</span
                >
              </td>
              <td class="px-6 py-4">
                <span class="text-xs font-medium text-slate-400">
                  {{ new Date(app.created_at).toLocaleDateString("en-ZM") }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-center">
                  <span
                    class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase border tracking-widest"
                    :class="getStatusClass(app.status)"
                  >
                    {{ app.status }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 text-right">
                <NuxtLink :to="`/admin/applications/view?id=${app.id}`">
                  <button
                    class="px-4 py-2 bg-slate-900 text-white rounded-md text-[10px] font-black uppercase tracking-widest hover:bg-primary transition-all"
                  >
                    Review
                  </button>
                </NuxtLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
