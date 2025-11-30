<script lang="ts" setup>
import type { NuxtError } from '#app';
import { AuthStatus, type User } from '~/types/auth';
const { t } = useI18n();

definePageMeta({
    layout: false
})



const background = computed(() => {
    const img = useImage()
    const imgUrl = img('/home-hero-3.png', { format: 'webp' }, { sizes: 'xs:100vw sm:100vw md:100vw lg:100vw xl:100vw' })

    return `url('${imgUrl}')`;
});

const logo_size = ref<number>(1);
const passHidden = ref<boolean>(true);

function resizeEvent() {
    if (window.innerWidth > 1280)
        logo_size.value = 5;
    else {
        logo_size.value = 3 + (((2 * window.innerWidth) - 1275) / 855);
    }
}

const name = ref<string>('');
const email = ref<string>('');
const password = ref<string>('');
const password_confirmation = ref<string>('');
const remember = ref<boolean>(false);
const authStatus = ref<AuthStatus | undefined>(undefined);
const showForm = ref<'LOGIN' | 'REGISTER'>('LOGIN');
const userStore = useUserStore();


async function login() {
    try {
        await userStore.login(email.value, password.value, remember.value);
    } catch (error) {
        authStatus.value = (((error as NuxtError).data as NuxtError).data as AuthStatus);
    }
}

const messageColor = computed(() => {
    if (authStatus.value === AuthStatus.INVALID_CREDENTIALS)
        return 'color: var(--red);';
    if (authStatus.value === AuthStatus.INTERNAL_SERVER_ERROR)
        return 'color: var(--red);';
    if (authStatus.value === AuthStatus.UNKNOWN_ERROR)
        return 'color: var(--red);';
    if (authStatus.value === AuthStatus.EMAIL_TAKEN)
        return 'color: var(--red);';
    return 'color: var(--orange);'
});

function switchForm() {
    showForm.value = (showForm.value === 'LOGIN') ? 'REGISTER' : 'LOGIN';
    authStatus.value = undefined;
    name.value = '';
    email.value = '';
    password.value = '';
    password_confirmation.value = '';
    remember.value = false;
    passHidden.value = true;
}

async function register() {
    try {
        await userStore.register(name.value, email.value, password.value, password_confirmation.value);
    } catch (error) {
        authStatus.value = (((error as NuxtError).data as NuxtError).data as AuthStatus);
    }
}

onMounted(() => {
    resizeEvent();
    window.addEventListener('resize', resizeEvent);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', resizeEvent);
});
</script>

