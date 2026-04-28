<script setup lang="ts">
const router = useRouter();
defineProps(["items"]);
defineEmits(["onClickItem", "onOverlayClick"]);
</script>

<template>
  <div
    id="overlay"
    @click="$emit('onOverlayClick')"
    class="fixed z-30 top-0 left-0 right-0 bottom-0 w-dvw h-dvh md:w-64 max-md:bg-black/55 backdrop-blur-md"
  >
    <aside
      class="fixed left-0 top-0 bottom-0 h-full w-64 flex flex-col gap-1 bg-background border-r"
    >
      <div class="w-full px-4 h-14 flex items-center">
        <h4 class="font-bold tracking-tight text-lg md:text-2xl">Guv'nor</h4>
      </div>
      <ul class="list-none grid px-2 gap-1">
        <li
          v-for="(item, index) in items"
          @click="$emit('onClickItem', item)"
          :key="index"
          class="w-full px-2 h-10 flex transition-all duration-200 gap-2 items-center rounded cursor-pointer hover:bg-primary hover:text-primary-foreground"
          :class="{
            'bg-primary font-bold text-primary-foreground hover:bg-primary/75':
              router.currentRoute.value.path == item.url,
          }"
        >
          <Icon :name="item.icon" />
          <p class="text-sm">{{ item.title }}</p>
        </li>
      </ul>
    </aside>
  </div>
</template>
