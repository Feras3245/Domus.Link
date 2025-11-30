<script setup lang="ts">
import { type Unit, type NewUnit } from '~/types/domain';
import VueMultiselect from 'vue-multiselect';
import { useMetaStore } from '~/stores/meta-store';

const { t } = useI18n();

const props = defineProps<{
    type: "VIEW"|"EDIT"|"NEW",
    immobilie?: string
}>();

const units = defineModel<Unit[]|NewUnit[]>({required: true});
const original = ref<Unit[]>(JSON.parse(JSON.stringify(units.value)));
const newUnits = ref<NewUnit[]>([]);
const changed = ref<boolean[]>(units.value.map(() => false));
const popoverHidden = ref<boolean>(true);
const deleteUnitIndex = ref<number|undefined>(undefined);
const immobilienStore = useImmobilienStore();
const metaStore = useMetaStore();

function detectChange(index:number) {
    if (JSON.stringify(units.value[index]) === JSON.stringify(original.value[index])) {
        changed.value[index] = false;
    } else {
        changed.value[index] = true;
    }
}

const notification = ref<{
    type: "INFO"|"WARNING"|"ERROR"|"OK"|"LOADING",
    text: string,
    show: boolean
}>({
    type: "INFO",
    text: "",
    show: false
});

function addNewRow() {
    if (props.type === 'EDIT') {
        newUnits.value.push({ price:0, rent:0, rooms:0, area:0, type:'Wohnung'});
    } else if (props.type === 'NEW') {
        units.value.push({id: 1, yield: 0, price:0, rent:0, rooms:0, area:0, type:'Wohnung'});
    }
}

function deleteNewUnit(index:number) {
    if (props.type === 'EDIT') {
        newUnits.value.splice(index, 1);
    } else if (props.type === 'NEW') {
        units.value.splice(index, 1);
    }
    
}

async function createNewUnit(index:number) {
    if (!props.immobilie) {
        throw Error('Immobilie ID was not provided to Units Table');
    }

    if (newUnits.value[index].price <= 0 || newUnits.value[index].rent <= 0 || newUnits.value[index].rooms <= 0 || newUnits.value[index].area <= 0
        || (typeof newUnits.value[index].price === 'string') || (typeof newUnits.value[index].rent === 'string') || (typeof newUnits.value[index].rooms === 'string') || (typeof newUnits.value[index].area === 'string')) {
        notification.value = {
            text: "notification.above-0",
            type: "WARNING",
            show: true
        }
        return;
    }

    try {
        notification.value = {
            text: "notification.saving",
            type: "LOADING",
            show: true
        }
        const response = await immobilienStore.createUnit(props.immobilie, newUnits.value[index]);
        notification.value = {
            text: "notification.added",
            type: "OK",
            show: true
        }
        units.value.push(response);
        newUnits.value.splice(index, 1);
        original.value = JSON.parse(JSON.stringify(units.value));
    } catch(reason) {
        notification.value = {
            text: "notification.unknown",
            type: "ERROR",
            show: true
        }
    }
}

function showDeleteDialog(index:number) {
    deleteUnitIndex.value = index;
    popoverHidden.value = false;
}

async function deleteUnit() {
    if (!props.immobilie) {
        throw Error('Immobilie ID was not provided to Units Table');
    }
    if (deleteUnitIndex.value !== undefined) {
        if (units.value.length === 1) {
            notification.value = {
                text: "notification.atleast-1",
                type: "WARNING",
                show: true
            }
            popoverHidden.value = true;
            return;
        }
        try {
            notification.value = {
                text: "notification.deleting",
                type: "LOADING",
                show: true
            }
            popoverHidden.value = true;
            const response = await immobilienStore.deleteUnit((units.value[deleteUnitIndex.value] as Unit).id);
            notification.value = {
                text: "notification.deleted",
                type: "OK",
                show: true
            }
            units.value.splice(deleteUnitIndex.value, 1);
            original.value = JSON.parse(JSON.stringify(units.value));
            deleteUnitIndex.value = undefined;
        } catch(error) {
            notification.value = {
                text: "notification.unknown",
                type: "ERROR",
                show: true
            }
        }
    }
}

function resetUnit(index:number) {
    units.value[index] = JSON.parse(JSON.stringify(original.value[index]));
    changed.value[index] = false;
}

