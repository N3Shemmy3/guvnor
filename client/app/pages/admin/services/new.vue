<script setup lang="ts">
import axios from "axios";
definePageMeta({ layout: "user" });

const config = useRuntimeConfig();
const router = useRouter();
const isSubmitting = ref(false);

const form = ref({
  name: "",
  service_code: "",
  description: "",
  type: "permit",
});

// Auto-generate trade code: MD rounded, flat style
watch(
  () => form.value.name,
  (val) => {
    if (val.length > 2 && form.value.service_code.length < 4) {
      const code = val.replace(/\s/g, "").substring(0, 3).toUpperCase();
      form.value.service_code = `${code}-${Math.floor(100 + Math.random() * 900)}`;
    }
  },
);

const handleSubmit = async () => {
  isSubmitting.value = true;
  try {
    await axios.post(
      `${config.public.api}/admin/services.php`,
      {
        action: "create-service", // Sent inside JSON body
        ...form.value,
      },
      { withCredentials: true },
    );

    router.push("/admin/services");
  } catch (error) {
    alert("System Error: Check Action Mapping");
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div class="w-full max-w-2xl mx-auto flex flex-col gap-6 px-6 py-10">
    <div class="border-b border-slate-200 pb-4">
      <h1
        class="text-2xl font-black font-bricolage tracking-tight text-slate-900"
      >
        New Service Configuration
      </h1>
      <p
        class="text-xs text-muted-foreground uppercase tracking-widest font-bold"
      >
        System Level Registry
      </p>
    </div>

    <form
      @submit.prevent="handleSubmit"
      class="bg-white border border-slate-200 rounded-md p-6 flex flex-col gap-5"
    >
      <div class="flex flex-col gap-1.5">
        <label class="text-[11px] font-bold text-slate-700 uppercase"
          >Service Name</label
        >
        <input
          v-model="form.name"
          type="text"
          placeholder="e.g. Health Permit"
          class="bg-slate-50 border border-slate-200 rounded-md px-3 py-2 text-sm outline-none focus:border-primary font-bold"
          required
        />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="text-[11px] font-bold text-slate-700 uppercase"
            >Service Code</label
          >
          <input
            v-model="form.service_code"
            type="text"
            class="bg-slate-100 border border-slate-200 rounded-md px-3 py-2 text-sm outline-none font-mono font-bold text-primary"
            required
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[11px] font-bold text-slate-700 uppercase"
            >Category</label
          >
          <select
            v-model="form.type"
            class="bg-slate-50 border border-slate-200 rounded-md px-3 py-2 text-sm outline-none"
          >
            <option value="permit">Permit</option>
            <option value="certificate">Certificate</option>
            <option value="registration">Registration</option>
          </select>
        </div>
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-[11px] font-bold text-slate-700 uppercase"
          >Internal Description</label
        >
        <textarea
          v-model="form.description"
          rows="3"
          class="bg-slate-50 border border-slate-200 rounded-md px-3 py-2 text-sm outline-none resize-none"
        ></textarea>
      </div>

      <Button
        type="submit"
        :disabled="isSubmitting"
        class="h-12 bg-slate-900 text-white rounded-md font-black uppercase tracking-widest text-[10px] hover:bg-slate-800 transition-all mt-2"
      >
        <Icon v-if="isSubmitting" name="svg-spinners:180-ring" class="mr-2" />
        Deploy Service
      </Button>
    </form>
  </div>
</template>
