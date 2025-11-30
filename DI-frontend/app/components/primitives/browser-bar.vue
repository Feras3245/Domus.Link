<script setup lang="ts">
const { t, locale } = useI18n();
import VueMultiselect from 'vue-multiselect';
import type { BrowserParams } from '~/types/nav';

const all = defineModel<BrowserParams>();

const showFilters = ref<boolean>(false);

const order = ref<number>(0);

watch(order, (val) => {
    if (all.value) {
        all.value.options.sortBy.order = (val === 0) ? 'ASC' : 'DSC';
    }
});

const emit = defineEmits(['apply', 'reset']);
</script>

<template>
<div class="browser-bar" v-if="all">
    <div class="options">
        <div class="option">
            <span class="label">{{ t('view') }}</span>
            <ToggleButton :options="[{icon: 'square-grid', text: `${t('grid')}`, title: `${t('grid')}`}, {icon: 'rectangle-grid', text: `${t('list')}`, title: `${t('list')}`}]" v-model:multiple="all.options.viewAs"/>
        </div>
        <div class="option">
            <span class="label">{{ t('order') }}</span>
            <div>
            <VueMultiselect
            style="width: 130px;z-index: 101;"
            id="order-selector"
            :multiple="false" :taggable="true"
            :show-labels="false"
            :searchable="false"
            :preselect-first="true"
            :allowEmpty="false"
            v-model="all.options.sortBy.sort"
            :options="['NONE', 'PRICE', 'YIELD', 'RENT', 'AREA']">
            <template #singleLabel="props">
                {{ t(`order-dropdown.${props.option}`) }}
            </template>
            <template #option="props">
                {{ t(`order-dropdown.${props.option}`) }}
            </template>
            </VueMultiselect>
            <ToggleButton :options="[{icon: 'asc-order', title: `${t('ascending')}`}, {icon: 'dsc-order', title: `${t('descending')}`}]" v-model:multiple="order"/>
            </div>
        </div>
        
        <!-- <div class="option">
            <span class="label">{{ t('order') }}</span>
            <VueMultiselect
            style="width: 130px;z-index: 101;"
            id="order-selector"
            :multiple="false" :taggable="true"
            :show-labels="false"
            :searchable="false"
            :preselect-first="true"
            :allowEmpty="false"
            v-model="all.options.sortBy.sort"
            :options="['NONE', 'PRICE', 'YIELD', 'RENT', 'AREA']">
            <template #singleLabel="props">
                {{ t(`order-dropdown.${props.option}`) }}
            </template>
            <template #option="props">
                {{ t(`order-dropdown.${props.option}`) }}
            </template>
            </VueMultiselect>
        </div> -->
        <div class="option">
            <ToggleButton :options="[{icon: 'filter', text: 'Filter', title: 'Filter'}]" v-model:single="showFilters"/>
        </div>
        <div class="option">
            <Button @click="$emit('reset')" variant="secondary"><ResetIcon/>{{ t('reset') }}</Button>
        </div>
        <div class="option">
            <Button @click="$emit('apply')" variant="primary" style="align-self: center;">{{ t('apply') }}</Button>
        </div>
    </div>
    <div class="transparent">
        <Transition name="dropdown">
            <div class="filters" v-if="showFilters">
                <div class="ranges">
                    <div class="filter">
                        <label class="label" for="price-range">{{ t('price') }}</label>
                        <div class="range-values">
                            <span>{{ all.filters.price.from }} &euro;</span>
                            <span>{{ all.filters.price.to }} &euro;</span>
                        </div>
                        <RangeSlider id="price-range" :min="all.filters.price.min" :max="all.filters.price.max" v-model:from="all.filters.price.from" v-model:to="all.filters.price.to"/>
                    </div>
                    <div class="filter">
                        <label class="label" for="rent-range">{{ t('rent') }}</label>
                        <div class="range-values">
                            <span>{{ all.filters.rent.from }} &euro;</span>
                            <span>{{ all.filters.rent.to }} &euro;</span>
                        </div>
                        <RangeSlider id="rent-range" :min="all.filters.rent.min" :max="all.filters.rent.max" v-model:from="all.filters.rent.from" v-model:to="all.filters.rent.to"/>
                    </div>
                    <div class="filter">
                        <label class="label" for="yield-range">{{ t('yield') }}</label>
                        <div class="range-values">
                            <span>{{ all.filters.yield.from }}%</span>
                            <span>{{ all.filters.yield.to }}%</span>
                        </div>
                        <RangeSlider id="yield-range" step="0.01" :min="all.filters.yield.min" :max="all.filters.yield.max" v-model:from="all.filters.yield.from" v-model:to="all.filters.yield.to"/>
                    </div>
                    <div class="filter">
                        <label class="label" for="area-range">{{ t('area') }}</label>
                        <div class="range-values">
                            <span>{{ all.filters.area.from }} m²</span>
                            <span>{{ all.filters.area.to }} m²</span>
                        </div>
                        <RangeSlider id="area-range" step="1" :min="all.filters.area.min" :max="all.filters.area.max" v-model:from="all.filters.area.from" v-model:to="all.filters.area.to"/>
                    </div>
                </div>
                <div class="dropdowns">
                    <div class="filter">
                        <label class="label" for="usage-selector">{{ t('usage') }}</label>
                        <VueMultiselect
                        id="usage-selector"
                        :multiple="false" :taggable="true"
                        :show-labels="false"
                        :searchable="false"
                        :preselect-first="false"
                        :allowEmpty="true"
                        :placeholder="t('placeholder')"
                        v-model="all.filters.usage.selected"
                        :options="all.filters.usage.options">
                        <template #singleLabel="props">
                            {{ t(props.option) }}
                        </template>
                        <template #option="props">
                            {{ t(props.option) }}
                        </template>
                        </VueMultiselect>
                    </div>
                    <div class="filter">
                        <label class="label" for="type-selector">{{ t('type') }}</label>
                        <VueMultiselect
                        id="type-selector"
                        :multiple="false" :taggable="true"
                        :show-labels="false"
                        :searchable="false"
                        :preselect-first="false"
                        :allowEmpty="true"
                        :placeholder="t('placeholder')"
                        v-model="all.filters.type.selected"
                        :options="all.filters.type.options">
                        <template #singleLabel="props">
                            {{ t(props.option) }}
                        </template>
                        <template #option="props">
                            {{ t(props.option) }}
                        </template>
                        </VueMultiselect>
                    </div>
                    <div class="filter">
                        <label class="label" for="location-selector">{{ t('locations') }}</label>
                        <VueMultiselect
                        id="location-selector"
                        :multiple="true" :taggable="false"
                        :show-labels="false"
                        :searchable="true"
                        :placeholder="t('placeholder')"
                        v-model="all.filters.locations.selected"
                        :options="all.filters.locations.options">
                        <template #noResult>
                            <span>{{ t('no-results') }}</span>
                        </template>
                        </VueMultiselect>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</div>
