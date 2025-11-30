<script lang="ts" setup>
const immobilienStore = useImmobilienStore();
await useAsyncData('init-index', () => immobilienStore.fetchImmobilien({}), {server: true});
</script>

<template>
    <div>
        <Header page="INDEX"></Header>  
        <ImmoBrowser
        :immobilien="immobilienStore.immobilien"
        :loading="immobilienStore.loading"
        :pagination="immobilienStore.pagination"
        @update="(query:object) => immobilienStore.fetchImmobilien(query)">
        </ImmoBrowser>
    </div>
</template>

<style lang="css" scoped>
</style>