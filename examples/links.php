<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM2\Client($account_link, $token);

    // Связи между сущностями
    // Метод для получения связей между сущностями аккаунта

    print_r($amo->links->apiList([
        'from' => 'leads',
        'from_id' => 1125199,
        'to' => 'contacts',
        'to_id' => 3673249
    ]));

    // Установка связи между сущностями
    // Метод позволяет устанавливать связи между сущностями

    $link = $amo->links;
    $link['from'] = 'leads';
    $link['from_id'] = 1125199;
    $link['to'] = 'contacts';
    $link['to_id'] = 3673249;
    var_dump($link->apiLink());

    // v4 - пример привязки элементов каталога
    $link = $amo->links;
    $link->debug(true); // Режим отладки
    $link['from'] = 'leads';
    $link['from_id'] = 12012159;
    $link['to'] = 'catalog_elements';
    $link['to_id'] = 393793;
    $link['catalog_id'] = 4849;
    $link['quantity'] = 1;
    //$link['price_id'] = 1234;
    //$link['main_contact'] = true;
    $res = $link->apiv4Link();

    // v4 - пример отвязки элементов каталога
    $link = $amo->links;
    $link->debug(true); // Режим отладки
    $link['from'] = 'leads';
    $link['from_id'] = 12012159;
    $link['to'] = 'catalog_elements';
    $link['to_id'] = 393793;
    $link['catalog_id'] = 4849;
    //$link['updated_by'] = 0;
    $res = $link->apiv4Unink();

    // Разрыв связи между сущностями
    // Метод позволяет удалять связи между сущностями

    $link = $amo->links;
    $link['from'] = 'leads';
    $link['from_id'] = 1125199;
    $link['to'] = 'contacts';
    $link['to_id'] = 3673249;
    var_dump($link->apiUnlink());

} catch (\AmoCRM2\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
