<script lang="ts" setup>
const immobilienStore = useImmobilienStore();
await useAsyncData('init-micro', () => immobilienStore.fetchImmobilien({usage:'MICRO'}), {server: true});
</script>

<template>
    <div>
        <Header page="MICRO"></Header>  
        <ImmoBrowser
        :immobilien="immobilienStore.immobilien"
        :loading="immobilienStore.loading"
        :pagination="immobilienStore.pagination"
        @update="(query:object) => immobilienStore.fetchImmobilien({...query, usage:'MICRO'})">
        </ImmoBrowser>
    </div>
</template>

<style lang="css" scoped>
</style>