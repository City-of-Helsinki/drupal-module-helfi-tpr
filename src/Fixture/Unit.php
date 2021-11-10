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
            'id' => 6608,
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
            'id' => 7470,
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
    }
    return $responses;
  }

}
