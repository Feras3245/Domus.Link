<script setup lang="ts">
import { type Immobilie, lands } from '~/types/domain';
import VueMultiselect from 'vue-multiselect';
const { t, locale } = useI18n();

const props = defineProps<{
    immobilie: Immobilie
}>();

const titleEn = ref<string>('');
const titleDe = ref<string>('');
const address = ref<string>('');
const zip = ref<string>('');
const city = ref<string>('');
const state = ref<string>('');
const price = ref<number>(0);
const rent = ref<number>(0);
const rooms = ref<number>(0);
const area = ref<number>(0);
const year = ref<number>(0);
const type = ref<'BESTAND' | 'NEUBAU'>('BESTAND');
const usage = ref<'WOHNEN' | 'MICRO' | 'PFLEGE'>('WOHNEN');
const shortEn = ref<string>('');
const shortDe = ref<string>('');
const longEn = ref<string>('');
const longDe = ref<string>('');
const images = ref<File[]>([]);
const hidden = ref<boolean>(false);

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
    const currentYear = new Date().getFullYear();

    if (!titleEn.value || titleEn.value.length > 100) {
        notification.value = {
            type: "WARNING",
            text: "validation.title-en",
            show: true
        };
        return;
    }

    if (!titleDe.value || titleDe.value.length > 100) {
        notification.value = {
            type: "WARNING",
            text: "validation.title-de",
            show: true
        };
        return;
    }

    if (!address.value || address.value.length > 100) {
        notification.value = {
            type: "WARNING",
            text: "validation.address",
            show: true
        };
        return;
    }

    if (!city.value || city.value.length > 80) {
        notification.value = {
            type: "WARNING",
            text: "validation.city",
            show: true
        };
        return;
    }

    if (!zip.value || zip.value.length > 5) {
        notification.value = {
            type: "WARNING",
            text: "validation.plz",
            show: true
        };
        return;
    }

    if (!state.value || state.value.length > 100) {
        notification.value = {
            type: "WARNING",
            text: "validation.state",
            show: true
        };
        return;
    }

    if (price.value === undefined || typeof price.value !== "number" || price.value <= 0) {
        notification.value = {
            type: "WARNING",
            text: "validation.price",
            show: true
        };
        return;
    }

    if (rent.value === undefined || typeof rent.value !== "number" || rent.value <= 0) {
        notification.value = {
            type: "WARNING",
            text: "validation.rent",
            show: true
        };
        return;
    }

    if (rooms.value === undefined || typeof rooms.value !== "number" || rooms.value <= 0) {
        notification.value = {
            type: "WARNING",
            text: "validation.rooms",
            show: true
        };
        return;
    }

    if (area.value === undefined || typeof area.value !== "number" || area.value <= 0) {
        notification.value = {
            type: "WARNING",
            text: "validation.area",
            show: true
        };
        return;
    }

    if (
        year.value === undefined ||
        typeof year.value !== "number" ||
        !Number.isInteger(year.value) ||
        year.value < 1000 ||
        year.value > currentYear
    ) {
        notification.value = {
            type: "WARNING",
            text: "validation.year",
            show: true
        };
        return;
    }

    if (type.value === undefined || !['BESTAND', 'NEUBAU'].includes(type.value)) {
        notification.value = {
            type: "WARNING",
            text: "validation.type",
            show: true
        };
        return;
    }

    if (usage.value === undefined || !['WOHNEN', 'MICRO', 'PFLEGE'].includes(usage.value)) {
        notification.value = {
            type: "WARNING",
            text: "validation.usage",
            show: true
        };
        return;
    }

    if (!shortEn.value || shortEn.value.length > 150) {
        notification.value = {
            type: "WARNING",
            text: "validation.short-en",
            show: true
        };
        return;
    }

    if (!shortDe.value || shortDe.value.length > 150) {
        notification.value = {
            type: "WARNING",
            text: "validation.short-de",
            show: true
        };
        return;
    }

    if (!longEn.value || longEn.value.length > 1200) {
        notification.value = {
            type: "WARNING",
            text: "validation.long-en",
            show: true
        };
        return;
    }

    if (!longDe.value || longDe.value.length > 1200) {
        notification.value = {
            type: "WARNING",
            text: "validation.long-de",
            show: true
        };
        return;
    }

    if (!images.value || images.value.length < 2 || images.value.some(file => !file.type.startsWith("image/"))) {
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

    try {
        const csrf = useCsrf();
        const immobilie = await $fetch<Immobilie>('/api/immobilien', {method: 'POST', body: {
            title: {de: titleDe.value, en: titleEn.value},
            address: address.value,
            zip: zip.value,
            city: city.value,
            state: state.value,
            price: price.value,
            rent: rent.value,
            rooms: rooms.value,
            area: area.value,
            year: year.value,
            type: type.value,
            usage: usage.value,
            short_description: {de: shortDe.value, en: shortEn.value},
            long_description: {de: longDe.value, en: longEn.value},
            hidden: hidden.value
        },
        headers: {
            "Accept": "application/json",
            "CSRF-Token": `${csrf.csrf}`
        }});

        try {
            const owner_type = 'immobilien';
            const owner_id = immobilie.id;
            const formData = new FormData();
            images.value?.forEach((image, index) => {
                formData.append('image-'+index, image);
            });
            const images_response = await $fetch(`/api/images/${owner_type}/${owner_id}`, {
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "CSRF-Token": `${csrf.csrf}`
                },
                body: formData
            });
            notification.value = {
                show: true,
                text: 'success',
                type: "OK"
            };
            form.value?.reset();
            usage.value = 'WOHNEN';
            type.value = 'BESTAND';
            hidden.value = false;
        } catch (error: any) {
            console.log(error);
            notification.value = {
                show: true,
                text: 'error.upload-failed',
                type: "ERROR"
            }
        }
    } catch (error:any) {
        console.log(error);
        notification.value = {
            show: true,
            text: 'error.create-failed',
            type: "ERROR"
        }
    }
    submitting.value = false;
}
</script>

