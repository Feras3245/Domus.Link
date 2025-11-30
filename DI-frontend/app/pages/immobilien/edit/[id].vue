<script setup lang="ts">
import ImagesIcon from '~/components/icons/images-icon.vue';
import LocationIcon from '~/components/icons/location-icon.vue';
import { lands, type Immobilie } from '~/types/domain';
import VueMultiselect from 'vue-multiselect';
import TitleIcon from '~/components/icons/title-icon.vue';
import ShortDescriptionIcon from '~/components/icons/short-description-icon.vue';
import LongDescriptionIcon from '~/components/icons/long-description-icon.vue';
import validator from '~/utils/validator';
import SquareGridIcon from '~/components/icons/square-grid-icon.vue';
import BuildingIcon from '~/components/icons/building-icon.vue';
import { type Notification } from "~/types/nav";
const { t } = useI18n();

definePageMeta({
    middleware: 'admin'
});

const route = useRoute();

const copy = ref<Immobilie>();
const original = ref<Immobilie>();
const immobilienStore = useImmobilienStore();

const popover = ref<{
    hidden: boolean,
    type: 'SAVE'|'RESET'
}>({hidden: true, type: 'RESET'});


{
    const response = await useAsyncData('fetch', () => immobilienStore.fetchImmobilie((route.params.id as string)));
    if (response.error.value) {
        response.error.value.fatal = true;
        throw response.error.value;
    } else if (response.data.value) {
        copy.value = JSON.parse(JSON.stringify(response.data.value));
        original.value = JSON.parse(JSON.stringify(response.data.value));
    }
}

const notification = ref<Notification>({
    type: "INFO",
    text: "",
    show: false
});

function detectChanges() {
    if (copy.value && original.value) {
        const body: any = {};
        if (!(copy.value.title.de === original.value.title.de)) 
            body.title = { ...(body.title || {}), de: copy.value.title.de };
        if (!(copy.value.title.en === original.value.title.en)) 
            body.title = { ...(body.title || {}), en: copy.value.title.en };
        if (!(copy.value.address === original.value.address)) 
            body.address = copy.value.address;
        if (!(copy.value.zip === original.value.zip)) 
            body.zip = copy.value.zip;
        if (!(copy.value.city === original.value.city)) 
            body.city = copy.value.city;
        if (!(copy.value.state === original.value.state)) 
            body.state = copy.value.state;
        if (!(copy.value.year === original.value.year)) 
            body.year = copy.value.year;
        if (!(copy.value.usage === original.value.usage)) 
            body.usage = copy.value.usage;
        if (!(copy.value.type === original.value.type)) 
            body.type = copy.value.type;
        if (!(copy.value.short_description.de === original.value.short_description.de)) {
            body.short_description = { ...(body.short_description || {}), de: copy.value.short_description.de };
        }
        if (!(copy.value.short_description.en === original.value.short_description.en)) {
            body.short_description = { ...(body.short_description || {}), en: copy.value.short_description.en };
        }
        if (!(copy.value.long_description.de === original.value.long_description.de)) {
            body.long_description = { ...(body.long_description || {}), de: copy.value.long_description.de };
        }
        if (!(copy.value.long_description.en === original.value.long_description.en)) {
            body.long_description = { ...(body.long_description || {}), en: copy.value.long_description.en };
        }
        return body;
    }
}

async function save() {
    const changes = detectChanges();
    if (copy.value && changes) {
        notification.value = {
            show: true,
            text: "saving",
            type: "LOADING"
        }
        popover.value.hidden = true;
        const id = copy.value.id;
        const response = await useAsyncData<Immobilie>('edit', () => immobilienStore.updateImmobilie(id, changes));
        if (response.status.value === 'error') {
            notification.value = {
                show: true,
                text: 'error',
                type: 'ERROR'
            }
        } else if (response.status.value === 'success') {
            copy.value = JSON.parse(JSON.stringify(response.data.value));
            original.value = JSON.parse(JSON.stringify(response.data.value));
            notification.value = {
                show: true,
                text: 'success',
                type: 'OK'
            }
        }
    }
}

function reset() {
    copy.value = JSON.parse(JSON.stringify(original.value));
    popover.value.hidden = true;
    notification.value.show = false;
}

function showSavePopover() {
    const changes = detectChanges();
    if (Object.keys(changes).length < 1) {
        notification.value = {
            type: "WARNING",
            text: "validation.no-changes",
            show: true
        };
        return;
    }
    const validation = validator(changes);
    if (!validation.valid) {
        notification.value = {
            type: "WARNING",
            text: validation.message,
            show: true
        }
        return;
    }
    popover.value.type = 'SAVE';
    popover.value.hidden = false;
}

