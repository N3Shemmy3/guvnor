<script setup>
import { ref, watch } from "vue";
import { useForm } from "vee-validate";

definePageMeta({
  layout: false,
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const passwordValue = ref("");
const confirmPasswordValue = ref("");
const isMinLength = ref(false);
const hasUppercase = ref(false);
const hasNumber = ref(false);

// Watch the password value and update live validation checks
watch(passwordValue, (newVal) => {
  isMinLength.value = newVal.length >= 8;
  hasUppercase.value = /[A-Z]/.test(newVal);
  hasNumber.value = /[0-9]/.test(newVal);
});

const validationSchema = {
  email: (value) => {
    if (!value) return "Email is required";
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value))
      return "Please enter a valid email address";
    return true;
  },
  password: (value) => {
    if (!value) return "Password is required";
    if (value.length < 8 || !/[A-Z]/.test(value) || !/[0-9]/.test(value))
      return "Invalid Password";
    return true;
  },
  confirmPassword: (value, { password }) => {
    if (!value) return "Confirm password is required";
    if (value !== password) return "Passwords do not match";
    return true;
  },
};

const { handleSubmit, isSubmitting, setErrors } = useForm({
  validationSchema,
});

const onSubmit = handleSubmit(async (values) => {
  try {
    const response = await $fetch("/api/auth/signup.php", {
      method: "POST",
      body: {
        email: values.email,
        password: values.password,
        confirmPassword: values.confirmPassword,
      },
    });

    // Assuming the API returns { success: true, message: '...', token: '...' } on success
    if (response.success) {
      // Handle successful sign-up, e.g., redirect to sign-in or dashboard
      await navigateTo("/signin");
      console.log("Sign-up successful:", response);
    } else {
      // Handle error, set form errors
      setErrors({ email: response.message || "Sign-up failed" });
    }
  } catch (error) {
    console.error("Sign-up error:", error);
    setErrors({ email: "An error occurred during sign-up" });
  }
});

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

const toggleConfirmPasswordVisibility = () => {
  showConfirmPassword.value = !showConfirmPassword.value;
};
</script>

<template>
  <div class="h-dvh w-dvw grid md:grid-cols-2">
    <div>
      <img
        src="/img/hero.webp"
        alt="Sign Up"
        class="w-full h-full object-cover"
      />
    </div>
    <div class="w-full flex flex-col items-center px-6 md:px-8 lg:px-12 py-20">
      <img src="/svg/logo.svg" width="150" height="150" />
      <h1 class="text-start text-3xl font-bold mb-4">Sign Up</h1>

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
            <FormDescription />
            <FormMessage />
          </FormItem>
        </FormField>

        <FormField name="confirmPassword" v-slot="{ componentField }">
          <FormItem>
            <FormLabel>Confirm Password</FormLabel>
            <FormControl>
              <div class="relative">
                <Input
                  class="shadow-none outline-none bg-card pr-10"
                  placeholder="Confirm Password"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  v-bind="componentField"
                  v-model="confirmPasswordValue"
                />
                <button
                  type="button"
                  @click="toggleConfirmPasswordVisibility"
                  class="absolute cursor-pointer right-3 top-1/2 -translate-y-1/2"
                >
                  <Icon
                    :name="showConfirmPassword ? 'mdi:eye-off' : 'mdi:eye'"
                    size="20"
                  />
                </button>
              </div>
            </FormControl>
            <FormDescription />
            <FormMessage />
          </FormItem>
        </FormField>

        <Button type="submit" class="w-full" :disabled="isSubmitting">
          {{ isSubmitting ? "Signing Up..." : "Sign Up" }}
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
