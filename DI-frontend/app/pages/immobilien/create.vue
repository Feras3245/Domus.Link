<script setup lang="ts">
import { lands, type NewImmobilie } from '~/types/domain';
import VueMultiselect from 'vue-multiselect';
import { omit } from '@nuxt/ui/runtime/utils/index.js';
import TitleIcon from '~/components/icons/title-icon.vue';
import LocationIcon from '~/components/icons/location-icon.vue';
import ShortDescriptionIcon from '~/components/icons/short-description-icon.vue';
import LongDescriptionIcon from '~/components/icons/long-description-icon.vue';
import ImagesIcon from '~/components/icons/images-icon.vue';
import BuildingIcon from '~/components/icons/building-icon.vue';
import SquareGridIcon from '~/components/icons/square-grid-icon.vue';
const { t } = useI18n();

definePageMeta({
    middleware: 'admin'
})

const immobilienStore = useImmobilienStore();
const immobilie = ref<NewImmobilie>({
    title: {de: '', en: ''},
    address: '',
    zip: '',
    city: '', 
    state: 'Niedersachsen',
    units: [],
    year: 0,
    type: 'BESTAND',
    usage: 'WOHNEN',
    short_description: {de: '', en: ''},
    long_description: {de: '', en: ''},
    images: [],
    hidden: false,
})

const notification = ref<{
    type: "INFO"|"WARNING"|"ERROR"|"OK"|"LOADING",
    text: string,
    show: boolean
}>({
    type: "INFO",
    text: "",
    show: false
});

const form = ref<HTMLFormElement>();
const submitting = ref<boolean>(false);

async function createImmobilie() {
    const validation = validator(omit(immobilie.value, ['images', 'units']));
    if (!validation.valid) {
        notification.value = {
            type: "WARNING",
            text: validation.message,
            show: true
        }
        return;
    }

    if (immobilie.value.units.length < 1) {
        notification.value = {
            type: "WARNING",
            text: "validation.atleast-1",
            show: true
        }
        return;
    }

    const invalidUnits = immobilie.value.units.some(unit => Object.values(unit).some(prop => typeof prop !== 'number' || prop <= 0));
    if (invalidUnits) {
        notification.value = {
            type: "WARNING",
            text: "validation.above-0",
            show: true
        }
    }
    
    if (!immobilie.value.images || immobilie.value.images.length < 2 || immobilie.value.images.some(file => !file.type.startsWith("image/"))) {
        notification.value = {
            type: "WARNING",
            text: "validation.images",
            show: true
        };
        return;
    }

    submitting.value = true;
    notification.value = {
        show: true,
        text: "submitting",
        type: "LOADING"
    }
    const response = await useAsyncData('create', () => immobilienStore.createImmobilie(immobilie.value));
    if (response.status.value === 'error') {
        notification.value = {
            show: true,
            text: (response.error.value?.message as string),
            type: "ERROR"
        }
    } else if (response.status.value === 'success') {
        notification.value = {
            show: true,
            text: "success",
            type: "OK"
        }
    }
    form.value?.reset();
    submitting.value = false;
}
</script>