<template>
<form class="flex-col" ref="form" id="create-immo-form" method="post" @submit.lazy.prevent="createImmobilie" autocomplete="off">
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
        v-model:text="titleDe"
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
        v-model:text="titleEn"
        />
    </div>
    <div class="flex-row">
        <InputField 
        id="address"
        type="text"
        :label="t('address')"
        orientation="v"
        :title="t('address')"
        autocomplete="off"
        form="create-immo-form"
        v-model:text="address"
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
        v-model:text="city"
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
        v-model:text="zip"
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
            v-model="state"
            :options="lands"
            form="create-immo-form"
            :title="t('state')">
            </VueMultiselect>
        </div>
    </div>
    <div class="flex-row">
        <InputField 
        id="price"
        type="number"
        :label="t('price')"
        orientation="v"
        :title="t('price')"
        autocomplete="off"
        form="create-immo-form"
        v-model:number="price"
        />
        <InputField 
        id="rent"
        type="number"
        :label="t('rent')"
        orientation="v"
        :title="t('rent')"
        autocomplete="off"
        form="create-immo-form"
        v-model:number="rent"
        />
    </div>
    <div class="flex-row">
        <InputField 
        id="rooms"
        type="number"
        :label="t('rooms')"
        orientation="v"
        :title="t('rooms')"
        autocomplete="off"
        form="create-immo-form"
        v-model:number="rooms"
        />
        <InputField 
        id="area"
        type="number"
        :label="t('area')"
        orientation="v"
        :title="t('area')"
        autocomplete="off"
        form="create-immo-form"
        v-model:number="area"
        />
        <InputField 
        id="year"
        type="number"
        :label="t('year')"
        orientation="v"
        :title="t('year')"
        autocomplete="off"
        form="create-immo-form"
        v-model:number="year"
        />
    </div>
    <div class="flex-row">
        <div class="dropdown" style="flex: 1 1 45%;">
            <label class="label" for="type-selector">{{ t('type') }}</label>
            <VueMultiselect
            id="type-selector"
            :multiple="false" :taggable="true"
            :show-labels="false"
            :searchable="false"
            :preselect-first="true"
            :allowEmpty="false"
            v-model="type"
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
        <div class="dropdown" style="flex: 1 1 45%;">
            <label class="label" for="usage-selector">{{ t('usage') }}</label>
            <VueMultiselect
            id="usage-selector"
            :multiple="false" :taggable="true"
            :show-labels="false"
            :searchable="false"
            :preselect-first="true"
            :allowEmpty="false"
            v-model="usage"
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
        v-model:text="shortDe"
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
        v-model:text="shortEn"
        />
    </div>
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
        v-model="longDe"
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
        v-model="longEn"
        />
    </div>
    <div>
        <InputField id="images" 
        type="file"
        accept="image/*" 
        multiple
        form="create-immo-form"
        :label="t('images')"
        :title="t('images')"
        orientation="v"
        v-model:files="images"
        />
    </div>
    <div>
        <InputField id="hide" 
        type="checkbox" 
        form="create-immo-form"
        :label="t('hide')"
        :title="t('hide')"
        orientation="v"
        v-model:boolean="hidden"
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
</template>

<style lang="css" scoped>
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
    "rooms": "Number of Rooms",
    "area": "Total Area",
    "year": "Year Built",
    "short-en": "Short Description (English)",
    "short-de": "Short Description (German)",
    "long-en": "Long Description (English)",
    "long-de": "Long Description (German)",
    "images": "Upload Images",
    "submit": "Submit",
    "success": "The real-estate was saved successfully!",
    "submitting": "Submitting...",
    "validation": {
      "title-en": "English title is required and must be max 100 characters.",
      "title-de": "German title is required and must be max 100 characters.",
      "address": "Street address is required and must be max 100 characters.",
      "city": "City is required and must be max 100 characters.",
      "plz": "Postal code is required and must be max 20 characters.",
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
      "images": "Please upload at least two valid image files."
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
    "hide": "Hide object"
  },
  "de": {
    "title-en": "Titel (Englisch)",
    "title-de": "Titel (Deutsch)",
    "price": "Kaufpreis (€)",
    "rent": "Miete pro Monat (€)",
    "rooms": "Zimmeranzahl",
    "area": "Gesamtfläche",
    "year": "Baujahr",
    "short-en": "Kurze Beschreibung (Englisch)",
    "short-de": "Kurze Beschreibung (Deutsch)",
    "long-en": "Lange Beschreibung (Englisch)",
    "long-de": "Lange Beschreibung (Deutsch)",
    "images": "Bilder hochladen",
    "submit": "Einreichen",
    "success": "Die Immobilie wurde erfolgreich gespeichert!",
    "submitting": "Wird gesendet...",
    "validation": {
      "title-en": "Der englische Titel ist erforderlich und darf maximal 100 Zeichen lang sein.",
      "title-de": "Der deutsche Titel ist erforderlich und darf maximal 100 Zeichen lang sein.",
      "address": "Die Adresse ist erforderlich und darf maximal 100 Zeichen lang sein.",
      "city": "Die Stadt ist erforderlich und darf maximal 255 Zeichen lang sein.",
      "plz": "Die PLZ ist erforderlich und darf maximal 5 Zeichen lang sein.",
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
      "images": "Please upload at least two valid image files."
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
    "hide": "Objekt verbergen"
  }
}
</i18n>