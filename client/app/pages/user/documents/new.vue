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

// Modal State
const isModalOpen = ref(false);
const modalContent = ref({ title: "", message: "", success: true });

const form = ref({
  document_type: "",
  file: undefined as File | undefined,
});

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    form.value.file = target.files?.[0];
  }
};

const uploadDocument = async () => {
  if (!form.value.document_type || !form.value.file) {
    modalContent.value = {
      title: "Missing Information",
      message: "Please select a document type and attach a file before saving.",
      success: false,
    };
    isModalOpen.value = true;
    return;
  }

  isSubmitting.value = true;

  const formData = new FormData();
  formData.append("document_type", form.value.document_type);
  formData.append("document", form.value.file);
  formData.append("action", "upload-document");

  try {
    const response = await axios.post(
      `${config.public.api}/users.php`,
      formData,
      { withCredentials: true },
    );

    if (response.data.success) {
      modalContent.value = {
        title: "Upload Successful",
        message: "Your document has been securely saved to the Guv'nor vault.",
        success: true,
      };
    } else {
      throw new Error(response.data.message || "Upload failed");
    }
  } catch (error: any) {
    modalContent.value = {
      title: "Upload Failed",
      message:
        error.response?.data?.message || error.message || "An error occurred.",
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
    navigateTo("/user/documents");
  }
};
</script>

<template>
  <div class="w-full flex flex-col gap-6 px-6 py-8">
    <section
      class="max-w-2xl mx-auto w-full flex flex-col gap-8 bg-card p-8 border rounded-xl"
    >
      <div class="flex flex-col gap-1">
        <h1
          class="text-2xl font-black font-bricolage tracking-tight italic text-primary"
        >
          Upload Document
        </h1>
        <p class="text-sm text-muted-foreground">
          Store your IDs and certificates for faster service applications.
        </p>
      </div>

      <form @submit.prevent="uploadDocument" class="flex flex-col gap-6">
        <div class="flex flex-col gap-2">
          <label class="text-sm font-bold flex items-center gap-2">
            <Icon name="ic:outline-category" class="text-primary" />
            Document Type
          </label>
          <select
            v-model="form.document_type"
            class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:ring-2 focus:ring-primary outline-none transition-all"
            required
          >
            <option value="" disabled>Select document category...</option>
            <option value="ID/Passport">National ID / Passport</option>
            <option value="Proof of Address">
              Proof of Address (Utility Bill)
            </option>
            <option value="Academic Certificate">Academic Certificate</option>
            <option value="Business Permit">Business Permit</option>
            <option value="Other">Other Supporting Document</option>
          </select>
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-sm font-bold flex items-center gap-2">
            <Icon name="ic:outline-file-present" class="text-primary" />
            File Attachment
          </label>
          <div class="flex items-center justify-center w-full">
            <label
              class="group flex flex-col items-center justify-center w-full h-44 border-2 border-dashed rounded-xl cursor-pointer transition-all"
              :class="
                form.file
                  ? 'border-primary bg-primary/5'
                  : 'border-muted-foreground/20 hover:bg-secondary/50 hover:border-primary/50'
              "
            >
              <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <div
                  :class="[
                    'p-3 rounded-full mb-3 transition-colors',
                    form.file
                      ? 'bg-primary text-white'
                      : 'bg-secondary text-secondary-foreground',
                  ]"
                >
                  <Icon
                    :name="
                      form.file ? 'ic:outline-check' : 'ic:outline-cloud-upload'
                    "
                    size="28"
                  />
                </div>
                <p class="text-sm">
                  <span class="font-bold">{{
                    form.file ? "File selected" : "Click to upload"
                  }}</span>
                </p>
                <p
                  class="text-xs text-muted-foreground mt-1 uppercase tracking-widest font-semibold"
                >
                  {{
                    form.file ? form.file.name : "PDF, PNG, OR JPG (MAX 5MB)"
                  }}
                </p>
              </div>
              <input
                type="file"
                class="hidden"
                @change="handleFileChange"
                accept=".pdf,.jpg,.jpeg,.png"
              />
            </label>
          </div>
        </div>

        <div class="flex items-center gap-3 justify-end mt-4">
          <Button variant="ghost" type="button" @click="$router.back()"
            >Cancel</Button
          >
          <Button
            type="submit"
            :disabled="isSubmitting"
            class="px-10 font-bold"
          >
            <Icon
              v-if="isSubmitting"
              name="svg-spinners:180-ring"
              class="mr-2"
            />
            {{ isSubmitting ? "Uploading..." : "Save to Vault" }}
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
                  ? 'ic:outline-verified'
                  : 'ic:outline-warning-amber'
              "
              size="32"
            />
          </div>
          <DialogTitle class="text-xl font-black font-bricolage">{{
            modalContent.title
          }}</DialogTitle>
          <DialogDescription class="pt-2 text-base">
            {{ modalContent.message }}
          </DialogDescription>
        </DialogHeader>
        <DialogFooter class="sm:justify-center mt-6">
          <Button
            :variant="modalContent.success ? 'default' : 'destructive'"
            @click="handleModalClose"
            class="w-full sm:w-32"
          >
            {{ modalContent.success ? "Done" : "Try Again" }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
