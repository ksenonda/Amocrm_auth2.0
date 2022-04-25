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

    // Список каталогов
    // Метод для получения списка каталогов аккаунта.
    $catalogs = $amo->catalog->apiv4List();
    $catalogs = $amo->catalog->apiv4List(['page' => 1, 'limit' => 250]);

    // получение списка по id 
    $result = $amo->catalog->apiv4One(7163);

    //добавление списка
    $catalog = $amo->catalog;
    $catalog['name'] = 'Тестовый список'; // Обязательный параметр
    $catalog['type'] = 'regular';
    $catalog['can_add_elements'] = true;
    $catalog['can_link_multiple'] = true;
    $result = $catalog->apiv4Add();

    //изменение списка
    $catalog = $amo->catalog;
    $catalog['name'] = 'Тестовый список: новое название'; // Обязательный параметр - даже если не меняется
    $catalog['id'] = 8671;
    $catalog['can_add_elements'] = false;
    $catalog['can_link_multiple'] = false;
    $result = $catalog->apiv4Update();

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

    // Список каталогов
    // Метод для получения списка каталогов аккаунта.

    print_r($amo->catalog->apiList());

    // С фильтрацией по ID

    print_r($amo->catalog->apiList(4143));

    // Добавление каталогов
    // Метод позволяет добавлять каталоги по одному или пакетно.

    $catalog = $amo->catalog;
    $catalog->debug(true); // Режим отладки
    $catalog['name'] = 'Products';

    $id = $catalog->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $catalog1 = clone $catalog;
    $catalog1['name'] = 'Products 1';
    $catalog2 = clone $catalog;
    $catalog2['name'] = 'Products 2';

    $ids = $amo->catalog->apiAdd([$catalog1, $catalog2]);
    print_r($ids);

    // Обновление каталогов
    $catalog = $amo->catalog;
    $catalog->debug(true); // Режим отладки
    $catalog['name'] = 'Tariffs';

    $catalog->apiUpdate((int)$id);

    // Удаление каталогов
    $amo->catalog->apiDelete((int)$id);

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
