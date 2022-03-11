<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Fixture;

use Drupal\helfi_api_base\Fixture\FixtureBase;
use GuzzleHttp\Psr7\Response;

/**
 * Provides fixture data for tpr_unit migration.
 */
final class Unit extends FixtureBase {

  /**
   * Gets the mock data.
   *
   * @return array[]
   *   The mock data.
   */
  public function getMockData() : array {
    return [
      [
        'id' => 60321,
        'org_id' => '520a4492-cb78-498b-9c82-86504de88dce',
        'dept_id' => 'b8eb3b35-a41c-415a-9c87-ae005d6906e5',
        'provider_type' => 'SELF_PRODUCED',
        'data_source_url' => 'www.espoo.fi',
        'name_fi' => 'Otaniemen kirjasto',
        'name_sv' => 'Otnäs bibliotek',
        'name_en' => 'Otaniemi library',
        'ontologyword_ids' => [
          483,
          813,
          929,
        ],
        'ontologytree_ids' => [
          328,
          341,
          349,
          2260,
        ],
        'sources' => [
          [
            'source' => 'kirkanta',
            'id' => '86654',
          ],
        ],
        'latitude' => 60.184093,
        'longitude' => 24.818619,
        'northing_etrs_gk25' => 6674597,
        'easting_etrs_gk25' => 25489935,
        'northing_etrs_tm35fin' => 6673912,
        'easting_etrs_tm35fin' => 379019,
        'manual_coordinates' => TRUE,
        'street_address_fi' => 'Tietotie 6',
        'street_address_sv' => 'Datavägen 6',
        'street_address_en' => 'Tietotie 6',
        'address_zip' => '02150',
        'address_city_fi' => 'Espoo',
        'address_city_sv' => 'Esbo',
        'address_city_en' => 'Espoo',
        'phone' => '+358 40 507 8204',
        'call_charge_info_fi' => 'pvm/mpm',
        'call_charge_info_sv' => 'lna/msa',
        'call_charge_info_en' => 'local network charge/mobile call charge',
        'email' => 'kirjasto.otaniemi@espoo.fi',
        'www_fi' => 'https://www.helmet.fi/otaniemenkirjasto',
        'www_sv' => 'https://www.helmet.fi/otaniemenkirjasto',
        'www_en' => 'https://www.helmet.fi/otaniemenkirjasto',
        'address_postal_full_fi' => 'PL 3619, 02070 ESPOON KAUPUNKI',
        'address_postal_full_sv' => 'PB 3619, 02070 ESPOON KAUPUNKI',
        'address_postal_full_en' => 'P.O. Box 3619, 02070 ESPOON KAUPUNKI',
        'picture_url' => 'https://kirkanta.kirjastot.fi/files/photos/medium/5d8cb23b761ea224727365.jpg',
        'streetview_entrance_url' => 'https://goo.gl/maps/B7cHBoevQU2yYZzL6',
        'accessibility_phone' => '+358 40 507 8204',
        'accessibility_email' => 'kirjasto.otaniemi@espoo.fi',
        'accessibility_www' => 'https://mobi.helmet.fi/fi-FI/Kirjastot_ja_palvelut/Otaniemen_kirjasto',
        'accessibility_viewpoints' => '00:unknown,11:red,12:red,13:red,21:green,22:green,23:green,31:red,32:red,33:red,41:green,51:red,52:red,61:red',
        'created_time' => '2019-09-18T10:32:56',
        'modified_time' => '2021-11-23T00:32:37',
        'services' => [],
        'connections' => [
          [
            'section_type' => 'OPENING_HOURS',
            'name_fi' => 'avoinna joka päivä 06-22',
            'name_en' => 'open every day 06-22',
            'name_sv' => 'öppet varje dag 06-22',
          ],
          [
            'section_type' => 'OPENING_HOURS',
            'name_fi' => 'Aukioloajat',
            'name_en' => 'Opening hours',
            'name_sv' => 'Öppettider',
            'www_fi' => 'http://www.helmet.fi/kirjasto/86654/Aukioloajat/',
            'www_en' => 'http://www.helmet.fi/library/86654/Contact_information/',
            'www_sv' => 'http://www.helmet.fi/bibliotek/86654/Kontaktuppgifter/',
          ],
          [
            'section_type' => 'LINK',
            'name_fi' => 'Kotisivu venäjäksi',
            'name_en' => 'Kotisivu venäjäksi',
            'name_sv' => 'Kotisivu venäjäksi',
            'www_fi' => 'https://www.helmet.fi/ru-RU/Bibliotechnye_uslugi/Biblioteka_Otaniemi',
            'www_en' => 'https://www.helmet.fi/ru-RU/Bibliotechnye_uslugi/Biblioteka_Otaniemi',
            'www_sv' => 'https://www.helmet.fi/ru-RU/Bibliotechnye_uslugi/Biblioteka_Otaniemi',
          ],
          [
            'section_type' => 'LINK',
            'name_fi' => 'Website',
            'name_en' => 'Website',
            'name_sv' => 'Website',
            'www_fi' => 'https://www.helmet.fi/otaniemilibrary',
            'www_en' => 'https://www.helmet.fi/otaniemilibrary',
            'www_sv' => 'https://www.helmet.fi/otaniemilibrary',
          ],
        ],
        'ontologyword_details' => [
          [
            'id' => 483,
          ],
          [
            'id' => 813,
          ],
          [
            'id' => 929,
          ],
        ],
        'service_descriptions' => [
          [
            'id' => 7822,
            'available_languages' => [
              'en',
              'fi',
              'sv',
            ],
          ],
          [
            'id' => 7951,
            'available_languages' => [
              'fi',
            ],
          ],
        ],
        'accessibility_sentences' => [
          [
            'sentence_group_name' => 'Esteettömät autopaikat',
            'sentence_group_fi' => 'Reitti pääsisäänkäynnille',
            'sentence_group_sv' => 'Rutten till huvudingången',
            'sentence_group_en' => 'The route to the main entrance',
            'sentence_fi' => '2 esteetöntä autopaikkaa sijaitsee ulkona yli 10 m sisäänkäynnistä. Pysäköintiruudun leveys on vähintään 3,6 m.',
            'sentence_sv' => '2 bilplatser för rörelsehindrade ligger utomhus över 10 m från ingången. Parkeringsrutans bredd är minst 3,6 m.',
            'sentence_en' => 'The 2 accessible parking spaces are located outdoors over 10 m from the entrance. The width of the parking spaces is at least 3.6 m.',
          ],
          [
            'sentence_group_name' => 'Saattoliikenne',
            'sentence_group_fi' => 'Reitti pääsisäänkäynnille',
            'sentence_group_sv' => 'Rutten till huvudingången',
            'sentence_group_en' => 'The route to the main entrance',
            'sentence_fi' => 'Saattoliikenteen pysähtymispaikka sijaitsee sisäänkäynnin läheisyydessä (etäisyys enintään 5 metriä), josta sisäänkäynnille pääsee siirtymään sujuvasti.',
            'sentence_sv' => 'Hållplatsen för skjutstrafik ligger i närheten av ingången, på en plats varifrån det är lätt att ta sig till trottoaren.',
            'sentence_en' => 'The pick-up and drop-off area is located in the vicinity of the entrance, giving easy access to the pavement.',
          ],
          [
            'sentence_group_name' => 'Kulkureitti pääsisäänkäynnille',
            'sentence_group_fi' => 'Reitti pääsisäänkäynnille',
            'sentence_group_sv' => 'Rutten till huvudingången',
            'sentence_group_en' => 'The route to the main entrance',
            'sentence_fi' => 'Kulkureitti sisäänkäynnille on opastettu, tasainen ja riittävän leveä sekä valaistu.',
            'sentence_sv' => 'Rutten till ingången är skyltad, jämn och tillräckligt bred samt belyst.',
            'sentence_en' => 'The route to the entrance is guided, smooth and sufficiently wide and illuminated.',
          ],
          [
            'sentence_group_name' => 'Pääsisäänkäynti',
            'sentence_group_fi' => 'Pääsisäänkäynti',
            'sentence_group_sv' => 'Huvudingången',
            'sentence_group_en' => 'The main entrance',
            'sentence_fi' => 'Sisäänkäynti erottuu selkeästi ja on valaistu. Sisäänkäynnin yläpuolella on katos.',
            'sentence_sv' => 'Ingången är lätt att urskilja och belyst. Ingången har ett tak.',
            'sentence_en' => 'The entrance stands out clearly and is illuminated. There is a canopy above the entrance.',
          ],
          [
            'sentence_group_name' => 'Pääsisäänkäynti',
            'sentence_group_fi' => 'Pääsisäänkäynti',
            'sentence_group_sv' => 'Huvudingången',
            'sentence_group_en' => 'The main entrance',
            'sentence_fi' => 'Sisäänkäynnin ovet erottuvat selkeästi ja lasiovissa on kontrastimerkinnät. Oven ulkopuolella on riittävästi vapaata tilaa liikkumiselle esim. pyörätuolin kanssa. Ovi aukeaa painikkeen avulla automaattisesti.',
            'sentence_sv' => 'Dörrarna vid ingången är lätta att urskilja och glasdörrarna har kontrastmarkeringar. Utanför dörren finns tillräckligt med fritt utrymme för att röra sig t.ex. med rullstol. Dörren öppnas automatiskt med en knapp.',
            'sentence_en' => 'The doors connected to the entrance stand out clearly and the glass doors have contrast markings. Outside the door there is sufficient room for moving e.g. with a wheelchair. The door opens automatically with a button.',
          ],
          [
            'sentence_group_name' => 'Sisätilat',
            'sentence_group_fi' => 'Sisätilat',
            'sentence_group_sv' => 'I lokalen',
            'sentence_group_en' => 'In the facility',
            'sentence_fi' => 'Asiointipiste sijaitsee samassa kerroksessa kuin sisäänkäynti.',
            'sentence_sv' => 'Servicepunkten finns på samma plan som ingången.',
            'sentence_en' => 'The customer service point is on the same floor as the entrance.',
          ],
          [
            'sentence_group_name' => 'Sisätilat',
            'sentence_group_fi' => 'Sisätilat',
            'sentence_group_sv' => 'I lokalen',
            'sentence_group_en' => 'In the facility',
            'sentence_fi' => 'Asiointipisteessä on opasteet eri tiloihin.',
            'sentence_sv' => 'Vid servicepunkten finns skyltar till olika lokaler.',
            'sentence_en' => 'The customer service point has directions to the different facilities.',
          ],
          [
            'sentence_group_name' => 'Sisätilat',
            'sentence_group_fi' => 'Sisätilat',
            'sentence_group_sv' => 'I lokalen',
            'sentence_group_en' => 'In the facility',
            'sentence_fi' => 'Asiointitilassa on 1 porrasaskelma.',
            'sentence_sv' => 'I servicelokalen finns 1 trappsteg.',
            'sentence_en' => 'The customer service point has 1 step.',
          ],
          [
            'sentence_group_name' => 'Sisätilat',
            'sentence_group_fi' => 'Sisätilat',
            'sentence_group_sv' => 'I lokalen',
            'sentence_group_en' => 'In the facility',
            'sentence_fi' => 'Asiointipisteen ovet eivät erotu selkeästi seinästä. Lasiovissa on kontrastimerkinnät.',
            'sentence_sv' => 'Dörrarna vid servicepunkten är svåra att överblicka. Glasdörrarna har kontrastmarkeringar.',
            'sentence_en' => 'The doors in the customer service point are hard to perceive. The glass doors have contrast markings.',
          ],
          [
            'sentence_group_name' => 'Sisätilat',
            'sentence_group_fi' => 'Sisätilat',
            'sentence_group_sv' => 'I lokalen',
            'sentence_group_en' => 'In the facility',
            'sentence_fi' => 'Asiointipisteen odotustilassa ei ole vuoronumerojärjestelmää. Odotustilassa on istuimia.',
            'sentence_sv' => 'I servicepunktens väntrum finns inget könummersystem. I väntrummet finns stolar.',
            'sentence_en' => 'The waiting room of the customer service point has no queue number system. The waiting room has chairs.',
          ],
          [
            'sentence_group_name' => 'Sisätilat',
            'sentence_group_fi' => 'Sisätilat',
            'sentence_group_sv' => 'I lokalen',
            'sentence_group_en' => 'In the facility',
            'sentence_fi' => 'Asiointipisteessä on esteettömäksi merkitty wc samassa kerroksessa kuin asiointipiste. Wc:ssä ei ole tarpeeksi tilaa pyörätuolille.',
            'sentence_sv' => 'I servicestället finns en toalett som har angetts som tillgänglig på samma plan som servicestället. Det finns inte tillräckligt med utrymme för en rullstol på toaletten.',
            'sentence_en' => 'The customer service point has a toilet marked as accessible on the same floor. The toilet does not have sufficient room for a wheelchair.',
          ],
        ],
      ],
      [
        'id' => 67763,
        'org_id' => '6d78f89c-9fd7-41d9-84e0-4b78c0fa25ce',
        'dept_id' => 'd6c56752-dec1-4e56-9113-10975030cd75',
        'provider_type' => 'SELF_PRODUCED',
        'data_source_url' => 'www.vantaa.fi',
        'name_fi' => 'Peijaksen sairaala',
        'ontologyword_ids' => [
          264,
          336,
        ],
        'ontologytree_ids' => [
          1012,
          1013,
        ],
        'short_desc_fi' => 'Peijaksen sairaala-alueella sijaitsee Vantaan sairaalan kaksi geriatrista arviointi- ja akuuttiosastoa,',
        'desc_fi' => 'Peijaksen sairaala-alueella sijaitsee Vantaan sairaalan kaksi geriatrista arviointi- ja akuuttiosastoa.

Akuuttigeriatrian osasto 1 sijaitsee rakennuksen ensimmäisessä kerroksessa ja akuuttigeriatrian osasto 2 sijaitsee rakennuksen kolmannessä kerroksessa.

Vantaan sairaala on opetussairaala, joten potilaiden hoitoon osallistuu sekä lääketieteen että sosiaali- ja terveysalan opiskelijoita. Opiskelijoiden suorittamat tutkimukset ja toimenpiteet tapahtuvat pätevän lääkärin tai hoitajan valvonnassa. Sinulla on potilaana oikeus kieltäytyä opetuspotilaana olosta. Kieltäytyminen ei vaikuta mitenkään hoitoosi.

',
        'latitude' => 60.33144,
        'longitude' => 25.060328,
        'northing_etrs_gk25' => 6691002,
        'easting_etrs_gk25' => 25503333,
        'northing_etrs_tm35fin' => 6689899,
        'easting_etrs_tm35fin' => 392904,
        'manual_coordinates' => TRUE,
        'street_address_fi' => 'Sairaalakatu 1',
        'street_address_sv' => 'Sjukhusgatan 1',
        'street_address_en' => 'Sairaalakatu 1',
        'address_zip' => '01400',
        'address_city_fi' => 'Vantaa',
        'address_city_sv' => 'Vanda',
        'address_city_en' => 'Vantaa',
        'phone' => '+358 9 839 11',
        'call_charge_info_fi' => 'pvm/mpm',
        'call_charge_info_sv' => 'lna/msa',
        'call_charge_info_en' => 'local network charge/mobile call charge',
        'extra_searchwords_fi' => 'fysioterapia, geriatrinen vastaanotto, kuntoutus, lääkärin vastaanotto, toimintaterapia aikuisille',
        'accessibility_viewpoints' => '00:unknown,11:green,12:green,13:green,21:green,22:green,23:green,31:green,32:green,33:green,41:green,51:red,52:red,61:red',
        'created_time' => '2021-10-11T11:03:55',
        'modified_time' => '2021-10-28T14:40:08',
        'services' => [],
        'connections' => [],
        'ontologyword_details' => [
          [
            'id' => 336,
          ],
          [
            'id' => 264,
          ],
        ],
        'service_descriptions' => [
          [
            'id' => 7822,
            'available_languages' => [
              'fi',
            ],
          ],
          [
            'id' => 8644,
            'available_languages' => [
              'fi',
            ],
          ],
        ],
        'accessibility_sentences' => [
          [
            'sentence_group_name' => 'Esteettömät autopaikat',
            'sentence_group_fi' => 'Reitti pääsisäänkäynnille',
            'sentence_group_sv' => 'Rutten till huvudingången',
            'sentence_group_en' => 'The route to the main entrance',
            'sentence_fi' => '7 esteetöntä autopaikkaa sijaitsee ulkona yli 10 m sisäänkäynnistä. Pysäköintiruudun leveys on vähintään 3,6 m.',
            'sentence_sv' => '7 bilplatser för rörelsehindrade ligger utomhus över 10 m från ingången. Parkeringsrutans bredd är minst 3,6 m.',
            'sentence_en' => 'The 7 accessible parking spaces are located outdoors over 10 m from the entrance. The width of the parking spaces is at least 3.6 m.',
          ],
          [
            'sentence_group_name' => 'Saattoliikenne',
            'sentence_group_fi' => 'Reitti pääsisäänkäynnille',
            'sentence_group_sv' => 'Rutten till huvudingången',
            'sentence_group_en' => 'The route to the main entrance',
            'sentence_fi' => 'Saattoliikenteen pysähtymispaikka sijaitsee sisäänkäynnin läheisyydessä (etäisyys enintään 5 metriä), josta sisäänkäynnille pääsee siirtymään sujuvasti.',
            'sentence_sv' => 'Hållplatsen för skjutstrafik ligger i närheten av ingången, på en plats varifrån det är lätt att ta sig till trottoaren.',
            'sentence_en' => 'The pick-up and drop-off area is located in the vicinity of the entrance, giving easy access to the pavement.',
          ],
          [
            'sentence_group_name' => 'Kulkureitti pääsisäänkäynnille',
            'sentence_group_fi' => 'Reitti pääsisäänkäynnille',
            'sentence_group_sv' => 'Rutten till huvudingången',
            'sentence_group_en' => 'The route to the main entrance',
            'sentence_fi' => 'Kulkureitti sisäänkäynnille on opastettu, tasainen ja riittävän leveä sekä valaistu.',
            'sentence_sv' => 'Rutten till ingången är skyltad, jämn och tillräckligt bred samt belyst.',
            'sentence_en' => 'The route to the entrance is guided, smooth and sufficiently wide and illuminated.',
          ],
          [
            'sentence_group_name' => 'Pääsisäänkäynti',
            'sentence_group_fi' => 'Pääsisäänkäynti',
            'sentence_group_sv' => 'Huvudingången',
            'sentence_group_en' => 'The main entrance',
            'sentence_fi' => 'Sisäänkäynti erottuu selkeästi ja on valaistu. Sisäänkäynnin yläpuolella on katos.',
            'sentence_sv' => 'Ingången är lätt att urskilja och belyst. Ingången har ett tak.',
            'sentence_en' => 'The entrance stands out clearly and is illuminated. There is a canopy above the entrance.',
          ],
          [
            'sentence_group_name' => 'Pääsisäänkäynti',
            'sentence_group_fi' => 'Pääsisäänkäynti',
            'sentence_group_sv' => 'Huvudingången',
            'sentence_group_en' => 'The main entrance',
            'sentence_fi' => 'Sisäänkäynnin ovet erottuvat selkeästi ja lasiovissa on kontrastimerkinnät. Oven ulkopuolella on riittävästi vapaata tilaa liikkumiselle esim. pyörätuolin kanssa. Ovi aukeaa automaattisesti liikkeentunnistimella.',
            'sentence_sv' => 'Dörrarna vid ingången är lätta att urskilja och glasdörrarna har kontrastmarkeringar. Utanför dörren finns tillräckligt med fritt utrymme för att röra sig t.ex. med rullstol. Dörren öppnas automatiskt med en rörelsedetektor.',
            'sentence_en' => 'The doors connected to the entrance stand out clearly and the glass doors have contrast markings. Outside the door there is sufficient room for moving e.g. with a wheelchair. The door opens automatically with a motion sensor.',
          ],
          [
            'sentence_group_name' => 'Sisätilat',
            'sentence_group_fi' => 'Sisätilat',
            'sentence_group_sv' => 'I lokalen',
            'sentence_group_en' => 'In the facility',
            'sentence_fi' => 'Asiointipiste sijaitsee samassa kerroksessa kuin sisäänkäynti.',
            'sentence_sv' => 'Servicepunkten finns på samma plan som ingången.',
            'sentence_en' => 'The customer service point is on the same floor as the entrance.',
          ],
          [
            'sentence_group_name' => 'Sisätilat',
            'sentence_group_fi' => 'Sisätilat',
            'sentence_group_sv' => 'I lokalen',
            'sentence_group_en' => 'In the facility',
            'sentence_fi' => 'Asiointipisteessä on opasteet eri tiloihin.',
            'sentence_sv' => 'Vid servicepunkten finns skyltar till olika lokaler.',
            'sentence_en' => 'The customer service point has directions to the different facilities.',
          ],
          [
            'sentence_group_name' => 'Sisätilat',
            'sentence_group_fi' => 'Sisätilat',
            'sentence_group_sv' => 'I lokalen',
            'sentence_group_en' => 'In the facility',
            'sentence_fi' => 'Asiointipisteen ovet erottuvat selkeästi. Lasiovissa on kontrastimerkinnät.',
            'sentence_sv' => 'Dörrarna vid servicepunkten är lätta att urskilja. Glasdörrarna har kontrastmarkeringar.',
            'sentence_en' => 'The doors in the customer service point stand out clearly. The glass doors have contrast markings.',
          ],
          [
            'sentence_group_name' => 'Sisätilat',
            'sentence_group_fi' => 'Sisätilat',
            'sentence_group_sv' => 'I lokalen',
            'sentence_group_en' => 'In the facility',
            'sentence_fi' => 'Asiointipisteen odotustilassa ei ole vuoronumerojärjestelmää. Odotustilassa on istuimia.',
            'sentence_sv' => 'I servicepunktens väntrum finns inget könummersystem. I väntrummet finns stolar.',
            'sentence_en' => 'The waiting room of the customer service point has no queue number system. The waiting room has chairs.',
          ],
          [
            'sentence_group_name' => 'Sisätilat',
            'sentence_group_fi' => 'Sisätilat',
            'sentence_group_sv' => 'I lokalen',
            'sentence_group_en' => 'In the facility',
            'sentence_fi' => 'Asiointipisteessä on esteetön wc samassa kerroksessa kuin asiointipiste.',
            'sentence_sv' => 'Vid servicepunkten finns en tillgänglig toalett på samma plan som servicepunkten.',
            'sentence_en' => 'The customer service point has an accessible toilet on the same floor.',
          ],
        ],
      ],
      [
        'id' => 63115,
        'org_id' => '520a4492-cb78-498b-9c82-86504de88dce',
        'dept_id' => 'b8eb3b35-a41c-415a-9c87-ae005d6906e5',
        'provider_type' => 'SELF_PRODUCED',
        'data_source_url' => 'www.espoo.fi',
        'name_fi' => 'Lippulaivan kirjasto',
        'name_sv' => 'Lippulaivabiblioteket',
        'name_en' => 'Lippulaiva library',
        'ontologyword_ids' => [
          483,
          813,
        ],
        'ontologytree_ids' => [
          328,
          341,
          349,
        ],
        'sources' => [
          [
            'source' => 'kirkanta',
            'id' => '86685',
          ],
        ],
        'latitude' => 60.150066,
        'longitude' => 24.652107,
        'northing_etrs_gk25' => 6670843,
        'easting_etrs_gk25' => 25480676,
        'northing_etrs_tm35fin' => 6670441,
        'easting_etrs_tm35fin' => 369652,
        'manual_coordinates' => TRUE,
        'street_address_fi' => 'Merikarhunkuja 11',
        'street_address_sv' => 'Sjöbjörnsgränden 11',
        'street_address_en' => 'Merikarhunkuja 11',
        'address_zip' => '02320',
        'address_city_fi' => 'Espoo',
        'address_city_sv' => 'Esbo',
        'address_city_en' => 'Espoo',
        'email' => 'kirjasto.lippulaiva@espoo.fi',
        'picture_url' => 'https://kirkanta.kirjastot.fi/files/photos/medium/5f8707669ca26011322092.jpg',
        'accessibility_viewpoints' => '00:unknown,11:unknown,12:unknown,13:unknown,21:unknown,22:unknown,23:unknown,31:unknown,32:unknown,33:unknown,41:unknown,51:unknown,52:unknown,61:unknown',
        'created_time' => '2020-10-09T21:38:01',
        'modified_time' => '2021-11-10T00:32:39',
        'services' => [],
        'connections' => [
          [
            'section_type' => 'OPENING_HOURS',
            'name_fi' => 'suljettu joka päivä',
            'name_en' => 'closed every day',
            'name_sv' => 'stängt varje dag',
          ],
          [
            'section_type' => 'OPENING_HOURS',
            'name_fi' => 'Aukioloajat',
            'name_en' => 'Opening hours',
            'name_sv' => 'Öppettider',
            'www_fi' => 'http://www.helmet.fi/kirjasto/86685/Aukioloajat/',
            'www_en' => 'http://www.helmet.fi/library/86685/Contact_information/',
            'www_sv' => 'http://www.helmet.fi/bibliotek/86685/Kontaktuppgifter/',
          ],
          [
            'section_type' => 'LINK',
            'name_fi' => 'Kirjava satama',
            'name_en' => 'Kirjava satama',
            'name_sv' => 'Kirjava satama',
            'www_fi' => 'https://kirjavasatama.espoonkirjastot.fi/kirjaston-toiminta-ja-tulevaisuus/lippulaivan-kirjastoa-suunnitellaan-yhdessa/',
            'www_en' => 'https://kirjavasatama.espoonkirjastot.fi/kirjaston-toiminta-ja-tulevaisuus/lippulaivan-kirjastoa-suunnitellaan-yhdessa/',
            'www_sv' => 'https://kirjavasatama.espoonkirjastot.fi/kirjaston-toiminta-ja-tulevaisuus/lippulaivan-kirjastoa-suunnitellaan-yhdessa/',
          ],
        ],
        'ontologyword_details' => [
          [
            'id' => 483,
          ],
          [
            'id' => 813,
          ],
        ],
        'service_descriptions' => [
          [
            'id' => 7822,
            'available_languages' => [
              'en',
              'fi',
              'sv',
            ],
          ],
          [
            'id' => 7951,
            'available_languages' => [
              'fi',
            ],
          ],
        ],
        'accessibility_sentences' => [],
      ],
      [
        'id' => 1,
        'name_fi' => 'Name fi 1',
        'name_sv' => 'Name sv 1',
        'name_en' => 'Name en 1',
        'latitude' => '60.19',
        'longitude' => '24.76',
        'street_address_fi' => 'Address fi 1',
        'street_address_sv' => 'Address sv 1',
        'address_zip' => '02180',
        'address_city_fi' => 'Espoo fi 1',
        'address_city_sv' => 'Espoo sv 1',
        'address_city_en' => 'Espoo en 1',
        'phone' => '+3581234',
        'call_charge_info_fi' => 'pvm fi 1',
        'call_charge_info_en' => 'pvm en 1',
        'call_charge_info_sv' => 'pvm sv 1',
        'www_fi' => 'localhost/fi/1',
        'www_sv' => 'localhost/sv/1',
        'www_en' => 'localhost/en/1',
        'created_time' => '2015-11-03T12:03:45',
        'modified_time' => '2015-11-03T12:03:45',
        'accessibility_sentences' => [
          [
            'unit_id' => 1,
            'sentence_group_name' => 'Group 1',
            'sentence_group_fi' => 'Group fi 1',
            'sentence_group_sv' => 'Group sv 1',
            'sentence_group_en' => 'Group en 1',
            'sentence_fi' => 'Sentence fi 1',
            'sentence_sv' => 'Sentence sv 1',
            'sentence_en' => 'Sentence en 1',
          ],
          [
            'unit_id' => 1,
            'sentence_group_name' => 'Group 2',
            'sentence_group_fi' => 'Group fi 2',
            'sentence_group_sv' => 'Group sv 2',
            'sentence_group_en' => 'Group en 2',
            'sentence_fi' => 'Sentence fi 2',
            'sentence_sv' => 'Sentence sv 2',
            'sentence_en' => 'Sentence en 2',
          ],
        ],
        'services' => [
          [
            'unit_id' => 1,
            'services' => [1 => 1],
          ],
        ],
        'connections' => [
          [
            'unit_id' => 1,
            'section_type' => 'OPENING_HOURS',
            'name_fi' => 'open fi 1',
            'name_en' => 'open en 1',
            'name_sv' => 'open sv 1',
          ],
          [
            'unit_id' => 1,
            'section_type' => 'OPENING_HOURS',
            'name_fi' => 'open fi 2',
            'name_en' => 'open en 2',
            'name_sv' => 'open sv 2',
            'www_fi' => 'https://localhost/fi',
            'www_en' => 'https://localhost/en',
            'www_sv' => 'https://localhost/sv',
          ],
          [
            'unit_id' => 1,
            'section_type' => 'HIGHLIGHT',
            'name_fi' => 'hilight fi 1',
            'name_en' => 'hilight en 1',
            'name_sv' => 'hilight sv 1',
          ],
          [
            'unit_id' => 1,
            'section_type' => 'HIGHLIGHT',
            'name_fi' => 'hilight fi 2',
            'name_en' => 'hilight en 2',
            'name_sv' => 'hilight sv 2',
          ],
          [
            'unit_id' => 1,
            'section_type' => 'PHONE_OR_EMAIL',
            'name_fi' => 'phone or email fi 1',
            'name_en' => 'phone or email en 1',
            'name_sv' => 'phone or email sv 1',
            'phone' => '040123456',
          ],
          [
            'unit_id' => 1,
            'section_type' => 'PHONE_OR_EMAIL',
            'name_fi' => 'phone or email fi 2',
            'contact_person' => 'contact person name',
            'phone' => '040654321',
          ],
          [
            'unit_id' => 1,
            'section_type' => 'ESERVICE_LINK',
            'name_fi' => 'eservice link fi',
            'www_fi' => 'https://link.fi',
          ],
        ],
        'provided_languages' => [
          'fi',
          'sv',
          'en',
        ],
      ],
      [
        'id' => 59369,
        'org_id' => '506a2b5e-216a-4da6-90d3-db01bf42ff10',
        'dept_id' => '506a2b5e-216a-4da6-90d3-db01bf42ff10',
        'provider_type' => 'SELF_PRODUCED',
        'data_source_url' => 'www.helsinki.fi',
        'name_fi' => 'Viikin kampuskirjasto',
        'name_sv' => 'Viikin kampuskirjasto',
        'name_en' => 'Viikin kampuskirjasto',
        'ontologyword_ids' => [
          879,
        ],
        'ontologytree_ids' => [
          2194,
          2197,
        ],
        'sources' => [
          [
            'source' => 'kirkanta',
            'id' => '86356',
          ],
        ],
        'latitude' => 60.22719,
        'longitude' => 25.012337,
        'northing_etrs_gk25' => 6679385,
        'easting_etrs_gk25' => 25500684,
        'northing_etrs_tm35fin' => 6678371,
        'easting_etrs_tm35fin' => 389905,
        'manual_coordinates' => TRUE,
        'street_address_fi' => 'Viikinkaari 11 A',
        'street_address_sv' => 'Viksbågen 11 A',
        'street_address_en' => 'Viikinkaari 11 A',
        'address_zip' => '00790',
        'address_city_fi' => 'Helsinki',
        'address_city_sv' => 'Helsingfors',
        'address_city_en' => 'Helsinki',
        'email' => 'kirjasto@helsinki.fi',
        'www_fi' => 'http://www.helsinki.fi/kirjasto/fi/toimipaikat/viikki',
        'www_sv' => 'http://www.helsinki.fi/kirjasto/fi/toimipaikat/viikki',
        'www_en' => 'http://www.helsinki.fi/kirjasto/fi/toimipaikat/viikki',
        'address_postal_full_fi' => 'PL 62, 00014 Helsingin yliopisto',
        'address_postal_full_sv' => 'PB 62, 00014 Helsingin yliopisto',
        'address_postal_full_en' => 'P.O. Box 62, 00014 Helsingin yliopisto',
        'picture_url' => 'https://kirkanta.kirjastot.fi/files/photos/medium/dsc-0442-54db4139.jpeg',
        'accessibility_viewpoints' => '00:unknown,11:unknown,12:unknown,13:unknown,21:unknown,22:unknown,23:unknown,31:unknown,32:unknown,33:unknown,41:unknown,51:unknown,52:unknown,61:unknown',
        'created_time' => '2015-02-11T13:36:50',
        'modified_time' => '2021-11-10T00:32:35',
        'services' => [],
        'connections' => [
          [
            'section_type' => 'OPENING_HOURS',
            'name_fi' => 'avoinna ma-pe 08-19, suljettu la-su',
            'name_en' => 'open Mon-Fri 08-19, closed Sat-Sun',
            'name_sv' => 'öppet mån-fre 08-19, stängt lör-sön',
          ],
        ],
        'ontologyword_details' => [
          [
            'id' => 879,
          ],
        ],
        'service_descriptions' => [],
        'accessibility_sentences' => [],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getMockResponses() : array {
    $units = $this->getMockData();
    $responses = [
      new Response(200, [], json_encode($units)),
    ];

    foreach ($units as $unit) {
      $responses[] = new Response(200, [], json_encode($unit['accessibility_sentences']));
      $responses[] = new Response(200, [], json_encode($unit['connections']));
      $responses[] = new Response(200, [], json_encode($unit['services']));
    }
    return $responses;
  }

}
