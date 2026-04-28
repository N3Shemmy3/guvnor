<script setup>
definePageMeta({
  layout: "app",
});

const applications = [
  {
    id: 1,
    title: "Application 1",
    applicant: "John Doe",
    service: "Marriage Registration",
    time: "2024-06-01 10:00",
    status: "Active",
  },
  {
    id: 2,
    title: "Application 2",
    applicant: "Jane Smith",
    service: "Birth Certificate",
    time: "2024-06-02 14:30",
    status: "Inactive",
  },
  {
    id: 3,
    title: "Application 3",
    applicant: "Bob Johnson",
    service: "Death Certificate",
    time: "2024-06-03 09:15",
    status: "Pending",
  },
  {
    id: 4,
    title: "Application 4",
    applicant: "Alice Brown",
    service: "Marriage Registration",
    time: "2024-06-04 11:45",
    status: "Active",
  },
];
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

    <section class="flex flex-col gap-4">
      <div class="flex gap-4 px-4 py-2 items-center justify-between">
        <div class="w-full max-w-md flex items-center gap-2">
          <InputGroup>
            <InputGroupInput placeholder="Search..." />
            <InputGroupAddon>
              <Icon name="ic:outline-search" />
            </InputGroupAddon>
          </InputGroup>
        </div>
        <Button>New Application</Button>
      </div>

      <table class="w-full border overflow-clip">
        <!-- head-->
        <thead class="border-b bg-card rounded-t">
          <tr class="w-full *:px-4 *:py-2 *:text-start">
            <th>ID</th>
            <th>Applicant</th>
            <th>Service</th>
            <th>Time</th>
            <th>Action</th>
          </tr>
        </thead>

        <!-- Body -->
        <tbody class="divide-y rounded-b">
          <tr
            class="table-fixed *:px-4 *:py-2 hover:bg-card/75 duration-300 transition-all transform-gpu"
            v-for="app in applications"
            :key="app.id"
          >
            <td>{{ app.id }}</td>
            <td>{{ app.applicant }}</td>
            <td>{{ app.service }}</td>
            <td>{{ app.time }}</td>
            <td class="flex items-center justify-between">
              <Button variant="secondary" size="icon">
                <Icon name="ic:outline-check" />
              </Button>
              <Button variant="destructive" size="icon">
                <Icon name="ic:outline-delete" />
              </Button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>
