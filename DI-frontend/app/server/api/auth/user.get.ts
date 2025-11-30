import { AuthStatus, User, UserSession } from "~/types/auth";
import { $fetch } from 'ofetch';

export default defineEventHandler(async (request) => {
    if (!getCookie(request, 'user_session')) {
        throw createError({
            statusMessage: "UNAUTHORIZED",
            message: "user session cookie was not found in the request",
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
        const response = await $fetch<User>(base_url + 'api/user', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${session.data.token}`
            },
        });
        await updateSession(request, {
            name: 'user_session',
            password: crypto_key
        }, {
            user: response
        });
        const data = (session.data as UserSession);
        return data.user;
    } catch (reason: any) {
        const code = reason.statusCode || 500;
        let data = AuthStatus.UNKNOWN_ERROR;
        let message = "either backend server is offline or an unhandled error is encountered";
        if (code === 401) {
            await clearSession(request, {
                name: 'user_session',
                password: crypto_key
            });
            deleteCookie(request, 'user_session');
            data = AuthStatus.INVALID_CREDENTIALS;
            message = "invalid credentials";
        } else if (code === 500) {
            data = AuthStatus.INTERNAL_SERVER_ERROR;
            message = "backend server encountered an internal error";
        }
        throw createError({ statusCode: code, message, data });
    }
});