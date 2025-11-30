import type { Immobilie } from "./domain"

export interface NavItem {
    title: string,
    text: string,
    url: string
}

export interface NavDropdown {
    text: string,
    show: boolean,
    menu: NavItem[]
}

export type Params = {
    locations: string[],
    price: {min: number, max: number},
    rent: {min: number, max: number},
    yield: {min: number, max: number},
    area: {min: number, max: number},
}

export type Filters = {
    price: [number, number],
    rent: [number, number],
    yield: [number, number],
    area: [number, number],
    type: 'BESTAND'|'NEUBAU'|'NONE',
    usage: 'WOHNEN'|'MICRO'|'PFLEGE'|'NONE',
    locations: string[]
}

export type Options = {
    viewAs: 'GRID'|'LIST',
    perPage: 3|6|9|12,
    sortBy: 'PRICE'|'YIELD'|'RENT'|'AREA'|'NONE',
    orderBy: 'ASC'|'DSC'
}

export type Page = 'INDEX'|'WOHNEN'|'PFLEGE'|'MICRO'|'NEUBAU'|'BESTAND';

export type PageLink = {
    url: string,
    label: string,
    active: boolean
}

export type PaginationMeta = {
    current_page: number,
    first_page_url: string,
    from: number,
    last_page: number,
    last_page_url: string,
    next_page_url: string,
    prev_page_url: string,
    path: string,
    per_page: number,
    to: number,
    total: number,
    links: PageLink[]
}

export type PaginatedResponse = {
    data: Immobilie[],
    meta: PaginationMeta
}

export type Notification = {
    show: boolean,
    text: string,
    type: "ERROR"|"WARNING"|"INFO"|"OK"|"LOADING"
}