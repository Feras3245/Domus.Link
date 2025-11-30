
import { type User } from "~/types/auth";

export default defineNuxtRouteMiddleware(async (to, from) => {
  const user = useState<User | null>('user', () => null);

  if (import.meta.server) {
    try {
      const fetchedUser = await $fetch<User>('/api/auth/user', {
        method: "GET",
        headers: useRequestHeaders()
      });
      user.value = fetchedUser;
    } catch (error) {
      console.error('Failed to fetch user:', error);
    }
  } else {
    if (!user.value && to.path !== '/login') {
      return navigateTo('/login', { external: true });
    } else if (user.value && to.path === '/login') {
      return navigateTo('/', {external: true});
    }
  }
});
