# RDW Voertuig Informatie Tool

Een PHP-script om voertuiginformatie op te halen van de RDW Open Data API. Het script ondersteunt het ophalen van basisvoertuiginformatie, brandstofgegevens, carrosseriedetails, APK-historie, terugroepacties en meer.

## Functies

- **Basisvoertuiginformatie**: Kenteken, merk, model, voertuigsoort, en meer.
- **Brandstofgegevens**: Brandstoftype, verbruik, CO2-uitstoot en vermogen.
- **Carrosseriedetails**: Type carrosserie, beschrijving en specificaties.
- **APK-historie**: Inspectiedata, aantal gebreken en gebreken details.
- **Terugroepacties**: Status en details van terugroepacties.
- **Caching**: Slaat API-responses lokaal op om de laadtijd te verbeteren.

## Installatie

1. Zorg ervoor dat PHP is geïnstalleerd op je server.
2. Clone deze repository naar je server:
   ```bash
   git clone https://github.com/yourusername/rdw-voertuig-info.git
   cd rdw-voertuig-info
   ```
3. Maak een `cache` map aan voor het opslaan van tijdelijke data:
   ```bash
   mkdir cache
   chmod 777 cache
   ```

## Gebruik

### API-eindpunten
Het script ondersteunt de volgende RDW API-eindpunten:
- Basisvoertuiginformatie
- APK-historie
- Gebreken
- Brandstofgegevens
- Carrosseriedetails
- Asinformatie
- Voertuigklasse
- Toegevoegde objecten
- Terugroepstatus
- Terugroepdetails

### Voorbeeldverzoek
Stuur een GET-verzoek naar het script met het kenteken als parameter:
```
https://jouwdomein.nl/rdw-voertuig-info/index.php?kenteken=XD494P
```

