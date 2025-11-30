import { joinURL, withQuery } from "ufo";

export function getImageUrl(ownerType:string, ownerId:string, imageId:string, size:string) {
    const base_url = useRuntimeConfig().public.nuxt_base_url;
    const imgUrl = withQuery(joinURL(base_url, "api/images/", ownerType, ownerId), { id: imageId, size:size });
    return imgUrl;
}

export function getImmobilieUrl(immobilieId: string) {
    const immobilieUrl = joinURL("/immobilien/", immobilieId);
    return immobilieUrl;
}