<script setup lang="ts">
import type { User } from '~/types/auth';
import { type Immobilie } from '~/types/domain';
const { t, locale } = useI18n();

const route = useRoute();
const immobilienStore = useImmobilienStore();
const immobilie = ref<Immobilie>();
const popoverHidden = ref<boolean>(true);

{
    const response = await useAsyncData('immobilie', () => immobilienStore.fetchImmobilie((route.params.id as string)), { server: true });
    if (response.error.value) {
        response.error.value.fatal = true;
        throw response.error.value;
    } else if (response.data.value) {
        immobilie.value = response.data.value;
    }
}

async function toggleHidden() {
    if (immobilie.value) {
        try {
            await immobilienStore.hideImmobilie(immobilie.value.id);
            immobilie.value = await immobilienStore.fetchImmobilie((route.params.id as string));
        } catch(reason) {
            console.error(reason);
        }
    }
}

async function deleteImmo() {
    if (immobilie.value) {
        try {
            await immobilienStore.deleteImmobilie(immobilie.value.id);
            return navigateTo('/');
        } catch(reason) {
            console.error(reason);
        }
    }
}
</script>

<template>
<div v-if="immobilie">
    <ImmoHeader :immobilie="immobilie"/>
    <section class="immobilie">
        <div>
            <div class="property-description flex-col">
                <h5>{{ t('about-immo') }}</h5>
                <p>{{ immobilie.long_description[locale] }}</p>
            </div>
            <div class="property-info flex-col">
                <h5>{{ t('immo-info') }}</h5>
                <ImmoFacts :immobilie="immobilie" :verbose="true"/>
            </div>
        </div>
        <div>
            <div class="units flex-col">
                <h5>{{ t('units') }}</h5>
                <UnitsTable :edit="false" type="VIEW" v-model="immobilie.units"></UnitsTable>
            </div>
            <div class="admin-actions" v-if="useState<User>('user').value?.role === 'ADMIN'">
                <h5>{{ t('admin') }}</h5>
                <div class="btns">
                    <Button :to="`/immobilien/edit/${immobilie.id}`" :title="t('edit')"><EditIcon/>{{ t('edit') }}</Button>
                    <Button @click="toggleHidden" :title="(immobilie.hidden) ? t('show') : t('hide')">
                        <HideIcon v-if="!immobilie.hidden"/>
                        <ShowIcon v-else/>
                        {{ (immobilie.hidden) ? t('show') : t('hide') }}</Button>
                    <Button @click="popoverHidden = false" :title="t('delete')"><DeleteIcon/>{{ t('delete') }}</Button>
                </div>
            </div>
            <div class="enquiry">
                <h5>{{ t('interested') }}</h5>
                <ContactForm/>
            </div>
        </div>
    </section>
    <Popover v-model="popoverHidden">
        <div class="dialog-box">
            <span>{{ t('popover.label') }}</span>
            <p>{{ t('popover.message') }}</p>
            <div>
                <Button variant="primary" @click="deleteImmo">{{ t('popover.confirm') }}</Button>
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

.admin-actions>.btns {
    display: flex;
    flex-flow: row wrap;
    align-items: stretch;
    justify-content: flex-start;
    gap: 10px;
    padding: 10px 0;
}

.enquiry {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.immobilie>div {
    max-width: 50%;
    display: flex;
    flex-flow: column nowrap;
    gap: 20px;
    flex: 1 1 auto;
}

.property-description>p {
    line-height: 1.8;
}

.flex-row {
    display: flex;
    flex-flow: row;
    justify-content: space-between;
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

h5 {
    font-family: var(--font-alt);
    font-size: calc(var(--font-size) * 1.6);
}

.immobilie {
    display: flex;
    flex-flow: row nowrap;
    justify-content: space-evenly;
    align-items: stretch;
    max-width: 80%;
    margin: 5% auto;
    gap: 40px;
}

.flex-col {
    display: flex;
    flex-flow: column nowrap;
    align-items: stretch;
    gap: 10px;
}

@media (max-width: 820px) {
    .immobilie {
        max-width: 90%;
    }
}

@media (max-width: 720px) {
    .immobilie {
        flex-flow: column nowrap;
    }

    .immobilie>div {
        max-width: 100%;
    }
}
</style>

<i18n lang="json">
{
  "en": {
    "about-immo": "About this property",
    "immo-info": "Information",
    "interested": "Interested? Leave our agent a message and we'll call you soon",
    "admin":"Adminstrator Actions",
    "edit":"Edit",
    "hide":"Hide",
    "show":"Show",
    "delete":"Delete",
    "units": "Available Units",
    "popover": {
        "label": "Are you sure?",
        "message": "The real-estate object will be permanently deleted.",
        "confirm": "Confirm",
        "cancel": "Cancel"
    }
  },

  "de": {
    "about-immo": "Über diese Immobilie",
    "immo-info": "Informationen",
    "interested": "Interessiert? Hinterlassen Sie unserem Makler eine Nachricht und wir rufen Sie bald zurück",
    "admin":"Administratoraktionen",
    "edit":"Bearbeiten",
    "hide":"Verbergen",
    "show":"Anzeigen",
    "delete":"Löschen",
    "units": "Verfügbare Einheiten",
    "popover": {
        "label": "Bist du sicher?",
        "message": "Das Immobilienobjekt wird dauerhaft gelöscht.",
        "confirm": "Bestätigen",
        "cancel": "Abbrechen"
    }
  }
}
</i18n>