<script setup lang="ts">
import axios from "axios";

definePageMeta({ layout: "user" });

const route = useRoute();
const router = useRouter();
const config = useRuntimeConfig();

const appId = computed(() => route.query.id);

const isLoading = ref(true);
const application = ref<any>(null);
const history = ref<any[]>([]);
const isSubmitting = ref(false);

const initPage = async () => {
  if (!appId.value) {
    router.push("/admin/applications");
    return;
  }

  isLoading.value = true;
  try {
    const response = await axios.get(
      `${config.public.api}/admin/applications.php`,
      {
        params: {
          action: "get-application",
          application_id: appId.value,
        },
        withCredentials: true,
      },
    );

    application.value = response.data.data.application;
    history.value = response.data.data.history;
  } catch (error: any) {
    if (error.response?.status === 401 || error.response?.status === 403) {
      router.push("/auth/login");
    }
    console.error("Access Denied or Load Error");
  } finally {
    isLoading.value = false;
  }
};

const updateStatus = async (newStatus: string) => {
  const comment = prompt(`Enter reason for ${newStatus}:`);
  if (comment === null) return;

  isSubmitting.value = true;
  try {
    await axios.post(
      `${config.public.api}/admin/applications.php`,
      {
        action: "update-status",
        application_id: appId.value,
        status: newStatus,
        admin_comment: comment,
      },
      { withCredentials: true },
    );

    await initPage();
  } catch (e) {
    alert("Update failed");
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(initPage);
</script>

<template>
  <div
    class="w-full max-w-5xl mx-auto flex flex-col gap-6 px-4 md:px-6 py-6 md:py-10"
  >
    <div
      class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-6"
    >
      <div class="flex flex-col gap-1">
        <h1
          class="text-2xl font-black font-bricolage tracking-tight text-slate-900 italic uppercase"
        >
          Review Application
        </h1>
        <p
          class="text-[10px] text-muted-foreground uppercase font-black tracking-[0.2em]"
        >
          Ref ID: #{{ appId }}
        </p>
      </div>

      <div v-if="application && !isLoading" class="flex items-center gap-2">
        <button
          :disabled="isSubmitting"
          @click="updateStatus('rejected')"
          class="flex-1 md:flex-none px-4 h-10 bg-rose-50 text-rose-600 border border-rose-100 rounded-md text-[11px] font-black uppercase tracking-widest hover:bg-rose-100 transition-colors disabled:opacity-50"
        >
          Reject
        </button>
        <button
          :disabled="isSubmitting"
          @click="updateStatus('approved')"
          class="flex-1 md:flex-none px-4 h-10 bg-slate-900 text-white rounded-md text-[11px] font-black uppercase tracking-widest hover:bg-slate-800 transition-colors disabled:opacity-50"
        >
          {{ isSubmitting ? "Processing..." : "Approve" }}
        </button>
      </div>
    </div>

    <div v-if="isLoading" class="flex justify-center py-20">
      <Icon name="svg-spinners:180-ring" size="32" class="text-primary" />
    </div>

    <div v-else-if="application" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 flex flex-col gap-6">
        <div class="bg-white border border-slate-200 rounded-md p-6">
          <h2
            class="text-xs font-black uppercase tracking-widest text-primary mb-6 border-b pb-2"
          >
            Submission Content
          </h2>

          <div class="space-y-6">
            <div>
              <label
                class="text-[10px] font-black uppercase text-slate-400 block mb-1"
                >Service Requested</label
              >
              <p class="text-lg font-bold text-slate-900">
                {{ application.service_name }}
              </p>
            </div>

            <div>
              <label
                class="text-[10px] font-black uppercase text-slate-400 block mb-1"
                >Applicant Remarks/Details</label
              >
              <div
                class="p-4 bg-slate-50 border border-slate-100 rounded-md text-sm leading-relaxed text-slate-700 font-medium"
              >
                {{ application.details || "No details provided by applicant." }}
              </div>
            </div>
          </div>
        </div>

        <div
          class="bg-white border border-slate-200 rounded-md overflow-hidden"
        >
          <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3
              class="text-[10px] font-black uppercase tracking-widest text-slate-500"
            >
              Audit Trail / Lifecycle
            </h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left"></table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