### Voorbeeldrespons
```json
{
    "success": true,
    "data": {
        "vehicle_info": {
            "kenteken": "XD494P",
            "voertuigsoort": "Personenauto",
            "merk": "BMW",
            "handelsbenaming": "318D",
            "vervaldatum_apk": "20251017",
            "datum_tenaamstelling": "20190117",
            "bruto_bpm": "4003",
            "inrichting": "stationwagen",
            "aantal_zitplaatsen": "5",
            "eerste_kleur": "ZWART",
            "tweede_kleur": "Niet geregistreerd",
            "aantal_cilinders": "4",
            "cilinderinhoud": "1995",
            "massa_ledig_voertuig": "1455",
            "toegestane_maximum_massa_voertuig": "2075",
            "massa_rijklaar": "1555",
            "maximum_massa_trekken_ongeremd": "745",
            "maximum_trekken_massa_geremd": "1600",
            "datum_eerste_toelating": "20150120",
            "datum_eerste_tenaamstelling_in_nederland": "20190117",
            "wacht_op_keuren": "Geen verstrekking in Open Data",
            "catalogusprijs": "50012",
            "wam_verzekerd": "Ja",
            "aantal_deuren": "4",
            "aantal_wielen": "4",
            "europese_voertuigcategorie": "M1",
            "technische_max_massa_voertuig": "2075",
            "type": "3K",
            "typegoedkeuringsnummer": "e1*2007/46*0315*12",
            "variant": "3K11",
            "uitvoering": "5H150000",
            "volgnummer_wijziging_eu_typegoedkeuring": "1",
            "vermogen_massarijklaar": "0.07",
            "wielbasis": "281",
            "export_indicator": "Nee",
            "openstaande_terugroepactie_indicator": "Ja",
            "taxi_indicator": "Nee",
            "maximum_massa_samenstelling": "3750",
            "jaar_laatste_registratie_tellerstand": "2024",
            "tellerstandoordeel": "Geen oordeel",
            "code_toelichting_tellerstandoordeel": "05",
            "tenaamstellen_mogelijk": "Ja",
            "vervaldatum_apk_dt": "2025-10-17T00:00:00.000",
            "datum_tenaamstelling_dt": "2019-01-17T00:00:00.000",
            "datum_eerste_toelating_dt": "2015-01-20T00:00:00.000",
            "datum_eerste_tenaamstelling_in_nederland_dt": "2019-01-17T00:00:00.000",
            "zuinigheidsclassificatie": "D",
            "api_gekentekende_voertuigen_assen": "https://opendata.rdw.nl/resource/3huj-srit.json",
            "api_gekentekende_voertuigen_brandstof": "https://opendata.rdw.nl/resource/8ys7-d773.json",
            "api_gekentekende_voertuigen_carrosserie": "https://opendata.rdw.nl/resource/vezc-m2t6.json",
            "api_gekentekende_voertuigen_carrosserie_specifiek": "https://opendata.rdw.nl/resource/jhie-znh9.json",
            "api_gekentekende_voertuigen_voertuigklasse": "https://opendata.rdw.nl/resource/kmfi-hrps.json"
        },
        "technical": {
            "fuel": [
                {
                    "type": "Diesel",
                    "consumption": {
                        "city": 5.6,
                        "highway": 3.9,
                        "combined": 4.5
                    },
                    "emissions": {
                        "co2": 119,
                        "particles": 0.00012,
                        "roet": 0.51
                    },
                    "power": 105
                }
            ],
            "body": [
                {
                    "type": "AC",
                    "description": "Stationwagen",
                    "sequence": 1
                }
            ],
            "axles": [
                {
                    "number": 1,
                    "track_width": 154,
                    "max_load": {
                        "legal": 910,
                        "technical": 910
                    }
                },
                {
                    "number": 2,
                    "track_width": 158,
                    "max_load": {
                        "legal": 1230,
                        "technical": 1230
                    }
                }
            ]
        },
        "inspections": [
            {
                "date": "2022-11-22T14:07:00.000",
                "time": "1407",
                "defect_count": 2,
                "defects": [
                    {
                        "id": "289",
                        "description": "Schijf-, trommelrem loopt niet vrij",
                        "article": "5.*.31",
                        "start_date": "2017-04-01T00:00:00.000"
                    }
                ]
            },
            {
                "date": "2024-01-05T10:35:00.000",
                "time": "1035",
                "defect_count": 1,
                "defects": [
                    {
                        "id": "AC1",
                        "description": "Band(en) aanwezig met een profieldiepte van 1,6 t/m 2,5 mm",
                        "article": "AC1",
                        "start_date": "2012-05-09T00:00:00.000"
                    }
                ]
            },
            {
                "date": "2024-10-17T09:10:00.000",
                "time": "910",
                "defect_count": 1,
                "defects": [
                    {
                        "id": "AC2",
                        "description": "Schokdemper aanwezig die lekkage vertoont",
                        "article": "AC2",
                        "start_date": null
                    }
                ]
            }
        ],
        "recalls": [
            {
                "status": "Openstaande terugroepactie",
                "reference": "MGP230024",
                "details": {
                    "publication_date": "2023-01-27T00:00:00.000",
                    "defect_description": "De gasgenerator van de airbag kan bij activering door een aanrijding mogelijk ongecontroleerd tot ontploffing komen.",
                    "consequences": "De airbag kan mogelijk niet volledig ontvouwen of de metalen huls van de gasgenerator kan openscheuren en de delen kunnen in het interieur terechtkomen.",
                    "repair_description": "De voertuigeigenaar wordt verzocht contact op te nemen met de merkdealer. De bestuurdersairbag wordt vervangen.",
                    "contact_phone": "0800 0992234",
                    "risk_assessment": "GEM"
                }
            }
        ],
        "additional": []
    },
    "metadata": {
        "timestamp": "2025-03-04T19:38:37+00:00",
        "version": "1.0.3",
        "api_endpoints_used": {
            "api_gekentekende_voertuigen_basis": "https://opendata.rdw.nl/resource/m9d7-ebf2.json",
            "api_apk_historie": "https://opendata.rdw.nl/resource/a34c-vvps.json",
            "api_gebreken": "https://opendata.rdw.nl/resource/hx2c-gt7k.json",
            "api_gekentekende_voertuigen_brandstof": "https://opendata.rdw.nl/resource/8ys7-d773.json",
            "api_gekentekende_voertuigen_carrosserie": "https://opendata.rdw.nl/resource/vezc-m2t6.json",
            "api_gekentekende_voertuigen_carrosserie_specifiek": "https://opendata.rdw.nl/resource/jhie-znh9.json",
            "api_gekentekende_voertuigen_assen": "https://opendata.rdw.nl/resource/3huj-srit.json",
            "api_gekentekende_voertuigen_voertuigklasse": "https://opendata.rdw.nl/resource/kmfi-hrps.json",
            "api_toegevoegde_objecten": "https://opendata.rdw.nl/resource/sghb-dzxx.json",
            "api_terugroep_status": "https://opendata.rdw.nl/resource/t49b-isb7.json",
            "api_terugroep_details": "https://opendata.rdw.nl/resource/j9yg-7rg9.json"
        }
    },
    "error": null
}
```

## Foutafhandeling
Als er een fout optreedt, bevat de response een `error`-veld met details:
```json
{
    "success": false,
    "error": {
        "message": "Invalid license plate format",
        "code": 400
    }
}
```

## Caching
Het script maakt gebruik van caching om API-responses lokaal op te slaan. Standaard worden gegevens 1 uur (3600 seconden) gecached. Dit kan worden aangepast in de `fetchRDWData` functie.

## Licentie
Dit project is vrijgegeven onder de MIT-licentie. Zie het [LICENSE](LICENSE) bestand voor meer details.

---

**Let op:** Dit script is bedoeld voor educatieve en onderzoeksdoeleinden. Gebruik het verantwoord en respecteer de RDW API-gebruiksvoorwaarden.