<template>
    <div class="page">
        <h2>{{ t('header') }}</h2>
        <form class="flex-col" ref="form" id="create-immo-form" method="post" @submit.lazy.prevent="createImmobilie" autocomplete="off">
            <SectionHeader :text="t('title')" :icon="TitleIcon"/>
            <div class="flex-row">
                <InputField 
                id="title-de"
                type="text"
                :label="t('title-de')"
                orientation="v"
                :title="t('title-de')"
                autocomplete="off"
                maxlength="100"
                form="create-immo-form"
                v-model:text="immobilie.title.de"
                />
                <InputField 
                id="title-en"
                type="text"
                :label="t('title-en')"
                orientation="v"
                :title="t('title-en')"
                autocomplete="off"
                maxlength="100"
                form="create-immo-form"
                v-model:text="immobilie.title.en"
                />
            </div>
            <SectionHeader :text="t('location')" :icon="LocationIcon"/>
            <div class="flex-row">
                <InputField 
                id="address"
                type="text"
                :label="t('address')"
                orientation="v"
                :title="t('address')"
                autocomplete="off"
                form="create-immo-form"
                v-model:text="immobilie.address"
                :maxlength="100"
                />
                <InputField 
                id="city"
                type="text"
                :label="t('city')"
                orientation="v"
                :title="t('city')"
                autocomplete="off"
                form="create-immo-form"
                v-model:text="immobilie.city"
                :maxlength="100"
                />
            </div>
            <div class="flex-row">
                <InputField 
                id="plz"
                type="text"
                :label="t('plz')"
                orientation="v"
                :title="t('plz')"
                autocomplete="off"
                form="create-immo-form"
                v-model:text="immobilie.zip"
                :maxlength="5"
                />
                <div class="dropdown" style="min-width: 60%;">
                    <label class="label" for="state-selector">{{ t('state') }}</label>
                    <VueMultiselect
                    id="state-selector"
                    :multiple="false" :taggable="false"
                    :show-labels="false"
                    :searchable="false"
                    :allowEmpty="false"
                    :placeholder="t('state')"
                    v-model="immobilie.state"
                    :options="lands"
                    form="create-immo-form"
                    :title="t('state')">
                    </VueMultiselect>
                </div>
            </div>
            <SectionHeader :text="t('units')" :icon="SquareGridIcon"/>
            <div class="units">
                <UnitsTable type="NEW" v-model="immobilie.units"></UnitsTable>
            </div>
            <SectionHeader :icon="BuildingIcon" :text="t('building')"/>
            <div class="flex-row">
                <div class="dropdown" style="flex: 1 1 40%">
                    <label class="label" for="type-selector">{{ t('type') }}</label>
                    <VueMultiselect
                    id="type-selector"
                    :multiple="false" :taggable="true"
                    :show-labels="false"
                    :searchable="false"
                    :preselect-first="true"
                    :allowEmpty="false"
                    v-model="immobilie.type"
                    form="create-immo-form"
                    :options="['BESTAND', 'NEUBAU']">
                    <template #singleLabel="props">
                        {{ t(props.option) }}
                    </template>
                    <template #option="props">
                        {{ t(props.option) }}
                    </template>
                    </VueMultiselect>
                </div>
                <InputField 
                id="year"
                type="number"
                :label="t('year')"
                orientation="v"
                :title="t('year')"
                autocomplete="off"
                form="create-immo-form"
                v-model:number="immobilie.year"
                />
                <div class="dropdown" style="flex: 1 1 40%">
                    <label class="label" for="usage-selector">{{ t('usage') }}</label>
                    <VueMultiselect
                    id="usage-selector"
                    :multiple="false" :taggable="true"
                    :show-labels="false"
                    :searchable="false"
                    :preselect-first="true"
                    :allowEmpty="false"
                    v-model="immobilie.usage"
                    form="create-immo-form"
                    :options="['WOHNEN', 'MICRO', 'PFLEGE']">
                    <template #singleLabel="props">
                        {{ t(props.option) }}
                    </template>
                    <template #option="props">
                        {{ t(props.option) }}
                    </template>
                    </VueMultiselect>
                </div>
            </div>
            <SectionHeader :icon="ShortDescriptionIcon" :text="t('short')"/>
            <div class="flex-col">
                <InputField 
                id="short-de"
                type="text"
                :label="t('short-de')"
                orientation="v"
                :title="t('short-de')"
                autocomplete="off"
                form="create-immo-form"
                maxlength="150"
                v-model:text="immobilie.short_description.de"
                />
                <InputField 
                id="short-en"
                type="text"
                :label="t('short-en')"
                orientation="v"
                :title="t('short-en')"
                autocomplete="off"
                form="create-immo-form"
                maxlength="150"
                v-model:text="immobilie.short_description.en"
                />
            </div>
            <SectionHeader :icon="LongDescriptionIcon" :text="t('long')"/>
            <div class="flex-col">
                <InputField 
                id="long-de"
                type="textarea"
                :label="t('long-de')"
                orientation="v"
                :title="t('long-de')"
                autocomplete="off"
                form="create-immo-form"
                maxlength="1200"
                v-model="immobilie.long_description.de"
                />
                <InputField 
                id="long-en"
                type="textarea"
                :label="t('long-en')"
                orientation="v"
                :title="t('long-en')"
                autocomplete="off"
                form="create-immo-form"
                maxlength="1200"
                v-model="immobilie.long_description.en"
                />
            </div>
            <SectionHeader :text="t('images')" :icon="ImagesIcon"/>
            <div>
                <InputField id="images" 
                type="file"
                accept="image/*" 
                multiple
                form="create-immo-form"
                :label="t('upload')"
                :title="t('upload')"
                orientation="v"
                v-model:files="immobilie.images"
                />
            </div>
            <div>
                <InputField id="hide" 
                type="checkbox" 
                form="create-immo-form"
                :label="t('hide')"
                :title="t('hide')"
                orientation="v"
                v-model:boolean="immobilie.hidden"
                />
            </div>
            <div class="flex-col" style="align-items: flex-start;">
                <div v-if="notification.show" class="notification">
                    <FormNotification :type="notification.type" :text="t(notification.text)"/>
                </div>
                <Button :disabled="submitting" form="create-immo-form">
                    <LoadingIcon v-if="submitting"/>
                    <DirectionIcon v-else direction="right"/>{{ t('submit') }}
                </Button>
            </div>
        </form>
    </div>
