import { joinURL } from "ufo";
import { AuthStatus, UserSession } from "~/types/auth";

export default defineEventHandler(async (request) => {
    if (getCookie(request, 'user_session')) {
        throw createError({
            statusMessage: "CONFLICT",
            message: "cannot register a new user when user is logged in",
            statusCode: 409,
            data: AuthStatus.ALREADY_AUTHENTICATED
        });
    }

    const { name, email, password, password_confirmation } = await readBody(request);

    if (!email || !password || !name || !password_confirmation) {
        throw createError({
        statusCode: 422,
        message: "A LOGIN FIELD IS MISSING",
        data: AuthStatus.MISSING_FIELD
        });
    }

    if (!nameValidator(name).success) {
        throw createError({
            statusMessage: "UNPROCESSABLE CONTENT",
            statusCode: 422,
            message: "invalid name",
            data: AuthStatus.INVALID_NAME
        });
    }

    if (!emailValidator(email).success) {
        throw createError({
            statusMessage: "UNPROCESSABLE CONTENT",
            statusCode: 422,
            message: "invalid email",
            data: AuthStatus.INVALID_EMAIL
        });  
    }

    if (password !== password_confirmation) {
        throw createError({
            statusMessage: "UNPROCESSABLE CONTENT",
            statusCode: 422,
            message: "password does not match password confirmation",
            data: AuthStatus.INVALID_PASS_CONFIRMATION
        });
    }

    if (!passwordValidator(password).success) {
        throw createError({
            statusMessage: "UNPROCESSABLE CONTENT",
            statusCode: 422,
            message: "invalid password",
            data: AuthStatus.INVALID_PASSWORD
        });
    }

    const config = useRuntimeConfig(request);
    const base_url = config.private.laravel_base_url;
    const crypto_key = config.private.crypto_pass;

    try {
        const target = joinURL(base_url, 'api/register');
        const response = await $fetch<UserSession>(target, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: { name, email, password, password_confirmation }
        });

        const session = await useSession(request, {
            name: 'user_session',
            password: crypto_key,
            cookie: {
                path: '/',
                sameSite: 'strict',
                httpOnly: true
            }
        });

        await session.update({
            user: response.user,
            token: response.token
        });

        return response.user;
    } catch (reason: any) {
        const code = reason.statusCode;
        let data = AuthStatus.UNKNOWN_ERROR;
        let message = "either backend server is offline or an unhandled error is encountered";
        if (code === 422) {
            data = AuthStatus.EMAIL_TAKEN;
            message = "a user with the provided email already exists";
        } else if (code === 500) {
            data = AuthStatus.INTERNAL_SERVER_ERROR;
            message = "backend server encountered an internal error";
        }
        throw createError({ statusCode: code, message, data });
    }
});