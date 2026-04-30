import axios from "axios";
export default defineNuxtPlugin(() => {
  const token = useCookie("auth_token");
  const instance = axios.create({
    baseURL: useRuntimeConfig().public.api,
  });

  instance.interceptors.request.use((config) => {
    if (token.value) {
      config.headers.Authorization = `Bearer ${token.value}`;
    }
    return config;
  });

  return { provide: { api: instance } };
});
