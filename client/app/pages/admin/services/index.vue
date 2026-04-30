<script setup lang="ts">
import axios from "axios";

definePageMeta({ layout: "user" });

const config = useRuntimeConfig();
const services = ref([]);
const isLoading = ref(true);

const fetchServices = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(
      `${config.public.api}/admin/services.php`,
      {
        params: { action: "list-services" },
        withCredentials: true,
      },
    );
    services.value = response.data.data || [];
  } catch (error) {
    console.error("Error fetching services:", error);
  } finally {
    isLoading.value = false;
  }
};

const deleteService = async (id: number) => {
  if (!confirm("Are you sure? This may affect existing applications.")) return;
  try {
    await axios.post(
      `${config.public.api}/admin/services.php`,
      { action: "delete-service", service_id: id },
      { withCredentials: true },
    );
    fetchServices();
  } catch (e) {
    alert("Delete failed");
  }
};

onMounted(fetchServices);

const stats = computed(() => ({
  total: services.value.length,
  permits: services.value.filter((s) => s.type === "permit").length,
  certificates: services.value.filter((s) => s.type === "certificate").length,
  registrations: services.value.filter((s) => s.type === "registration").length,
}));
</script>

<template>
  <div class="w-full flex flex-col gap-8 px-6 py-8">
    <div class="flex justify-between items-end">
      <div class="flex flex-col gap-1">
        <h1
          class="text-3xl font-black font-bricolage tracking-tight italic text-primary"
        >
          System Services
        </h1>
        <p class="text-sm text-muted-foreground">
          Configure public services and document types.
        </p>
      </div>
      <NuxtLink to="/admin/services/new">
        <Button class="font-bold gap-2">
          <Icon name="ic:outline-plus" /> New Service
        </Button>
      </NuxtLink>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div
        v-for="(val, label) in stats"
        :key="label"
        class="bg-card p-6 rounded-2xl border flex flex-col gap-2"
      >
        <span
          class="text-xs font-bold uppercase tracking-widest text-muted-foreground"
          >{{ label }}</span
        >
        <span class="text-4xl font-black font-bricolage">{{ val }}</span>
      </div>
    </div>

    <section class="flex flex-col bg-card rounded-2xl border overflow-hidden">
      <div class="overflow-x-auto">
        <table v-if="services.length > 0" class="w-full text-sm text-left">
          <thead
            class="bg-muted/50 border-b *:px-6 *:py-4 *:font-bold *:text-muted-foreground uppercase text-[11px] tracking-tighter"
          >
            <tr>
              <th>Code</th>
              <th>Service Name</th>
              <th>Type</th>
              <th>Created</th>
              <th class="text-right">Manage</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr
              v-for="service in services"
              :key="service.id"
              class="hover:bg-muted/30 transition-colors *:px-6 *:py-4"
            >
              <td class="font-mono font-bold text-primary">
                {{ service.service_code }}
              </td>
              <td class="font-bold">{{ service.name }}</td>
              <td>
                <span
                  class="px-2 py-0.5 rounded-full text-[10px] font-black border uppercase bg-slate-100 text-slate-700 border-slate-200"
                >
                  {{ service.type }}
                </span>
              </td>
              <td class="text-muted-foreground">
                {{ new Date(service.created_at).toLocaleDateString() }}
              </td>
              <td class="text-right flex justify-end gap-2">
                <Button
                  variant="ghost"
                  size="sm"
                  @click="deleteService(service.id)"
                  class="text-destructive hover:bg-destructive/10 h-8 w-8 p-0"
                >
                  <Icon name="ic:outline-delete" size="18" />
                </Button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else-if="isLoading" class="py-20 flex justify-center">
          <Icon name="svg-spinners:180-ring" size="40" class="text-primary" />
        </div>
      </div>
    </section>
  </div>
</template>
