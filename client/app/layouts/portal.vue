<script setup>
import Main from "~/components/layout/Main.vue";
import Header from "~/components/layout/Header.vue";
import Sidebar from "~/components/layout/Sidebar.vue";
import Root from "~/components/layout/Root.vue";

const router = useRouter();
const items = ref([
  {
    title: "Dashboard",
    url: "/portal",
    icon: "ic:outline-dashboard",
  },
  {
    title: "Applications",
    url: "/portal/applications",
    icon: "ic:outline-apps",
  },
  {
    title: "Document vault",
    url: "/portal/documents",
    icon: "ic:outline-folder",
  },
]);
const selectedItem = ref(items.value[0]);

const onClickItem = (item) => {
  console.log(item);
  router.push(item.url);
};
</script>

<template>
  <Root>
    <Sidebar :items @onClickItem="onClickItem" />
    <Header :title="selectedItem.title" />
    <Main>
      <slot />
    </Main>
    <footer>footer</footer>
  </Root>
</template>
