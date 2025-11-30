<script setup lang="ts">
import type { User } from '~/types/auth';
import { type Immobilie } from '~/types/domain';
import { getImageUrl, getImmobilieUrl } from '~/utils/url';
const { t, locale } = useI18n();
const props = defineProps<{
  immobilie: Immobilie
}>();
const popoverHidden = ref<boolean>(true);
const emit = defineEmits(['delete', 'hide']);
</script>

<template>
<div class="object-card" :class="{hidden: immobilie.hidden}">
    <div class="admin-btns" v-if="useState<User>('user').value?.role === 'ADMIN'">
      <Button variant="primary" :title="t('edit')" :to="`/immobilien/edit/${immobilie.id}`"><EditIcon/></Button>
      <Button variant="primary" :title="(immobilie.hidden) ? t('show') : t('hide')" @click="$emit('hide')"><UnhiddenIcon v-if="immobilie.hidden"/><HiddenIcon v-else/></Button>
      <Button variant="primary" :title="t('delete')" @click="popoverHidden = false"><DeleteIcon/></Button>
    </div>
    <div class="root">
      <Carousel 
      :i18n="{
          ariaNextSlide: t('next'),
          ariaPreviousSlide: t('previous'),
          iconArrowLeft: t('previous'),
          iconArrowRight: t('next')
      }"
      :items-to-show="1" 
      :wrap-around="true" 
      :mouse-drag="false" 
      :touch-drag="false"
      style="height: 330px;">
          <Slide v-for="image in immobilie.images" :key="image">
              <NuxtImg :src="getImageUrl('immobilien', immobilie.id, image, 'small')" style="height: 100%; width: 100%; object-fit: cover;" draggable="false" loading="lazy"></NuxtImg>
          </Slide>
          <template #addons>
              <Navigation/>
              <Pagination/>
          </template>
      </Carousel>
      <div class="object-text">
          <div>
            <h5 class="title">{{ immobilie.title[locale] }}</h5>
            <span class="location">{{ immobilie.address }}, {{ immobilie.zip }} {{ immobilie.city }}</span>
            <p class="description">{{ immobilie.short_description[locale] }}</p>
          </div>
          <ImmoFacts :immobilie="immobilie" :verbose="false"/>
          <Button :size="1.2" class="object-button" :to="getImmobilieUrl(immobilie.id)">{{ t('button-text') }}</Button>
      </div>
      <Button :size="1.2" class="object-button" :to="getImmobilieUrl(immobilie.id)">{{ t('button-text') }}</Button>
  </div>
  <Popover v-model="popoverHidden">
    <div class="dialog-box">
        <span>{{ t('popover.label') }}</span>
        <p>{{ t('popover.message') }}</p>
        <div>
            <Button variant="primary" @click="$emit('delete')">{{ t('popover.confirm') }}</Button>
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

.object-card.hidden:hover {
  opacity: 100%;
}

.object-card.hidden {
  opacity: 50%;
  transition: opacity 0.3s ease;
}

.admin-btns>:deep(.button.primary) {
  padding: 5px;
}

.admin-btns {
  position: absolute;
  z-index: 10;
  display: flex;
  flex-flow: row nowrap;
  gap: 5px;
  left: 10px;
  top: 10px;
  opacity: 40%;
  transition: opacity 0.3s ease;
}

.admin-btns:hover {
  opacity: 100%;
}

.carousel {
  height: 35%;
}

.object-card {
  width: 100%;
  container-type: inline-size;
  height: 100%;
  position: relative;
}

.root {
  box-sizing: border-box;
  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
  align-items: stretch;
  gap: 10px;
  background-color: var(--input-background);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0px 5px 19px -7px rgba(0,0,0,1);
  /* 750px */
  /* 920 */
  height: 100%;
}

:deep(img){
  max-width: 100%;
}

.object-text {
  line-height: 1.4;
  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
  align-items: stretch;
  gap: 10px;
  padding: 5px 15px;
  flex: 1 1 auto;
}

.object-text>div{
  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
  align-items: stretch;
  text-align: center;
  gap: 5px;
}

.title{
  font-weight: 800;
  font-family: var(--font-alt);
  font-size: calc(var(--font-size) * 1.2);
}

.location{
  font-weight: 700;
}

.description{
  line-height: 1.1;
  font-weight: 400;
  font-size: calc(var(--font-size) * 0.9);
}

.info-list {
  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
  align-items: stretch;
  gap: 10px;
  font-size: calc(var(--font-size) * 0.8);
}

.info-item {
  display: flex;
  flex-flow: row nowrap;
  gap: 10px;
  align-items: center;
  padding: 10px;
  background-color: var(--contrast-normal);
  border-radius: 10px;
}

.info-item>p{
  margin-left: auto;
}

.info-item>:deep(svg){
  height: var(--font-size);
}

.root>.object-button {
  border-radius: 0;
  padding: 15px 0;
  border-bottom: 20px;
  font-weight: 400;
}

.object-text>.object-button {
  display: none;
  align-self: center;
}

.root:deep(.button.primary):hover{
  transform: unset;
}

@container (width > 550px) {
  .root {
    flex-flow: row nowrap;
    height: 100%;
  }

  :deep(.carousel) {
    max-width: 50%;
  }

  .root>.object-button {
    display: none;
  }

  .object-text>.object-button {
    display: flex;
  }

  .object-text {
    padding-top: 20px;
    padding-bottom: 20px;
    justify-content: space-around;
  }

  .object-text>div {
    text-align: left;
    gap: 10px;
  }

  .carousel {
    height: unset;
  }
}
</style>

<i18n lang="json">
{
  "en": {
    "image_alt": "Object Image",
    "year": "Year built",
    "price": "Price from",
    "rent": "Monthly rent",
    "area": "Total area",
    "yield": "Rental Yield p. a.",
    "button-text": "View Object",
    "next": "next",
    "previous": "previous",
    "edit": "Edit property",
    "hide": "Hide property",
    "show": "Show property",
    "delete": "Delete property",
    "popover": {
        "label": "Are you sure?",
        "message": "The real-estate object will be permanently deleted.",
        "confirm": "Confirm",
        "cancel": "Cancel"
    }
  },
  "de": {
    "image_alt": "Objekt Bild",
    "year": "Baujahr",
    "price": "Kaufspreis ab",
    "rent": "Monatliche Miete",
    "area": "Gesamtfläche",
    "yield": "Mietrendite p. a.",
    "button-text": "Zum Objekt",
    "next": "nächste",
    "previous": "vorherige",
    "edit": "Objekt bearbeiten",
    "hide": "Objekt verbergen",
    "show": "Objekt anzeigen",
    "delete": "Objekt löschen",
    "popover": {
        "label": "Bist du sicher?",
        "message": "Das Immobilienobjekt wird dauerhaft gelöscht.",
        "confirm": "Bestätigen",
        "cancel": "Abbrechen"
    }
  }
}
</i18n>