function showResetPopover() {
    popover.value.type = 'RESET';
    popover.value.hidden = false;
}

const loadingimages = ref<boolean>(false);
async function uploadImages(images:File[]) {
    if (copy.value && original.value) {
        loadingimages.value = true;
        const response = await immobilienStore.uploadImages(copy.value.id, images);
        if (response) {
            copy.value.images = copy.value.images.concat(response);
            original.value.images = original.value.images.concat(response);
        }
        loadingimages.value = false;
    }
}

async function deleteImages(images:string[]) {
    if (copy.value && original.value) {
        try {
            loadingimages.value = true;
            const response = await immobilienStore.deleteimages(images);
            copy.value.images = copy.value.images.filter((img, _) => !images.includes(img));
            original.value.images = original.value.images.filter((img, _) => !images.includes(img));
            loadingimages.value = false;
        } catch (reason) {
            console.log(reason)
        }
    }
}
</script>

<template>
    <div v-if="copy" class="page">
        <h2>{{ t('page-title') }}</h2>
        <div>
            <SectionHeader :text="t('images')" :icon="ImagesIcon"/>
            <ImageManager v-model="copy.images" owner-type="immobilien" 
            @upload="uploadImages"
            @delete="deleteImages"
            :loading="loadingimages" :owner-id="copy.id"></ImageManager>
        </div>
        <div class="units">
            <SectionHeader :text="t('units')" :icon="SquareGridIcon"/>
            <UnitsTable type="EDIT" :immobilie="copy.id" v-model="copy.units"></UnitsTable>
        </div>
        <form id="update-immobilien" method="post" autocomplete="off">
            <div>
                <SectionHeader :text="t('title')" :icon="TitleIcon"/>
                <div class="fields">
                    <div style="flex: 1 1 49%">
                        <InputField 
                        id="title-de"
                        type="text"
                        :label="t('title-de')"
                        orientation="v"
                        :title="t('title-de')"
                        autocomplete="off"
                        maxlength="100"
                        v-model:text="copy.title.de"
                        form="update-immobilien"
                        />
                    </div>
                    <div style="flex: 1 1 49%">
                        <InputField 
                        id="title-en"
                        type="text"
                        :label="t('title-en')"
                        orientation="v"
                        :title="t('title-en')"
                        autocomplete="off"
                        maxlength="100"
                        v-model:text="copy.title.en"
                        form="update-immobilien"
                        />
                    </div>
                </div>
            </div>
            <div>
                <SectionHeader :text="t('location')" :icon="LocationIcon"/>
                <div class="fields">
                    <InputField 
                    id="address"
                    type="text"
                    :label="t('address')"
                    orientation="v"
                    :title="t('address')"
                    autocomplete="off"
                    v-model:text="copy.address"
                    :maxlength="100"
                    form="update-immobilien"
                    />
                    <div style="max-width: 10ch;">
                        <InputField 
                        id="zip"
                        type="text"
                        :label="t('zip')"
                        orientation="v"
                        :title="t('zip')"
                        autocomplete="off"
                        v-model:text="copy.zip"
                        :maxlength="5"
                        form="update-immobilien"
                        />
                    </div>
                    <InputField 
                    id="city"
                    type="text"
                    :label="t('city')"
                    orientation="v"
                    :title="t('city')"
                    autocomplete="off"
                    v-model:text="copy.city"
                    :maxlength="80"
                    form="update-immobilien"
                    />
                    <div class="dropdown">
                        <label class="label" for="state-selector">{{ t('state') }}</label>
                        <VueMultiselect
                        style="min-width: 27ch;"
                        id="state-selector"
                        :multiple="false" :taggable="false"
                        :show-labels="false"
                        :searchable="false"
                        :allowEmpty="false"
                        :placeholder="t('state')"
                        v-model="copy.state"
                        :options="lands"
                        form="update-immobilien"
                        :title="t('state')">
                        </VueMultiselect>
                    </div>
                </div>
            </div>
            <div>
                <SectionHeader :icon="BuildingIcon" :text="t('building')"/>
                <div class="fields">
                    <div class="dropdown" style="flex: 1 1 40%">
                        <label class="label" for="type-selector">{{ t('type') }}</label>
                        <VueMultiselect
                        id="type-selector"
                        :multiple="false" :taggable="true"
                        :show-labels="false"
                        :searchable="false"
                        :preselect-first="true"
                        :allowEmpty="false"
                        v-model="copy.type"
                        form="update-immobilien"
                        :title="t('type')"
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
                    form="update-immobilien"
                    v-model:number="copy.year"
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
                        form="update-immobilien"
                        :title="t('usage')"
                        v-model="copy.usage"
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
            </div>
            <div>
                <SectionHeader :icon="ShortDescriptionIcon" :text="t('short')"/>
                <div class="fields">
                    <div style="flex: 1 1 100%">
                        <InputField 
                        id="short-de"
                        type="text"
                        :label="t('short-de')"
                        orientation="v"
                        :title="t('short-de')"
                        autocomplete="off"
                        form="update-immobilien"
                        maxlength="150"
                        v-model:text="copy.short_description.de"
                        />
                    </div>
                    <div style="flex: 1 1 100%">
                        <InputField 
                        id="short-en"
                        type="text"
                        :label="t('short-en')"
                        orientation="v"
                        :title="t('short-en')"
                        autocomplete="off"
                        form="update-immobilien"
                        maxlength="150"
                        v-model:text="copy.short_description.en"
                        />
                    </div>
                </div>
            </div>
            <div>
                <SectionHeader :icon="LongDescriptionIcon" :text="t('long')"/>
                <div class="fields">
                    <div style="flex: 1 1 100%">
                        <InputField 
                        id="long-de"
                        type="textarea"
                        :label="t('long-de')"
                        orientation="v"
                        :title="t('long-de')"
                        form="update-immobilien"
                        autocomplete="off"
                        maxlength="1200"
                        v-model:text="copy.long_description.de"
                        />
                    </div>
                    <div style="flex: 1 1 100%">
                        <InputField 
                        id="long-en"
                        type="textarea"
                        :label="t('long-en')"
                        orientation="v"
                        :title="t('long-en')"
                        autocomplete="off"
                        form="update-immobilien"
                        maxlength="1200"
                        v-model:text="copy.long_description.en"
                        />
                    </div>
                </div>
            </div>
            <div v-if="notification.show" class="notification">
                <FormNotification :type="notification.type" :text="t(notification.text)"/>
            </div>
            <div class="btns">
                <Button variant="primary" :title="t('save')" @click="showSavePopover">{{ t('save') }}</Button>
                <Button variant="secondary" :title="t('reset')" @click="showResetPopover">{{ t('reset') }}</Button>
            </div>
        </form>
        <Popover v-model="popover.hidden">
                <div class="dialog-box" v-if="popover.type === 'RESET'">
                    <span>{{ t('confirm.label') }}</span>
                    <p>{{ t('confirm.reset') }}</p>
                    <div>
                        <Button variant="primary" @click="reset">{{ t('yes') }}</Button>
                        <Button variant="secondary" @click="popover.hidden=true">{{ t('cancel') }}</Button>
                    </div>
                </div>
                <div class="dialog-box" v-if="popover.type === 'SAVE'">
                    <span>{{ t('confirm.label') }}</span>
                    <p>{{ t('confirm.save') }}</p>
                    <div>
                        <Button variant="primary" @click="save">{{ t('yes') }}</Button>
                        <Button variant="secondary" @click="popover.hidden=true">{{ t('cancel') }}</Button>
                    </div>
                </div>
        </Popover>
    </div>
