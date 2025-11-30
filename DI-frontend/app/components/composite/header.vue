<script setup lang="ts">
const { t, locale } = useI18n();
const props = defineProps<{
    page: 'INDEX'|'WOHNEN'|'PFLEGE'|'MICRO'|'NEUBAU'|'BESTAND'
}>();

const background = computed(() => {
    const img = useImage()
    const imgUrl = img(`header/${props.page.toLocaleLowerCase()}.webp`, { format: 'webp' }, { sizes: 'xs:20vw sm:40vw md:60vw lg:80vw xl:100vw' })
    return `url('${imgUrl}')`;
});
</script>

<template>
    <header>
      <nuxt-img :src="`header/${props.page.toLocaleLowerCase()}.webp`" class="bkg-img" 
      :draggable="false" sizes="xs:20vw sm:40vw md:60vw lg:80vw xl:100vw"></nuxt-img>
        <div class="message">
            <h1>{{ t(`title.${page}`) }}</h1>
            <p>{{ t(`subtitle.${page}`) }}</p>
        </div>
    </header>
</template>

<style lang="css" scoped>
.bkg-img {
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  object-fit: cover;
  width: 100%;
  height: 100%;
  position: absolute;
}

.message>h1 {
    font-size: calc(var(--font-size) * 2);
    font-family: var(--font-alt);
}

.message>p {
    color: var(--pink);
    font-weight: 500;
    font-size: calc(var(--font-size) * 1.3);
}

.message {
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    justify-content: center;
    background-color: var(--contrast-normal);
    z-index: 100;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0px 5px 19px -7px rgba(0,0,0,1);
    gap: 10px;
    text-align: center;
    margin: 10px;
}

header {
  width: 100%;
  height: 500px;
  overflow: hidden;
  position: relative;
  display: flex;
  flex-flow: column;
  align-items: center;
  justify-content: center;
}
</style>

<i18n lang="json">
{
  "en": {
    "next": "Next",
    "previous": "Previous",
    "learn": "Learn more",
    "title": {
      "INDEX": "Welcome",
      "WOHNEN": "Residential Properties",
      "PFLEGE": "Nursing Care Real Estate",
      "MICRO": "Micro and Student Apartments",
      "NEUBAU": "New Construction",
      "BESTAND": "Existing Real Estate"
    },
    "subtitle": {
      "INDEX": "Your Gateway to Smart Real Estate Investments",
      "WOHNEN": "Secure and Steady Returns from Rental Income",
      "PFLEGE": "Invest with Social Impact in a Future-Proof Market",
      "MICRO": "High-Demand, Efficient Investments for Modern Urban Living",
      "NEUBAU": "Benefit from Modern Standards and First-Time Rental Guarantees",
      "BESTAND": "Immediate Returns from Proven and Established Properties"
    }
  },
  "de": {
    "next": "Nächste",
    "previous": "Vorherige",
    "learn": "Mehr erfahren",
    "title": {
      "INDEX": "Willkommen",
      "WOHNEN": "Wohnimmobilien",
      "PFLEGE": "Pflegeimmobilien",
      "MICRO": "Mikro- und Studentenapartments",
      "NEUBAU": "Neubau",
      "BESTAND": "Bestandsimmobilien"
    },
    "subtitle": {
      "INDEX": "Ihr Einstieg in intelligente Immobilieninvestitionen",
      "WOHNEN": "Sichere und stabile Renditen aus Mieteinnahmen",
      "PFLEGE": "Investieren mit sozialer Verantwortung in einen zukunftssicheren Markt",
      "MICRO": "Gefragte und effiziente Investments für modernes, urbanes Wohnen",
      "NEUBAU": "Profitieren Sie von modernen Standards und Erstvermietungsgarantien",
      "BESTAND": "Sofortige Renditen aus bewährten und etablierten Objekten"
    }
  }
}
</i18n>