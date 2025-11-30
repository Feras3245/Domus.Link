import type { Params } from "~/types/nav";

export const useMetaStore = defineStore('meta', () => {
    const params = ref<Params>();
    const unitTypes = ref<string[]>([]);

    async function fetchParams() {
        try {
            const response = await $fetch<Params>('/api/meta/immobilien', {
                method: 'GET',
                headers: useRequestHeaders()
            });
            params.value = response;
            return response;
        } catch (reason) {
            console.error(reason);
            throw reason;
        }
    }

    async function fetchUnitTypes() {
        try {
            const response = await $fetch<{unit_types: string[]}>('/api/meta/utypes', {
                method: 'GET',
                headers: useRequestHeaders()
            });
            unitTypes.value = response.unit_types;
            return response.unit_types;
        } catch (reason) {
            console.error(reason);
            throw reason;
        }
    }

    return {
        params, unitTypes,
        fetchParams,
        fetchUnitTypes
    }
});