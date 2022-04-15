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

    // Список покупателей
    $customers = $amo->customer->apiv4List(
    [
        //список всех возможных параметров ф-ии
        'id' => 000000, // поиск по id покупателя
        'query' => '8005553535', // поисковый запрос
        'with' => ['catalog_elements', 'contacts', 'companies'], // доп параметры
        'limit' => 250, // кол-во шт на странице
        'page' => 1, // номер страницы
        'filter[...]' => '...'|[], // https://www.amocrm.ru/developers/content/crm_platform/filters-api по фильтрации
    ]);

    //Покупатель по id, доступны доп параметры через with (необязательно)
    $customer = $amo->customer->apiv4One(000000, ['with' => []]);

    // Добавление покупателя
    $customer = $amo->customer;
    $customer['name'] = 'Тестовый покупатель через в4';
    $customer['next_price'] = 1;
    $customer['next_date'] = ''; // необязательное поле, можно подать дату в следующих вариантах: ''|'2022-04-15'|'+ 6 months'
    $customer->addv4CustomField(167411, 'Спецпроект'); // добавляет кастомное поле
    $customer->addTags(['тест1', 'тест2']); //добавляет тэги
    $result = $customer->apiv4Add();

    // Обновление покупателя
    $customer = $amo->customer;
    $customer['id'] = 24180715;
    $customer['name'] = 'Тестовый покупатель: новое название';
    $result = $customer->apiv4Update();

    // Или массовое обновление:
    $customer = $amo->customer;
    $customer->debug(true); // Режим отладки
    $customer1 = clone $customer;
    $customer1['id'] = 24180715;
    $customer1['name'] = 'Тестовый покупатель 1: новое название';
    $customer2 = clone $customer;
    $customer2['id'] = 24190891;
    $customer2['name'] = 'Тестовый покупатель 2: новое название';

    $result = $amo->customer->apiv4Update([$customer1, $customer2]);

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}

/**
*
* Примеры версии в2
*
*/

try 
{
    $amo = new \AmoCRM2\Client($account_link, $token);

    // Список покупателей
    // Метод для получения покупателей аккаунта

    print_r($amo->customer->apiList([
        'limit_rows' => 100,
    ]));

    // Добавление покупателей
    // Метод позволяет добавлять покупателей по одному или пакетно

    $customer = $amo->customer;
    $customer->debug(true); // Режим отладки
    $customer['name'] = 'ФИО';
    $customer['request_id'] = '123456789';
    $customer['main_user_id'] = 151516;
    $customer['next_price'] = 5000;
    $customer['periodicity'] = 7;
    $customer['tags'] = ['тест1', 'тест2'];
    $customer['next_date'] = '+2 DAYS';

    $id = $customer->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $customer1 = clone $customer;
    $customer1['name'] = 'ФИО 1';
    $customer2 = clone $customer;
    $customer2['name'] = 'ФИО 2';

    $ids = $amo->customer->apiAdd([$customer1, $customer2]);
    print_r($ids);

    // Обновление покупателей
    $customer = $amo->customer;
    $customer->debug(true); // Режим отладки
    $customer['name'] = 'ФИО 3';

    $customer->apiUpdate((int)$id);

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}
