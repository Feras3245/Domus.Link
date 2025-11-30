<script setup lang="ts">
import SquareGridIcon from '../icons/square-grid-icon.vue';
import VueMultiselect from 'vue-multiselect';
import AscOrderIcon from '../icons/asc-order-icon.vue';
import DscOrderIcon from '../icons/dsc-order-icon.vue';
import FilterIcon from '../icons/filter-icon.vue';
import RectangleGridIcon from '../icons/rectangle-grid-icon.vue';
import Slider from '@vueform/slider';
import type { Options, Filters, Params } from '~/types/nav';
import { useMetaStore } from '~/stores/meta-store';
const { t, locale } = useI18n();

const showFilters = ref<boolean>(false);
const metaStore = useMetaStore();

const props = defineProps<{
    hideUsageFilter: boolean,
    hideTypeFilter: boolean,
}>();

const filters = ref<Filters>();
const options = ref<Options>();
const emits = defineEmits(['reset', 'apply', 'change-view']);

options.value = {
    viewAs: 'GRID',
    perPage: 6,
    sortBy: 'NONE',
    orderBy: 'ASC'
}

if (!metaStore.params) {
    await useAsyncData('params', () => metaStore.fetchParams(), {server: true});
}

if (metaStore.params) {
    filters.value = {
        price: [metaStore.params.price.min, metaStore.params.price.max],
        rent: [metaStore.params.rent.min, metaStore.params.rent.max],
        yield: [metaStore.params.yield.min, metaStore.params.yield.max],
        area: [metaStore.params.area.min, metaStore.params.area.max],
        locations: [],
        type: 'NONE',
        usage: 'NONE'
    }
}

function reset() {
    if (metaStore.params) {
        options.value = {
            viewAs: 'GRID',
            perPage: 6,
            sortBy: 'NONE',
            orderBy: 'ASC'
        }
        filters.value = {
            price: [metaStore.params.price.min, metaStore.params.price.max],
            rent: [metaStore.params.rent.min, metaStore.params.rent.max],
            yield: [metaStore.params.yield.min, metaStore.params.yield.max],
            area: [metaStore.params.area.min, metaStore.params.area.max],
            locations: [],
            type: 'NONE',
            usage: 'NONE'
        }
        emits('reset');
    }
}

function apply() {
    let queryParams = {};
    if (filters.value && metaStore.params && options.value) {
        if (options.value.sortBy && options.value.orderBy && (options.value.sortBy !== 'NONE')) {
            queryParams = {sort: options.value.sortBy, order: options.value.orderBy};
        }
        if (options.value.perPage && (options.value.perPage !== 6)) {
            queryParams = {per_page: options.value.perPage};
        }
        if (filters.value.type && (filters.value.type !== 'NONE')) {
            queryParams = {...queryParams, type: filters.value.type};
        }
        if (filters.value.usage && (filters.value.usage !== 'NONE')) {
            queryParams = {...queryParams, usage: filters.value.usage};
        }
        if (filters.value.price && metaStore.params.price && !(filters.value.price[0] === metaStore.params.price.min && filters.value.price[1] === metaStore.params.price.max)) {
            queryParams = {...queryParams, pmin: filters.value.price[0], pmax: filters.value.price[1]};
        }
        if (filters.value.rent && metaStore.params.rent && !(filters.value.rent[0] === metaStore.params.rent.min && filters.value.rent[1] === metaStore.params.rent.max)) {
            queryParams = {...queryParams, rmin: filters.value.rent[0], rmax: filters.value.rent[1]};
        }
        if (filters.value.yield && metaStore.params.yield && !(filters.value.yield[0] === metaStore.params.yield.min && filters.value.yield[1] === metaStore.params.yield.max)) {
            queryParams = {...queryParams, ymin: filters.value.yield[0], ymax: filters.value.yield[1]};
        }
        if (filters.value.area && metaStore.params.area && !(filters.value.area[0] === metaStore.params.area.min && filters.value.area[1] === metaStore.params.area.max)) {
            queryParams = {...queryParams, amin: filters.value.area[0], amax: filters.value.area[1]};
        }
        if (filters.value.locations && (filters.value.locations.length > 0)) {
            const combinedLocations = filters.value.locations.join(',');
            queryParams = {...queryParams, locations: combinedLocations};
        }
    }
    emits('apply', queryParams);
}
</script>

