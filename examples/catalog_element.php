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

    // Список элементов каталога
    // Метод для получения элементов каталога аккаунта.

    $elements = $amo->catalog_element->apiv4List(7163);
    $elements = $amo->catalog_element->apiv4List(7163,
        [
            'query' => 'виджет', // поисковый запрос
            'with' => ['invoice_link'], // доп параметры
            'limit' => 250, // кол-во шт на странице
            'page' => 1, // номер страницы
            'filter[id]' => '...'|[],
        ]);

    // элемент каталога по id
    $element = $amo->catalog_element->apiv4One(7163, 1908721);
    $element = $amo->catalog_element->apiv4One(7163, 1908721, 'with' => ['invoice_link']);

    // добавление элемента
    $element = $amo->catalog_element;
    $element['name'] = 'Тестовый элемент списка';
    //$element->addv4CustomField(630431, 10); // кастомные поля добавляются через общий метод - пока недоступно, поля подавать полным массивом
    $result = $element->apiv4Add(7163);

    // изменение элемента
    $element = $amo->catalog_element;
    $element['id'] = 1909035;
    $element['name'] = 'Тестовый элемент списка'; // обязательное поле, даже если не меняется
    //$element->addv4CustomField(630429, 'тест описания');
    $result = $element->apiv4Update(7163);

    

} catch (\AmoCRM2\Exception $e) 
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

    // Список элементов каталога
    // Метод для получения элементов каталога аккаунта.

    print_r($amo->catalog_element->apiList([
        'catalog_id' => 4179,
        'term' => 'Product'
    ]));

    // Добавление элементов каталога
    // Метод позволяет добавлять элементы каталога по одному или пакетно

    $element = $amo->catalog_element;
    $element->debug(true); // Режим отладки
    $element['catalog_id'] = 4179;
    $element['name'] = 'Product';
    $element->addCustomField(212937, 1);

    $id = $element->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $element1 = clone $element;
    $element1['name'] = 'Product 1';
    $element2 = clone $element;
    $element2['name'] = 'Product 2';

    $ids = $amo->catalog_element->apiAdd([$element1, $element2]);
    print_r($ids);

    // Обновление элементов каталога
    $element = $amo->catalog_element;
    $element->debug(true); // Режим отладки
    $element['name'] = 'New product';
    $element['catalog_id'] = 4179; // без catalog_id amocrm не обновит
    $element->addCustomField(212937, 5000);

    $element->apiUpdate((int)$id);

    // Удаление каталогов
    $amo->catalog_element->apiDelete((int)$id);

} catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
