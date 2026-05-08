<?php

/*
|--------------------------------------------------------------------------
| PROXY DEFINITIONS - ADD NEW PROXIES HERE ONLY!
|--------------------------------------------------------------------------
|
| To add a new proxy, simply add one entry to the array below.
| Format: 'proxy_code' => ['port' => 'PORT', 'label' => 'COUNTRY NAME', 'type' => 'premium|adfree']
|
| Example: 'adfree_mx' => ['port' => '10127', 'label' => 'Mexico', 'type' => 'adfree'],
|
*/
$proxyDefinitions = [
    // Premium Proxies
    'premium_worldwide' => ['port' => '18801', 'label' => 'Worldwide', 'type' => 'premium'],
    'premium_de'        => ['port' => '18810', 'label' => 'Germany', 'type' => 'premium'],
    'premium_in'        => ['port' => '18804', 'label' => 'India', 'type' => 'premium'],
    'premium_id'        => ['port' => '18806', 'label' => 'Indonesia', 'type' => 'premium'],
    'premium_qatar'     => ['port' => '18805', 'label' => 'Qatar', 'type' => 'premium'],
    'premium_sg'        => ['port' => '18808', 'label' => 'Singapore', 'type' => 'premium'],
    'premium_tr'        => ['port' => '18807', 'label' => 'Turkey', 'type' => 'premium'],
    'premium_uk'        => ['port' => '18803', 'label' => 'United Kingdom', 'type' => 'premium'],
    'premium_us'        => ['port' => '18802', 'label' => 'United States', 'type' => 'premium'],
    'premium_uruguay'   => ['port' => '18809', 'label' => 'Uruguay', 'type' => 'premium'],
    // Ad-Free Proxies (Alphabetically by Country Name)
    'adfree_worldwide' => ['port' => '10005', 'label' => 'Worldwide', 'type' => 'adfree'],
    'adfree_al'        => ['port' => '19936', 'label' => 'Albania', 'type' => 'adfree'],
    'adfree_au'        => ['port' => '19904', 'label' => 'Australia', 'type' => 'adfree'],
    'adfree_at'        => ['port' => '19917', 'label' => 'Austria', 'type' => 'adfree'],
    'adfree_br'        => ['port' => '19907', 'label' => 'Brazil', 'type' => 'adfree'],
    'adfree_ca'        => ['port' => '19908', 'label' => 'Canada', 'type' => 'adfree'],
    'adfree_cl'        => ['port' => '19920', 'label' => 'Chile', 'type' => 'adfree'],
    'adfree_co'        => ['port' => '19926', 'label' => 'Colombia', 'type' => 'adfree'],
    'adfree_hr'        => ['port' => '19909', 'label' => 'Croatia', 'type' => 'adfree'],
    'adfree_fr'        => ['port' => '19911', 'label' => 'France', 'type' => 'adfree'],
    'adfree_de'        => ['port' => '19913', 'label' => 'Germany', 'type' => 'adfree'],
    'adfree_hk'        => ['port' => '19922', 'label' => 'Hongkong', 'type' => 'adfree'],
    'adfree_in'        => ['port' => '19901', 'label' => 'India', 'type' => 'adfree'],
    'adfree_ie'        => ['port' => '19930', 'label' => 'Ireland', 'type' => 'adfree'],
    'adfree_it'        => ['port' => '19912', 'label' => 'Italy', 'type' => 'adfree'],
    'adfree_jp'        => ['port' => '19928', 'label' => 'Japan', 'type' => 'adfree'],
    'adfree_kz'        => ['port' => '19935', 'label' => 'Kazakhstan', 'type' => 'adfree'],
    'adfree_ke'        => ['port' => '19914', 'label' => 'Kenya', 'type' => 'adfree'],
    'adfree_ng'        => ['port' => '19916', 'label' => 'Nigeria', 'type' => 'adfree'],
    'adfree_pk'        => ['port' => '19932', 'label' => 'Pakistan', 'type' => 'adfree'],
    'adfree_pl'        => ['port' => '19939', 'label' => 'Poland', 'type' => 'adfree'],
    'adfree_qa'        => ['port' => '19933', 'label' => 'Qatar', 'type' => 'adfree'],
    'adfree_ro'        => ['port' => '19937', 'label' => 'Romania', 'type' => 'adfree'],
    'adfree_sa'        => ['port' => '19931', 'label' => 'Saudi Arabia', 'type' => 'adfree'],
    'adfree_sg'        => ['port' => '19924', 'label' => 'Singapore', 'type' => 'adfree'],
    'adfree_za'        => ['port' => '19906', 'label' => 'South Africa', 'type' => 'adfree'],
    'adfree_es'        => ['port' => '19902', 'label' => 'Spain', 'type' => 'adfree'],
    'adfree_ch'        => ['port' => '19910', 'label' => 'Switzerland', 'type' => 'adfree'],
    'adfree_tw'        => ['port' => '19923', 'label' => 'Taiwan', 'type' => 'adfree'],
    'adfree_th'        => ['port' => '19934', 'label' => 'Thailand', 'type' => 'adfree'],
    'adfree_tr'        => ['port' => '19921', 'label' => 'Turkey', 'type' => 'adfree'],
    'adfree_ua'        => ['port' => '19938', 'label' => 'Ukraine', 'type' => 'adfree'],
    'adfree_ae'        => ['port' => '19905', 'label' => 'United Arab Emirates', 'type' => 'adfree'],
    'adfree_uk'        => ['port' => '19903', 'label' => 'United Kingdom', 'type' => 'adfree'],
    'adfree_us'        => ['port' => '10001', 'label' => 'United States', 'type' => 'adfree'],
    'adfree_uy'        => ['port' => '19925', 'label' => 'Uruguay', 'type' => 'adfree'],
    'adfree_vn'        => ['port' => '19927', 'label' => 'Vietnam', 'type' => 'adfree'],
];

