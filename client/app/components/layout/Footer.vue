<script setup lang="ts">
const quickLinks = [
  { name: "Home", href: "#hero" },
  { name: "Services", href: "#services" },
  { name: "Contact", href: "#contact" },
];

const contactInfo = [
  {
    label: "Address",
    value: "Civic Centre, Plot 1, Along Great East Road, Chipata, Zambia",
  },
  { label: "Hours", value: "Mon - Fri, 08:00 - 17:00" },
  { label: "Phone", value: "+260 216 221XXX" },
  { label: "Email", value: "support@chiptatacitycouncil.gov.zm" },
];
</script>
<template>
  <footer
    class="w-full h-fit border-t dark:border-background/10 px-6 py-4 flex-1 justify-between md:px-8 md:py-6 lg:px-12 lg:py-8 bg-foreground text-background"
  >
    <div class="w-full md:h-20 lg:h-24 *:select-text" />
    <div class="grid md:grid-cols-4 lg:grid-cols-7 gap-8">
      <div class="w-full col-span-3">
        <img name="/svg/logo.svg" class="h-24 w-fit object-fill" />
        <p
          class="w-fit mt-2 leading-7 text-balance text-base md:text-lg text-background/75"
        >
          Providing efficient, transparent, and accessible digital services to
          the people of Chipata.
        </p>
      </div>
      <div class="w-full flex flex-col gap-2 col-span-2">
        <h4
          class="scroll-m-20 text-xl md:text-2xl font-medium font-bricolage tracking-tight"
        >
          Quick Links
        </h4>
        <ul class="list-none">
          <li v-for="link in quickLinks" :key="link.name" class="mt-1">
            <a
              :href="link.href"
              class="text-background/75 hover:text-background/90 transition-colors duration-200"
            >
              {{ link.name }}
            </a>
          </li>
        </ul>
      </div>
      <div class="w-full flex flex-col gap-2 col-span-2">
        <h4
          class="scroll-m-20 text-xl md:text-2xl font-medium font-bricolage tracking-tight"
        >
          Contact Us
        </h4>
        <ul class="list-none">
          <li v-for="link in contactInfo" :key="link.label" class="mt-1">
            <p
              class="text-background/75 hover:text-background/90 transition-colors cursor-pointer duration-200"
            >
              {{ link.value }}
            </p>
          </li>
        </ul>
      </div>
    </div>

    <div class="w-full h-16 md:h-20 *:select-text" />
    <div
      class="w-full flex flex-col md:flex-row md:items-center md:justify-between gap-2 text-sm text-background/70"
    >
      <p>&copy; {{ new Date().getFullYear() }} Guv’nor. All rights reserved.</p>
      <p>Designed and Developed by Shemmy Nyirenda</p>
    </div>
  </footer>
</template>
