<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";

const router = useRouter();
const route = useRoute();

const props = defineProps<{
  title: string;
}>();

const emit = defineEmits(["toggleSidebar"]);

const isLogoutOpen = ref(false);
const isLoggingOut = ref(false);

// Logic: Show back button only on deeper pages (e.g., /admin/users/update)
const showBackButton = computed(() => {
  const segments = route.path.split("/").filter(Boolean);
  return segments.length > 2;
});

const goBack = () => router.back();

const formattedDate = computed(() => {
  return new Intl.DateTimeFormat("en-GB", {
    day: "numeric",
    month: "short",
    year: "numeric",
  }).format(new Date());
});

const executeLogout = async () => {
  isLoggingOut.value = true;
  try {
    await $fetch("https://n3shemmy.icuprojects.icu/api/auth/signout.php", {
      credentials: "include",
    });
    window.location.href = "/signin";
  } catch (error) {
    console.error("Logout failed", error);
    isLoggingOut.value = false;
    isLogoutOpen.value = false;
  }
};
</script>

<template>
  <header
    class="sticky top-0 z-40 h-16 w-full px-4 md:px-6 flex items-center justify-between bg-white border-b border-slate-200 transition-all duration-300"
  >
    <div class="flex items-center gap-2 md:gap-4">
      <button
        @click="emit('toggleSidebar')"
        class="p-2 hover:bg-slate-100 rounded-md md:hidden transition-colors"
      >
        <Icon name="ic:outline-menu" size="24" class="text-slate-600" />
      </button>

      <transition name="fade">
        <button
          v-if="showBackButton"
          @click="goBack"
          class="h-9 w-9 flex items-center justify-center rounded-md border border-slate-200 bg-slate-50 hover:bg-slate-100 transition-all active:scale-95"
        >
          <Icon name="ic:outline-arrow-back" size="18" class="text-slate-700" />
        </button>
      </transition>

      <div class="flex flex-col">
        <h1
          class="text-base md:text-lg font-black tracking-tight text-slate-900 uppercase italic leading-none"
        >
          {{ props.title }}
        </h1>
        <p
          class="text-[9px] text-muted-foreground uppercase tracking-widest font-bold mt-1 md:hidden"
        >
          {{ formattedDate }}
        </p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <div
        class="hidden md:flex flex-col items-end border-r pr-4 border-slate-200"
      >
        <span
          class="text-[9px] uppercase text-slate-400 font-black tracking-widest"
          >Calendar</span
        >
        <span class="text-xs font-bold text-slate-700 uppercase">{{
          formattedDate
        }}</span>
      </div>

      <div class="flex items-center gap-1 md:gap-2">
        <NuxtLink to="/user/notifications">
          <button
            class="p-2 text-slate-500 hover:text-primary transition-colors relative"
          >
            <Icon name="ic:outline-notifications" size="22" />
            <span
              class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full border-2 border-white"
            ></span>
          </button>
        </NuxtLink>

        <button
          @click="isLogoutOpen = true"
          class="flex items-center gap-2 text-rose-600 hover:bg-rose-50 rounded-md px-3 h-9 transition-colors"
        >
          <Icon name="ic:outline-log-out" size="18" />
          <span
            class="hidden lg:inline text-[10px] font-black uppercase tracking-widest"
            >Exit</span
          >
        </button>
      </div>
    </div>

    <Dialog v-model:open="isLogoutOpen">
      <DialogContent
        class="sm:max-w-[400px] rounded-md border-slate-200 shadow-none"
      >
        <DialogHeader class="flex flex-col items-center text-center">
          <div
            class="w-12 h-12 rounded-md bg-rose-50 text-rose-600 flex items-center justify-center mb-2"
          >
            <Icon name="ic:outline-info" size="28" />
          </div>
          <DialogTitle
            class="text-xl font-black italic tracking-tighter uppercase"
            >Terminate Session?</DialogTitle
          >
          <DialogDescription class="text-sm">
            End your current session. You will need to re-authenticate to access
            the registry.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter class="grid grid-cols-2 gap-3 mt-4">
          <button
            @click="isLogoutOpen = false"
            :disabled="isLoggingOut"
            class="h-10 border border-slate-200 rounded-md text-[11px] font-bold uppercase hover:bg-slate-50 transition-colors"
          >
            Stay
          </button>
          <button
            @click="executeLogout"
            :disabled="isLoggingOut"
            class="h-10 bg-rose-600 text-white rounded-md text-[11px] font-bold uppercase tracking-widest hover:bg-rose-700 transition-colors flex items-center justify-center"
          >
            <Icon
              v-if="isLoggingOut"
              name="svg-spinners:180-ring"
              class="mr-2"
            />
            Log Out
          </button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </header>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateX(-5px);
}
</style>