</template>

<style lang="css" scoped>
.units {
    display: flex;
    flex-flow: column nowrap;
    gap: 1rem;
}

.dialog-box>div {
    display: flex;
    flex-flow: row nowrap;
    align-items: stretch;
    justify-content: flex-end;
    gap: 1rem;
}

.dialog-box>span {
    line-height: 1.25rem;
    font-weight: 600;
}

.dialog-box {
    display: flex;
    flex-flow: column nowrap;
    gap: 10px;
    padding: 20px;
    background-color: var(--input-background);
    border-radius: 1rem;
}

.btns {
    display: flex;
    flex-flow: row wrap;
    align-items: center;
    justify-content: flex-start;
    gap: 1rem;
}

.page>h2 {
    font-size: calc(var(--font-size) * 2);
    font-family: var(--font-alt);
}

.fields {
    display: flex;
    flex-flow: row wrap;
    align-items: center;

    gap: 10px;
    padding: 20px 0;

    &>* {
        flex: 1 1 auto;
    }
}

.dropdown {
    display: flex;
    flex-flow: column nowrap;
    align-items: start;
    gap: 0.5rem;
}

.label {
    line-height: 1.25rem;
    font-weight: 600;
}

.page>form {
    display: flex;
    flex-flow: column nowrap;
    align-items: stretch;
    gap: 20px;
}

