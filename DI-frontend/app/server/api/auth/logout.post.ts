import { AuthStatus, UserSession } from "~/types/auth";

export default defineEventHandler(async (request) => {
    if (!getCookie(request, 'user_session')) {
        throw createError({
            statusMessage: "UNAUTHERIZED",
            message: "user is not logged in",
            statusCode: 401,
            data: AuthStatus.NO_SESSION_COOKIE
        });
    }

    const config = useRuntimeConfig(request);
    const base_url = config.private.laravel_base_url;
    const crypto_key = config.private.crypto_pass;

    const session = await getSession(request, {
        name: 'user_session',
        password: crypto_key
    });

    try {
        await clearSession(request, {
            name: 'user_session',
            password: crypto_key
        });
        deleteCookie(request, 'user_session');
        const response = await $fetch(base_url + 'api/logout', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${session.data.token}`
            },
        });
        return AuthStatus.AUTH_SUCCESS;
    } catch (reason: any) {
        const code = reason.statusCode || 500;
        let data = AuthStatus.UNKNOWN_ERROR;
        let message = "either backend server is offline or an unhandled error is encountered";
        if (code === 401) {
            data = AuthStatus.INVALID_CREDENTIALS;
            message = "invalid credentials";
        } else if (code === 500) {
            data = AuthStatus.INTERNAL_SERVER_ERROR;
            message = "backend server encountered an internal error";
        }
        throw createError({ statusCode: code, message, data });
    }
});