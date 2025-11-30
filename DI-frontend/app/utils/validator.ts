import { lands } from "~/types/domain";

interface ValidationRule {
    isValid: (value: any) => boolean;
    message: string;
}

type ValidationRules = {
    [key: string]: ValidationRule;
};

const validationRules: ValidationRules = {
    'title.de': {
        isValid: (value: string) => value.length > 0 && value.length <= 100,
        message: 'validation.title-de',
    },
    'title.en': {
        isValid: (value: string) => value.length > 0 && value.length <= 100,
        message: 'validation.title-en',
    },
    'address': {
        isValid: (value: string) => value.length > 0 && value.length <= 100,
        message: 'validation.address',
    },
    'zip': {
        isValid: (value: string) => value.length === 5,
        message: 'validation.zip',
    },
    'city': {
        isValid: (value: string) => value.length > 0 && value.length <= 80,
        message: 'validation.city',
    },
    'state': {
        isValid: (value: string) => (typeof lands !== 'undefined' && lands.includes(value)),
        message: 'validation.state',
    },
    'price': {
        isValid: (value: number) => !isNaN(value) && value >= 1,
        message: 'validation.price',
    },
    'rent': {
        isValid: (value: number) => !isNaN(value) && value >= 1,
        message: 'validation.rent',
    },
    'rooms': {
        isValid: (value: number) => !isNaN(value) && value >= 1,
        message: 'validation.rooms',
    },
    'area': {
        isValid: (value: number) => !isNaN(value) && value >= 1,
        message: 'validation.area',
    },
    'year': {
        isValid: (value: number) => {
            const currentYear = new Date().getFullYear();
            return Number.isInteger(value) && value >= 1000 && value <= currentYear;
        },
        message: 'validation.year',
    },
    'type': {
        isValid: (value: string) => ['NEUBAU', 'BESTAND'].includes(value),
        message: 'validation.type',
    },
    'usage': {
        isValid: (value: string) => ['WOHNEN', 'MICRO', 'PFLEGE'].includes(value),
        message: 'validation.usage',
    },
    'short_description.de': {
        isValid: (value: string) => value.length > 0 && value.length <= 100,
        message: 'validation.short-de',
    },
    'short_description.en': {
        isValid: (value: string) => value.length > 0 && value.length <= 100,
        message: 'validation.short-en',
    },
    'long_description.de': {
        isValid: (value: string) => value.length > 0 && value.length <= 1200,
        message: 'validation.long-de',
    },
    'long_description.en': {
        isValid: (value: string) => value.length > 0 && value.length <= 1200,
        message: 'validation.long-en',
    },
    'hidden': {
        isValid: (value: any) => typeof value === 'boolean',
        message: 'validation.hidden'
    }
};


type Validation = {
    valid: boolean,
    message: string
}

export default function validator(obj: any): Validation {
    for (const key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
            const rule = validationRules[key];
            const value = obj[key];
            if (rule && !rule.isValid(value)) {
                return {valid: false, message: rule.message};
            }
            if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
                 for (const nestedKey in value) {
                     if (Object.prototype.hasOwnProperty.call(value, nestedKey)) {
                        const fullKey = `${key}.${nestedKey}`;
                        const nestedRule = validationRules[fullKey];
                        const nestedValue = value[nestedKey];

                        if (nestedRule && !nestedRule.isValid(nestedValue)) {
                            return { valid: false, message: nestedRule.message };
                        }
                     }
                 }
            }
        }
    }
    return {valid: true, message: "validation.valid"};
}