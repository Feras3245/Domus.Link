<script lang="ts" setup>
const immobilienStore = useImmobilienStore();
await useAsyncData('init-wohnen', () => immobilienStore.fetchImmobilien({usage:'WOHNEN'}), {server: true});
</script>

<template>
    <div>
        <Header page="WOHNEN"></Header>  
        <ImmoBrowser
        :immobilien="immobilienStore.immobilien"
        :loading="immobilienStore.loading"
        :pagination="immobilienStore.pagination"
        @update="(query:object) => immobilienStore.fetchImmobilien({...query, usage:'WOHNEN'})">
        </ImmoBrowser>
    </div>
</template>

<style lang="css" scoped>
</style>