<template>
    <div>
        <div class="root">
            <div class="left">
                <logo :size="logo_size" orientation="v" to="/login" />
            </div>
            <div class="right">
                <div class="top">
                    <ThemeSwitcher :size="1.2" />
                    <LocaleSwitcher :size="1.2" />
                </div>
                <logo :size="logo_size" orientation="h" class="mobile-logo" to="/login" />
                <h1>{{ t('welcome') }}</h1>
                <div class="content">
                    <div class="divider"><span class="line"></span></div>
                    <div class="login" v-if="showForm === 'LOGIN'">
                        <h2>{{ t('login') }}</h2>
                        <form id="login-form" method="post" tabindex="0" @submit.prevent="login">
                            <TextInput v-model="email" name="email" form="login-form" type="email"
                                :placeholder="t('email')" :label-text="t('email')"></TextInput>
                            <div class="password-row">
                                <TextInput v-model="password" style="flex: 1 1 auto" name="password" form="login-form"
                                    :type="(passHidden) ? 'password' : 'text'" :placeholder="t('password')"
                                    :label-text="t('password')" autocomplete="off"></TextInput>
                                <Button variant="tertiary" style="padding: 0 0 11px 0;"
                                    @click="passHidden = !passHidden">
                                    <ShowIcon v-if="passHidden" />
                                    <HideIcon v-else />
                                </Button>
                            </div>
                            <div class="message-row" :style="messageColor" v-if="authStatus">
                                <WarningIcon style="height: var(--font-size);" />
                                <p v-if="authStatus === AuthStatus.INVALID_CREDENTIALS">{{ t('login-message.invalid-credentials') }}</p>
                                <p v-if="authStatus === AuthStatus.MISSING_FIELD">{{ t('login-message.missing-field') }}</p>
                                <p v-if="authStatus === AuthStatus.INTERNAL_SERVER_ERROR">{{ t('login-message.server-error') }}</p>
                                <p v-if="authStatus === AuthStatus.UNKNOWN_ERROR">{{ t('login-message.unknown-error') }}</p>
                            </div>
                            <div class="row">
                                <Checkbox v-model="remember" name="remember-user" form="login-form">{{ t('remember') }}
                                </Checkbox>
                                <Button variant="primary" form="login-form" type="submit">
                                    <LoginIcon />{{ t('login') }}
                                </Button>
                            </div>
                            <div class="row">
                                <Button variant="secondary" @click="switchForm">{{ t('create-acc') }}</Button>
                                <a href="/nowhere" class="link">{{ t('forgot') }}?</a>
                            </div>
                        </form>
                    </div>
                    <div class="register" v-if="showForm === 'REGISTER'">
                        <h2>{{ t('register') }}</h2>
                        <form id="register-form" method="post" tabindex="0" @submit.prevent="register">
                            <TextInput v-model="name" name="name" form="register-form" type="text"
                                placeholder="Name" label-text="Name"></TextInput>
                            <TextInput v-model="email" name="email" form="register-form" type="email"
                                :placeholder="t('email')" :label-text="t('email')"></TextInput>
                            <TextInput v-model="password" style="flex: 1 1 auto" name="password" form="register-form"
                                type="password" :placeholder="t('password')" :label-text="t('password')"
                                autocomplete="off"></TextInput>
                            <TextInput v-model="password_confirmation" style="flex: 1 1 auto"
                                name="password_confirmation" form="register-form" type="password"
                                :placeholder="t('confirm')" :label-text="t('confirm')" autocomplete="off"></TextInput>
                            <div class="message-row" :style="messageColor" v-if="authStatus">
                                <WarningIcon style="height: var(--font-size);" />
                                <p v-if="authStatus === AuthStatus.MISSING_FIELD">{{ t('login-message.missing-field') }}</p>
                                <p v-if="authStatus === AuthStatus.INVALID_NAME">{{ t('login-message.invalid-name') }}</p>
                                <p v-if="authStatus === AuthStatus.INVALID_EMAIL">{{ t('login-message.invalid-email') }}</p>
                                <p v-if="authStatus === AuthStatus.INVALID_PASSWORD">{{ t('login-message.invalid-password') }}</p>
                                <p v-if="authStatus === AuthStatus.INVALID_PASS_CONFIRMATION">{{ t('login-message.pass-mismatch') }}</p>
                                <p v-if="authStatus === AuthStatus.EMAIL_TAKEN">{{ t('login-message.already-taken') }}</p>
                                <p v-if="authStatus === AuthStatus.INTERNAL_SERVER_ERROR">{{ t('login-message.server-error') }}</p>
                                <p v-if="authStatus === AuthStatus.UNKNOWN_ERROR">{{ t('login-message.unknown-error') }}</p>
                            </div>
                            <div class="row">
                                <Button variant="secondary" @click="switchForm">{{ t('login') }}</Button>
                                <Button variant="primary" form="register-form" type="submit">
                                    <RegisterIcon />{{ t('create-acc') }}
                                </Button>
                            </div>
                        </form>
                    </div>
                    <div class="divider"><span class="line"></span>{{ t('or') }}<span class="line"></span></div>
                    <div class="social">
                        <Button variant="primary" style="background-color: #EB4335;"><span>
                                <GoogleIcon />
                                <p>Google</p>
                            </span></Button>
                        <Button variant="primary" style="background-color: grey;"><span>
                                <MicrosoftIcon />
                                <p>Microsoft</p>
                            </span></Button>
                        <Button variant="primary" style="background-color: #4460A0;"><span>
                                <FacebookIcon />
                                <p>Facebook</p>
                            </span></Button>
                    </div>
                </div>
                <p class="copyrights">© Copyright 2025 Deutschland.Immobilien GmbH</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.register {
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    align-self: stretch;
}

.login {
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    align-self: stretch;
}

.message-row {
    display: flex;
    flex-flow: row nowrap;
    justify-content: flex-start;
    gap: 10px;
}

.password-row {
    display: flex;
    flex-flow: row nowrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 10px;
}

.copyrights {
    font-size: calc(var(--font-size) * 0.7);
    margin-top: auto;
}

.mobile-logo {
    display: none;
}

.right>.top {
    display: flex;
    flex-flow: row nowrap;
    justify-content: flex-end;
    align-items: center;
    width: 100%;
}

.social>:deep(.button) {
    padding: 0;
    padding-right: 20px;
    overflow: hidden;
    background-color: var(--orange);
    width: 50%;
}

.social>:deep(.button:hover) {
    background-color: var(--pink) !important;
}

.social span {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    justify-content: flex-start;
    flex: 1 1 auto;

    &>p {
        margin: auto;
    }

    &:deep(svg) {
        backdrop-filter: brightness(80%);
        padding: 10px 20px;
    }
}

