<script setup lang="ts">
import { ref } from "vue";
import { useForm } from "vee-validate";
import { z } from "zod";
import { toTypedSchema } from "@vee-validate/zod";

definePageMeta({
  layout: false,
});

const showPassword = ref(false);

const validationSchema = toTypedSchema(
  z.object({
    email: z
      .string()
      .min(1, "Email is required")
      .email("Please enter a valid email address"),
    password: z
      .string()
      .min(1, "Password is required")
      .min(8, "Password must be at least 8 characters")
      .regex(/[A-Z]/, "Password must contain at least one uppercase letter")
      .regex(/[0-9]/, "Password must contain at least one number"),
    remember: z.boolean().default(false),
  }),
);

const { handleSubmit, isSubmitting } = useForm({
  validationSchema,
});

const onSubmit = handleSubmit(async (values) => {
  console.log("Form submitted:", values);
});

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};
</script>

<template>
  <div class="h-dvh w-dvw grid md:grid-cols-2">
    <div>
      <img
        src="/img/hero.webp"
        alt="Sign In"
        class="w-full h-full object-cover"
      />
    </div>
    <div class="w-full flex flex-col items-center px-6 md:px-8 lg:px-12 py-20">
      <img name="/svg/logo.svg" size="150" />
      <h1 class="text-start text-3xl font-bold mb-4">Sign In</h1>

      <form class="w-full space-y-4" @submit="onSubmit">
        <FormField name="email" v-slot="{ componentField }">
          <FormItem>
            <FormLabel>Email</FormLabel>
            <FormControl>
              <Input
                class="shadow-none outline-none"
                placeholder="Email"
                type="email"
                v-bind="componentField"
              />
            </FormControl>
            <FormDescription />
            <FormMessage />
          </FormItem>
        </FormField>

        <FormField name="password" v-slot="{ componentField }">
          <FormItem>
            <FormLabel>Password</FormLabel>
            <FormControl>
              <div class="relative">
                <Input
                  class="shadow-none outline-none bg-card pr-10"
                  placeholder="Password"
                  :type="showPassword ? 'text' : 'password'"
                  v-bind="componentField"
                />
                <button
                  type="button"
                  @click="togglePasswordVisibility"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                >
                  <Icon
                    :name="showPassword ? 'mdi:eye-off' : 'mdi:eye'"
                    size="20"
                  />
                </button>
              </div>
            </FormControl>
            <FormDescription />
            <FormMessage />
          </FormItem>
        </FormField>

        <div class="flex items-center justify-between">
          <div>
            <Checkbox id="remember" class="mr-2" name="remember" />
            <label for="remember" class="text-sm">Remember me</label>
          </div>
          <a href="#" class="text-sm text-primary hover:underline">
            Forgot password?
          </a>
        </div>

        <Button type="submit" class="w-full" :disabled="isSubmitting">
          {{ isSubmitting ? "Signing In..." : "Sign In" }}
        </Button>
      </form>
    </div>
  </div>
</template>

<style scoped>
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  margin: 0;
}
</style>