async function updateUnit(index:number) {
    try {
        if (units.value[index].price <= 0 || units.value[index].rent <= 0 || units.value[index].rooms <= 0 || units.value[index].area <= 0
            || (typeof units.value[index].price === 'string') || (typeof units.value[index].rent === 'string') || (typeof units.value[index].rooms === 'string') || (typeof units.value[index].area === 'string')) {
            notification.value = {
                text: "notification.above-0",
                type: "WARNING",
                show: true
            }
            return;
        }
        let changes = {};
        if (units.value[index].price !== original.value[index].price) {
            changes = {...changes, price: units.value[index].price};
        }
        if (units.value[index].rent !== original.value[index].rent) {
            changes = {...changes, rent: units.value[index].rent};
        }
        if (units.value[index].area !== original.value[index].area) {
            changes = {...changes, area: units.value[index].area};
        }
        if (units.value[index].rooms !== original.value[index].rooms) {
            changes = {...changes, rooms: units.value[index].rooms};
        }
        if (units.value[index].type !== original.value[index].type) {
            changes = {...changes, type: units.value[index].type};
        }
        notification.value = {
            text: "notification.saving",
            type: "LOADING",
            show: true
        }
        const response = await immobilienStore.updateUnit((units.value[index] as Unit).id, changes)
        notification.value = {
            text: "notification.saved",
            type: "OK",
            show: true
        };
        original.value = JSON.parse(JSON.stringify(units.value));
        changed.value[index] = false;
    } catch(error) {
        notification.value = {
            text: "notification.unknown",
            type: "ERROR",
            show: true
        }
    }
}


await useAsyncData('unit=types', () => metaStore.fetchUnitTypes(), {server: true});
const typeOptions = ref<string[]>([]);
typeOptions.value = metaStore.unitTypes;
</script>

<template>
<div class="units-table">
    <table>
        <thead>
            <tr>
                <th>Nr.</th>
                <th>{{ t('price') }}</th>
                <th>{{ t('rent') }}</th>
                <th v-if="type === 'VIEW'">{{ t('yield') }}</th>
                <th>{{ t('area') }}</th>
                <th>{{ t('rooms') }}</th>
                <th>{{ t('type') }}</th>
                <th v-if="!(type==='VIEW')">{{ t('actions') }}</th>
            </tr>
        </thead>
        <tbody v-if="type==='EDIT'">
            <tr v-for="(unit, index) in units" :key="index">
                <td class="nr">
                    <div class="edit-cell">
                        <p>{{ index+1 }}</p>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.price" id="unit-price" @update:number="detectChange(index)"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.rent" id="unit-rent" @update:number="detectChange(index)"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.area" id="unit-area" @update:number="detectChange(index)"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.rooms" id="unit-rooms" @update:number="detectChange(index)"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <VueMultiselect
                        id="usage-selector"
                        :multiple="false" :taggable="true"
                        :show-labels="false"
                        :searchable="true"
                        :preselect-first="true"
                        :allowEmpty="false"
                        v-model="unit.type"
                        @update:model-value="detectChange(index)"
                        form="create-immo-form"
                        tag-placeholder="Add"
                        @tag="(tag:string) => {typeOptions.push(tag); unit.type=tag}"
                        :options="typeOptions">
                        </VueMultiselect>
                    </div>
                </td>
                <td>
                    <div class="btns">
                        <Button variant="primary" :title="t('save')" v-show="changed[index]" @click="updateUnit(index)"><SuccessIcon/></Button>
                        <Button variant="secondary" :title="t('reset')" v-show="changed[index]" @click="resetUnit(index)"><ResetIcon/></Button>
                        <Button variant="secondary" :title="t('delete')" @click="showDeleteDialog(index)"><DeleteIcon/></Button>
                    </div>
                </td>
            </tr>
            <tr v-for="(unit, index) in newUnits" :key="index">
                <td class="nr">
                    <div class="edit-cell">
                        <p>{{ units.length + (index+1) }}</p>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.price" id="unit-price"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.rent" id="unit-rent"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.area" id="unit-area"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.rooms" id="unit-rooms"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <VueMultiselect
                        id="usage-selector"
                        :multiple="false" :taggable="true"
                        :show-labels="false"
                        :searchable="true"
                        :preselect-first="true"
                        :allowEmpty="false"
                        v-model="unit.type"
                        form="create-immo-form"
                        tag-placeholder="Add"
                        @tag="(tag:string) => {typeOptions.push(tag); unit.type=tag}"
                        :options="typeOptions">
                        </VueMultiselect>
                    </div>
                </td>
                <td>
                    <div class="btns">
                        <Button variant="primary" :title="t('save')" @click="createNewUnit(index)"><SuccessIcon/></Button>
                        <Button variant="secondary" :title="t('delete')" @click="deleteNewUnit(index)"><DeleteIcon/></Button>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="7" style="border: none; padding: 0;">
                    <div class="add-btn">
                        <Button variant="tertiary" @click="addNewRow"><PlusIcon/></Button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-if="type === 'NEW'">
            <tr v-for="(unit, index) in units" :key="index">
                <td class="nr">
                    <div class="edit-cell">
                        <p>{{ index + 1 }}</p>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.price" id="unit-price" @update:number="detectChange(index)"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.rent" id="unit-rent" @update:number="detectChange(index)"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.area" id="unit-area" @update:number="detectChange(index)"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <InputField type="number" v-model:number="unit.rooms" id="unit-rooms" @update:number="detectChange(index)"></InputField>
                    </div>
                </td>
                <td>
                    <div class="edit-cell">
                        <VueMultiselect
                        id="usage-selector"
                        :multiple="false" :taggable="true"
                        :show-labels="false"
                        :searchable="true"
                        :preselect-first="true"
                        :allowEmpty="false"
                        v-model="unit.type"
                        form="create-immo-form"
                        tag-placeholder="Add"
                        @tag="(tag:string) => {typeOptions.push(tag); unit.type=tag}"
                        :options="typeOptions">
                        </VueMultiselect>
                    </div>
                </td>
                <td>
                    <div class="btns">
                        <Button variant="secondary" :title="t('delete')" @click="deleteNewUnit(index)"><DeleteIcon/></Button>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="7" style="border: none; padding: 0;">
                    <div class="add-btn">
                        <Button variant="tertiary" @click="addNewRow"><PlusIcon/></Button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-if="type === 'VIEW'">
            <tr v-for="(unit, index) in units" :key="index">
                <td class="nr">
                    <div>
                        <p>{{ index+1 }}</p>
                    </div>
                </td>
                <td>
                    <div>
                        <p>&euro;{{ unit.price }}</p>
                    </div>
                </td>
                <td>
                    <div>
                        <p>&euro;{{ unit.rent }}</p>
                    </div>
                </td>
                <td>
                    <div>
                        <p>{{ (unit as Unit).yield }}%</p>
                    </div>
                </td>
                <td>
                    <div>
                        <p>{{ unit.area }}m²</p>
                    </div>
                </td>
                <td>
                    <div>
                        <p>{{ unit.rooms }}</p>
                    </div>
                </td>
                <td>
                    <div>
                        <p>{{ unit.type }}</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div v-if="notification.show" class="notification">
        <FormNotification :type="notification.type" :text="t(notification.text)"/>
    </div>
    <Popover v-model="popoverHidden">
        <div class="dialog-box">
            <span>{{ t('popover.label') }}</span>
            <p>{{ t('popover.message') }}</p>
            <div>
                <Button variant="primary" @click="deleteUnit">{{ t('popover.confirm') }}</Button>
                <Button variant="secondary" @click="popoverHidden=true">{{ t('popover.cancel') }}</Button>
            </div>
        </div>
    </Popover>
