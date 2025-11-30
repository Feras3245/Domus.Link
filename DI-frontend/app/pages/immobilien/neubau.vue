<script lang="ts" setup>
const immobilienStore = useImmobilienStore();
await useAsyncData('init-neubau', () => immobilienStore.fetchImmobilien({type:'NEUBAU'}), {server: true});
</script>

<template>
    <div>
        <Header page="NEUBAU"></Header>  
        <ImmoBrowser
        :immobilien="immobilienStore.immobilien"
        :loading="immobilienStore.loading"
        :pagination="immobilienStore.pagination"
        @update="(query:object) => immobilienStore.fetchImmobilien({...query, type:'NEUBAU'})">
        </ImmoBrowser>
    </div>
</template>

<style lang="css" scoped>
</style>