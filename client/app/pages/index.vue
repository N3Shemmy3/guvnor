<script setup lang="ts">
const services = [
  {
    name: "Land & Planning Services",
    description:
      "Access building permits and occupancy licenses. Track site inspections and receive notifications when your structural plans are approved.",
    image: "/img/hero-image.webp",
  },
  {
    name: "Smart Permits & Licensing",
    description:
      "Simplify applications for trading licenses, fire certificates, and health permits. Apply and pay online in minutes.",
    image: "/img/hero-image.webp",
  },
  {
    name: "Community Certificates",
    description:
      "Register vital events and community groups securely. Process marriage notices, burial permits, and club registrations with accuracy.",
    image: "/img/hero-image.webp",
  },
];

const incentives = [
  {
    name: "Time Savings",
    description:
      "Skip the queues and manage your permits, business registrations, and community certificates from the comfort of your home or office.",
    image: "/img/hero-image.webp",
  },
  {
    name: "Convenience",
    description:
      "Access building permits and occupancy licenses. Track site inspections and receive notifications when your structural plans are approved.",
    image: "/img/hero-image.webp",
  },
  {
    name: "Transparency",
    description:
      "Simplify applications for trading licenses, fire certificates, and health permits. Apply and pay online in minutes.",
    image: "/img/hero-image.webp",
  },
];

const apply = (serviceName: string) => {
  alert(`Applying for ${serviceName}`);
};
</script>
<template>
  <div class="w-full flex flex-col">
    <section id="hero" class="w-full">
      <div id="hero-text" class="w-full px-6 md:px-8 lg:px-12 py-20">
        <div class="h-12 md:h-20 lg:h-24 *:select-text" />
        <h1
          class="w-fit scroll-m-20 text-4xl font-extrabold tracking-tight text-start text-balance font-bricolage md:text-5xl lg:text-6xl"
        >
          Guv’nor — Chipata City Services at <br />
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
          class="w-full max-md:aspect-video object-cover"
          src="/img/hero-image.webp"
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
          class="h-fit flex md:flex-row px-6 py-4 md:px-8 md:py-6 lg:px-12 lg:py-8 gap-4 md:gap-6"
          :class="
            index % 2 === 0
              ? 'flex-col md:flex-row lg:flex-col'
              : 'flex-col md:flex-row-reverse lg:flex-col'
          "
        >
          <NuxtImg
            class="w-full aspect-video object-cover"
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

              <Button @click="apply(service.name)" class="md:w-fit mt-8"
                >Apply now</Button
              >
            </div>
          </div>
          <hr class="mt-4" />
        </li>
      </ul>
    </section>

    <section id="incentives">
      <div id="incentives-text" class="w-full px-6 md:px-8 lg:px-12">
        <div class="h-12 md:h-20 lg:h-24 *:select-text" />
        <h2
          class="w-fit scroll-m-20 text-4xl font-extrabold tracking-tight text-start text-balance font-bricolage md:text-5xl lg:text-6xl"
        >
          Why Choose Guv’nor?
        </h2>
        <p
          class="leading-7 not-first:mt-6 md:max-w-2xl text-balance text-lg text-secondary-foreground"
        >
          We offer a range of incentives to meet the needs of Chipata residents,
          including:
        </p>
      </div>

      <ul class="w-full grid gap-4 md:grid-cols-2 md:gap-8 list-none pt-12">
        <li
          v-for="incentive in incentives"
          :key="incentive.name"
          class="flex-1 px-6 py-4 md:px-8 md:py-6 lg:px-12 lg:py-8 hover:bg-secondary/10 rounded-md transition-all duration-200"
        >
          <h4
            class="scroll-m-20 text-xl md:text-2xl lg:w-3xl font-semibold font-bricolage tracking-tight"
          >
            {{ incentive.name }}
          </h4>
          <p
            class="w-fit mt-2 leading-7 text-balance text-lg text-secondary-foreground"
          >
            {{ incentive.description }}
          </p>
          <hr class="mt-4" />
        </li>
      </ul>
    </section>
    <section
      id="cta"
      class="w-full aspect-video bg-foreground text-background flex flex-col text-center items-center justify-center"
    >
      <h2
        class="w-fit scroll-m-20 text-4xl font-extrabold tracking-tight text-start text-balance font-bricolage md:text-5xl lg:text-6xl"
      >
        Ready to Get Started?
      </h2>
      <p
        class="leading-7 not-first:mt-6 md:max-w-2xl mx-auto text-balance text-lg text-scard"
      >
        Sign up today and experience the convenience of managing your city
        services online.
      </p>
      <button
        class="mt-8 bg-background text-foreground text-center hover:bg-background/75 px-4 py-2 rounded-md cursor-pointer transition-all duration-200"
      >
        Sign Up Now
      </button>
    </section>
  </div>
</template>

<style>
.font-bricolage {
  font-family: "Bricolage Grotesque", sans-serif;
}
</style>