.page {
    margin: 20px;
    display: flex;
    flex-flow: column nowrap;
    align-items: stretch;
    gap: 20px;
}
</style>

<i18n lang="json">
{
    "en": {
        "page-title": "Edit Real-estate property",
        "images": "Images",
        "title": "Title",
        "title-de": "Title (German)",
        "title-en": "Title (English)",
        "location": "Location",
        "address": "Address",
        "zip": "Postal Code",
        "city": "City",
        "state": "State",
        "classification": "Classification",
        "type": "Project type",
        "usage": "Usage type",
        "short": "Short description",
        "short-de": "Short description (German)",
        "short-en": "Short description (English)",
        "long": "Long description",
        "long-de": "Long description (German)",
        "long-en": "Long description (English)",
        "WOHNEN":"Residential",
        "PFLEGE":"Care",
        "MICRO":"Micro",
        "NEUBAU":"New",
        "BESTAND":"Existing",
        "save": "Save",
        "reset": "Reset",
        "close": "Close",
        "cancel": "Cancel",
        "saving": "Saving changes...",
        "success": "Changes were saved successfully!",
        "units": "Units",
        "building": "Building",
        "confirm": {
            "label": "Are you sure?",
            "reset": "All changes will be discarded.",
            "save": "All changes will be saved."
        },
        "yes": "Confirm",
        "price": "Purchase price (€)",
        "rent": "Rent per month (€)",
        "rooms": "Number of Rooms",
        "area": "Total Area",
        "year": "Year Built",
        "numbers": "Numbers",
        "error": "Something went wrong!",
        "validation": {
        "title-en": "English title is required and must be max 100 characters.",
        "title-de": "German title is required and must be max 100 characters.",
        "address": "Street address is required and must be max 100 characters.",
        "city": "City is required and must be max 100 characters.",
        "zip": "Postal code is required and must be 5 characters.",
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
        "images": "Please upload at least two valid image files.",
        "no-changes": "No changes were made."
        }
    },
    "de": {
        "images": "Bilder",
        "page-title": "Immobilie bearbeiten",
        "title": "Titel",
        "title-de": "Titel (Deutsch)",
        "title-en": "Titel (Englisch)",
        "location": "Standort",
        "address": "Adresse",
        "zip": "Postleitzahl",
        "city": "Stadt",
        "state": "Bundesland",
        "classification": "Klassifizierung",
        "type": "Projekttyp",
        "usage": "Nutzungstyp",
        "short": "Kurzbeschreibung",
        "short-de": "Kurzbeschreibung (Deutsch)",
        "short-en": "Kurzbeschreibung (Englisch)",
        "long": "Langbeschreibung",
        "long-de": "Langbeschreibung (Deutsch)",
        "long-en": "Langbeschreibung (Englisch)",
        "WOHNEN": "Wohnen",
        "PFLEGE": "Pflege",
        "MICRO": "Mikro",
        "NEUBAU": "Neubau",
        "BESTAND": "Bestand",
        "save": "Speichern",
        "reset": "Zurücksetzen",
        "close": "Schließen",
        "cancel": "Abbrechen",
        "saving": "Änderungen speichern...",
        "success": "Änderungen wurden erfolgreich gespeichert!",
        "units": "Einheiten",
        "building": "Gebäude",
        "confirm": {
            "label": "Bist du sicher?",
            "reset": "Alle Änderungen werden verworfen.",
            "save": "Alle Änderungen werden gespeichert."
        },
        "yes": "Bestätigen",
        "price": "Kaufpreis (€)",
        "rent": "Miete pro Monat (€)",
        "rooms": "Zimmeranzahl",
        "area": "Gesamtfläche",
        "year": "Baujahr",
        "numbers": "Zahlen",
        "error": "Etwas ist schiefgelaufen!",
        "validation": {
            "title-en": "Der englische Titel ist erforderlich und darf maximal 100 Zeichen lang sein.",
            "title-de": "Der deutsche Titel ist erforderlich und darf maximal 100 Zeichen lang sein.",
            "address": "Die Adresse ist erforderlich und darf maximal 100 Zeichen lang sein.",
            "city": "Die Stadt ist erforderlich und darf maximal 255 Zeichen lang sein.",
            "zip": "Die PLZ ist erforderlich und muss 5 Zeichen lang sein.",
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
            "no-changes": "Es wurden keine Änderungen vorgenommen."
        }
    }
}
</i18n>
