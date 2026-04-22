import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: "2026-03-09",
  devtools: { enabled: true },
  css: ["./app/assets/css/main.css"],
  modules: [
    "@nuxt/icon",
    "@nuxt/image",
    "@nuxt/fonts",
    "@nuxtjs/color-mode",
    "shadcn-nuxt",
  ],
  shadcn: {
    prefix: "",
    componentDir: "@/components/ui",
  },
  vite: {
    plugins: [tailwindcss()],
  },
  icon: {
    size: "24",
  },
});