<?php

require_once __DIR__ . '/../vendor/autoload.php';

try 
{
    $amo = new \AmoCRM2\Client($account_link, $token);

    // Запуск сейлсбота
    $salesbot = $amo->salesbot;
    $salesbot->debug(true); // Режим отладки
    $salesbot['entity_id'] = 123456789;
    $salesbot['entity_type'] = 2;

    $salesbot->apiRun((int)$bot_id);



} catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}