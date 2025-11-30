<script lang="ts" setup>
const immobilienStore = useImmobilienStore();
await useAsyncData('init-bestand', () => immobilienStore.fetchImmobilien({type:'BESTAND'}), {server: true});
</script>

<template>
    <div>
        <Header page="BESTAND"></Header>  
        <ImmoBrowser
        :immobilien="immobilienStore.immobilien"
        :loading="immobilienStore.loading"
        :pagination="immobilienStore.pagination"
        @update="(query:object) => immobilienStore.fetchImmobilien({...query, type:'BESTAND'})">
        </ImmoBrowser>
    </div>
</template>

<style lang="css" scoped>
</style>