.social {
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    gap: 10px;
    align-self: stretch;
}

.divider {
    align-self: stretch;
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 10px;
}

.divider>.line {
    height: 1px;
    background-color: var(--contrast-reverse);
    flex: 1 1 auto;
}

form>.row {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    justify-content: space-between;
}

.root {
    background-image: v-bind(background);
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
    min-height: 100vh;
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
}

.left {
    margin: auto;
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    background-color: rgba(from var(--contrast-normal) r g b / 50%);
    backdrop-filter: blur(15px);
    padding: 20px;
    border-radius: 20px;
}

.right {
    background-color: var(--contrast-normal);
    min-height: 100vh;
    width: 50%;
    box-sizing: border-box;
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    justify-content: flex-start;
    padding: 10px 30px;
    gap: 20px;
}

form {
    display: flex;
    flex-flow: column nowrap;
    align-self: stretch;
    gap: 20px;
    align-items: stretch;
}

h2 {
    font-size: calc(var(--font-size) * 2.2);
    font-weight: 700;
}

.content {
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    padding: 20px 40px;
    border-radius: 20px;
    width: 80%;
    gap: 20px;
}

.right>h1 {
    font-family: var(--font-alt);
    font-size: calc(var(--font-size) * 2.5);
    color: var(--pink);
}

@media screen and (max-width: 1140px) {
    .social>:deep(.button) {
        padding: 0;
        padding-right: 20px;
        overflow: hidden;
        background-color: var(--orange);
        width: 80%;
    }
}

@media screen and (max-width: 880px) {
    .content {
        padding: 0;
    }

    .right {
        padding-left: 10px;
        padding-right: 10px;
    }
}

@media screen and (max-width: 768px) {
    .mobile-logo {
        display: flex;
    }

    .content {
        width: 100%;
        padding: 10px;
    }

    .root {
        flex-flow: column nowrap;
    }

    .left {
        display: none;
    }

    .right {
        padding: 15px;
        width: 80%;
        min-height: fit-content;
        margin: 10% 0;
        border-radius: 20px;
    }
}

@media screen and (max-width: 425px) {
    .right {
        width: 90%;
    }

    form>.row {
        flex-flow: column nowrap;
        align-items: stretch;
        gap: 10px;
    }
}
</style>

<i18n lang="json">
{
    "en": {
        "welcome": "Welcome",
        "login": "Sign in",
        "email": "E-Mail",
        "password": "Password",
        "consfirm": "Confirm Password",
        "remember": "Remember me",
        "register": "Sign up",
        "create-acc": "Register",
        "forgot": "Forgot password",
        "or": "or",
        "login-message": {
            "invalid-credentials": "The credentials that you provided are incorrect",
            "missing-field": "Please fill in all required fields",
            "server-error": "Unexpected server error, please try again later",
            "unknown-error": "Something went wrong, please try again later",
            "invalid-name": "The name you provided is invalid, please use a different one",
            "invalid-email": "The E-mail you provided is invalid, please use a different one",
            "invalid-password": "The password you provided is invalid, please use a different one",
            "pass-mismatch": "Password must match the password confirmation",
            "already-taken": "A user with the provided E-mail already exists"
        }
    },
    "de": {
        "welcome": "Willkommen",
        "login": "Anmelden",
        "email": "Email",
        "password": "Passwort",
        "confirm": "Passwort bestätigen",
        "remember": "Angemeldet bleiben",
        "register": "Registrieren",
        "create-acc": "Registerien",
        "forgot": "Passwort vergessen",
        "or": "oder",
        "login-message": {
            "invalid-credentials": "Die von Ihnen angegebenen Anmeldedaten sind falsch",
            "missing-field": "Bitte füllen Sie alle erforderlichen Felder aus",
            "server-error": "Unerwarteter Serverfehler, bitte versuchen Sie es später erneut",
            "unknown-error": "Etwas ist schiefgelaufen, bitte versuchen Sie es später erneut",
            "invalid-name": "Der angegebene Name ist ungültig, bitte verwenden Sie einen anderen",
            "invalid-email": "Die angegebene E-Mail ist ungültig, bitte verwenden Sie eine andere",
            "invalid-password": "Das angegebene Passwort ist ungültig, bitte verwenden Sie ein anderes",
            "pass-mismatch": "Das Passwort muss mit der Passwortbestätigung übereinstimmen",
            "already-taken": "Ein Benutzer mit der angegebenen E-Mail existiert bereits"
        }
    }
}
</i18n>