<script setup lang="ts">
import { getImageUrl } from '~/utils/url';
const { t, locale } = useI18n();

const props = defineProps<{
    ownerId: string,
    ownerType: string,
    loading?: boolean
}>();

const images = defineModel<string[]>({required: true});
const popoverImageIndex = ref<number|undefined>(0);
const immobilienStore = useImmobilienStore();
const emits = defineEmits(['upload', 'delete']);
const popover = ref<{
    hidden: boolean,
    type: 'MAGNIFY'|'SINGLE'|'MULTIPLE'
}>({hidden: true, type: 'MAGNIFY'});

function showImageLarge(index:number) {
    popoverImageIndex.value = index;
    popover.value.type = 'MAGNIFY';
    popover.value.hidden = false;
}

function showDeleteImageConfirmation(index:number) {
    popoverImageIndex.value = index;
    popover.value.type = 'SINGLE';
    popover.value.hidden = false;
}

const imageUpload = useTemplateRef<HTMLInputElement>('image-upload');
const form = useTemplateRef<HTMLFormElement>('upload-form')
function clickedImageUpload() {
    imageUpload.value?.click();
}

function imageUploadEvent() {
    if (imageUpload.value && imageUpload.value.files) {
        const files = Array.from(imageUpload.value.files);
        emits('upload', files);
        form.value?.reset();
    }
}

function cancel() {
    selected.value = [];
    popoverImageIndex.value = undefined;
    popover.value.hidden = true;
}

function deleteImage() {
    if (popoverImageIndex.value) {
        emits('delete', [images.value[popoverImageIndex.value]]);
        popoverImageIndex.value = undefined;
        popover.value.hidden = true;
    }
}

const selected = ref<number[]>([]);
const selectEnabled = ref<boolean>(false);

function cancelSelect() {
    selected.value = [];
    selectEnabled.value = false;
}

function deselectAll() {
    selected.value = []
}

function selectAll() {
    selected.value = [];
    for (let i = 0; i < images.value.length; i++) {
        selected.value.push(i);
    }
}

function showDeleteImagesConfirmation() {
    if (selected.value.length > 0) {
        popover.value.type = 'MULTIPLE';
        popover.value.hidden = false;
    }
}

function deleteImages() {
    if (selected.value.length > 0) {
        const selectedIds = selected.value.map(i => images.value[i]);
        emits('delete', selectedIds);
        selected.value = [];
        popover.value.hidden = true;
        cancelSelect();
    }
}
</script>

<template>
    <div class="image-manager">
        <div class="btns">
            <Button variant="secondary" v-if="!selectEnabled" @click="selectEnabled = true">{{ t('select') }}</Button>
            <Button variant="primary" v-if="selectEnabled" @click="cancelSelect">{{ t('cancel') }}</Button>
            <Button variant="secondary" v-if="selectEnabled" @click="selectAll">{{ t('select-all') }}</Button>
            <Button variant="secondary" v-if="selectEnabled" @click="deselectAll">{{ t('deselect-all') }}</Button>
            <Button variant="primary" v-if="selectEnabled" @click="showDeleteImagesConfirmation">{{ t('delete') }}</Button>
        </div>
        <div class="images">
            <div class="image" v-for="(image, index) in images" :key="index">
                <div class="img-btns" v-if="!selectEnabled">
                    <Button @click="showDeleteImageConfirmation(index)" :title="t('del-image')"><DeleteIcon/></Button>
                    <Button @click="showImageLarge(index)" :title="t('magnify')"><MagnifyIcon/></Button>
                </div>
                <div class="img-btns" v-else>
                    <Button 
                    style="background-color: var(--red);"
                    v-if="selected.includes(index)" 
                    @click="selected = selected.filter(n => n !== index);"
                    :title="t('deselect')">
                        <FailureIcon/>
                    </Button>
                    <Button
                    style="background-color: var(--grey);"
                    v-else @click="selected.push(index)"
                    :title="t('select')">
                        <SuccessIcon/>
                    </Button>
                </div>
                <NuxtImg  :src="getImageUrl(ownerType, ownerId, image, 'small')" :title="image"/>
            </div>
            <div class="loading" v-if="loading">
                <LoadingIcon></LoadingIcon>
            </div>
            <Button v-if="!selectEnabled" style="height: 200px;box-sizing: border-box;" @click="clickedImageUpload" :title="t('upload')">
                <PlusIcon style="width: 60px; height: 60px;"/>
            </Button>
            <form ref="upload-form">
                <input
                class="image-upload"
                id="image-upload"
                multiple
                ref="image-upload"
                type="file"
                style="display: none;"
                accept="image/*"
                @input="imageUploadEvent">
            </form>
        </div>
    </div>
    <Popover v-model="popover.hidden">
        <NuxtImg v-if="popover.type === 'MAGNIFY' && (popoverImageIndex !== undefined)" :src="getImageUrl(ownerType, ownerId, images[popoverImageIndex], 'large')" class="popover-image"></NuxtImg>
        <div class="dialog-box" v-if="popover.type === 'SINGLE'">
            <span>{{ t('confirm.label') }}</span>
            <p>{{ t('confirm.single') }}</p>
            <div>
                <Button variant="primary" @click="deleteImage">{{ t('yes') }}</Button>
                <Button variant="secondary" @click="cancel">{{ t('cancel') }}</Button>
            </div>
        </div>
        <div class="dialog-box" v-if="popover.type === 'MULTIPLE'">
            <span>{{ t('confirm.label') }}</span>
            <p>{{ t('confirm.multiple') }}</p>
            <div>
                <Button variant="primary" @click="deleteImages">{{ t('yes') }}</Button>
                <Button variant="secondary" @click="cancel">{{ t('cancel') }}</Button>
            </div>
        </div>
    </Popover>
