<script setup lang="ts">
const { t, locale } = useI18n();
</script>

<template>
<form class="contact-form" method="post" @submit.prevent="">
    <div>
        <InputField 
        :label="t('telephone')" 
        :placeholder="t('tel-placeholder')"
        required
        pattern="[0-9\s\-\(\)\+]+"
        type="tel" 
        id="phone"
        :title="t('telephone')"
        orientation="v"></InputField>
    </div>
    <div class="time-group">
        <div>
            <InputField 
            :label="t('call-from')" 
            required
            type="time" 
            id="call-from"
            :title="t('call-from')"
            orientation="v"></InputField>
        </div>
        <div>
            <InputField 
            :label="t('call-to')" 
            required
            type="time" 
            id="call-to"
            :title="t('call-to')"
            orientation="v"></InputField>
        </div>
    </div>
    <div>
        <InputField 
        :label="t('message')" 
        required
        type="textarea" 
        id="message"
        :title="t('message')"
        orientation="v"></InputField>
    </div>
    <div class="btn">
        <Button type="submit">
            <MailIcon/>
            {{ t('submit') }}
        </Button>
    </div>
</form>
</template>

<style lang="css" scoped>
.time-group {
    display: flex;
    flex-flow: row nowrap;
    align-items: stretch;
    gap: 15px;
    &>* {
        flex: 1 1 50%;
    }
}

.contact-form {
    display: flex;
    flex-flow: column nowrap;
    gap: 15px;
    align-items: stretch;
    container-type: inline-size;
}

@container (width < 540px) {
    .time-group {
        flex-flow: column nowrap;
    }
    
    .btn {
        align-self: center;
    }

}
</style>

<i18n lang="json">
{
  "en": {
    "telephone": "Your Phone Number",
    "tel-placeholder": "e.g., +49 1234 567 8900",
    "call-from": "Appropriate time to call from",
    "call-to": "Appropriate time to call to",
    "message-placeholder": "Tell us what your enquiry is about...",
    "message": "Leave us a message",
    "submit": "Send message"
  },

  "de": {
    "telephone": "Ihre Telefonnummer",
    "tel-placeholder": "z.B. +49 1234 567 8900",
    "call-from": "Geeignete Anrufzeit von",
    "call-to": "Geeignete Anrufzeit bis",
    "message-placeholder": "Erzählen Sie uns, worum es in Ihrer Anfrage geht...",
    "message": "Hinterlassen Sie uns eine Nachricht",
    "submit": "Nachricht senden"
  }
}
</i18n>