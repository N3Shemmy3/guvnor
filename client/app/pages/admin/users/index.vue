<script setup lang="ts">
import axios from "axios";

definePageMeta({ layout: "user" });

const config = useRuntimeConfig();
const users = ref([]);
const isLoading = ref(true);

const fetchUsers = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(`${config.public.api}/admin/users.php`, {
      params: { action: "list-users" },
      withCredentials: true,
    });
    users.value = response.data.data || [];
  } catch (error) {
    console.error("Error fetching users:", error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchUsers);

const stats = computed(() => ({
  total: users.value.length,
  admins: users.value.filter((u) => u.role === "admin").length,
  active: users.value.filter((u) => u.status === "active").length,
  inactive: users.value.filter((u) => u.status === "inactive").length,
}));
</script>

<template>
  <div class="w-full flex flex-col gap-8 px-6 py-8">
    <div class="flex flex-col gap-1">
      <h1
        class="text-3xl font-black font-bricolage tracking-tight italic text-primary"
      >
        User Directory
      </h1>
      <p class="text-sm text-muted-foreground">
        Manage resident accounts and administrative permissions.
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
          >{{ label }}</span
        >
        <span class="text-4xl font-black font-bricolage">{{ val }}</span>
      </div>
    </div>

    <section class="flex flex-col bg-card rounded-2xl border overflow-hidden">
      <div class="p-6 border-b flex items-center justify-between bg-muted/10">
        <h3 class="font-bold text-lg flex items-center gap-2">
          <Icon name="ic:outline-people" class="text-primary" />
          Resident Registry
        </h3>
        <Button variant="outline" size="sm" @click="fetchUsers">
          <Icon
            name="ic:outline-refresh"
            :class="{ 'animate-spin': isLoading }"
          />
        </Button>
      </div>

      <div class="overflow-x-auto">
        <table v-if="users.length > 0" class="w-full text-sm text-left">
          <thead
            class="bg-muted/50 border-b *:px-6 *:py-4 *:font-bold *:text-muted-foreground uppercase text-[11px] tracking-tighter"
          >
            <tr>
              <th>Resident</th>
              <th>NRC / Identity</th>
              <th>Role</th>
              <th>Status</th>
              <th>Joined</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr
              v-for="user in users"
              :key="user.id"
              class="hover:bg-muted/30 transition-colors *:px-6 *:py-4"
            >
              <td>
                <div class="flex flex-col">
                  <span class="font-bold text-foreground">{{ user.name }}</span>
                  <span
                    class="text-[10px] text-muted-foreground font-mono uppercase"
                    >{{ user.email }}</span
                  >
                </div>
              </td>
              <td class="font-mono text-xs uppercase">
                {{ user.nrc || "No NRC" }}
              </td>
              <td>
                <span
                  class="text-[10px] font-black uppercase px-2 py-0.5 rounded border bg-slate-50 border-slate-200"
                >
                  {{ user.role }}
                </span>
              </td>
              <td>
                <span
                  :class="[
                    'px-2.5 py-0.5 rounded-full text-[10px] font-black border uppercase',
                    user.status === 'active'
                      ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
                      : 'bg-rose-100 text-rose-700 border-rose-200',
                  ]"
                >
                  {{ user.status }}
                </span>
              </td>
              <td class="text-muted-foreground">
                {{ new Date(user.created_at).toLocaleDateString() }}
              </td>
              <td class="text-right">
                <NuxtLink :to="`/admin/users/update?id=${user.id}`">
                  <Button variant="secondary" size="sm" class="font-bold h-8">
                    Edit
                  </Button>
                </NuxtLink>
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
