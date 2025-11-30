<script lang="ts" setup>
const immobilienStore = useImmobilienStore();
await useAsyncData('init-pflege', () => immobilienStore.fetchImmobilien({usage:'PFLEGE'}), {server: true});
</script>

<template>
    <div>
        <Header page="PFLEGE"></Header>  
        <ImmoBrowser
        :immobilien="immobilienStore.immobilien"
        :loading="immobilienStore.loading"
        :pagination="immobilienStore.pagination"
        @update="(query:object) => immobilienStore.fetchImmobilien({...query, usage:'PFLEGE'})">
        </ImmoBrowser>
    </div>
</template>

<style lang="css" scoped>
</style>