import { type User } from "~/types/auth";

export const useUserStore = defineStore('user', () => {
    const user = ref<User>();


    async function fetchUser() {
        try {
            const csrf = useCsrf();
            const fetchedUser = await $fetch<User>('/api/auth/user', {
                method: "GET",
                headers: {...useRequestHeaders(), "csrf-token": csrf.csrf}
            });
            user.value = fetchedUser;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async function login(email:string, password:string, remember:boolean) {
        try {
            const csrf = useCsrf();
            const response = await $fetch<User>('/api/auth/login', {
                method: "POST",
                headers: {...useRequestHeaders(), "csrf-token": csrf.csrf},
                body: {
                    email,
                    password,
                    remember
                }
            });
            user.value = response;
            return navigateTo('/', {external: true});
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async function register(name:string, email:string, password:string, password_confirmation:string) {
        try {
            const csrf = useCsrf();
            const response = await $fetch<User>('/api/auth/register', {
                method: "POST",
                headers: {...useRequestHeaders(), "csrf-token": csrf.csrf},
                body: {
                    name,
                    email,
                    password,
                    password_confirmation
                }
            });
            user.value = response;
            return navigateTo('/', {external: true});
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async function logout() {
        try {
            const csrf = useCsrf();
            const response = await $fetch('/api/auth/logout', {
                method: 'POST',
                headers: {...useRequestHeaders(), "csrf-token": csrf.csrf}
            });
            user.value = undefined;
            return navigateTo('/login', {external: true});
        } catch(error) {
            console.error(error);
            throw error;
        }
    }

    return {user, fetchUser, login, register, logout};
});