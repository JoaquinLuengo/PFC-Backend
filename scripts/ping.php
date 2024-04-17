<?php

// Arreglo con las direcciones IP que deseas verificar
$ipAddresses = [
    '192.162.0.1',
    '192.162.1.1',
];
// Función para verificar la conectividad de una dirección IP
function checkPing($ip) {
    
    $command = "ping -c 4 " . escapeshellarg($ip);
    exec($command, $output, $returnCode);

    $result = [
        'ip' => $ip,
        'success' => ($returnCode === 0),
        'output' => $output,
    ];

    return $result;
}

$results = [];

// Iterar a través de las direcciones IP y verificar la conectividad
foreach ($ipAddresses as $ip) {
    $results[] = checkPing($ip);
}

echo json_encode($results);