</template>

<style lang="css" scoped>
.option>div {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 0.5rem;
}

.option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.options {
  align-self: center;
  display: flex;
  flex-flow: row wrap;
  gap: 10px;
  justify-content: center;
  align-items: center;
  padding: 10px;
  white-space: nowrap;
}

.browser-bar {
    display: flex;
    flex-flow: column;
    gap: 10px;
    background-color: var(--input-background);
    padding: 20px 0;
    border-radius: 15px;
    box-shadow: 0px 5px 19px -7px rgba(0,0,0,1);
    max-width: 1100px;
    margin: auto;
    position: relative;
}

.transparent {
    position: absolute;
    z-index: 100;
    top: 100%;
    padding: 20px 0;
    box-sizing: border-box;
    width: 100%;
}

.label {
    line-height: 1.25rem;
    font-weight: 600;
    text-align: center;
}

.range-values {
    display: flex;
    margin-bottom: 0.5rem;
    justify-content: space-between;
}

.filter {
    display: flex;
    flex-flow: column nowrap;
    gap: 0.5rem;
}

.filters {
    display: flex;
    flex-flow: column nowrap;
    justify-items: stretch;
    align-items: stretch;
    gap: 30px;
    background-color: var(--contrast-normal);
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0px 5px 19px -7px rgba(0,0,0,1);
}

.ranges {
    display: flex;
    flex-flow: row wrap;
    gap: 15px;
    align-items: stretch;
}

.ranges>.filter {
    flex: 1 1 49%;
}

.dropdowns {
    display: flex;
    flex-flow: row wrap;
    align-items: stretch;
    gap: 15px;
}

.dropdowns>.filter {
    flex: 1 1 340px;
}


.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 200ms ease;
}

.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.dropdown-enter-to {
  opacity: 1;
  transform: translateY(0);
}

.dropdown-leave-from {
  opacity: 1;
  transform: translateY(0);
}

.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

@media screen and (max-width: 330px) {
    .option {
        flex-flow: column nowrap;
    }
}
</style>

<i18n lang="json">
{
  "en": {
    "grid": "Grid",
    "list": "List",
    "order": "Sorting",
    "view": "Viewing",
    "ascending": "Ascending",
    "descending": "Descending",
    "price": "Price",
    "rent": "Monthly Rent",
    "area": "Total Area",
    "locations": "Locations",
    "yield": "Rental Yield p. a.",
    "placeholder" : "None selected",
    "apply": "Apply",
    "reset": "Reset",
    "no-results": "No results found",
    "usage": "Usage type",
    "WOHNEN":"Residential",
    "PFLEGE":"Care",
    "MICRO":"Micro",
    "type": "Project type",
    "NEUBAU":"New",
    "BESTAND":"Existing",
    "order-dropdown": {
        "NONE": "None",
        "PRICE": "Price",
        "RENT": "Rent",
        "YIELD": "Yield",
        "AREA": "Area"
    } 
  },
  "de": {
    "grid": "Raster",
    "list": "Liste",
    "order": "Sortierung",
    "view": "Ansicht",
    "ascending": "Aufsteigend",
    "descending": "Absteigend",
    "price": "Kaufspreis",
    "rent": "Monatliche Miete",
    "area": "Gesamtfläche",
    "locations": "Standorte",
    "yield": "Mietrendite p. a.",
    "placeholder" : "Keine Auswahl",
    "apply": "Anwenden",
    "reset": "Zurücksetzen",
    "no-results": "Keine Ergebnisse gefunden",
    "usage": "Nutzungstyp",
    "WOHNEN": "Wohnen",
    "PFLEGE": "Pflege",
    "MICRO": "Mikro",
    "type": "Projekttyp",
    "NEUBAU": "Neubau",
    "BESTAND": "Bestand",
    "none": "Keiner",
    "order-dropdown": {
        "NONE": "Keiner",
        "PRICE": "Preis",
        "RENT": "Miete",
        "YIELD": "Rendite",
        "AREA": "Fläche"
    } 
  }
}
</i18n>