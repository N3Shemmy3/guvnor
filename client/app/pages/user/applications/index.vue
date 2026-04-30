<script setup lang="ts">
import axios from "axios";

definePageMeta({
  layout: "user",
});

const config = useRuntimeConfig();
const applications = ref([]);
const searchQuery = ref("");

const stats = ref({
  total: 0,
  accepted: 0,
  rejected: 0,
  pending: 0,
});

// Helper to determine badge colors
const getStatusClass = (status: string) => {
  switch (status.toLowerCase()) {
    case "approved":
      return "bg-green-100 text-green-700 border-green-200";
    case "rejected":
      return "bg-red-100 text-red-700 border-red-200";
    default:
      return "bg-yellow-100 text-yellow-700 border-yellow-200";
  }
};

onMounted(async () => {
  try {
    const response = await axios.get(`${config.public.api}/applications.php`, {
      params: { action: "my-applications" },
      withCredentials: true,
    });

    if (response.data.success) {
      stats.value = {
        total: response.data.total || 0,
        accepted: response.data.accepted || 0,
        rejected: response.data.rejected || 0,
        pending: response.data.pending || 0,
      };
      applications.value = response.data.data || [];
    }
  } catch (error: any) {
    console.error(
      "Fetching applications error:",
      error.response?.data || error.message,
    );
  }
});

// Filter applications based on search
const filteredApplications = computed(() => {
  return applications.value.filter(
    (app) =>
      app.reference_no
        ?.toLowerCase()
        .includes(searchQuery.value.toLowerCase()) ||
      app.service?.toLowerCase().includes(searchQuery.value.toLowerCase()),
  );
});

const statItems = computed(() => [
  { label: "Total", value: stats.value.total, color: "text-primary" },
  { label: "Accepted", value: stats.value.accepted, color: "text-green-600" },
  { label: "Rejected", value: stats.value.rejected, color: "text-red-600" },
  { label: "Pending", value: stats.value.pending, color: "text-yellow-600" },
]);
</script>

<template>
  <div class="w-full flex flex-col gap-8 px-6 py-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div
        v-for="stat in statItems"
        :key="stat.label"
        class="p-6 flex flex-col gap-1 bg-card rounded-xl border transition-hover hover:"
      >
        <p
          class="text-xs font-bold uppercase tracking-wider text-muted-foreground"
        >
          {{ stat.label }}
        </p>
        <h4
          :class="[
            'text-3xl md:text-4xl font-black font-bricolage',
            stat.color,
          ]"
        >
          {{ stat.value }}
        </h4>
        <p class="text-[10px] text-muted-foreground mt-2 italic">
          Since account creation
        </p>
      </div>
    </div>

    <section
      class="flex flex-col gap-4 bg-card rounded-xl border overflow-hidden"
    >
      <div
        class="flex flex-col md:flex-row gap-4 p-4 items-center justify-between bg-muted/30 border-b"
      >
        <div class="w-full max-w-sm relative">
          <Input
            v-model="searchQuery"
            placeholder="Search by Ref or Service..."
            class="pl-10"
          />
          <Icon
            name="ic:outline-search"
            class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
            size="18"
          />
        </div>
        <NuxtLink to="/user/applications/new">
          <Button class="rounded-full px-6">
            <Icon name="ic:outline-add" class="mr-2" />
            New Application
          </Button>
        </NuxtLink>
      </div>

      <div class="overflow-x-auto">
        <table v-if="filteredApplications.length > 0" class="w-full text-sm">
          <thead>
            <tr
              class="bg-muted/50 border-b *:px-6 *:py-4 *:text-left *:font-bold *:text-muted-foreground"
            >
              <th>Reference No</th>
              <th>Service Type</th>
              <th>Submission Date</th>
              <th>Status</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr
              v-for="app in filteredApplications"
              :key="app.id"
              class="hover:bg-muted/30 transition-colors *:px-6 *:py-4"
            >
              <td class="font-mono font-bold text-primary">
                {{ app.reference_no }}
              </td>
              <td class="font-medium">{{ app.service_name }}</td>
              <td>{{ app.submitted_at }}</td>
              <td>
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-[10px] font-bold border uppercase',
                    getStatusClass(app.status),
                  ]"
                >
                  {{ app.status }}
                </span>
              </td>
              <td class="text-right">
                <Button variant="ghost" size="sm" class="rounded-full">
                  <Icon name="ic:outline-remove-red-eye" size="18" />
                </Button>
              </td>
            </tr>
          </tbody>
        </table>

        <div
          v-else
          class="w-full py-20 flex flex-col items-center justify-center gap-4 text-center"
        >
          <div
            class="h-20 w-20 bg-muted rounded-full flex items-center justify-center"
          >
            <Icon
              name="ic:outline-inbox"
              size="40"
              class="text-muted-foreground"
            />
          </div>
          <div>
            <p class="text-xl font-bold">No applications found</p>
            <p class="text-sm text-muted-foreground">
              Try adjusting your search or create a new request.
            </p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
