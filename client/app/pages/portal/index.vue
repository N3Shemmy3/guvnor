<script setup>
definePageMeta({
  layout: "portal",
});
</script>

<template>
  <div class="w-full flex flex-col gap-4 px-4 py-4">
    <div class="grid grid-cols-3 gap-6">
      <ul
        v-for="n in 3"
        class="p2 md:p-4 flex flex-col gap-2 bg-card rounded text-card-foreground border"
      >
        <li class="flex gap-2 justify-between">
          <h4 class="text-2xl md:text-6xl">10</h4>
          <div class="size-8 bg-chart-2 rounded"></div>
        </li>
        <p>Total applications</p>
      </ul>
    </div>
    <table class="w-full border rounded overflow-clip">
      <!-- head-->
      <thead class="border-b">
        <tr>
          <th>Header 1</th>
          <th>Header 2</th>
        </tr>
      </thead>

      <!-- Body -->
      <tbody class="divide-y *:h-8">
        <tr>
          <td>Data 1</td>
          <td>Data 2</td>
        </tr>
        <tr>
          <td>Data 3</td>
          <td>Data 4</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
