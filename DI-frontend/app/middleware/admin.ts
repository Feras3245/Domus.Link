import type { User } from "~/types/auth";

export default defineNuxtRouteMiddleware(async (to, from) => {
    const user = useState<User>('user');
    if (user.value.role !== 'ADMIN') {
        return createError({
            statusMessage: "UNAUTHERIZED",
            message: "You are unautherized to complete this action",
            statusCode: 401
        });
    }
});