<script setup lang="ts">
import Header from "~/components/layout/Header.vue";
import Main from "~/components/layout/Main.vue";
import Button from "../components/button/Button.vue";

const links = [
  { name: "Home", href: "#hero" },
  { name: "Services", href: "#services" },
  { name: "Contact", href: "#contact" },
];
</script>

<template>
  <div class="h-dvh w-dvw overflow-hidden flex flex-col">
    <header
      class="fixed z-50 h-14 left-0 top-0 right-0 px-6 md:px-8 lg:px-12 bg-background flex items-center justify-between"
    >
      <div class="flex">
        <Img class="h-6" src="/svg/text-logo.svg" alt="Logo" />
      </div>

      <div class="flex items-center gap-4">
        <nav class="hidden md:flex">
          <ul class="w-fit flex gap-2 md:gap-4 lg:gap-6">
            <li v-for="link in links" :key="link.name">
              <a :href="link.href">{{ link.name }}</a>
            </li>
          </ul>
        </nav>
        <Button>Sign in</Button>
      </div>
    </header>
    <Main class="pt-14">
      <slot />
    </Main>
    <footer>footer</footer>
  </div>
</template>
