<script setup lang="ts">
const { t } = useI18n();
</script>

<template>
<footer>
    <div class="root">
        <div class="row">
            <div class="column">
                <Button variant="tertiary" :title="t('home')" to="/" external><HouseIcon/>{{ t('home') }}</Button>
                <span class="address" :title="t('address')"><LocationIcon style="height: var(--font-size);"/><p>Deutschland.Immobilien<br>AG Georgstr. 44<br>30159 Hannover</p></span>
            </div>
            <div class="column">
                <Button variant="tertiary" :title="t('contact')"><MessageIcon/>{{ t('contact') }}</Button>
                <Button variant="tertiary" to="mailto:info@deutschland.immobilien" :title="t('email')"><MailIcon/>info@deutschland.immobilien</Button>
                <Button variant="tertiary" to="tel:08009991212" :title="t('call')"><TelephoneIcon/>0800 9991212*</Button>
            </div>
            <div class="column">
                <Button variant="tertiary" :title="t('buy')"><HomeBuyIcon/>{{ t('buy') }}</Button>
                <Button variant="tertiary" :title="t('events')"><CalendarIcon/>{{ t('events') }}</Button>
                <Button variant="tertiary" :title="t('careers')"><SuitcaseIcon/>{{ t('careers') }}</Button>
            </div>
        </div>
        <div class="line horizontal"></div>
        <div class="row">
            <div class="row">
                <Button variant="tertiary" :title="t('privacy')">{{ t('privacy') }}</Button>
                <div class="line vertical"></div>
                <Button variant="tertiary" :title="t('imprint')">{{ t('imprint') }}</Button>
                <div class="line vertical"></div>
                <Button variant="tertiary" :title="t('terms')">{{ t('terms') }}</Button>
                <div class="line vertical"></div>
                <Button variant="tertiary" :title="t('cookies')">{{ t('cookies') }}</Button>
                <div class="line vertical"></div>
                <Button variant="tertiary" :title="t('whistleblower')">{{ t('whistleblower') }}</Button>
            </div>
            <div class="row">
                <Button variant="tertiary" :size="2" title="LinkedIn"><LinkedinIcon/></Button>
                <Button variant="tertiary" :size="2" title="Xing"><XingIcon/></Button>
                <Button variant="tertiary" :size="2" title="Instagram"><InstagramIcon/></Button>
                <Button variant="tertiary" :size="2" title="Facebook"><FacebookIcon2/></Button>
                <Button variant="tertiary" :size="2" title="Youtube"><YoutubeIcon/></Button>
            </div>
        </div>
        <div class="line horizontal"></div>
        <div class="row">
            <Logo orientation="h" :size="1"/>
            <p class="copyrights">© Copyright 2025 Deutschland.Immobilien GmbH</p>
        </div>
    </div>
</footer>
</template>

<style lang="css" scoped>
footer {
    background-color: var(--contrast-5-percent);
    padding: 30px 0;
}

.root {
    display: flex;
    flex-flow: column nowrap;
    align-items: stretch;
    gap: 20px;
    max-width: 90%;
    margin: auto;
}

.row {
    display: flex;
    flex-flow: row wrap;
    align-items: stretch;
    justify-content: space-between;
    flex: 1 1 auto;
    gap: 10px;
}

.column {
    display: flex;
    flex-flow: column nowrap;
    align-items: flex-start;
    gap: 15px;
}

footer:deep(.button.tertiary) {
    color: var(--contrast-reverse);
    padding: 0;
}

footer:deep(.button.tertiary:hover) {
    color: var(--pink);
}

footer:deep(.button.tertiary:active) {
    color: var(--orange);
}

.address {
    display: flex;
    flex-flow: row nowrap;
    gap: 7px;
}

.line {
    background-color: var(--grey);
}

.line.vertical {
    width: 2px;
}

.line.horizontal {
    height: 2px;
}

.row>.row {
    justify-content: center;
    flex: 1 1 fit-content;
}

.copyrights {
    font-size: calc(var(--font-size) * 0.7);
    margin-top: 20px;
}
</style>


<i18n lang="json">
{
    "en": {
        "home": "Homepage",
        "contact": "Contact form",
        "buy": "Buy real-estate",
        "events": "Events",
        "careers": "Careers",
        "privacy": "Privacy policy",
        "imprint": "Imprint",
        "terms": "ToS",
        "cookies": "Cookie settings",
        "whistleblower": "Whistleblower system",
        "email": "Send us an email",
        "call": "Call us",
        "address": "Location of our headquarters"
    },
    "de": {
        "home": "Stratseite",
        "contact": "Kontaktformular",
        "buy": "Immobilie kaufen",
        "events": "Veranstaltungen",
        "careers": "Karriere",
        "privacy": "Datenschutzerklärung",
        "imprint": "Impressum",
        "terms": "AGB",
        "cookies": "Cookie-Einstellungen",
        "whistleblower": "Hinweisgebersystem",
        "email": "Schick uns eine E-Mail",
        "call": "Ruf uns an",
        "address": "Standort unseres Hauptsitzes"
    }
}
</i18n>