/*
|--------------------------------------------------------------------------
| AUTO-GENERATED ARRAYS - DO NOT EDIT BELOW THIS LINE
|--------------------------------------------------------------------------
|
| These arrays are automatically generated from $proxyDefinitions above.
|
*/

// Auto-generate port_map
$portMap = [];
foreach ($proxyDefinitions as $code => $config) {
    $portMap[$config['port']] = $code;
}

// Auto-generate proxy_urls
$proxyUrls = [];
foreach ($proxyDefinitions as $code => $config) {
    $subdomain = $config['type'] === 'premium' ? 'premium-proxy' : 'adfree-proxy';
    $proxyUrls[$code] = "{$subdomain}.sparkcliks.com:{$config['port']}";
}

// Auto-generate proxy_labels
$proxyLabels = [];
foreach ($proxyDefinitions as $code => $config) {
    $proxyLabels[$code] = $config['label'];
}

// Auto-generate groups
$groups = [
    'Premium Proxy' => [],
    'Ad-Free Proxy' => [],
];
foreach ($proxyDefinitions as $code => $config) {
    if ($config['type'] === 'premium') {
        $groups['Premium Proxy'][] = $code;
    } else {
        $groups['Ad-Free Proxy'][] = $code;
    }
}

return [
    /*
    |--------------------------------------------------------------------------
    | Proxy Domain
    |--------------------------------------------------------------------------
    |
    | The base domain used for all proxy servers (both premium and ad-free)
    |
    */
    'domain' => 'sparkcliks.com',

    /*
    |--------------------------------------------------------------------------
    | Default Proxy
    |--------------------------------------------------------------------------
    |
    | The default proxy type to use when no proxy is configured
    |
    */
    'default' => 'premium_worldwide',

    /*
    |--------------------------------------------------------------------------
    | Proxy Port Mapping (AUTO-GENERATED)
    |--------------------------------------------------------------------------
    |
    | Maps proxy server ports to their corresponding proxy type codes.
    | Generated automatically from $proxyDefinitions above.
    |
    */    'port_map' => $portMap,

    /*
    |--------------------------------------------------------------------------
    | Proxy URLs (AUTO-GENERATED)
    |--------------------------------------------------------------------------
    |
    | Full proxy URLs for each proxy type code.
    | Generated automatically from $proxyDefinitions above.
    |
    */
    'proxy_urls' => $proxyUrls,

    /*
    |--------------------------------------------------------------------------
    | Proxy Display Labels (AUTO-GENERATED)
    |--------------------------------------------------------------------------
    |
    | Human-readable labels for each proxy type code.
    | Generated automatically from $proxyDefinitions above.
    |
    */
    'proxy_labels' => $proxyLabels,

    /*
    |--------------------------------------------------------------------------
    | Proxy Groups (AUTO-GENERATED)
    |--------------------------------------------------------------------------
    |
    | Groups proxy types for organized display in dropdowns.
    | Generated automatically from $proxyDefinitions above.
    |
    */
    'groups' => $groups,
];