<template>
    <div class="toolbar" v-if="options && filters && metaStore.params">
        <div class="options">
            <div class="option">
                <span>{{ t('view') }}</span>
                <ToggleButton v-model="options.viewAs" @update:model-value="$emit('change-view', options.viewAs)" :options="[{icon: SquareGridIcon, value: 'GRID', title: t('grid')}, {icon: RectangleGridIcon, value: 'LIST', title: t('list')}]"/>
            </div>
            <div class="option">
                <span>{{ t('per-page') }}</span>
                <VueMultiselect
                style="width: 80px; z-index: 101;"
                id="per-page-selector"
                :multiple="false" :taggable="false"
                :show-labels="false"
                :searchable="false"
                :preselect-first="true"
                :allowEmpty="false"
                v-model="options.perPage"
                :options="[3, 6, 9, 12]"
                :title="t('per-page-title')">
                </VueMultiselect>
            </div>
            <div class="option">
                <span class="label">{{ t('sort') }}</span>
                <div class="together">
                <VueMultiselect
                style="width: 130px;z-index: 101;"
                id="sort-selector"
                :multiple="false" :taggable="false"
                :show-labels="false"
                :searchable="false"
                :preselect-first="true"
                :allowEmpty="false"
                v-model="options.sortBy"
                :options="['NONE', 'PRICE', 'YIELD', 'RENT', 'AREA']"
                :title="t('sort-title')">
                <template #singleLabel="props">
                    {{ t(`${props.option}`) }}
                </template>
                <template #option="props">
                    {{ t(`${props.option}`) }}
                </template>
                </VueMultiselect>
                <ToggleButton :options="[{icon: AscOrderIcon, title: `${t('ascending')}`, value: 'ASC'}, {icon: DscOrderIcon, title: `${t('descending')}`, value: 'DSC'}]" v-model="options.orderBy"/>
                </div>
            </div>
            <div class="option">
                <span class="label">Filter</span>
                <ToggleButton :options="[{icon: FilterIcon, title: `${t('show-filters')}`, value: true}]" v-model="showFilters"/>
            </div>
            <div class="option">
                <Button @click="reset()" variant="secondary" :title="t('reset')"><ResetIcon/></Button>
            </div>
            <div class="option">
                <Button @click="apply()" variant="primary" style="align-self: center;">{{ t('apply') }}</Button>
            </div>
            <Transition name="dropdown">
                <div class="transparent" v-if="showFilters">
                    <div class="filters">
                        <div class="ranges">
                            <div class="filter">
                                <label class="label" for="price-range">{{ t('price') }}</label>
                                <div class="range-values">
                                    <span>{{ filters.price[0] }} &euro;</span>
                                    <span>{{ filters.price[1] }} &euro;</span>
                                </div>
                                <Slider showTooltip="drag" id="price-range" :min="metaStore.params.price.min" :max="metaStore.params.price.max" v-model="filters.price"></Slider>
                            </div>
                            <div class="filter">
                                <label class="label" for="rent-range">{{ t('rent') }}</label>
                                <div class="range-values">
                                    <span>{{ filters.rent[0] }} &euro;</span>
                                    <span>{{ filters.rent[1] }} &euro;</span>
                                </div>
                                <Slider showTooltip="drag" id="price-range" :min="metaStore.params.rent.min" :max="metaStore.params.rent.max" v-model="filters.rent"></Slider>
                            </div>
                            <div class="filter">
                                <label class="label" for="yield-range">{{ t('yield') }}</label>
                                <div class="range-values">
                                    <span>{{ filters.yield[0] }}%</span>
                                    <span>{{ filters.yield[1] }}%</span>
                                </div>
                                <Slider showTooltip="drag" id="price-range" :step="0.01" :min="metaStore.params.yield.min" :max="metaStore.params.yield.max" v-model="filters.yield"></Slider>
                            </div>
                            <div class="filter">
                                <label class="label" for="area-range">{{ t('area') }}</label>
                                <div class="range-values">
                                    <span>{{ filters.area[0] }} m²</span>
                                    <span>{{ filters.area[1] }} m²</span>
                                </div>
                                <Slider showTooltip="drag" id="price-range" :step="1" :min="metaStore.params.area.min" :max="metaStore.params.area.max" v-model="filters.area"></Slider>
                            </div>
                        </div>
                        <div class="dropdowns">
                            <div class="filter" v-if="!(hideUsageFilter)">
                                <label class="label" for="usage-selector">{{ t('usage') }}</label>
                                <VueMultiselect
                                id="usage-selector"
                                :multiple="false" :taggable="true"
                                :show-labels="false"
                                :searchable="false"
                                :preselect-first="false"
                                :allowEmpty="true"
                                :placeholder="t('placeholder')"
                                v-model="filters.usage"
                                :options="['WOHNEN', 'PFLEGE', 'MICRO']">
                                <template #singleLabel="props">
                                    {{ t(props.option) }}
                                </template>
                                <template #option="props">
                                    {{ t(props.option) }}
                                </template>
                                </VueMultiselect>
                            </div>
                            <div class="filter" v-if="!(hideTypeFilter)">
                                <label class="label" for="type-selector">{{ t('type') }}</label>
                                <VueMultiselect
                                id="type-selector"
                                :multiple="false" :taggable="true"
                                :show-labels="false"
                                :searchable="false"
                                :preselect-first="false"
                                :allowEmpty="true"
                                :placeholder="t('placeholder')"
                                v-model="filters.type"
                                :options="['NEUBAU', 'BESTAND']">
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
                                v-model="filters.locations"
                                :options="metaStore.params.locations">
                                <template #noResult>
                                    <span>{{ t('no-results') }}</span>
                                </template>
                                </VueMultiselect>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </div>
