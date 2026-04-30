<script setup>
import Main from "~/components/layout/Main.vue";
import Header from "~/components/layout/Header.vue";
import Sidebar from "~/components/layout/Sidebar.vue";
import Root from "~/components/layout/Root.vue";

const router = useRouter();
const route = useRoute();

// Responsive Sidebar State
const isSidebarOpen = ref(false);

// Auto-close sidebar on mobile when navigating
watch(
  () => route.path,
  () => {
    isSidebarOpen.value = false;
  },
);

const userItems = [
  {
    title: "Dashboard",
    url: "/user/applications",
    icon: "ic:outline-dashboard",
  },
  {
    title: "Document Vault",
    url: "/user/documents",
    icon: "ic:outline-folder",
  },
  {
    title: "Notifications",
    url: "/user/notifications",
    icon: "ic:outline-notifications",
  },
];

const adminItems = [
  {
    title: "Admin Console",
    url: "/admin",
    icon: "ic:outline-admin-panel-settings",
  },
  {
    title: "Applications",
    url: "/admin/applications",
    icon: "ic:outline-assignment",
  },
  {
    title: "User Directory",
    url: "/admin/users",
    icon: "ic:outline-people",
  },
  {
    title: "System Services",
    url: "/admin/services",
    icon: "ic:outline-settings-suggest",
  },
];

const items = computed(() => {
  return route.path.startsWith("/admin") ? adminItems : userItems;
});

const selectedItem = computed(() => {
  return (
    items.value.find((item) => route.path.startsWith(item.url)) ||
    items.value[0]
  );
});

const onClickItem = (item) => {
  router.push(item.url);
};
</script>

<template>
  <Root>
    <Sidebar
      :items="items"
      :isOpen="isSidebarOpen"
      @onClickItem="onClickItem"
      @close="isSidebarOpen = false"
    />

    <div class="flex flex-col flex-1 min-w-0 overflow-hidden relative">
      <Header
        :title="selectedItem.title"
        @toggleSidebar="isSidebarOpen = !isSidebarOpen"
      />

      <Main class="flex-1 overflow-y-auto">
        <slot />
      </Main>
    </div>
  </Root>
</template>
