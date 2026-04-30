<script setup lang="ts">
import axios from "axios";

definePageMeta({ layout: "user" });

const config = useRuntimeConfig();
const router = useRouter();
const route = useRoute();
const isSubmitting = ref(false);
const isLoading = ref(true);

const userId = computed(() => route.query.id);

const form = ref({
  name: "",
  email: "",
  phone: "",
  role: "",
  status: "",
  nrc: "",
  address: "",
  gender: "",
  date_of_birth: "",
});

const fetchUser = async () => {
  if (!userId.value) return router.push("/admin/users");
  isLoading.value = true;
  try {
    const response = await axios.get(`${config.public.api}/admin/users.php`, {
      params: { action: "get-user", user_id: userId.value },
      withCredentials: true,
    });
    const data = response.data.data;
    Object.keys(form.value).forEach((key) => {
      form.value[key] = data[key] || "";
    });
  } catch (e) {
    console.error(e);
  } finally {
    isLoading.value = false;
  }
};

const handleUpdate = async () => {
  isSubmitting.value = true;
  try {
    await axios.post(
      `${config.public.api}/admin/users.php`,
      { action: "update-user", user_id: userId.value, ...form.value },
      { withCredentials: true },
    );
    router.push("/admin/users");
  } catch (error) {
    alert("Update failed");
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(fetchUser);
</script>

<template>
  <div class="w-full max-w-4xl mx-auto flex flex-col gap-6 px-6 py-8">
    <div class="flex flex-col gap-1 border-b border-slate-200 pb-4">
      <h1
        class="text-2xl font-black font-bricolage tracking-tight text-slate-900"
      >
        Resident File
      </h1>
      <p
        class="text-xs text-muted-foreground uppercase tracking-widest font-bold"
      >
        UID: {{ userId }}
      </p>
    </div>

    <div v-if="isLoading" class="flex justify-center py-20">
      <Icon name="svg-spinners:180-ring" size="30" class="text-primary" />
    </div>

    <form v-else @submit.prevent="handleUpdate" class="flex flex-col gap-6">
      <div
        class="bg-white border border-slate-200 rounded-md p-6 flex flex-col gap-4"
      >
        <h2
          class="text-[10px] font-black uppercase tracking-widest text-slate-400"
        >
          Account Identity
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-bold text-slate-700"
              >Full Name</label
            >
            <input
              v-model="form.name"
              type="text"
              class="bg-slate-50 border border-slate-200 rounded-md px-3 py-2 text-sm outline-none focus:border-primary font-bold transition-colors"
            />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-bold text-slate-700"
              >Email Address</label
            >
            <input
              v-model="form.email"
              type="email"
              class="bg-slate-50 border border-slate-200 rounded-md px-3 py-2 text-sm outline-none focus:border-primary font-bold transition-colors"
            />
          </div>
        </div>
      </div>

      <div
        class="bg-white border border-slate-200 rounded-md p-6 flex flex-col gap-4"
      >
        <h2
          class="text-[10px] font-black uppercase tracking-widest text-slate-400"
        >
          Resident Details
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-bold text-slate-700"
              >NRC Number</label
            >
            <input
              v-model="form.nrc"
              type="text"
              class="bg-slate-50 border border-slate-200 rounded-md px-3 py-2 text-sm outline-none focus:border-primary font-mono"
            />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-bold text-slate-700">Phone</label>
            <input
              v-model="form.phone"
              type="text"
              class="bg-slate-50 border border-slate-200 rounded-md px-3 py-2 text-sm outline-none focus:border-primary"
            />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-bold text-slate-700">Gender</label>
            <select
              v-model="form.gender"
              class="bg-slate-50 border border-slate-200 rounded-md px-3 py-2 text-sm outline-none focus:border-primary appearance-none"
            >
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[11px] font-bold text-slate-700"
            >Residential Address</label
          >
          <input
            v-model="form.address"
            type="text"
            class="bg-slate-50 border border-slate-200 rounded-md px-3 py-2 text-sm outline-none focus:border-primary"
          />
        </div>
      </div>

      <div
        class="bg-slate-900 border border-slate-800 rounded-md p-6 flex flex-col gap-4"
      >
        <h2
          class="text-[10px] font-black uppercase tracking-widest text-slate-500"
        >
          System Permissions
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-bold text-slate-300">Role</label>
            <select
              v-model="form.role"
              class="bg-slate-800 border border-slate-700 text-white rounded-md px-3 py-2 text-sm outline-none focus:border-primary font-bold"
            >
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-bold text-slate-300">Status</label>
            <select
              v-model="form.status"
              class="bg-slate-800 border border-slate-700 text-white rounded-md px-3 py-2 text-sm outline-none focus:border-primary font-bold"
            >
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
      </div>

      <Button
        type="submit"
        :disabled="isSubmitting"
        class="h-12 rounded-md font-black uppercase tracking-widest text-xs bg-primary text-white hover:bg-primary/90 transition-all"
      >
        <Icon v-if="isSubmitting" name="svg-spinners:180-ring" class="mr-2" />
        Commit Changes
      </Button>
    </form>
  </div>
</template>
