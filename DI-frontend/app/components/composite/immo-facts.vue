<script setup lang="ts">
import type { Immobilie } from '~/types/domain';
const { t, locale } = useI18n();

const props = defineProps<{
    verbose: boolean,
    immobilie: Immobilie
}>();
</script>

<template>
    <div class="facts flex-col" v-bind="$attrs" v-if="verbose">
        <div class="collection flex-col">
            <h6><Icon name="info" :size="1.4"/> {{ t('general') }}</h6>
            <ul class="list flex-col">
                <li class="flex-row">
                    <span class="head">{{ t('location') }}</span>
                    <span class="tail">{{ immobilie.zip }} {{ immobilie.city }}</span>
                </li>
                <li class="flex-row">
                    <span class="head">{{ t('address') }}</span>
                    <span class="tail">{{ immobilie.address }}</span>
                </li>
                <li class="flex-row"><span class="head">{{ t('usage') }}</span><span class="tail">{{ t(immobilie.usage) }}</span></li>
            </ul>
        </div>
        <div class="collection flex-col">
            <h6><Icon name="house" :size="1.4"/> {{ t('building') }}</h6>
            <ul class="list flex-col">
                <li class="flex-row"><span class="head">{{ t('year') }}</span><span class="tail">{{ immobilie.year }}</span></li>
                <li class="flex-row"><span class="head">{{ t('units') }}</span><span class="tail">{{ immobilie.units.length }}</span></li>
                <li class="flex-row"><span class="head">{{ t('project') }}</span><span class="tail">{{ t(immobilie.type) }}</span></li>
                <li class="flex-row"><span class="head">{{ t('rooms') }}</span><span class="tail">{{ (immobilie.units.length === 1) ? immobilie.stats.rooms.min : (immobilie.stats.rooms.min + ' - ' + immobilie.stats.rooms.max) }}</span></li>
                <li class="flex-row"><span class="head">{{ t('area') }}</span><span class="tail">{{ (immobilie.units.length === 1) ? immobilie.stats.area.min : (immobilie.stats.area.min + ' - ' + immobilie.stats.area.max) }} m²</span></li>
            </ul>
        </div>
        <div class="collection flex-col">
            <h6><Icon name="moneybag" :size="1.4"/> {{ t('financials') }}</h6>
            <ul class="list flex-col">
                <li class="flex-row"><span class="head">{{ t('price') }}</span><span class="tail">{{ (immobilie.units.length === 1) ? immobilie.stats.price.min : (immobilie.stats.price.min + ' - ' + immobilie.stats.price.max) }} &euro;</span></li>
                <li class="flex-row"><span class="head">{{ t('rent') }}</span><span class="tail">{{ (immobilie.units.length === 1) ? immobilie.stats.rent.min : (immobilie.stats.rent.min + ' - ' + immobilie.stats.rent.max) }} &euro;</span></li>
                <li class="flex-row"><span class="head">{{ t('yield') }}</span><span class="tail">{{ (immobilie.units.length === 1) ? immobilie.stats.yield.min : (immobilie.stats.yield.min + ' - ' + immobilie.stats.yield.max) }} %</span></li>
            </ul>
        </div>
    </div>

    <div class="facts brief flex-col" v-bind="$attrs" v-else>
        <ul class="list flex-col">
            <li class="flex-row"><span class="head"><CalendarIcon/>{{ t('year') }}</span><span class="tail">{{ immobilie.year }}</span></li>
            <li class="flex-row"><span class="head"><SquareGridIcon/>{{ t('units') }}</span><span class="tail">{{ immobilie.units.length }}</span></li>
            <li class="flex-row"><span class="head"><HomeBuyIcon/>{{ t('price') }}</span><span class="tail">{{ (immobilie.units.length === 1) ? immobilie.stats.price.min : (immobilie.stats.price.min + ' - ' + immobilie.stats.price.max) }} &euro;</span></li>
            <li class="flex-row"><span class="head"><EuroIcon/>{{ t('rent') }}</span><span class="tail">{{ (immobilie.units.length === 1) ? immobilie.stats.rent.min : (immobilie.stats.rent.min + ' - ' + immobilie.stats.rent.max) }} &euro;</span></li>
            <li class="flex-row"><span class="head"><RulerIcon/>{{ t('area') }}</span><span class="tail">{{ (immobilie.units.length === 1) ? immobilie.stats.area.min : (immobilie.stats.area.min + ' - ' + immobilie.stats.area.max) }} m²</span></li>
            <li class="flex-row"><span class="head"><PercentIcon/>{{ t('yield') }}</span><span class="tail">{{ (immobilie.units.length === 1) ? immobilie.stats.yield.min : (immobilie.stats.yield.min + ' - ' + immobilie.stats.yield.max) }} %</span></li>
        </ul>
    </div>
</template>

<style lang="css" scoped>
.facts.brief>.list>.flex-row {
    padding: 10px;
    border-radius: 10px;
    background-color: var(--contrast-normal);
}

.flex-col {
    display: flex;
    flex-flow: column nowrap;
    align-items: stretch;
    gap: 10px;
}

.collection>h6>:deep(.icon){
    height: calc(var(--font-size) * 1.4);
}

.collection>h6 {
    font-size: calc(var(--font-size) * 1.4);
    font-weight: 700;
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background-color: var(--pink);
    color: var(--contrast-normal);
    border-radius: 10px;
}

.flex-row>.head>:deep(svg) {
    height: var(--font-size);
}

.flex-row>.head {
    font-weight: 600;
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 5px;
}

.flex-row {
    display: flex;
    flex-flow: row;
    justify-content: space-between;
}
</style>

<i18n lang="json">
{
  "en": {
    "general": "General",
    "location": "Location",
    "address": "Street Address",
    "building": "Building",
    "year": "Year Built",
    "project": "Project Type",
    "rooms": "Rooms",
    "area": "Total Area",
    "financials": "Financials",
    "price": "Purchase Price",
    "rent": "Monthly Rent",
    "yield": "Rental Yield p. a.",
    "usage": "Usage type",
    "WOHNEN":"Residential",
    "PFLEGE":"Care",
    "MICRO":"Micro",
    "type": "Project type",
    "NEUBAU":"New",
    "BESTAND":"Existing",
    "units": "Units"
  },

  "de": {
    "general": "Allgemein",
    "location": "Standort",
    "address": "Straßenadresse",
    "building": "Gebäude",
    "year": "Baujahr",
    "project": "Projekttyp",
    "rooms": "Zimmer",
    "area": "Gesamtfläche",
    "financials": "Finanzen",
    "price": "Kaufpreis",
    "rent": "Monatliche Miete",
    "yield": "Mietrendite p. a.",
    "usage": "Nutzungstyp",
    "WOHNEN": "Wohnen",
    "PFLEGE": "Pflege",
    "MICRO": "Mikro",
    "type": "Projekttyp",
    "NEUBAU": "Neubau",
    "BESTAND": "Bestand",
    "units": "Einheiten"
  }
}
</i18n>