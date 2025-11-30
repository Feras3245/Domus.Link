import { omit } from "@nuxt/ui/runtime/utils/index.js";
import { joinRelativeURL } from "ufo";
import type { Immobilie, NewImmobilie, NewUnit, Unit } from "~/types/domain";
import type { PaginatedResponse, Params } from "~/types/nav";

export const useImmobilienStore = defineStore('immobilien', () => {
    const immobilien = ref<Immobilie[]>([]);
    
    const pagination = ref<{current:number, last: number}>({current: 1, last: 1});
    const loading = ref<boolean>(false);

    async function fetchImmobilien(query:object){
        try {
            loading.value = true;
            const response = await $fetch<PaginatedResponse>('/api/immobilien', {
                method: 'GET',
                headers: {...useRequestHeaders(), "Accept": "application/json"},
                query: query
            });
            immobilien.value = response.data;
            pagination.value.current = response.meta.current_page;
            pagination.value.last = response.meta.last_page;
            loading.value = false;
            return response;
        } catch(reason) {
            console.error(reason);
            throw reason
        }
    }

    async function deleteImmobilie(id:string){
        try {
            const response = await $fetch(`/api/immobilien/${id}`, {
            method: 'DELETE',
            headers: {...useRequestHeaders(), "Accept": "application/json"}
            });
            const deletedIndex = immobilien.value.findIndex((value) => value.id === id);
            if (deletedIndex !== -1) {
                immobilien.value.splice(deletedIndex, 1);
            }
        } catch (reason) {
            console.error(reason);
            throw reason;
        }
    }

    async function hideImmobilie(id: string) {
        try {
            const csrf = useCsrf();
            const response = await $fetch(`/api/immobilien/${id}`, {
            method: 'PATCH',
            headers: {
                ...useRequestHeaders(),
                "Accept": "application/json",
                "CSRF-Token": `${csrf.csrf}`
            }
            });
            const hiddenIndex = immobilien.value.findIndex((value) => value.id === id);
            if (hiddenIndex !== -1) {
                immobilien.value[hiddenIndex].hidden = !immobilien.value[hiddenIndex].hidden;
            }
        } catch (reason) {
            console.error(reason);
            throw reason;
        }
    }

    async function fetchImmobilie(id:string) {
        const immobilieIndex = immobilien.value.findIndex((value) => value.id === id);
        if (immobilieIndex !== -1) {
            return immobilien.value[immobilieIndex];
        } else {
            try {
                const url = joinRelativeURL('/api/immobilien/', id);
                const response = await $fetch<Immobilie>(url, {
                    method: "GET",
                    headers: {...useRequestHeaders(), "Accept": "application/json"}
                });
                return response;
            } catch(reason) {
                console.error(reason);
                throw reason;
            }
        }
    }

    async function createImmobilie(immobilie:NewImmobilie) {
        try {
            const images = immobilie.images;
            const payload = omit(immobilie, ['images']);
            const csrf = useCsrf();
            const immoResponse = await $fetch<Immobilie>('/api/immobilien', {
                method: 'POST',
                headers: {
                    ...useRequestHeaders(),
                    "Accept": "application/json",
                    "CSRF-Token": `${csrf.csrf}`
                },
                body: payload
            });
            try {
                const formData = new FormData();
                images.forEach((image, index) => {
                    formData.append('image-'+index, image);
                });
                const url = joinRelativeURL('/api/images/', 'immobilien', immoResponse.id);
                const imagesResponse = await $fetch(url, {
                    method: 'POST',
                    headers: {
                        ...useRequestHeaders(),
                        "Accept": "application/json",
                        "CSRF-Token": `${csrf.csrf}`
                    },
                    body: formData
                });
                return immoResponse;
            } catch (reason) {
                console.error(reason);
                throw Error('error.upload-failed', {cause: reason});
            }
        } catch (reason) {
            console.error(reason);
            throw Error('error.create-failed', {cause: reason});
        }
    }

    async function updateImmobilie(id:string, changes: any): Promise<Immobilie> {
        try {
            const url = joinRelativeURL('/api/immobilien', id);
            const csrf = useCsrf();
            const response = await $fetch<Immobilie>(url, {
                method: 'PUT',
                headers: {
                    ...useRequestHeaders(),
                    "Accept": "application/json",
                    "CSRF-Token": `${csrf.csrf}`
                },
                body: changes
            });
            return response;
        } catch (reason) {
            console.error(reason);
            throw reason
        }
    }

    async function uploadImages(id: string, images: File[]) {
        try {
            const formData = new FormData();
            const csrf = useCsrf();
            images.forEach((image, index) => {
                formData.append('image-'+index, image);
            });
            const url = joinRelativeURL('/api/images/', 'immobilien', id);
            const imagesResponse = await $fetch<string[]>(url, {
                method: 'POST',
                headers: {
                    ...useRequestHeaders(),
                    "Accept": "application/json",
                    "CSRF-Token": `${csrf.csrf}`
                },
                body: formData
            });
            return imagesResponse;
        } catch (reason) {
            console.error(reason);
            throw reason;
        }
    }

    async function deleteimages(images: string[]) {
        try {
            const csrf = useCsrf();
            const response = await $fetch('/api/images/', {
                method: "DELETE",
                body: {
                    images: images
                }
            });
        } catch (reason) {
            console.error(reason);
            throw reason;
        }
    }

    async function createUnit(id:string, unit:NewUnit) {
        try {
            const csrf = useCsrf();
            const payload = {...unit, immobilie: id};
            const response = await $fetch<Unit>('/api/einheiten/', {
                method: 'POST',
                headers: {
                    ...useRequestHeaders(),
                    "Accept": "application/json",
                    "CSRF-Token": `${csrf.csrf}`
                },
                body: payload
            });
            return response;
        } catch(error) {
            console.error(error);
            throw error;
        }
    }

    async function deleteUnit(id: number) {
        try {
            const csrf = useCsrf();
            const url = joinRelativeURL("/api/einheiten/", "" + id);
            const response = await $fetch(url, {
                method: 'DELETE',
                headers: {
                    ...useRequestHeaders(),
                    "Accept": "application/json",
                    "CSRF-Token": `${csrf.csrf}`
                }
            });
        } catch (error) {
            console.error(error)
            throw error;
        }
    }

    async function updateUnit(id:number, changes:object) {
        try {
            const csrf = useCsrf();
            const url = joinRelativeURL("/api/einheiten/", "" + id);
            const response = await $fetch<Unit>(url, {
                method: 'PUT',
                headers: {
                    ...useRequestHeaders(),
                    "Accept": "application/json",
                    "CSRF-Token": `${csrf.csrf}`
                },
                body: changes
            });
            return response;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    return {immobilien, pagination, loading,
        deleteImmobilie, 
        hideImmobilie, 
        fetchImmobilie, 
        createImmobilie, 
        updateImmobilie, 
        uploadImages, 
        deleteimages,
        createUnit,
        deleteUnit,
        updateUnit,
        fetchImmobilien
    };
});