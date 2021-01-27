<?php

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $amo = new \AmoCRM2\Client($account_link, $token);

    // Агрегирование неразобранных заявок
    // Метод для получения агрегированной информации о неразобранных заявках.

    print_r($amo->unsorted->apiGetAllSummary());

} catch (\AmoCRM2\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
