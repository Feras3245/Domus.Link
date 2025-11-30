<script setup lang="ts">
const { t } = useI18n();

const usage = ref<number>(0);
</script>

<template>
  <form action="#" method="post" enctype="multipart/form-data">
    <!-- Title -->
    <div>
      <label for="title-en">{{ t('title-en') }}</label>
      <input type="text" id="title-en" name="title_en" required />
    </div>
    <div>
      <label for="title-de">{{ t('title-de') }}</label>
      <input type="text" id="title-de" name="title_de" required />
    </div>

    <!-- Address -->
    <div>
      <label for="street">{{ t('street') }}</label>
      <input type="text" id="street" name="street" required />
    </div>
    <div>
      <label for="location">{{ t('location') }}</label>
      <input type="text" id="location" name="location" required />
    </div>

    <!-- Price, Rent, Rooms, Year -->
    <div>
      <label for="price">{{ t('price') }}</label>
      <input type="number" id="price" name="price" required />
    </div>
    <div>
      <label for="rent">{{ t('rent') }}</label>
      <input type="number" id="rent" name="rent" required />
    </div>
    <div>
      <label for="rooms">{{ t('rooms') }}</label>
      <input type="number" id="rooms" name="rooms" required />
    </div>
    <div>
      <label for="year">{{ t('year') }}</label>
      <input type="number" id="year" name="year" required />
    </div>

    <!-- Project Type -->
    <div class="radio">
      <span>{{ t('project-type') }}</span>
      <div>
        <div>
            <input type="radio" id="new" name="project_type" value="New" required />
            <label for="new">{{ t('project-new') }}</label>
        </div>
        <div>
            <input type="radio" id="existing" name="project_type" value="Existing" />
            <label for="existing">{{ t('project-existing') }}</label>
        </div>
      </div>
    </div>

    <!-- Usage Type -->
    <div>
      <label for="usage">{{ t('usage') }}</label>
      <DropdownSelector name="usage" 
      :items="[t('usage-residential'), t('usage-micro'), t('usage-care')]"
      v-model="usage"/>
    </div>

    <!-- Short Description -->
    <div>
      <label for="short-en">{{ t('short-en') }}</label>
      <input type="text" id="short-en" name="short_description_en" maxlength="255" required />
    </div>
    <div>
      <label for="short-de">{{ t('short-de') }}</label>
      <input type="text" id="short-de" name="short_description_de" maxlength="255" required />
    </div>

    <!-- Long Description -->
    <div>
      <label for="long-en">{{ t('long-en') }}</label>
      <textarea id="long-en" name="long_description_en" rows="6" required></textarea>
    </div>
    <div>
      <label for="long-de">{{ t('long-de') }}</label>
      <textarea id="long-de" name="long_description_de" rows="6" required></textarea>
    </div>

    <!-- Images -->
    <div>
      <label for="images">{{ t('images') }}</label>
      <input type="file" id="images" name="images" accept="image/*" multiple />
    </div>

    <Button type="submit">
      <MailIcon />
      {{ t('submit') }}
    </Button>
  </form>
</template>

<style scoped>
.radio input[type="radio"] {
    accent-color: var(--pink);
}

.radio>div {
    display: flex;
    gap: 20px;
}

.radio>div>div {
    display: inline-flex;
    align-items: baseline;
    gap: 5px;
}

.radio>div>div>label {
    font-weight: 400;
}

.radio>span {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    user-select: none;
}

form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

label {
  display: block;
  font-weight: 600;
  margin-bottom: 8px;
}

input[type="text"],
input[type="number"],
input[type="file"],
select,
textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  box-sizing: border-box;
}

textarea {
  resize: vertical;
}

input:focus,
select:focus,
textarea:focus {
  border-color: var(--pink);
  outline: none;
}
</style>

<i18n lang="json">
{
  "en": {
    "title-en": "Title (English)",
    "title-de": "Title (German)",
    "street": "Street",
    "location": "Location",
    "price": "Price (€)",
    "rent": "Rent per month (€)",
    "rooms": "Number of Rooms",
    "year": "Year Built",
    "project-type": "Project Type",
    "project-new": "New",
    "project-existing": "Existing",
    "usage": "Usage Type",
    "usage-residential": "Residential",
    "usage-micro": "Micro",
    "usage-care": "Care",
    "short-en": "Short Description (English)",
    "short-de": "Short Description (German)",
    "long-en": "Long Description (English)",
    "long-de": "Long Description (German)",
    "images": "Upload Images",
    "submit": "Add Immobilie"
  },
  "de": {
    "title-en": "Titel (Englisch)",
    "title-de": "Titel (Deutsch)",
    "street": "Straße",
    "location": "Ort",
    "price": "Preis (€)",
    "rent": "Miete pro Monat (€)",
    "rooms": "Zimmeranzahl",
    "year": "Baujahr",
    "project-type": "Projekttyp",
    "project-new": "Neubau",
    "project-existing": "Bestand",
    "usage": "Nutzungstyp",
    "usage-residential": "Wohnen",
    "usage-micro": "Mikro",
    "usage-care": "Pflege",
    "short-en": "Kurze Beschreibung (Englisch)",
    "short-de": "Kurze Beschreibung (Deutsch)",
    "long-en": "Lange Beschreibung (Englisch)",
    "long-de": "Lange Beschreibung (Deutsch)",
    "images": "Bilder hochladen",
    "submit": "Immobilie hinzufügen"
  }
}
</i18n>
