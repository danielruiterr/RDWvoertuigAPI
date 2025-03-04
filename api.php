<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

function fetchRDWData($url, $cacheTime = 3600) {
    $cacheKey = md5($url);
    $cacheFile = "cache/{$cacheKey}.json";
    
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
        return json_decode(file_get_contents($cacheFile), true);
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception('CURL Error: ' . curl_error($ch));
    }
    
    curl_close($ch);
    $data = json_decode($response, true);
    
    if (!is_dir('cache')) mkdir('cache', 0777, true);
    file_put_contents($cacheFile, $response);
    
    return $data;
}

$apiEndpoints = [
    'vehicle_basic' => 'https://opendata.rdw.nl/resource/m9d7-ebf2.json',
    'apk_history' => 'https://opendata.rdw.nl/resource/a34c-vvps.json',
    'defects' => 'https://opendata.rdw.nl/resource/hx2c-gt7k.json',
    'fuel' => 'https://opendata.rdw.nl/resource/8ys7-d773.json',
    'body' => 'https://opendata.rdw.nl/resource/vezc-m2t6.json',
    'body_specific' => 'https://opendata.rdw.nl/resource/jhie-znh9.json',
    'axles' => 'https://opendata.rdw.nl/resource/3huj-srit.json',
    'vehicle_class' => 'https://opendata.rdw.nl/resource/kmfi-hrps.json',
    'added_objects' => 'https://opendata.rdw.nl/resource/sghb-dzxx.json',
    'recall_status' => 'https://opendata.rdw.nl/resource/t49b-isb7.json',
    'recall_details' => 'https://opendata.rdw.nl/resource/j9yg-7rg9.json'
];

$response = [
    'success' => false,
    'data' => [],
    'metadata' => [
        'timestamp' => date('c'),
        'version' => '1.0.3',
        'api_endpoints_used' => [
            "api_gekentekende_voertuigen_basis" => "https://opendata.rdw.nl/resource/m9d7-ebf2.json",
            "api_apk_historie" => "https://opendata.rdw.nl/resource/a34c-vvps.json",
            "api_gebreken" => "https://opendata.rdw.nl/resource/hx2c-gt7k.json",
            "api_gekentekende_voertuigen_brandstof" => "https://opendata.rdw.nl/resource/8ys7-d773.json",
            "api_gekentekende_voertuigen_carrosserie" => "https://opendata.rdw.nl/resource/vezc-m2t6.json",
            "api_gekentekende_voertuigen_carrosserie_specifiek" => "https://opendata.rdw.nl/resource/jhie-znh9.json",
            "api_gekentekende_voertuigen_assen" => "https://opendata.rdw.nl/resource/3huj-srit.json",
            "api_gekentekende_voertuigen_voertuigklasse" => "https://opendata.rdw.nl/resource/kmfi-hrps.json",
            "api_toegevoegde_objecten" => "https://opendata.rdw.nl/resource/sghb-dzxx.json",
            "api_terugroep_status" => "https://opendata.rdw.nl/resource/t49b-isb7.json",
            "api_terugroep_details" => "https://opendata.rdw.nl/resource/j9yg-7rg9.json"
        ]
    ],
    'error' => null
];

