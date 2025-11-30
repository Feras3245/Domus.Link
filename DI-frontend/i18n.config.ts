export default defineI18nConfig(() => ({
    legacy: false,
    globalInjection: true,
    locale: 'de',
    fallbackLocale: 'de',
    messages: {
      en: {
          "location": "Location",
          "address": "Address",
          "city": "City",
          "plz": "Postal code",
          "state": "State",
          "usage": "Usage type",
          "WOHNEN":"Residential",
          "PFLEGE":"Care",
          "MICRO":"Micro",
          "type": "Project type",
          "NEUBAU":"New",
          "BESTAND":"Existing"
      },

      de: {
          "location": "Standort",
          "address": "Adresse",
          "city": "Stadt",
          "plz": "Postleitzahl",
          "state": "Land",
          "usage": "Nutzungstyp",
          "WOHNEN": "Wohnen",
          "PFLEGE": "Pflege",
          "MICRO": "Mikro",
          "type": "Projekttyp",
          "NEUBAU": "Neubau",
          "BESTAND": "Bestand"
      }
    }
  }))
  