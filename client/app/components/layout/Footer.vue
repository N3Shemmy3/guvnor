<template>
  <footer
    class="w-full border border-t border-background/10 px-6 py-4 md:px-8 md:py-6 lg:px-12 lg:py-8 bg-foreground text-background"
  >
    <div class="w-full h-12 md:h-20 lg:h-24 *:select-text" />
    <div class="w-full px-6 md:px-8 lg:px-12">
      <p class="text-center text-sm text-background/70">
        &copy; {{ new Date().getFullYear() }} Guv’nor. All rights reserved.
      </p>
    </div>
    <div class="w-full h-12 md:h-20 lg:h-24 *:select-text" />
  </footer>
</template>
