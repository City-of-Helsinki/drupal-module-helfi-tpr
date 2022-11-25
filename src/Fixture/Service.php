<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Fixture;

use Drupal\helfi_api_base\Fixture\FixtureBase;
use GuzzleHttp\Psr7\Response;

/**
 * Provides fixture data for tpr_service migration.
 */
final class Service extends FixtureBase {

  /**
   * Gets the mock data.
   *
   * @return array
   *   The mock data.
   */
  public function getMockData() : array {
    $services = [
      [
        'id' => 1,
      ],
      [
        'id' => 2,
      ],
      [
        'id' => 3,
      ],
    ];

    foreach ($services as $key => $service) {
      $id = $service['id'];

      foreach (['fi', 'en', 'sv'] as $language) {
        $services[$key][$language] = $service;

        $services[$key][$language] += [
          'title' => sprintf('Service %s %s', $language, $id),
          'description_short' => sprintf('Description short %s %s', $language, $id),
          'description_long' => sprintf('Description long %s %s', $language, $id),
          'exact_errand_services' => [
            123,
            456,
          ],
          'links' => [
            [
              'type' => 'INTERNET',
              'title' => sprintf('0: %s link title %s', $language, $id),
              'url' => sprintf('https://localhost/1/%s/%s', $language, $id),
            ],
            [
              'type' => 'INTERNET',
              'title' => sprintf('1: %s link title %s', $language, $id),
              'url' => sprintf('https://localhost/2/%s/%s', $language, $id),
            ],
          ],
          'unit_ids' => $id != 2 ? [] : [
            1563,
            1940,
          ],
          'name_synonyms' =>  sprintf('Name synonyms %s %s', $language, $id),
          'service_id' => 10554
        ];
      }
    }

    $services[] = [
      'fi' => [
        'id' => 7822,
        'main_description' => TRUE,
        'service_id' => 10554,
        'title' => 'Digituki',
        'name_synonyms' => 'Digineuvonta, digiopastus',
        'description_short' => 'Digitukea saa tietokoneen, tabletin ja älypuhelimen käytössä. Tukea saa myös sähköisen asioinnin, verkkopalveluiden ja yleisimpien sovellusten käyttöön liittyvissä ongelmissa.',
        'description_long' => 'Digituki ja digineuvonta tarkoittavat Helsingin kaupungilla
- Pika-apua tietokoneen, tablettien ja älypuhelimien käyttöön
- Opastusta sähköiseen tunnistautumiseen sekä eri verkkopalveluiden käyttöön. Kaupungin omat palvelut, Kela, Omakanta, verottaja jne.
- Rohkaisua kokeilemaan, innostumaan ja kehittämään itsenäisesti omia digitaitoja.

Digituen antajat ovat kaupungin työntekijöitä tai koulutettuja vapaaehtoisia.
Palvelu on maksutonta.',
        'servicemap_url' => 'https://palvelukartta.hel.fi/fi/search?service_node=341&organization=83e74666-0836-4c1d-948a-4b34a8b90301',
        'general_description_id' => '18510b60-1bb5-43a6-82f2-32b7530351a4',
        'provided_languages' => [
          'en',
          'fi',
          'sv',
        ],
        'responsible_depts' => [
          '421399be-a090-4298-8745-ab4899873308',
        ],
        'target_groups' => [],
        'life_events' => [],
        'errand_services' => [
          4422,
        ],
        'exact_errand_services' => [
          4422,
        ],
        'links' => [
          [
            'type' => 'INTERNET',
            'title' => 'Lue lisää digituesta Helsingissä',
            'url' => 'https://digituki.hel.fi/fi/',
            'file_format' => 'HTML',
          ],
        ],
        'availabilities' => [],
        'unit_ids' => [
          1563,
          1923,
          1933,
          1940,
          1949,
          1950,
          1955,
          2692,
          2694,
          3191,
          3235,
          3243,
          3246,
          7585,
          7588,
          7606,
          8138,
          8141,
          8143,
          8149,
          8150,
          8154,
          8158,
          8177,
          8178,
          8184,
          8192,
          8199,
          8205,
          8215,
          8220,
          8232,
          8244,
          8254,
          8269,
          8277,
          8288,
          8289,
          8292,
          8294,
          8302,
          8308,
          8310,
          8324,
          8325,
          8344,
          8348,
          8350,
          8359,
          8362,
          8369,
          8392,
          8397,
          8416,
          8560,
          8841,
          11182,
          11382,
          11407,
          32307,
          33009,
          33179,
          45317,
          48740,
          48762,
          51342,
          51869,
        ],
      ],
      'sv' => [],
      'en' => [],
    ];
    $services[] = [
      'fi' => [
        'id' => 7716,
        'main_description' => FALSE,
        'service_id' => 10290,
        'title' => 'Parkletit',
        'name_synonyms' => 'pysäköintipaikka, pysäköintiruutu',
        'description_short' => 'Kivijalkayritys voi hakea liiketilansa edustalla sijaitsevaa ruutua parklet-käyttöön ja perustaa ruutuun terassin tai muuta paikkaan sopivaa toimintaa.',
        'description_long' => 'Parkletilla tarkoitetaan pysäköintiruutua, joka otetaan tilapäisesti muuhun käyttöön. Kivijalkayritys voi hakea liiketilansa edustalla sijaitsevaa ruutua parklet-käyttöön ja perustaa ruutuun terassin tai muuta paikkaan sopivaa toimintaa. Ruutua ei kuitenkaan saa käyttää varasto- tai mainostilana, moottorikäyttöisten tai hinattavien ajoneuvojen pysäköintiin tai säilyttämiseen eikä liikkuvan elintarvikehuoneiston toimintaan.

Parklet-ruudun voi vuokrata kesäkaudeksi eli huhti-lokakuuksi. Pysäköintiruudun soveltuvuus parklet-käyttöön arvioidaan tapauskohtaisesti.

Lisätietoja parklet-luvan hakemisesta saat Lue lisää-kohdan linkistä.',
        'provided_languages' => [
          'en',
          'fi',
          'sv',
        ],
        'responsible_depts' => [
          '85080572-9a95-4a18-be6b-5bc306eb3d85',
        ],
        'target_groups' => [
          'ASSOCIATIONS',
          'ENTERPRISES',
        ],
        'life_events' => [],
        'errand_services' => [
          2585,
        ],
        'exact_errand_services' => [],
        'links' => [
          [
            'type' => 'INTERNET',
            'title' => 'Pysäköintipaikkojen parklet-käyttö',
            'url' => 'https://www.hel.fi/helsinki/fi/asuminen-ja-ymparisto/tontit/luvat/terassit-ja-parkletit/parkletit/',
          ],
        ],
        'availabilities' => [],
        'unit_ids' => [
          7408,
          7411,
        ],
      ],
      'en' => [],
      'sv' => [],
    ];

    $services[] = [
      'fi' => [
        'id' => 7705,
        'main_description' => FALSE,
        'service_id' => 10014,
        'title' => 'Sosiaalineuvonta',
        'name_synonyms' => 'sosiaalineuvonta',
        'description_short' => 'Sosiaalineuvonta palvelee helsinkiläisiä kaikissa aikuissosiaalityöhön liittyvissä kysymyksissä.',
        'description_long' => 'Sosiaalineuvonta palvelee helsinkiläisiä kaikissa aikuissosiaalityöhön liittyvissä kysymyksissä. Sosiaalineuvonnassa tarjotaan sosiaalihuoltolain mukaista neuvontaa ja ohjausta sosiaalihuoltoon ja etuuksiin liittyvissä kysymyksissä ja on osa Helsingin kaupungin aikuissosiaalityötä. Sosiaalineuvonnassa neuvontaa antavat sosiaaliohjaajat.

Sosiaalineuvonnassa voi asioida ilman erillistä ajanvarausta asioimalla sosiaalineuvonnan palvelupisteissä, soittamalla puhelinpalveluun tai ottamalla yhteyttä chatin välityksellä. Asiointi on mahdollista myös nimettömänä. Sosiaalineuvonnassa helsinkiläiset voivat asioida ilman aluerajoja.

Tarvittaessa sosiaalineuvonnasta asiakasta ohjataan eteenpäin palveluissa palvelutarpeen mukaan. Sosiaalineuvonta tekee tiivistä yhteistyötä alueellisen aikuissosiaalityön, perusterveydenhuollon kanssa sekä Kelan kanssa.',
        'prerequisites' => 'Palvelu on tarkoitettu helsinkiläisille.
',
        'provided_languages' => [
          'fi',
          'sv',
        ],
        'responsible_depts' => [
          'd4b3a166-9fa0-4846-9d63-ff1fdab670b6',
        ],
        'target_groups' => [],
        'life_events' => [],
        'errand_services' => [],
        'exact_errand_services' => [],
        'links' => [
          [
            'type' => 'INTERNET',
            'title' => 'Yhteystiedot',
            'url' => 'https://www.hel.fi/helsinki/fi/sosiaali-ja-terveyspalvelut/sosiaalinen-tuki-ja-toimeentulo/sosiaalityo/sosiaalineuvonta/tule/',
            'file_format' => 'HTML',
          ],
        ],
        'availabilities' => [],
        'unit_ids' => [
          56598,
          56599,
          56601,
          56605,
        ],
      ],
      'sv' => [
        'id' => 7705,
        'main_description' => FALSE,
        'service_id' => 10014,
        'title' => 'Socialrådgivning',
        'name_synonyms' => 'rådgiving i sociala problem, socialarbete för vuxna',
        'description_short' => 'Socialrådgivningen betjänar helsingforsbor i alla frågor som gäller vuxensocialarbete.',
        'description_long' => 'I socialrådgivningen betjänar socialhandledarna helsingforsbor i alla frågor som gäller vuxensocialarbete. Socialrådgivningen erbjuder rådgivning och handledning om socialvård och sociala förmåner. Socialrådgivningen är en del av stadens vuxensocialarbete.

Socialrådgivningen kan du besöka utan tidsbeställning. Du kan också ringa eller ställa frågor via chatten. Du kan också vara anonym i tjänsten. Helsingforsborna får service vid vilken som helst enhet, utan områdesgränser.

Vid behov hänvisar rådgivningen klienten vidare till rätta tjänster. Socialrådgivningen har aktivt samarbete med olika områdens vuxensocialarbete, primärhälsovården och Folkpensionsanstalten.',
        'prerequisites' => 'Tjänsten är avsedd för helsingforsbor.',
        'provided_languages' => [
          'fi',
          'sv',
        ],
        'responsible_depts' => [
          'd4b3a166-9fa0-4846-9d63-ff1fdab670b6',
        ],
        'target_groups' => [],
        'life_events' => [],
        'errand_services' => [],
        'exact_errand_services' => [],
        'links' => [
          [
            'type' => 'INTERNET',
            'title' => 'Kontaktuppgifter',
            'url' => 'https://www.hel.fi/helsinki/sv/social-och-halso/ekonomiskt/socialt-arbete-och-radgivning/socialradgivning/',
          ],
        ],
        'availabilities' => [],
        'unit_ids' => [
          56598,
          56599,
          56601,
          56605,
        ],
      ],
      'en' => [
        'id' => 7705,
        'main_description' => FALSE,
        'service_id' => 10014,
        'title' => 'Social welfare counselling',
        'name_synonyms' => 'social work for adults, counselling in social welfare',
        'description_short' => 'Social welfare counselling helps residents of Helsinki with all issues related to adult social work.',
        'description_long' => "Social welfare counselling helps residents of Helsinki with all issues related to adult social work. In accordance with the Social Welfare Act, social welfare counselling services consist of counselling and guidance on issues related social welfare and benefits. The service is part of the City of Helsinki's adult social work unit. Social welfare counselling is provided by social instructors.

Social welfare counselling services can be used with no need for an appointment by visiting a social welfare counselling service point, calling the telephone service or via chat. The services can also be used anonymously. Residents of Helsinki are free to use all social welfare counselling services regardless of their district of residence.

Clients of social welfare counselling can be directed to other services in accordance with their service needs, if necessary. Social welfare counselling services engage in close cooperation with regional adult social work, primary health care services and Kela.",
        'prerequisites' => 'The service is intended for residents of Helsinki.',
        'provided_languages' => [
          'fi',
          'sv',
        ],
        'responsible_depts' => [
          'd4b3a166-9fa0-4846-9d63-ff1fdab670b6',
        ],
        'target_groups' => [],
        'life_events' => [],
        'errand_services' => [],
        'exact_errand_services' => [],
        'links' => [
          [
            'type' => 'INTERNET',
            'title' => 'Contact information',
            'url' => 'https://www.hel.fi/helsinki/fi/sosiaali-ja-terveyspalvelut/sosiaalinen-tuki-ja-toimeentulo/sosiaalityo/sosiaalineuvonta/tule/',
          ],
        ],
        'availabilities' => [],
        'unit_ids' => [
          56598,
          56599,
          56601,
          56605,
        ],
      ],
    ];
    return $services;
  }

  /**
   * {@inheritdoc}
   */
  public function getMockResponses() : array {
    $services = $this->getMockData();
    return [
      new Response(200, [], json_encode($services)),
    ];
  }

}
