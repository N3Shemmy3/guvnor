<script setup lang="ts">
import axios from "axios";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";

definePageMeta({ layout: "user" });

const config = useRuntimeConfig();
const isSubmitting = ref(false);
const services = ref([]);
const userDocuments = ref([]);

// Dialog State
const isModalOpen = ref(false);
const modalContent = ref({ title: "", message: "", success: true });

const form = ref({
  service_id: "",
  details: "",
  selected_document_path: "",
});

onMounted(async () => {
  try {
    const [resServices, resDocs] = await Promise.all([
      axios.get(`${config.public.api}/services.php`, {
        params: { action: "list-services" },
        withCredentials: true,
      }),
      axios.get(`${config.public.api}/users.php`, {
        params: { action: "list-documents" },
        withCredentials: true,
      }),
    ]);
    services.value = resServices.data.data;
    userDocuments.value = resDocs.data.data;
  } catch (error) {
    console.error("Initialization error:", error);
  }
});

const submitApplication = async () => {
  if (!form.value.service_id || !form.value.details) return;

  isSubmitting.value = true;
  try {
    const response = await axios.post(
      `${config.public.api}/applications.php`,
      {
        action: "submit-application",
        service_id: form.value.service_id,
        details: form.value.details,
        document_path: form.value.selected_document_path,
      },
      { withCredentials: true },
    );

    if (response.data.success) {
      modalContent.value = {
        title: "Application Submitted",
        message: `Reference: ${response.data.reference_no}`,
        success: true,
      };
    } else {
      throw new Error(response.data.message);
    }
  } catch (error: any) {
    modalContent.value = {
      title: "Error",
      message: error.response?.data?.message || "Failed to submit.",
      success: false,
    };
  } finally {
    isSubmitting.value = false;
    isModalOpen.value = true;
  }
};

const handleModalClose = () => {
  isModalOpen.value = false;
  if (modalContent.value.success) {
    navigateTo("/user/applications");
  }
};
</script>

<template>
  <div class="w-full flex flex-col gap-6 px-6 py-8">
    <section
      class="max-w-3xl mx-auto w-full bg-card p-8 border rounded-xl flex flex-col gap-8"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-black font-bricolage tracking-tight">
          Submit New Application
        </h1>
        <p class="text-sm text-muted-foreground">
          Fill in the details below to request a service from the City Council.
        </p>
      </div>

      <form @submit.prevent="submitApplication" class="flex flex-col gap-6">
        <div class="flex flex-col gap-2">
          <label class="font-bold text-sm flex items-center gap-2">
            <Icon name="ic:outline-settings-suggest" class="text-primary" />
            Target Service
          </label>
          <select
            v-model="form.service_id"
            class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:ring-2 focus:ring-primary outline-none transition-all"
            required
          >
            <option value="" disabled>Select a service category...</option>
            <option v-for="s in services" :key="s.id" :value="s.id">
              {{ s.name }}
            </option>
          </select>
        </div>

        <div class="flex flex-col gap-2">
          <label class="font-bold text-sm flex items-center gap-2">
            <Icon name="ic:outline-attach-file" class="text-primary" />
            Supporting Document (Optional)
          </label>

          <div v-if="userDocuments.length > 0">
            <select
              v-model="form.selected_document_path"
              class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:ring-2 focus:ring-primary outline-none"
            >
              <option value="">No document attached</option>
              <option
                v-for="doc in userDocuments"
                :key="doc.id"
                :value="doc.file_path"
              >
                {{ doc.document_type }}: {{ doc.original_name }}
              </option>
            </select>
            <p class="text-[11px] text-muted-foreground mt-1 px-1">
              Linking a document helps speed up the verification process.
            </p>
          </div>

          <div
            v-else
            class="flex flex-col items-center gap-2 p-6 bg-muted/30 rounded-lg border border-dashed text-center"
          >
            <Icon
              name="ic:outline-cloud-off"
              class="text-muted-foreground"
              size="24"
            />
            <p class="text-xs text-muted-foreground">
              You haven't uploaded any documents yet.
            </p>
            <NuxtLink
              to="/user/documents"
              class="text-xs text-primary font-bold hover:underline"
            >
              Upload supporting files first
            </NuxtLink>
          </div>
        </div>

        <div class="flex flex-col gap-2">
          <label class="font-bold text-sm flex items-center gap-2">
            <Icon name="ic:outline-description" class="text-primary" />
            Application Details
          </label>
          <textarea
            v-model="form.details"
            rows="5"
            placeholder="Describe your request in detail..."
            class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:ring-2 focus:ring-primary outline-none resize-none transition-all"
            required
          ></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <Button variant="outline" type="button" @click="$router.back()"
            >Cancel</Button
          >
          <Button type="submit" :disabled="isSubmitting" class="px-8">
            <Icon
              v-if="isSubmitting"
              name="svg-spinners:180-ring"
              class="mr-2"
            />
            {{ isSubmitting ? "Processing..." : "Send Application" }}
          </Button>
        </div>
      </form>
    </section>

    <Dialog :open="isModalOpen" @update:open="handleModalClose">
      <DialogContent class="sm:max-w-md">
        <DialogHeader class="flex flex-col items-center text-center">
          <div
            :class="[
              'w-16 h-16 rounded-full flex items-center justify-center mb-4',
              modalContent.success
                ? 'bg-green-100 text-green-600'
                : 'bg-red-100 text-red-600',
            ]"
          >
            <Icon
              :name="
                modalContent.success
                  ? 'ic:outline-check-circle'
                  : 'ic:outline-error-outline'
              "
              size="32"
            />
          </div>
          <DialogTitle class="text-xl font-bold">{{
            modalContent.title
          }}</DialogTitle>
          <DialogDescription class="pt-2">
            {{ modalContent.message }}
          </DialogDescription>
        </DialogHeader>
        <DialogFooter class="sm:justify-center mt-4">
          <Button
            :variant="modalContent.success ? 'default' : 'destructive'"
            @click="handleModalClose"
            class="w-full sm:w-32"
          >
            {{ modalContent.success ? "Continue" : "Close" }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
