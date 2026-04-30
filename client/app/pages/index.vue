<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";

// 1. Services Data
const services = [
  {
    name: "Land & Planning Services",
    description:
      "Access building permits and occupancy licenses. Track site inspections and receive notifications when your structural plans are approved.",
    image: "/img/services/1.webp",
  },
  {
    name: "Smart Permits & Licensing",
    description:
      "Simplify applications for trading licenses, fire certificates, and health permits. Apply and pay online in minutes.",
    image: "/img/services/2.webp",
  },
  {
    name: "Community Certificates",
    description:
      "Register vital events and community groups securely. Process marriage notices, burial permits, and club registrations with accuracy.",
    image: "/img/services/3.webp",
  },
];

const incentives = [
  {
    name: "Time Savings",
    description:
      "Skip the queues and manage your permits, business registrations, and community certificates from the comfort of your home or office.",
  },
  {
    name: "Convenience",
    description:
      "Access building permits and occupancy licenses. Track site inspections and receive notifications when your structural plans are approved.",
  },
  {
    name: "Transparency",
    description:
      "Simplify applications for trading licenses, fire certificates, and health permits. Apply and pay online in minutes.",
  },
];

// 2. Dialog Logic
const isDialogOpen = ref(false);
const selectedServiceName = ref("");

const apply = (serviceName: string) => {
  selectedServiceName.value = serviceName;
  isDialogOpen.value = true;
};

const goToLogin = () => {
  isDialogOpen.value = false;
  navigateTo("/signin");
};
</script>

<template>
  <div class="w-full flex flex-col">
    <section id="hero" class="w-full">
      <div id="hero-text" class="w-full px-6 md:px-8 lg:px-12 py-20">
        <h1
          class="w-fit scroll-m-20 text-4xl font-extrabold tracking-tight text-start text-balance font-bricolage md:text-5xl lg:text-6xl"
        >
          Chipata City Services at <br />
          Your Fingertips.
        </h1>
        <p
          class="leading-7 not-first:mt-6 md:max-w-2xl text-balance text-lg text-secondary-foreground"
        >
          The official digital gateway for Chipata residents. <br />
          Skip the queues and manage your permits, business registrations, and
          community certificates from the comfort of your home or office.
        </p>
      </div>
      <div id="hero-img" class="w-full">
        <NuxtImg
          class="w-full max-md:aspect-video aspect-16/8 object-cover"
          src="/img/hero.webp"
          alt="Hero Image"
        />
      </div>
    </section>

    <section id="services">
      <div id="services-text" class="w-full px-6 md:px-8 lg:px-12">
        <div class="h-12 md:h-20 lg:h-24 *:select-text" />
        <h2
          class="w-fit scroll-m-20 text-4xl font-extrabold tracking-tight text-start text-balance font-bricolage md:text-5xl lg:text-6xl"
        >
          Our Services
        </h2>
        <p
          class="leading-7 not-first:mt-6 md:max-w-2xl text-balance text-lg text-secondary-foreground"
        >
          We offer a range of services to meet the needs of Chipata residents,
          including:
        </p>
      </div>
      <ul class="grid lg:grid-cols-2 gap-4 md:gap-8 list-none pt-12">
        <li
          v-for="(service, index) in services"
          :key="service.name"
          class="h-fit flex flex-col px-6 py-4 md:px-8 md:py-6 lg:px-12 lg:py-8 gap-4 md:gap-6 border-b lg:border-none"
        >
          <NuxtImg
            class="w-full aspect-video object-cover rounded-xl"
            :src="service.image"
            alt="Services Image"
          />
          <div class="w-full">
            <h4
              class="scroll-m-20 text-xl md:text-2xl lg:w-3xl font-semibold font-bricolage tracking-tight"
            >
              {{ service.name }}
            </h4>
            <div class="mt-2">
              <p
                class="leading-7 text-balance text-lg text-secondary-foreground"
              >
                {{ service.description }}
              </p>

              <Button
                @click="apply(service.name)"
                class="md:w-fit mt-8 rounded-full px-8"
              >
                Apply now
              </Button>
            </div>
          </div>
        </li>
      </ul>
    </section>

    <section id="incentives" class="bg-secondary/5 pb-20">
      <div id="incentives-text" class="w-full px-6 md:px-8 lg:px-12 pt-20">
        <h2
          class="w-fit scroll-m-20 text-4xl font-extrabold tracking-tight text-start text-balance font-bricolage md:text-5xl lg:text-6xl"
        >
          Why Choose Guv’nor?
        </h2>
        <p
          class="leading-7 not-first:mt-6 md:max-w-2xl text-balance text-lg text-secondary-foreground"
        >
          Digital transformation designed for the efficiency of Chipata.
        </p>
      </div>

      <ul
        class="w-full grid gap-4 md:grid-cols-3 px-6 md:px-12 list-none pt-12"
      >
        <li
          v-for="incentive in incentives"
          :key="incentive.name"
          class="flex flex-col p-8 bg-background border rounded-2xl hover: transition-all duration-300"
        >
          <div
            class="h-12 w-12 bg-primary/10 rounded-full flex items-center justify-center mb-4 text-primary"
          >
            <Icon name="ic:outline-check-circle" size="24" />
          </div>
          <h4 class="text-xl font-bold font-bricolage tracking-tight">
            {{ incentive.name }}
          </h4>
          <p class="mt-2 leading-7 text-sm text-secondary-foreground">
            {{ incentive.description }}
          </p>
        </li>
      </ul>
    </section>

    <section
      id="cta"
      class="w-full py-24 bg-foreground text-background flex flex-col text-center items-center justify-center px-6"
    >
      <h2
        class="scroll-m-20 text-3xl md:text-5xl font-extrabold tracking-tight text-balance font-bricolage"
      >
        Ready to Get Started?
      </h2>
      <p
        class="mt-6 md:max-w-2xl mx-auto text-balance text-base md:text-lg opacity-80"
      >
        Sign up today and experience the convenience of managing your city
        services online.
      </p>
      <NuxtLink to="/signup">
        <Button
          size="lg"
          variant="secondary"
          class="mt-8 rounded-full px-12 font-bold uppercase tracking-wider"
        >
          Create Account
        </Button>
      </NuxtLink>
    </section>

    <Dialog v-model:open="isDialogOpen">
      <DialogContent class="sm:max-w-md bg-background border rounded-lg">
        <DialogHeader class="flex flex-col items-center text-center">
          <div
            class="mx-auto w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4"
          >
            <Icon name="ic:outline-lock" class="text-primary" size="28" />
          </div>
          <DialogTitle
            class="text-2xl font-bricolage font-bold text-foreground"
          >
            Authentication Required
          </DialogTitle>
          <DialogDescription class="text-base pt-2 text-muted-foreground">
            To apply for
            <span class="font-bold text-foreground">{{
              selectedServiceName
            }}</span
            >, you must be logged into your account.
          </DialogDescription>
        </DialogHeader>

        <DialogFooter class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6 w-full">
          <Button
            variant="outline"
            @click="isDialogOpen = false"
            class="w-full order-2 sm:order-1"
          >
            Maybe Later
          </Button>
          <Button
            @click="goToLogin"
            class="w-full order-1 sm:order-2 bg-primary text-primary-foreground hover:bg-primary/90"
          >
            Sign In Now
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>

<style>
@import url("https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap");

.font-bricolage {
  font-family: "Bricolage Grotesque", sans-serif;
}
</style>
