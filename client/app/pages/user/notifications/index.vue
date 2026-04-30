<script setup lang="ts">
import axios from "axios";

definePageMeta({ layout: "user" });

const config = useRuntimeConfig();
const notifications = ref([]);
const isLoading = ref(true);

// 1. Fetch Notifications
const fetchNotifications = async () => {
  try {
    const response = await axios.get(`${config.public.api}/notifications.php`, {
      params: { action: "user-notifications" },
      withCredentials: true,
    });
    // The PHP returns 'notifications' key
    notifications.value = response.data.notifications || [];
  } catch (error) {
    console.error("Error fetching notifications:", error);
  } finally {
    isLoading.value = false;
  }
};

// 2. Mark single notification as read
const markAsRead = async (id: number) => {
  try {
    const response = await axios.post(
      `${config.public.api}/notifications.php`,
      { action: "mark-notification-read", notification_id: id },
      { withCredentials: true },
    );
    if (response.data.success) {
      // Update local state to reflect read status
      const index = notifications.value.findIndex((n) => n.id === id);
      if (index !== -1) notifications.value[index].is_read = 1;
    }
  } catch (error) {
    console.error("Error marking as read:", error);
  }
};

onMounted(fetchNotifications);

// Helper for relative time (e.g., "2 hours ago")
const formatTime = (dateStr: string) => {
  const date = new Date(dateStr);
  return (
    date.toLocaleDateString() +
    " " +
    date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" })
  );
};
</script>

<template>
  <div class="w-full flex flex-col gap-6 px-6 py-8 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
      <div>
        <h1
          class="text-2xl font-black font-bricolage tracking-tight italic text-primary"
        >
          Notifications
        </h1>
        <p class="text-sm text-muted-foreground">
          Stay updated on your application statuses and city alerts.
        </p>
      </div>
      <Button
        variant="ghost"
        size="sm"
        @click="fetchNotifications"
        class="rounded-full"
      >
        <Icon
          name="ic:outline-refresh"
          :class="{ 'animate-spin': isLoading }"
          class="mr-2"
        />
        Refresh
      </Button>
    </div>

    <div v-if="notifications.length > 0" class="flex flex-col gap-3">
      <div
        v-for="note in notifications"
        :key="note.id"
        @click="markAsRead(note.id)"
        class="group relative flex flex-col gap-2 p-5 rounded-xl border bg-card transition-all cursor-pointer hover:"
        :class="
          note.is_read
            ? 'opacity-70 bg-secondary/10'
            : 'border-primary/30 ring-1 ring-primary/5'
        "
      >
        <div
          v-if="!note.is_read"
          class="absolute top-5 right-5 w-2.5 h-2.5 bg-primary rounded-full"
        ></div>

        <div class="flex items-start gap-4">
          <div
            :class="[
              'p-2 rounded-lg',
              note.is_read
                ? 'bg-muted text-muted-foreground'
                : 'bg-primary/10 text-primary',
            ]"
          >
            <Icon
              :name="
                note.type === 'status'
                  ? 'ic:outline-info'
                  : 'ic:outline-campaign'
              "
              size="24"
            />
          </div>

          <div class="flex-1">
            <div class="flex items-center gap-2">
              <h4
                :class="[
                  'font-bold text-base',
                  !note.is_read ? 'text-foreground' : 'text-muted-foreground',
                ]"
              >
                {{ note.title }}
              </h4>
              <span
                v-if="note.application_id"
                class="text-[10px] font-mono bg-muted px-2 py-0.5 rounded uppercase"
              >
                Ref: #{{ note.application_id }}
              </span>
            </div>
            <p
              :class="[
                'text-sm mt-1 leading-relaxed',
                !note.is_read
                  ? 'text-secondary-foreground'
                  : 'text-muted-foreground',
              ]"
            >
              {{ note.message }}
            </p>
            <p
              class="text-[10px] mt-3 font-medium uppercase tracking-wider text-muted-foreground/60"
            >
              {{ formatTime(note.created_at) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div
      v-else-if="!isLoading"
      class="flex flex-col items-center justify-center py-20 text-center gap-4 bg-muted/20 rounded-3xl border-2 border-dashed"
    >
      <div class="p-6 bg-background rounded-full shadow-inner">
        <Icon
          name="ic:outline-notifications-off"
          size="48"
          class="text-muted-foreground/40"
        />
      </div>
      <div>
        <h3 class="text-xl font-bold">All caught up!</h3>
        <p class="text-muted-foreground text-sm">
          You have no new notifications at the moment.
        </p>
      </div>
    </div>

    <div v-else class="flex flex-col gap-3">
      <div
        v-for="i in 3"
        :key="i"
        class="h-24 w-full bg-muted animate-pulse rounded-xl"
      ></div>
    </div>
  </div>
</template>

<style scoped>
.font-bricolage {
  font-family: "Bricolage Grotesque", sans-serif;
}
</style>
