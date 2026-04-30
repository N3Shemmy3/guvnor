<script setup lang="ts">
import axios from "axios";

definePageMeta({ layout: "user" }); // Using the same layout for design parity

const config = useRuntimeConfig();
const stats = ref({
  total: 0,
  pending: 0,
  approved: 0,
  rejected: 0,
});
const recentApplications = ref([]);
const isLoading = ref(true);

const fetchDashboardData = async () => {
  isLoading.value = true;
  try {
    const [resStats, resApps] = await Promise.all([
      axios.get(`${config.public.api}/admin/applications.php`, {
        params: { action: "list-applications" }, // Updated to match your file structure
        withCredentials: true,
      }),
      axios.get(`${config.public.api}/admin/applications.php`, {
        params: { action: "list-applications" },
        withCredentials: true,
      }),
    ]);

    // Calculating stats from the list since admin/applications.php returns all
    const apps = resApps.data.data || [];
    recentApplications.value = apps;

    stats.value = {
      total: apps.length,
      pending: apps.filter((a) => a.status === "pending").length,
      approved: apps.filter((a) => a.status === "approved").length,
      rejected: apps.filter((a) => a.status === "rejected").length,
    };
  } catch (error) {
    console.error("Admin Dashboard Error:", error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchDashboardData);

const getStatusClass = (status: string) => {
  switch (status?.toLowerCase()) {
    case "pending":
      return "bg-amber-100 text-amber-700 border-amber-200";
    case "approved":
      return "bg-emerald-100 text-emerald-700 border-emerald-200";
    case "rejected":
      return "bg-rose-100 text-rose-700 border-rose-200";
    default:
      return "bg-slate-100 text-slate-700";
  }
};
</script>

<template>
  <div class="w-full flex flex-col gap-8 px-6 py-8">
    <div class="flex flex-col gap-1">
      <h1
        class="text-3xl font-black font-bricolage tracking-tight italic text-primary"
      >
        Admin Console
      </h1>
      <p class="text-sm text-muted-foreground">
        Monitoring system activity and service requests.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div
        v-for="(val, label) in stats"
        :key="label"
        class="bg-card p-6 rounded-2xl border flex flex-col gap-2"
      >
        <span
          class="text-xs font-bold uppercase tracking-widest text-muted-foreground"
        >
          {{ label }}
        </span>
        <span class="text-4xl font-black font-bricolage">{{ val }}</span>
      </div>
    </div>

    <section class="flex flex-col bg-card rounded-2xl border overflow-hidden">
      <div class="p-6 border-b flex items-center justify-between bg-muted/10">
        <h3 class="font-bold text-lg flex items-center gap-2">
          <Icon name="ic:outline-assignment" class="text-primary" />
          Recent Service Requests
        </h3>
        <Button variant="outline" size="sm" @click="fetchDashboardData">
          <Icon
            name="ic:outline-refresh"
            :class="{ 'animate-spin': isLoading }"
          />
        </Button>
      </div>

      <div class="overflow-x-auto">
        <table
          v-if="recentApplications.length > 0"
          class="w-full text-sm text-left"
        >
          <thead
            class="bg-muted/50 border-b *:px-6 *:py-4 *:font-bold *:text-muted-foreground uppercase text-[11px] tracking-tighter"
          >
            <tr>
              <th>Reference</th>
              <th>Applicant</th>
              <th>Service</th>
              <th>Status</th>
              <th>Date</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr
              v-for="app in recentApplications"
              :key="app.id"
              class="hover:bg-muted/30 transition-colors *:px-6 *:py-4"
            >
              <td class="font-mono font-bold text-primary">
                #{{ app.reference_no }}
              </td>
              <td>
                <div class="flex flex-col">
                  <span class="font-bold text-foreground">{{
                    app.user_name
                  }}</span>
                  <span class="text-[10px] text-muted-foreground">{{
                    app.user_email
                  }}</span>
                </div>
              </td>
              <td>{{ app.service_name }}</td>
              <td>
                <span
                  :class="[
                    'px-2.5 py-0.5 rounded-full text-[10px] font-black border uppercase',
                    getStatusClass(app.status),
                  ]"
                >
                  {{ app.status }}
                </span>
              </td>
              <td class="text-muted-foreground">
                {{ new Date(app.submitted_at).toLocaleDateString() }}
              </td>
              <td class="text-right">
                <NuxtLink :to="`/admin/applications/detail?id=${app.id}`">
                  <Button variant="secondary" size="sm" class="font-bold h-8">
                    Review
                  </Button>
                </NuxtLink>
              </td>
            </tr>
          </tbody>
        </table>

        <div
          v-else-if="isLoading"
          class="py-20 flex flex-col items-center justify-center gap-3"
        >
          <Icon name="svg-spinners:180-ring" size="40" class="text-primary" />
          <p class="text-muted-foreground animate-pulse">
            Gathering intelligence...
          </p>
        </div>

        <div
          v-else
          class="py-20 flex flex-col items-center justify-center text-center gap-4"
        >
          <div class="p-4 bg-muted rounded-full">
            <Icon
              name="ic:outline-cloud-done"
              size="48"
              class="text-muted-foreground/50"
            />
          </div>
          <p class="text-lg font-bold text-muted-foreground">
            All queues are clear.
          </p>
        </div>
      </div>
    </section>
  </div>
</template>
