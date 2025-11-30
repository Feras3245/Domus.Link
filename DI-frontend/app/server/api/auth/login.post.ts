
import { UserSession, AuthStatus } from '~/types/auth';
import { emailValidator } from '../../utils/validators';
import { joinURL } from 'ufo';

export default defineEventHandler(async (request) => {

  if (getCookie(request, "user_session")) {
    throw createError({
      statusCode: 422,
      message: "USER IS ALREADY AUTHENTICATED",
      data: AuthStatus.ALREADY_AUTHENTICATED
    });
  }

  const config = useRuntimeConfig(request);
  const base_url = config.private.laravel_base_url;
  const crypto_key = config.private.crypto_pass;
  const { email, password, remember } = await readBody(request);

  if (!email || !password || remember === undefined) {
    throw createError({
      statusCode: 422,
      message: "A LOGIN FIELD IS MISSING",
      data: AuthStatus.MISSING_FIELD
    });
  }

  if (!emailValidator(email).success) {
    throw createError({
      statusCode: 422,
      message: "INVALID EMAIL",
      data: AuthStatus.INVALID_EMAIL
    });
  }

  try {
    const target = joinURL(base_url, 'api/login');
    const response = await $fetch<UserSession>(target, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: { email, password }
    });
    const maxAge = remember ? (60 * 60 * 24 * 7 * 2) : undefined;

    const session = await useSession(request, {
      name: 'user_session',
      password: crypto_key,
      cookie: {
        path: '/',
        sameSite: 'strict',
        httpOnly: true,
        maxAge: maxAge
      }
    });

    await session.update({
      user: response.user,
      token: response.token
    });

    console.log("USER_TOKEN: " + response.token);

    return response.user;

  } catch (reason: any) {
    const code = reason.statusCode;
    let data = AuthStatus.UNKNOWN_ERROR;
    let message = "either backend server is offline or an unhandled error is encountered";

    if (code === 401) {
      data = AuthStatus.INVALID_CREDENTIALS;
      message = "invalid credentials";
    } else if (code === 422) {
      data = AuthStatus.SERVER_VALIDATION_ERROR;
      message = "backend server detected invalid input data";
    } else if (code === 500) {
      data = AuthStatus.INTERNAL_SERVER_ERROR;
      message = "backend server encountered an internal error";
    }

    throw createError({ statusCode: code, message, data });
  }
});
