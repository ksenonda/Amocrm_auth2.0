<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM2\Client($account_link, $token);

    // Получение информации по аккаунту в котором произведена авторизация:
    // название, оплаченный период, пользователи аккаунта и их права,
    // справочники дополнительных полей контактов и сделок, справочник статусов сделок,
    // справочник типов событий, справочник типов задач и другие параметры аккаунта.

    // Полный формат
    print_r($amo->account->apiCurrent());

    // Краткий формат (если полный не влезает в буфер консоли)
    print_r($amo->account->apiCurrent(true));

    // Возвращает сведения о пользователе.
    print_r($amo->account->apiUser());

} catch (\AmoCRM2\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
