export default defineNuxtRouteMiddleware(async (to) => {
  // const authPages = ["/signin", "/signup", "/"];
  // try {
  //   const data = await $fetch<{ loggedIn: boolean; role: string }>(
  //     "https://n3shemmy.icuprojects.icu/api/auth/check-auth.php",
  //     { credentials: "include" },
  //   );
  //   // If logged in
  //   if (data.loggedIn) {
  //     // 1. Prevent logged-in users from seeing signin/signup
  //     if (authPages.includes(to.path)) {
  //       return navigateTo(
  //         data.role === "admin" ? "/admin" : "/user/applications",
  //       );
  //     }
  //     if (to.path.startsWith("/admin") && data.role !== "admin") {
  //       return navigateTo("/user/applications"); // Kick non-admins to user area
  //     }
  //     // if (to.path.startsWith("/user") && data.role === "admin") {
  //     //   return navigateTo("/admin");
  //     // }
  //   } else {
  //     if (!authPages.includes(to.path)) {
  //       return navigateTo("/signin");
  //     }
  //   }
  // } catch (error) {
  //   if (!authPages.includes(to.path)) {
  //     return navigateTo("/signin");
  //   }
  // }
});
