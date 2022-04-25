<?php

require_once __DIR__ . '/../vendor/autoload.php';
/**
*
* Примеры версии в4
*
*/

try 
{
    $amo = new \AmoCRM2\Client($account_link, $token);

    // Связи между сущностями
    // Метод для получения связей между сущностями аккаунта
    $links = $amo->links->apiv4List('leads', 24449959);
    $links = $amo->links->apiv4List('leads', 24449959, 
    [
      'filter[to_entity_id]' => 88691907, 
      'filter[to_entity_type]' => 'companies', 
      'filter[to_catalog_id]' => 7190, 
    ]);

    // привязка сущностей
    $link = $amo->links;
    $link['from'] = 'leads';
    $link['from_id'] = 24449959;
    $link['to'] = 'catalog_elements';
    $link['to_id'] = 123345;
    $link['catalog_id'] = 4849;
    $link['quantity'] = 1;
    $res = $link->apiv4Link();

    // отвязка сущностей
    $link = $amo->links;
    $link['from'] = 'leads';
    $link['from_id'] = 24449959;
    $link['to'] = 'catalog_elements';
    $link['to_id'] = 123345;
    $link['catalog_id'] = 4849;
    $res = $link->apiv4Unlink();

    

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}

/**
*
* Примеры версии в2
*
*/
try 
{
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
    $res = $link->apiv4Unlink();

    // Разрыв связи между сущностями
    // Метод позволяет удалять связи между сущностями

    $link = $amo->links;
    $link['from'] = 'leads';
    $link['from_id'] = 1125199;
    $link['to'] = 'contacts';
    $link['to_id'] = 3673249;
    var_dump($link->apiUnlink());

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
