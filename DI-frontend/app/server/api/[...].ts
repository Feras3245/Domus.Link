import { joinURL } from "ufo";

export default defineEventHandler(async (request) => {
    const config = useRuntimeConfig();
    const base_url = config.private.laravel_base_url;
    const crypto_key = config.private.crypto_pass;

    const target = joinURL(base_url, request.path);

    if (!getCookie(request, 'user_session')) {
        throw createError({
            statusMessage: "UNAUTHERIZED",
            message: "You are unautherized to complete this action",
            statusCode: 401
        });
    }
    
    const session = await getSession(request, {
        name: 'user_session',
        password: crypto_key
    });
    
    return proxyRequest(request, target, {
        headers: {
            "Accept": getHeader(request, "Accept"),
            "Authorization": `Bearer ${session.data.token}`,
        }
    })
})