</template>

<style lang="css" scoped>
.btns {
    /* margin-left: 20px; */
    padding: 10px 0;
    display: flex;
    flex-flow: row wrap;
    gap: 10px;
}

.loading>:deep(svg) {
    height: 150px;
    color: var(--pink);
}

.loading {
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    justify-content: center;
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

.popover-image {
    height: 90vh;
    width: 90vw;
    border-radius: 20px;
    object-fit: cover;
    box-shadow: 0px 5px 19px -7px rgba(0,0,0,1);
}

.images>:deep(.button.primary:active) {
    background-color: var(--pink);
}

.images>:deep(.button.primary:hover) {
    opacity: 100%;
}

.images>:deep(.button.primary) {
    background-color: var(--grey);
    opacity: 50%;
}

.img-btns>:deep(.button) {
    padding: 10px;
}

.img-btns {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    flex-flow: row nowrap;
    gap: 5px;
}

.image>:deep(img) {
    width: 300px;
    height: 200px;
}

.image {
    height: 200px;
    border-radius: 0.5rem;
    overflow: hidden;
    position: relative;
}

.images {
    display: grid;
    grid-template-columns: repeat(auto-fill, 300px);
    justify-content: center;
    gap: 10px;
    box-sizing: border-box;
}
</style>

<i18n lang="json">
{
    "en": {
        "select": "Select",
        "deselect": "Deselect",
        "cancel": "Cancel",
        "select-all": "Select all",
        "deselect-all": "Deselect all",
        "delete": "Delete",
        "del-image": "Delete image",
        "magnify": "View full image",
        "upload": "Upload new image(s)",
        "close": "Close",
        "confirm": {
            "label": "Are you sure?",
            "single": "The image will be permanently deleted.",
            "multiple": "The selected images will be permanently deleted"
        },
        "yes": "Confirm"
    },
    "de": {
        "select": "Auswählen",
        "deselect": "Abwählen",
        "cancel": "Abbrechen",
        "select-all": "Alle auswählen",
        "deselect-all": "Alle abwählen",
        "delete": "Löschen",
        "del-image": "Bild löschen",
        "magnify": "Bild in voller Größe anzeigen",
        "upload": "Neues/n Bild(er) hochladen",
        "close": "Schließen",
        "confirm": {
            "label": "Bist du sicher?",
            "single": "Das Bild wird dauerhaft gelöscht.",
            "multiple": "Die ausgewählten Bilder werden dauerhaft gelöscht."
        },
        "yes": "Bestätigen"
    }
}
</i18n>
