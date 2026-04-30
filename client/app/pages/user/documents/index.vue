<script setup lang="ts">
import axios from "axios";
// Import individual components if auto-import is failing
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
const documents = ref([]);
const searchQuery = ref("");
const isLoading = ref(true); // Added loading state

// Dialog States
const isConfirmOpen = ref(false);
const isStatusOpen = ref(false);
const statusContent = ref({ title: "", message: "", success: true });
const docToDelete = ref<number | null>(null);
const isDeleting = ref(false);

const fetchDocs = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(`${config.public.api}/users.php`, {
      params: { action: "list-documents" },
      withCredentials: true,
    });
    // Ensure we are accessing the correct key from your PHP response
    documents.value = response.data.data || [];
  } catch (error: any) {
    console.error("Fetching documents error:", error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchDocs);

const filteredDocs = computed(() => {
  if (!documents.value) return [];
  return documents.value.filter(
    (doc) =>
      doc.original_name
        ?.toLowerCase()
        .includes(searchQuery.value.toLowerCase()) ||
      doc.document_type
        ?.toLowerCase()
        .includes(searchQuery.value.toLowerCase()),
  );
});

const viewFile = (path: string) => {
  const fileUrl = `https://n3shemmy.icuprojects.icu/api/${path}`;
  window.open(fileUrl, "_blank");
};

const promptDelete = (id: number) => {
  docToDelete.value = id;
  isConfirmOpen.value = true;
};

const confirmDelete = async () => {
  if (!docToDelete.value) return;
  isDeleting.value = true;
  try {
    const response = await axios.post(
      `${config.public.api}/users.php`,
      { action: "delete-document", id: docToDelete.value },
      { withCredentials: true },
    );

    if (response.data.success) {
      documents.value = documents.value.filter(
        (d) => d.id !== docToDelete.value,
      );
      isConfirmOpen.value = false;
    } else {
      throw new Error(response.data.message || "Failed to delete");
    }
  } catch (e: any) {
    statusContent.value = {
      title: "Action Failed",
      message: e.message || "Could not delete document.",
      success: false,
    };
    isStatusOpen.value = true;
  } finally {
    isDeleting.value = false;
    docToDelete.value = null;
  }
};
</script>

<template>
  <div class="w-full flex flex-col gap-6 px-6 py-6">
    <div
      class="flex flex-col md:flex-row md:items-center justify-between gap-4"
    >
      <div>
        <h2
          class="text-2xl font-black font-bricolage tracking-tight text-foreground"
        >
          Document Vault
        </h2>
        <p class="text-sm text-muted-foreground">
          Manage your uploaded certificates, IDs, and permits.
        </p>
      </div>
      <NuxtLink to="/user/documents/new">
        <Button class="rounded-full px-6">
          <Icon name="ic:outline-upload-file" class="mr-2" />
          Upload Document
        </Button>
      </NuxtLink>
    </div>

    <section class="flex flex-col bg-card rounded-xl border overflow-hidden">
      <div class="p-4 bg-muted/20 border-b">
        <div class="w-full max-w-md relative">
          <Input
            v-model="searchQuery"
            placeholder="Search documents..."
            class="pl-10 bg-background"
          />
          <Icon
            name="ic:outline-search"
            class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
            size="18"
          />
        </div>
      </div>

      <div class="overflow-x-auto min-h-50">
        <div
          v-if="isLoading"
          class="w-full py-20 flex flex-col items-center justify-center gap-2"
        >
          <Icon name="svg-spinners:180-ring" size="32" class="text-primary" />
          <p class="text-sm text-muted-foreground">Loading documents...</p>
        </div>

        <table v-else-if="filteredDocs.length > 0" class="w-full text-sm">
          <thead>
            <tr
              class="bg-muted/50 border-b *:px-6 *:py-4 *:text-left *:font-bold *:text-muted-foreground uppercase tracking-wider text-[11px]"
            >
              <th>File Name</th>
              <th>Category</th>
              <th>Upload Date</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr
              v-for="doc in filteredDocs"
              :key="doc.id"
              class="hover:bg-muted/30 transition-colors *:px-6 *:py-4"
            >
              <td class="flex items-center gap-3">
                <div
                  class="h-9 w-9 rounded bg-primary/10 flex items-center justify-center text-primary"
                >
                  <Icon name="ic:outline-insert-drive-file" size="20" />
                </div>
                <div class="flex flex-col">
                  <span class="font-bold text-foreground truncate max-w-50">{{
                    doc.original_name
                  }}</span>
                  <span class="text-[10px] text-muted-foreground font-mono"
                    >ID: #{{ doc.id }}</span
                  >
                </div>
              </td>
              <td>
                <span
                  class="px-2 py-1 bg-secondary text-secondary-foreground rounded text-[11px] font-semibold"
                >
                  {{ doc.document_type }}
                </span>
              </td>
              <td class="text-muted-foreground">
                {{ new Date(doc.uploaded_at).toLocaleDateString() }}
              </td>
              <td class="text-right">
                <div class="flex justify-end gap-2">
                  <Button
                    variant="outline"
                    size="icon"
                    class="h-8 w-8 rounded-full"
                    @click="viewFile(doc.file_path)"
                  >
                    <Icon name="ic:outline-remove-red-eye" size="16" />
                  </Button>
                  <Button
                    variant="ghost"
                    size="icon"
                    class="h-8 w-8 rounded-full text-destructive hover:bg-destructive/10"
                    @click="promptDelete(doc.id)"
                  >
                    <Icon name="ic:outline-delete" size="16" />
                  </Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <div
          v-else
          class="w-full py-20 flex flex-col items-center justify-center gap-4 text-center"
        >
          <Icon
            name="ic:outline-folder-open"
            size="40"
            class="text-muted-foreground"
          />
          <p class="text-xl font-bold">No documents found</p>
          <p class="text-sm text-muted-foreground">
            Get started by uploading your first document.
          </p>
        </div>
      </div>
    </section>

    <Dialog v-model:open="isConfirmOpen">
      <DialogContent class="sm:max-w-106.25 bg-background">
        <DialogHeader>
          <DialogTitle>Confirm Deletion</DialogTitle>
          <DialogDescription class="py-3">
            Are you sure you want to delete this document? This action cannot be
            undone.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter class="flex gap-3">
          <Button
            variant="outline"
            @click="isConfirmOpen = false"
            class="flex-1"
            >Cancel</Button
          >
          <Button
            variant="destructive"
            :disabled="isDeleting"
            @click="confirmDelete"
            class="flex-1"
          >
            <Icon v-if="isDeleting" name="svg-spinners:180-ring" class="mr-2" />
            Delete
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <Dialog v-model:open="isStatusOpen">
      <DialogContent class="sm:max-w-md bg-background">
        <DialogHeader class="flex flex-col items-center">
          <div
            class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-4"
          >
            <Icon name="ic:outline-error-outline" size="28" />
          </div>
          <DialogTitle>{{ statusContent.title }}</DialogTitle>
          <DialogDescription class="text-center">{{
            statusContent.message
          }}</DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button class="w-full" @click="isStatusOpen = false">Dismiss</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
