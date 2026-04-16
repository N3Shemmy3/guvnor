<script setup lang="ts">
const date = ref("March 9th, 2026");
const route = useRoute();
const props = defineProps<{
  title: string;
}>();
</script>
<template>
  <header
    class="fixed left-0 md:left-64 top-0 right-0 h-14 px-4 flex items-center justify-between bg-background/55 backdrop-blur-md border-b"
  >
    <div class="flex items-center gap-1">
      <h4 class="font-bold">{{ props.title }}</h4>
    </div>
    <div class="flex items-center gap-1">
      <button
        class="flex items-center justify-center gap-1 aspect-square size-10 rounded-full border-2 transition-all duration-200 cursor-pointer hover:bg-primary hover:text-primary-foreground"
      >
        <p>S</p>
      </button>
    </div>
  </header>
</template>