try {
    if (!isset($_GET['kenteken'])) {
        throw new Exception('License plate parameter is required');
    }

    $rawKenteken = $_GET['kenteken'];
    $kenteken = strtoupper(preg_replace('/[^A-HJ-NPR-Z0-9]/', '', $rawKenteken));
    
    if (!preg_match('/^[A-HJ-NPR-Z0-9]{2,8}$/', $kenteken)) {
        throw new Exception('Invalid license plate format');
    }

    $vehicleData = fetchRDWData("{$apiEndpoints['vehicle_basic']}?kenteken=$kenteken");
    if (empty($vehicleData)) {
        throw new Exception('Vehicle not found');
    }

    $response['data'] = [
        'vehicle_info' => $vehicleData[0] ?? [],
        'technical' => [],
        'inspections' => [],
        'recalls' => [],
        'additional' => []
    ];

    // Fuel information
    $fuelData = fetchRDWData("{$apiEndpoints['fuel']}?kenteken=$kenteken");
    if (!empty($fuelData)) {
        $response['data']['technical']['fuel'] = array_map(function($fuel) {
            return [
                'type' => $fuel['brandstof_omschrijving'] ?? 'Unknown',
                'consumption' => [
                    'city' => floatval($fuel['brandstofverbruik_stad'] ?? 0),
                    'highway' => floatval($fuel['brandstofverbruik_buiten'] ?? 0),
                    'combined' => floatval($fuel['brandstofverbruik_gecombineerd'] ?? 0)
                ],
                'emissions' => [
                    'co2' => intval($fuel['co2_uitstoot_gecombineerd'] ?? 0),
                    'particles' => floatval($fuel['uitstoot_deeltjes_licht'] ?? 0),
                    'roet' => floatval($fuel['roetuitstoot'] ?? 0)
                ],
                'power' => floatval($fuel['nettomaximumvermogen'] ?? 0)
            ];
        }, $fuelData);
    }

    // Body information
    $bodyData = fetchRDWData("{$apiEndpoints['body']}?kenteken=$kenteken");
    if (!empty($bodyData)) {
        $response['data']['technical']['body'] = array_map(function($body) {
            return [
                'type' => $body['carrosserietype'] ?? null,
                'description' => $body['type_carrosserie_europese_omschrijving'] ?? null,
                'sequence' => intval($body['carrosserie_volgnummer'] ?? 0)
            ];
        }, $bodyData);
    }

    // Specific body information
    $bodySpecificData = fetchRDWData("{$apiEndpoints['body_specific']}?kenteken=$kenteken");
    if (!empty($bodySpecificData)) {
        $response['data']['technical']['body_specific'] = array_map(function($body) {
            return [
                'specific_type' => $body['type_carrosserie_europese_omschrijving'] ?? null,
                'code' => $body['code_carrosserie_voertuigklasse'] ?? null
            ];
        }, $bodySpecificData);
    }

    // Axle information
    $axleData = fetchRDWData("{$apiEndpoints['axles']}?kenteken=$kenteken");
    if (!empty($axleData)) {
        $response['data']['technical']['axles'] = array_map(function($axle) {
            return [
                'number' => intval($axle['as_nummer'] ?? 0),
                'track_width' => intval($axle['spoorbreedte'] ?? 0),
                'max_load' => [
                    'legal' => intval($axle['wettelijk_toegestane_maximum_aslast'] ?? 0),
                    'technical' => intval($axle['technisch_toegestane_maximum_aslast'] ?? 0)
                ]
            ];
        }, $axleData);
    }

    // Vehicle class information
    $vehicleClassData = fetchRDWData("{$apiEndpoints['vehicle_class']}?kenteken=$kenteken");
    if (!empty($vehicleClassData)) {
        $response['data']['technical']['vehicle_class'] = array_map(function($class) {
            return [
                'class' => $class['voertuigklasse'] ?? null,
                'description' => $class['omschrijving'] ?? null
            ];
        }, $vehicleClassData);
    }

    // APK history with defects
    $apkData = fetchRDWData("{$apiEndpoints['apk_history']}?kenteken=$kenteken");
    if (!empty($apkData)) {
        $response['data']['inspections'] = array_map(function($apk) use ($apiEndpoints) {
            $inspection = [
                'date' => $apk['meld_datum_door_keuringsinstantie_dt'] ?? null,
                'time' => $apk['meld_tijd_door_keuringsinstantie'] ?? null,
                'defect_count' => intval($apk['aantal_gebreken_geconstateerd'] ?? 0),
                'defects' => []
            ];
            
            if (!empty($apk['gebrek_identificatie'])) {
                $defectData = fetchRDWData("{$apiEndpoints['defects']}?gebrek_identificatie={$apk['gebrek_identificatie']}");
                if (!empty($defectData)) {
                    $inspection['defects'] = array_map(function($defect) {
                        return [
                            'id' => $defect['gebrek_identificatie'] ?? null,
                            'description' => $defect['gebrek_omschrijving'] ?? 'Unknown defect',
                            'article' => $defect['gebrek_artikel_nummer'] ?? null,
                            'start_date' => $defect['ingangsdatum_gebrek_dt'] ?? null
                        ];
                    }, $defectData);
                }
            }
            return $inspection;
        }, $apkData);
    }

    // Recall information
    $recallStatus = fetchRDWData("{$apiEndpoints['recall_status']}?kenteken=$kenteken");
    if (!empty($recallStatus)) {
        $response['data']['recalls'] = array_map(function($recall) use ($apiEndpoints) {
            $details = fetchRDWData("{$apiEndpoints['recall_details']}?referentiecode_rdw={$recall['referentiecode_rdw']}");
            $detail = $details[0] ?? [];
            
            return [
                'status' => $recall['status'] ?? 'Unknown',
                'reference' => $recall['referentiecode_rdw'] ?? null,
                'details' => [
                    'publication_date' => $detail['publicatiedatum_rdw_dt'] ?? null,
                    'defect_description' => $detail['omschrijving_defect'] ?? null,
                    'consequences' => $detail['materi_le_gevolgen'] ?? null,
                    'repair_description' => $detail['beschrijving_van_het_herstel'] ?? null,
                    'contact_phone' => $detail['meer_informatie_via_telefoonnummer'] ?? null,
                    'risk_assessment' => $detail['risicobeoordeling_rdw'] ?? null
                ]
            ];
        }, $recallStatus);
    }

    // Added objects
    $addedObjects = fetchRDWData("{$apiEndpoints['added_objects']}?kenteken=$kenteken");
    if (!empty($addedObjects)) {
        $response['data']['additional']['modifications'] = $addedObjects;
    }

    $response['success'] = true;

} catch (Exception $e) {
    $response['error'] = [
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ];
    http_response_code(400);
}

header('Content-Type: application/json');
header('Cache-Control: max-age=3600');
// Use JSON_UNESCAPED_SLASHES to prevent escaping of forward slashes
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit();
