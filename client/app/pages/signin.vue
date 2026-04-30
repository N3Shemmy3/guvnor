<script setup>
import { ref, watch } from "vue";
import { useForm, useField } from "vee-validate";

definePageMeta({ layout: false });

const config = useRuntimeConfig();
const showPassword = ref(false);

const validationSchema = {
  email: (val) =>
    val
      ? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) || "Invalid email"
      : "Required",
  password: (val) => (val && val.length >= 8 ? true : "Invalid Password"),
  remember: () => true,
};

const { handleSubmit, setErrors } = useForm({ validationSchema });
const { value: passwordValue } = useField("password");

const isMinLength = ref(false);
const hasUppercase = ref(false);
const hasNumber = ref(false);

watch(passwordValue, (newVal) => {
  const val = newVal || "";
  isMinLength.value = val.length >= 8;
  hasUppercase.value = /[A-Z]/.test(val);
  hasNumber.value = /[0-9]/.test(val);
});

const onSubmit = handleSubmit(async (values) => {
  try {
    const data = await $fetch(`${config.public.api}/auth/signin.php`, {
      method: "POST",
      body: values,
      credentials: "include",
    });

    if (data.success) {
      const token = useCookie("auth_token", {
        maxAge: values.remember ? 60 * 60 * 24 * 30 : undefined,
        sameSite: "lax",
      });
      token.value = data.token;

      await navigateTo("/user/applications");
    } else {
      setErrors({ email: data.message || "Sign-in failed" });
    }
  } catch (err) {
    const errorMessage = err.data?.message || "An error occurred";
    setErrors({ email: errorMessage });
  }
});
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
    <div
      class="w-full flex flex-col px-6 md:px-8 lg:px-12 py-20 overflow-scroll"
    >
      <img src="/svg/logo.svg" width="150" height="150" class="mb-4" />
      <h1 class="text-start text-3xl font-bold mb-4">Sign In</h1>

      <form class="w-full space-y-4" @submit.prevent="onSubmit">
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
                  v-model="passwordValue"
                />
                <button
                  type="button"
                  @click="togglePasswordVisibility"
                  class="absolute cursor-pointer right-3 top-1/2 -translate-y-1/2"
                >
                  <Icon
                    :name="showPassword ? 'mdi:eye-off' : 'mdi:eye'"
                    size="20"
                  />
                </button>
              </div>
            </FormControl>
            <ul class="text-sm space-y-1 mt-2">
              <li :class="{ 'line-through opacity-50': isMinLength }">
                At least 8 characters
              </li>
              <li :class="{ 'line-through opacity-50': hasUppercase }">
                At least one uppercase letter
              </li>
              <li :class="{ 'line-through opacity-50': hasNumber }">
                At least one number
              </li>
            </ul>
          </FormItem>
        </FormField>

        <div class="flex items-center justify-between">
          <div>
            <FormField name="remember" v-slot="{ componentField }">
              <Checkbox id="remember" class="mr-2" v-bind="componentField" />
            </FormField>
            <label for="remember" class="text-sm">Remember me</label>
          </div>

          <NuxtLink
            to="/forgot-password"
            class="text-sm text-primary hover:underline"
          >
            Forgot password?
          </NuxtLink>
        </div>

        <Button type="submit" class="w-full" :disabled="isSubmitting">
          {{ isSubmitting ? "Signing In..." : "Sign In" }}
        </Button>
        <center>
          <p>Or</p>
        </center>
        <NuxtLink to="/signup">
          <Button class="w-full" variant="outline" :disabled="isSubmitting">
            Create an account
          </Button>
        </NuxtLink>
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
