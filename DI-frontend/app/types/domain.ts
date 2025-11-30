export type Unit = {
    id: number,
    price: number,
    rent: number,
    yield: number,
    area: number,
    rooms: number,
    type: string
};

export type NewUnit = {
    price: number,
    rent: number,
    area: number,
    rooms: number,
    type: string
}

export type Immobilie = {
    id: string,
    title: {de: string, en: string},
    address: string,
    zip: string,
    city: string,
    state: 'Baden-Württemberg' | 'Bayern' | 'Berlin' | 'Brandenburg' | 'Bremen' | 'Hamburg' | 'Hessen' | 'Mecklenburg-Vorpommern' | 'Niedersachsen' | 'Nordrhein-Westfalen' | 'Rheinland-Pfalz' | 'Saarland' | 'Sachsen' | 'Sachsen-Anhalt' | 'Schleswig-Holstein' | 'Thüringen',
    units: Unit[],
    year: number,
    hidden: boolean,
    type: 'BESTAND' | 'NEUBAU',
    usage: 'WOHNEN' | 'MICRO' | 'PFLEGE',
    short_description: {de: string, en: string},
    long_description: {de: string, en: string},
    images: string[],
    stats: {
        price: {min: number, max: number},
        rent: {min: number, max: number},
        yield: {min: number, max: number},
        rooms: {min: number, max: number},
        area: {min: number, max: number},
    }
};

export type NewImmobilie = {
    title: {de: string, en: string},
    address: string,
    zip: string,
    city: string,
    state: 'Baden-Württemberg' | 'Bayern' | 'Berlin' | 'Brandenburg' | 'Bremen' | 'Hamburg' | 'Hessen' | 'Mecklenburg-Vorpommern' | 'Niedersachsen' | 'Nordrhein-Westfalen' | 'Rheinland-Pfalz' | 'Saarland' | 'Sachsen' | 'Sachsen-Anhalt' | 'Schleswig-Holstein' | 'Thüringen',
    units: NewUnit[],
    year: number,
    hidden: boolean,
    type: 'BESTAND' | 'NEUBAU',
    usage: 'WOHNEN' | 'MICRO' | 'PFLEGE',
    short_description: {de: string, en: string},
    long_description: {de: string, en: string},
    images: File[]
}



export const lands = [
    'Baden-Württemberg',
    'Bayern',
    'Berlin',
    'Brandenburg',
    'Bremen',
    'Hamburg',
    'Hessen',
    'Mecklenburg-Vorpommern',
    'Niedersachsen',
    'Nordrhein-Westfalen',
    'Rheinland-Pfalz',
    'Saarland',
    'Sachsen',
    'Sachsen-Anhalt',
    'Schleswig-Holstein',
    'Thüringen'
];