</template>

<style lang="css" scoped>
.page>h2 {
    font-size: calc(var(--font-size) * 2);
    padding: 20px;
    font-family: var(--font-alt);
}

.page>form {
    padding: 20px;
}

.dropdown {
    display: flex;
    flex-flow: row wrap;
    align-items: start;
    gap: 0.5rem;
}

.label {
    line-height: 1.25rem;
    font-weight: 600;
}

.flex-row {
    display: flex;
    flex-flow: row wrap;
    align-items: center;
    gap: 15px;
    &>* {
        flex: 1 1 max-content;
    }
}

.flex-col {
    display: flex;
    flex-flow: column nowrap;
    gap: 15px;
    align-items: stretch;
}
</style>

<i18n lang="json">
{
  "en": {
    "title-en": "Title (English)",
    "title-de": "Title (German)",
    "price": "Purchase price (€)",
    "rent": "Rent per month (€)",
    "title": "Title",
    "classification": "Classification",
    "numbers": "Numbers",
    "long": "Long description",
    "short": "Short description",
    "rooms": "Number of Rooms",
    "area": "Total Area",
    "year": "Year Built",
    "short-en": "Short Description (English)",
    "short-de": "Short Description (German)",
    "long-en": "Long Description (English)",
    "long-de": "Long Description (German)",
    "images": "Images",
    "upload": "Upload Images",
    "submit": "Submit",
    "success": "The real-estate was saved successfully!",
    "submitting": "Submitting...",
    "units": "Units",
    "building": "Building",
    "validation": {
        "title-en": "English title is required and must be max 100 characters.",
        "title-de": "German title is required and must be max 100 characters.",
        "address": "Street address is required and must be max 100 characters.",
        "city": "City is required and must be max 100 characters.",
        "zip": "Postal code is required and must be max 20 characters.",
        "state": "State is required.",
        "price": "Price is required and must be a number ≥ 0.",
        "rent": "Rent is required and must be a number ≥ 0.",
        "rooms": "Number of rooms is required and must be ≥ 0.",
        "area": "Area is required and must be ≥ 0.",
        "year": "Year must be between 1000 and current year.",
        "type": "Type is required and must be NEUBAU (1) or BESTAND (2).",
        "usage": "Usage is required and must be WOHNEN (1), MICRO (2), or PFLEGE (3).",
        "short-en": "English short description is required and max 255 characters.",
        "short-de": "German short description is required and max 255 characters.",
        "long-en": "English long description is required and max 1500 characters.",
        "long-de": "German long description is required and max 1500 characters.",
        "above-0": "All units must have attribute values above 0",
        "atleast-1": "A property must have at least one unit",
        "images": "At least one image must be uploaded."
    },
    "error": {
        "upload-failed": "Real-estate created, but the server could not upload images",
        "create-failed": "Server could not create the real-estate"
    },
    "location": "Location",
    "address": "Address",
    "city": "City",
    "plz": "Postal code",
    "state": "State",
    "usage": "Usage type",
    "WOHNEN":"Residential",
    "PFLEGE":"Care",
    "MICRO":"Micro",
    "type": "Project type",
    "NEUBAU":"New",
    "BESTAND":"Existing",
    "hide": "Hide object",
    "header": "Create new real-estate object"
  },
  "de": {
    "title-en": "Titel (Englisch)",
    "title-de": "Titel (Deutsch)",
    "price": "Kaufpreis (€)",
    "rent": "Miete pro Monat (€)",
    "rooms": "Zimmeranzahl",
    "area": "Gesamtfläche",
    "year": "Baujahr",
    "title": "Titel",
    "classification": "Klassifizierung",
    "short": "Kurzbeschreibung",
    "long": "Langbeschreibung",
    "numbers": "Zahlen",
    "short-en": "Kurze Beschreibung (Englisch)",
    "short-de": "Kurze Beschreibung (Deutsch)",
    "long-en": "Lange Beschreibung (Englisch)",
    "long-de": "Lange Beschreibung (Deutsch)",
    "images": "Bilder",
    "upload": "Bilder hochladen",
    "submit": "Einreichen",
    "success": "Die Immobilie wurde erfolgreich gespeichert!",
    "submitting": "Wird gesendet...",
    "units": "Einheiten",
    "building": "Gebäude",
    "validation": {
        "title-en": "Der englische Titel ist erforderlich und darf maximal 100 Zeichen lang sein.",
        "title-de": "Der deutsche Titel ist erforderlich und darf maximal 100 Zeichen lang sein.",
        "address": "Die Adresse ist erforderlich und darf maximal 100 Zeichen lang sein.",
        "city": "Die Stadt ist erforderlich und darf maximal 255 Zeichen lang sein.",
        "zip": "Die PLZ ist erforderlich und darf maximal 5 Zeichen lang sein.",
        "state": "Das Land ist erforderlich.",
        "price": "Der Kaufpreis ist erforderlich und muss > 0 sein.",
        "rent": "Die Miete ist erforderlich und muss > 0 sein.",
        "rooms": "Die Zimmeranzahl ist erforderlich und muss > 0 sein.",
        "area": "Die Gesamtfläche ist erforderlich und muss > 0 sein.",
        "year": "Das Baujahr muss zwischen 1000 und dem aktuellen Jahr liegen.",
        "type": "Der Projekttyp ist erforderlich und muss NEUBAU (1) oder BESTAND (2) sein.",
        "usage": "Die Nutzung ist erforderlich und muss WOHNEN (1), MICRO (2) oder PFLEGE (3) sein.",
        "short-en": "Die kurze englische Beschreibung ist erforderlich und darf maximal 255 Zeichen lang sein.",
        "short-de": "Die kurze deutsche Beschreibung ist erforderlich und darf maximal 255 Zeichen lang sein.",
        "long-en": "Die lange englische Beschreibung ist erforderlich und darf maximal 1500 Zeichen lang sein.",
        "long-de": "Die lange deutsche Beschreibung ist erforderlich und darf maximal 1500 Zeichen lang sein.",
        "above-0": "Alle Werte müssen über 0 liegen",
        "atleast-1": "Alle Einheiten müssen Eigenschaftswerte über 0 haben",
        "images": "Mindestens ein Bild muss hochgeladen werden"
    },
    "error": {
        "upload-failed": "Immobilie erstellt, aber der Server konnte keine Bilder hochladen",
        "create-failed": "Server konnte die Immobilie nicht erstellen"
    },
    "location": "Standort",
    "address": "Adresse",
    "city": "Stadt",
    "plz": "Postleitzahl",
    "state": "Land",
    "usage": "Nutzungstyp",
    "WOHNEN": "Wohnen",
    "PFLEGE": "Pflege",
    "MICRO": "Mikro",
    "type": "Projekttyp",
    "NEUBAU": "Neubau",
    "BESTAND": "Bestand",
    "hide": "Objekt verbergen",
    "header": "Neues Immobilienobjekt erstellen"
  }
}
</i18n>