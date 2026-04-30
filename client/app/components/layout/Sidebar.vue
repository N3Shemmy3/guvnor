<script setup lang="ts">
const router = useRouter();
const props = defineProps({
  items: { type: Array, required: true },
  isOpen: { type: Boolean, default: false }, // Controlled by the parent layout
});

defineEmits(["onClickItem", "close"]);

// Session state
const userName = ref("Loading...");
const userRole = ref("user");

onMounted(async () => {
  try {
    const data = await $fetch<any>(
      "https://n3shemmy.icuprojects.icu/api/auth/check-auth.php",
      { credentials: "include" },
    );

    if (data.loggedIn) {
      userName.value = data.name;
      userRole.value = data.role;
    }
  } catch (err) {
    userName.value = "Guest";
  }
});

const roleTitle = computed(() => {
  return userRole.value.toLowerCase() === "admin"
    ? "Gov'nor Official"
    : "Registered Citizen";
});

// Helper to check active route
const isActive = (url: string) => router.currentRoute.value.path === url;
</script>

<template>
  <div
    class="fixed inset-0 z-50 transition-opacity duration-300 md:relative md:z-0 md:flex"
    :class="[
      isOpen
        ? 'visible opacity-100'
        : 'invisible opacity-0 md:visible md:opacity-100',
    ]"
  >
    <div
      class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm md:hidden"
      @click="$emit('close')"
    ></div>

    <aside
      class="relative w-64 h-full flex flex-col bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out md:translate-x-0"
      :class="[isOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0']"
    >
      <div class="h-16 flex items-center px-6 border-b border-slate-200">
        <h4 class="font-black italic tracking-tighter text-2xl text-primary">
          Guv'nor<span class="text-slate-900">.</span>
        </h4>
      </div>

      <nav class="flex-1 py-4 overflow-y-auto">
        <ul class="px-3 space-y-1">
          <li
            v-for="(item, index) in items"
            :key="index"
            @click="$emit('onClickItem', item)"
            class="group flex items-center gap-3 px-3 h-11 rounded-md cursor-pointer transition-colors"
            :class="[
              isActive(item.url)
                ? 'bg-primary text-white font-bold'
                : 'hover:bg-slate-100 text-slate-500 hover:text-slate-900',
            ]"
          >
            <Icon
              :name="item.icon"
              size="20"
              :class="
                isActive(item.url) ? 'text-white' : 'group-hover:text-primary'
              "
            />
            <span class="text-sm uppercase tracking-tight">{{
              item.title
            }}</span>
          </li>
        </ul>
      </nav>

      <div class="p-4 border-t border-slate-200 bg-slate-50">
        <div class="flex items-center gap-3">
          <div
            class="h-10 w-10 rounded-md shrink-0 flex items-center justify-center bg-slate-900 text-white font-black italic text-lg"
          >
            {{ userName.charAt(0).toUpperCase() }}
          </div>

          <div class="flex flex-col min-w-0">
            <p
              class="text-xs font-black truncate text-slate-900 uppercase tracking-tighter"
            >
              {{ userName }}
            </p>
            <p
              class="text-[9px] uppercase tracking-[0.15em] font-extrabold text-primary"
            >
              {{ roleTitle }}
            </p>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>