</template>


<style lang="css" scoped>
.options {
    display: flex;
    flex-flow: row wrap;
    gap: 1rem;
    justify-content: flex-start;
    align-items: flex-start;
    padding: 0 20px;
    max-width: fit-content;
}

.option>span {
    line-height: 1.25rem;
    font-weight: 600;
    align-self: center;
}

.option>.together {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 0.5rem;
}
.option {
    display: flex;
    flex-flow: row nowrap;
    align-items: stretch;
    gap: 0.5rem;
}

.toolbar {
    background-color: var(--input-background);
    padding: 20px 0;
    max-width: 1300px;
    margin: auto;
    border-radius: 15px;
    box-shadow: 0px 5px 19px -7px rgba(0,0,0,1);
    display: flex;
    flex-flow: row wrap;
    align-items: center;
    justify-content: center;
    position: relative;
}

.label {
    line-height: 1.25rem;
    font-weight: 600;
    text-align: center;
}

.transparent {
    position: absolute;
    z-index: 100;
    top: 100%;
    left: 0;
    padding: 20px 0;
    box-sizing: border-box;
    width: 100%;
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
    flex: 1 1 45%;
    padding: 0 10px;
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

@media screen and (max-width: 320px) {
    .options {
        padding: 0 10px;
    }
}
</style>

<style src="@vueform/slider/themes/default.css"></style>

<i18n lang="json">
{
  "en": {
    "grid": "Grid view",
    "list": "List view",
    "sort": "Sorting",
    "sort-title": "Sort by",
    "view": "Viewing",
    "ascending": "Ascending order",
    "descending": "Descending order",
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
    "NONE": "None",
    "PRICE": "Price",
    "RENT": "Rent",
    "YIELD": "Yield",
    "AREA": "Area",
    "show-filters": "Show Filters",
    "per-page": "Per Page",
    "per-page-title": "Real-estate per page"
  },
  "de": {
    "grid": "Rasteransicht",
    "list": "Listenansicht",
    "sort": "Sortierung",
    "sort-title": "Sortieren nach",
    "view": "Ansicht",
    "ascending": "Aufsteigende Reihenfolge",
    "descending": "Absteigende Reihenfolge",
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
    "NONE": "Keiner",
    "PRICE": "Preis",
    "RENT": "Miete",
    "YIELD": "Rendite",
    "AREA": "Fläche",
    "show-filters": "Filter anzeigen",
    "per-page": "Pro Seite",
    "per-page-title": "Immobilien pro Seite"
  }
}
</i18n>