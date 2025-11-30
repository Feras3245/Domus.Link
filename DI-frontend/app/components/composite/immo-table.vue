<script setup lang="ts">
import type { User } from '~/types/auth';
const { t, locale } = useI18n();

const immobilien = useImmobilienStore();

const delId = ref<string>();
const popoverHidden = ref<boolean>(true);

function showDelPopover(id:string) {
    delId.value = id;
    popoverHidden.value = false
}

async function deleteImmobilie() {
    if (delId.value) {
        popoverHidden.value = true;
        await immobilien.deleteImmobilie(delId.value);
    }
}
</script>

<template>
    <div class="immo-table">
        <table>
            <thead>
                <tr>
                    <th>{{ t('title') }}</th>
                    <th>{{ t('description') }}</th>
                    <th>{{ t('location') }}</th>
                    <th>{{ t('units') }}</th>
                    <th>{{ t('year') }}</th>
                    <th>{{ t('price') }} (&euro;)</th>
                    <th>{{ t('rent') }} (&euro;)</th>
                    <th>{{ t('yield') }} (%)</th>
                    <th>{{ t('area') }} (m²)</th>
                    <th>{{ t('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(immobilie, index) in immobilien.immobilien" :class="{hidden: immobilie.hidden}" :key="index">
                    <td>
                        <div class="long-text">
                            <p style="font-weight: 700;">{{ immobilie.title[locale] }}</p>
                        </div>
                    </td>
                    <td>
                        <div class="long-text">
                            <p>{{ immobilie.short_description[locale] }}</p>
                        </div>
                    </td>
                    <td>
                        <div class="long-text">
                            <p>{{ immobilie.address }}, {{ immobilie.zip }} {{ immobilie.city }}</p>
                        </div>
                    </td>
                    <td>
                        <div>
                            <p>{{ immobilie.units.length }}</p>
                        </div>
                    </td>
                    <td>
                        <div>
                            <p>{{ immobilie.year }}</p>
                        </div>
                    </td>
                    <td>
                        <div>
                            <p>{{ (immobilie.units.length === 1) ? immobilie.stats.price.min : (immobilie.stats.price.min + ' - ' + immobilie.stats.price.max) }}</p>
                        </div>
                    </td>
                    <td>
                        <div>
                            <p>{{ (immobilie.units.length === 1) ? immobilie.stats.rent.min : (immobilie.stats.rent.min + ' - ' + immobilie.stats.rent.max) }}</p>
                        </div>
                    </td>
                    <td>
                        <div>
                            <p>{{ (immobilie.units.length === 1) ? immobilie.stats.yield.min : (immobilie.stats.yield.min + ' - ' + immobilie.stats.yield.max) }}</p>
                        </div>
                    </td>
                    <td>
                        <div>
                            <p>{{ (immobilie.units.length === 1) ? immobilie.stats.area.min : (immobilie.stats.area.min + ' - ' + immobilie.stats.area.max) }}</p>
                        </div>
                    </td>
                    <td>
                        <div class="btns" v-if="useState<User>('user').value?.role === 'ADMIN'">
                            <Button :title="t('view')" :to="getImmobilieUrl(immobilie.id)" :external="false"><HouseIcon/></Button>
                            <Button :title="t('delete')" variant="secondary" @click="showDelPopover(immobilie.id)"><DeleteIcon/></Button>
                            <Button :title="t('edit')" :to="`/immobilien/edit/${immobilie.id}`" variant="secondary"><EditIcon/></Button>
                            <Button variant="secondary" :title="(immobilie.hidden) ? t('show') : t('hide')" @click="immobilien.hideImmobilie(immobilie.id)"><UnhiddenIcon v-if="immobilie.hidden"/><HiddenIcon v-else/></Button>
                        </div>
                        <div class="btns" v-else>
                            <Button :title="t('view')" :to="getImmobilieUrl(immobilie.id)"><HouseIcon/></Button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <Popover v-model="popoverHidden">
        <div class="dialog-box">
            <span>{{ t('popover.label') }}</span>
            <p>{{ t('popover.message') }}</p>
            <div>
                <Button variant="primary" @click="deleteImmobilie">{{ t('popover.confirm') }}</Button>
                <Button variant="secondary" @click="popoverHidden=true">{{ t('popover.cancel') }}</Button>
            </div>
        </div>
    </Popover>
</template>

<style lang="css" scoped>
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

tr.hidden:hover {
  opacity: 100%;
}

tr.hidden {
  opacity: 30%;
  transition: opacity 0.3s ease;
}

td>.btns>:deep(.button) {
    padding: 8px;
}

td>.btns {
    display: flex;
    flex-flow: row wrap;
    min-width: 200px;
    gap: 10px;
}

.long-text {
    text-align: left;
}

.immo-table {
    overflow-x: scroll;
    text-align: center;
    padding: 10px;
}

th, td {
    padding: 15px;
    box-sizing: border-box;
}

th {
    background-color: var(--pink);
    color: var(--white);
    font-weight: 700;
    white-space: nowrap;
}

thead>tr>th:first-child {
    border-top-left-radius: 15px;
}

thead>tr>th:last-child {
    border-top-right-radius: 15px;
}

tbody>tr:last-child>td:first-child {
    border-bottom-left-radius: 15px;
}

tbody>tr:last-child>td:last-child {
    border-bottom-right-radius: 15px;
}

tbody>tr:nth-child(odd) {
    background-color: var(--input-background);
}

tbody>tr:nth-child(even) {
    background-color: var(--contrast-5-percent);
}

tr>td:nth-child(odd), tr>th:nth-child(odd) {
    border-right: 1px solid var(--contrast-10-percent);
    border-left: 1px solid var(--contrast-10-percent);
}


tr>td:first-child, tr>th:first-child {
    border-left: none;
}

tr>td:last-child, tr>th:last-child {
    border-right: none;
}
</style>

<i18n lang="json">
{
  "en": {
    "title": "Title",
    "description": "Description",
    "location": "Location",
    "units": "Units",
    "year": "Year",
    "price": "Price",
    "rent": "Rent",
    "yield": "Rental Yield p. a.",
    "area": "Area",
    "actions": "Actions",
    "edit": "Edit property",
    "hide": "Hide property",
    "show": "Show property",
    "delete": "Delete property",
    "view": "To Property Page",
    "popover": {
        "label": "Are you sure?",
        "message": "The real-estate object will be permanently deleted.",
        "confirm": "Confirm",
        "cancel": "Cancel"
    }
  },
  "de": {
    "title": "Titel",
    "description": "Beschreibung",
    "location": "Standort",
    "units": "Einheiten",
    "year": "Baujahr",
    "price": "Kaufpreis",
    "rent": "Miete",
    "yield": "Mietrendite p. a.",
    "area": "Fläche",
    "actions": "Aktionen",
    "edit": "Objekt bearbeiten",
    "hide": "Objekt verbergen",
    "show": "Objekt anzeigen",
    "delete": "Objekt löschen",
    "view": "zur Objektseite",
    "popover": {
        "label": "Bist du sicher?",
        "message": "Das Immobilienobjekt wird dauerhaft gelöscht.",
        "confirm": "Bestätigen",
        "cancel": "Abbrechen"
    }
  }
}

</i18n>