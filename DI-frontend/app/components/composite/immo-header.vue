<script setup lang="ts">
import { type Immobilie } from '~/types/domain';
import { Carousel, type CarouselExposed } from "vue3-carousel/dist/carousel";
import { getImageUrl } from '~/utils/url';
const { t, locale } = useI18n();

const props = defineProps<{
    immobilie: Immobilie
}>();

const headerCarousel = ref<CarouselExposed>();
const popover = ref<{index: number, hidden: boolean}>({
    index: 0, 
    hidden: true
});
function showImageLarge(index:number) {
    popover.value.index = index;
    popover.value.hidden = false;
}
</script>

<template>
<header class="header" v-bind="$attrs">
    <Carousel 
    ref="headerCarousel"
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
    style="width: 100%; height: 100%;" 
    slide-effect="fade" :transition="200" 
    :autoplay="5000" 
    :pause-autoplay-on-hover="false">
        <Slide v-for="(image, index) in immobilie.images">
            <NuxtImg :src="getImageUrl('immobilien', immobilie.id, image, 'small')" draggable="false" loading="lazy" class="background" sizes="200px"></NuxtImg>
            <div class="foreground">
                <div class="content">
                    <h4 class="address">{{ immobilie.address }}, {{ immobilie.zip }} {{ immobilie.city }}</h4>
                    <h2 class="title">{{ immobilie.title[locale] }}</h2>
                    <p class="description">{{ immobilie.short_description[locale] }}</p>
                </div>
                <div class="image-carousel">
                    <div class="images">
                        <NuxtImg v-if="headerCarousel" :src="getImageUrl('immobilien', immobilie.id, (index > 0) ? immobilie.images[index-1] : immobilie.images[headerCarousel.maxSlide], 'medium')" 
                        draggable="false" loading="lazy" @click="headerCarousel?.prev()"
                        class="image previous"></NuxtImg>
                        <NuxtImg :src="getImageUrl('immobilien', immobilie.id, image, 'large')" draggable="false" loading="lazy" class="image current" @click="showImageLarge(index)"></NuxtImg>
                        <NuxtImg v-if="headerCarousel" @click="headerCarousel?.next()"
                        :src="getImageUrl('immobilien', immobilie.id, (index < (headerCarousel.maxSlide)) ? immobilie.images[index+1] : immobilie.images[0], 'medium')" 
                        draggable="false" loading="lazy" 
                        class="image previous"></NuxtImg>
                    </div>
                    <div class="carousel-controls">
                        <a class="arrow" @click="headerCarousel?.prev()"><ArrowIcon direction="left"/></a>
                        <div class="pages">
                            <a class="page" v-for="(img, idx) in immobilie.images" :key="idx"
                                :data-selected="(idx === headerCarousel?.currentSlide) ? 'yes' : 'no'"
                                @click="headerCarousel?.slideTo(idx)"><BarIcon/></a>
                        </div>
                        <a class="arrow" @click="headerCarousel?.next()"><ArrowIcon direction="right"/></a>
                    </div>
                </div>
            </div>
        </Slide>
    </Carousel>
</header>
<Popover v-model="popover.hidden">
    <div class="popover-content">
        <NuxtImg :src="getImageUrl('immobilien', immobilie.id, immobilie.images[popover.index], 'large')" class="popover-image"></NuxtImg>
    </div>
</Popover>
</template>

<style lang="css" scoped>
.popover-image {
    height: 90vh;
    width: 90vw;
    border-radius: 20px;
    object-fit: cover;
    box-shadow: 0px 5px 19px -7px rgba(0,0,0,1);
}

.image-carousel {
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    padding-bottom: 30px;
    margin: 0 20px;
}
.image-carousel>.images {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    justify-content: space-evenly;
    gap: 10px;
}

.foreground>.content>.description {
    font-size: calc(var(--font-size) * 1.3);
}

.foreground>.content>.address {
    padding: 5px;
    background-color: var(--pink);
    border-radius: 10px;
    font-size: calc(var(--font-size) * 1.3);
}

.foreground>.content>.title {
    font-family: var(--font-alt);
    font-size: calc(var(--font-size) * 2);
}

.page[data-selected="yes"] {
    color: var(--pink);
}

.carousel-controls>a:hover {
    color: var(--purple);
}

.carousel-controls>a:active {
    color: var(--pink);
}

.carousel-controls>.arrow {
    margin: 5px;
}

.carousel-controls>.pages {
    display: flex;
    flex-flow: row wrap;
    gap: 5px;
    align-items: center;
    justify-content: center;
}

.carousel-controls {
    display: flex;
    flex-flow: row nowrap;
    gap: 5px;
    align-items: center;
    justify-content: center;
    padding: 5px;
}

.carousel-controls a {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    justify-content: center;
    color: var(--contrast-reverse);
}
.arrow:deep(svg) {
    height: var(--font-size);
}
.page:deep(svg) {
    width: var(--font-size);
}

.header {
    /* height: 85vh; */
    height: 900px;
    position: relative;
}

.background {
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 0;
}

.foreground {
    width: 100%;
    height: 100%;
    position: absolute;
    background-color: rgba(from var(--contrast-normal) r g b / 50%);
    backdrop-filter: blur(10px);
    z-index: 10;
    display: flex;
    gap: 20px;
    flex-flow: column nowrap;
    align-items: stretch;
    justify-content: flex-end;
}

.foreground>.content {
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    gap: 10px;
    text-align: center;
    margin: 0 20px;
}

.image.current {
    height: 600px;
    width: 65%;
}

.image {
    object-fit: cover;
    border-radius: 20px;
    height: 300px;
    width: 15%;
    cursor: pointer;
    box-shadow: 0px 5px 19px -7px rgba(0,0,0,1);

}

@media screen and (max-width: 700px) {
    .image.current {
        width: 95vw;
    }

    .image.previous, 
    .image.next {
        display: none;
    }
}

@media screen and (max-width: 500px) {
    .header {
        height: 1000px;
    }
}
</style>

<i18n lang="json">
{
  "en": {
    "next": "next",
    "previous": "previous"
  },
  "de": {
    "next": "nächste",
    "previous": "vorherige"
  }
}
</i18n>