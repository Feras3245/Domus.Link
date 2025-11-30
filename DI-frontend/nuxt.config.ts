// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',
  devtools: { enabled: true },
  srcDir: './app',

  modules: ['@nuxt/fonts', '@nuxt/image', '@nuxt/scripts', '@nuxt/ui', '@nuxtjs/i18n', 'nuxt-security', 'vue3-carousel-nuxt', '@pinia/nuxt'],

  app: {
    head: {
      title: 'Domus.Link',
      meta: [
        { name: "author", content: "Feras Hennidi" },
        { name: "description", content: "Eine App zur Präsentation von Immobilien in Deutschland, entwickelt von der Deutschland.Immobilien GmbH" },
        { name: "robots", content: "all" },
        { name: "application-name", content: "Domus.Link" }
      ],
      bodyAttrs: {class: 'app'},
    },
  },

  runtimeConfig: {

    public: {
      nuxt_base_url: process.env.NUXT_PUBLIC_URL_BASE
    },

    private: {
      laravel_base_url: process.env.LARAVEL_PUBLIC_URL_BASE,
      crypto_pass: process.env.NUXT_ENCRYPTION_SECRET
    }
  },

  security: {
    csrf: {
      cookie: {
        httpOnly: true,
        sameSite: 'strict',
        secure: true,
      },
      encryptSecret: process.env.NUXT_ENCRYPTION_SECRET,
      cookieKey: 'csrf_token'
    },
    headers: {
      contentSecurityPolicy: {
        "img-src": ["'self'", 'data:', 'blob:']
      }
    },
    hidePoweredBy: true
  },

  css: ['vue-multiselect/dist/vue-multiselect.css', '@/assets/styles/main.css'],

  i18n: {
    vueI18n: '@@/i18n.config.ts',
    strategy: 'no_prefix',
    locales: [
      {
        code: 'de',
        language: 'de-DE',
        name: 'Deutsch'
      },
      {
        code: 'en',
        language: 'en-US',
        name: 'English'
      }
    ],
    defaultLocale: 'de',
    bundle: {
      optimizeTranslationDirective: false,
    },

    detectBrowserLanguage: {
      useCookie: true,
      cookieKey: 'preferred_language',
      redirectOn: 'root'
    }
  },
  
  components: [
    {
      path: '~/components',
      pathPrefix: false,
    },
  ],

  image: {
    quality: 100,
    dir: 'assets/images',
    format: ['webp', 'png', 'jpg'],
    screens: {
      xs: 320,
      sm: 640,
      md: 768,
      lg: 1024,
      xl: 1280,
      xxl: 1536,
      '2xl': 1536
    },
  },
})