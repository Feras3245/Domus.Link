<script setup lang="ts">
const navEN = [
    {
        text: "Properties",
        show: false,
        menu: [
          {
            title: "Care",
            text: "Demographic change as an opportunity",
            url: "/immobilien/pflege"
          },
          {
            title: "Residential",
            text: "Strong future market segment",
            url: "/immobilien/Wohnen"
          },
          {
            title: "New Construction",
            text: "High rental potential",
            url: "/immobilien/neubau"
          },
          {
            title: "Existing Properties",
            text: "Investment with immediate rental income",
            url: "/immobilien/bestand"
          },
          {
            title: "Micro / Student",
            text: "Future living concepts",
            url: "/immobilien/micro"
          }
        ]
    },

    {
        text: "Service",
        show: false,
        menu: [
          {
            title: "Your Dream Property",
            text: "We will find the right investment property for you",
            url: "#"
          },
          {
            title: "Real Estate Know-how",
            text: "Expert tips for your successful investment",
            url: "#"
          },
          {
            title: "Priority Placement Right PLUS",
            text: "Enjoy priority on the waiting list",
            url: "#"
          },
          {
            title: "Glossary",
            text: "All About Real Estate and Finance",
            url: "#"
          }
        ]
    },

    {
        text: "About us",
        show: false,
        menu: [
          {
            title: "Company",
            text: "With Passion for the Task",
            url: "#"
          },
          {
            title: "Team",
            text: "The DI Power Team",
            url: "#"
          },
          {
            title: "Career",
            text: "Launch Your Career with DI",
            url: "#"
          },
          {
            title: "Locations",
            text: "We welcome you!",
            url: "#"
          },
          {
            title: "News",
            text: "Constantly Updated",
            url: "#"
          }
        ]
    },
];


const navDE = [
    {
        text: "Immobilien",
        show: false,
        menu: [
            {
              "title": "Pflege",
              "text": "Demografischer Wandel als Chance",
              "url": "/immobilien/pflege"
            },
            {
              "title": "Wohnen",
              "text": "Starkes Zukunftsmarktsegment",
              "url": "/immobilien/Wohnen"
            },
            {
              "title": "Neubau",
              "text": "Hohes Mietpotenzial",
              "url": "/immobilien/neubau"
            },
            {
              "title": "Bestandsimmobilien",
              "text": "Investition mit sofortigen Mieteinnahmen",
              "url": "/immobilien/bestand"
            },
            {
              "title": "Micro / Studenten",
              "text": "Wohnkonzepte der Zukunft",
              "url": "/immobilien/micro"
            }
          ]

    },

    {
        text: "Dienstleistung",
        show: false,
        menu: [
          {
            title: "Ihre Wunschimmobilie",
            text: "Wir finden die passende Anlageimmobilie für Sie",
            url: "#"
          },
          {
            title: "Immobilien Know-how",
            text: "Expertentipps für Ihre erfolgreiche Kapitalanlage",
            url: "#"
          },
          {
            title: "Bevorzugtes Belegungsrecht PLUS",
            text: "Genießen Sie Wartelistenpriorität",
            url: "#"
          },
          {
            title: "Glossar",
            text: "Rund um Immobilien und Finanzen",
            url: "#"
          }
        ]
    },

    {
        text: "Über uns",
        show: false,
        menu: [
          {
            title: "Unternehmen",
            text: "Mit Leidenschaft bei der Sache",
            url: "#"
          },
          {
            title: "Team",
            text: "Das Powerteam von DI",
            url: "#"
          },
          {
            title: "Karriere",
            text: "Durchstarten mit DI",
            url: "#"
          },
          {
            title: "Standorte",
            text: "Herzlich willkommen!",
            url: "#"
          },
          {
            title: "News",
            text: "Stets up-to-date",
            url: "#"
          }
        ]
    },
];
</script>


<template>
    <NavBar :dropdowns="(useI18n().locale.value === 'de') ? navDE : navEN"/>
    <div style="height: 80px;"></div>
    <slot></slot>
    <Teleport to="#teleports">
      <Footer></Footer>
    </Teleport>
</template>