</div>
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

.notification {
    padding: 5px;
}

.add-btn {
    display: flex;
    flex-flow: column;
}

.btns>:deep(.button) {
    padding: 10px;
}

.btns {
    display: flex;
    flex-flow: row nowrap;
    justify-content: center;
    gap: 10px;
}

.edit-cell {
    display: flex;
    flex-flow: column nowrap;
}

.units-table {
    overflow-x: scroll;
    text-align: center;
    scrollbar-width: none;
    display: flex;
    flex-flow: column nowrap;
    overflow: visible;
}

.units-table>table {
    width: 100%;
}

th, td {
    padding: 15px;
    box-sizing: border-box;
    vertical-align: top;
}

td.nr {
    vertical-align: middle;
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
    "price": "Price",
    "rent": "Rent",
    "yield": "Yield",
    "area": "Area",
    "rooms": "Rooms",
    "actions": "Actions",
    "save": "Save",
    "reset": "Reset",
    "delete": "Delete",
    "type": "Type",
    "notification": {
        "above-0": "All units must have attribute values above 0",
        "unknown": "Something went wrong!",
        "saving": "Saving Changes...",
        "saved": "Chanes were successfully saved",
        "added": "New unit was successfully added",
        "atleast-1": "A property must have at least one unit",
        "deleted": "Unit was successfully deleted",
        "deleting": "Deleting unit..."
    },
    "popover": {
        "label": "Are you sure?",
        "message": "The unit will be permenantely deleted.",
        "confirm": "Confirm",
        "cancel": "Cancel"
    }
  },

  "de": {
    "price": "Preis",
    "rent": "Miete",
    "yield": "Rendite",
    "area": "Fläche",
    "rooms": "Zimmer",
    "actions": "Aktionen",
    "save": "Speichern",
    "reset": "Zurücksetzen",
    "delete": "Löschen",
    "type": "Typ",
    "notification": {
        "above-0": "Alle Einheiten müssen Eigenschaftswerte über 0 haben",
        "unknown": "Etwas ist schiefgelaufen!",
        "saving": "Änderungen speichern...",
        "saved": "Änderungen wurden erfolgreich gespeichert",
        "added": "Neue Einheit wurde erfolgreich hinzugefügt",
        "atleast-1": "Eine Immobilie muss mindestens eine Einheit haben",
        "deleted": "Einheit wurde erfolgreich gelöscht",
        "deleting": "Einheit löschen..."
    },
    "popover": {
        "label": "Bist du sicher?",
        "message": "Die Einheit wird dauerhaft gelöscht.",
        "confirm": "Bestätigen",
        "cancel": "Abbrechen"
    }
  }
}
</i18n>