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

    // Список компаний
    $companies = $amo->company->apiv4List(
    [
        //список всех возможных параметров ф-ии
        'id' => 000000, // поиск по id компании
        'query' => '8005553535', // поисковый запрос
        'with' => ['catalog_elements', 'leads', 'contacts', 'customers'], // доп параметры
        'limit' => 250, // кол-во шт на странице
        'page' => 1, // номер страницы
        'order[updated_at|id]' => 'asc|desc', //сортировка доступна по 2 типам полей updated_at или id и по 2 значениям asc или desc
        'filter[...]' => '...'|[], // https://www.amocrm.ru/developers/content/crm_platform/filters-api по фильтрации
    ]);

    //Контакт по id, доступны доп параметры через with (необязательно)
    $company = $amo->company->apiv4One(000000, ['with' => []]);

    // Добавление компании
    $company = $amo->company;
    $company['name'] = 'Тестовая компания через в4';
    $company->addv4CustomField(167411, 'Спецпроект'); // добавляет кастомное поле
    $company->addTags(['тест1', 'тест2']); //добавляет тэги
    $result = $company->apiv4Add();

    // Обновление компании
    $company = $amo->company;
    $company['id'] = 24180715;
    $company['name'] = 'Тестовая компания: новое название';
    $result = $company->apiv4Update();

    // Или массовое обновление:
    $company = $amo->company;
    $company->debug(true); // Режим отладки
    $company1 = clone $company;
    $company1['id'] = 24180715;
    $company1['name'] = 'Тестовая компания 1: новое название';
    $company2 = clone $company;
    $company2['id'] = 24190891;
    $company2['name'] = 'Тестовая компания 2: новое название';

    $result = $amo->company->apiv4Update([$company1, $company2]);

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

    // Список компаний
    // Метод для получения списка компаний с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 500 компаний.

    print_r($amo->company->apiList([
        'query' => 'mail',
    ]));

    // С доп. фильтрацией по (изменено с)
    print_r($amo->company->apiList([
        'query' => 'mail',
        'limit_rows' => 10,
    ], '-100 DAYS'));

    // Добавление и обновление компаний
    // Метод позволяет добавлять компании по одной или пакетно,
    // а также обновлять данные по уже существующим компаниям

    $company = $amo->company;
    $company->debug(true); // Режим отладки
    $company['name'] = 'ООО Тестовая компания';
    $company['request_id'] = '123456789';
    $company['date_create'] = '-2 DAYS';
    $company['responsible_user_id'] = 697344;
    $company['tags'] = ['тест1', 'тест2'];
    $company->addCustomField(448, [
        ['+79261112233', 'WORK'],
        ['+79261112200', 'MOB'],
    ]);

    $id = $company->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $company1 = clone $company;
    $company1['name'] = 'ООО Тестовая компания 1';
    $company2 = clone $company;
    $company2['name'] = 'ООО Тестовая компания 2';

    $ids = $amo->company->apiAdd([$company1, $company2]);
    print_r($ids);

    // Обновление компаний
    $company = $amo->company;
    $company->debug(true); // Режим отладки
    $company['name'] = 'ООО Тестовая компания 3';

    $company->apiUpdate((int)$id, 'now');

    // Или массовое обновление:
    $company = $amo->company;
    $company->debug(true); // Режим отладки
    $company1 = clone $company;
    $company1['id'] = 24180715;
    $company1['name'] = 'Тестовая компания 1: новое название';
    $company2 = clone $company;
    $company2['id'] = 24190891;
    $company2['name'] = 'Тестовая компания 2: новое название';

    $result = $amo->company->apiv4Update([$company1, $company2]);

    // доступно массовое обновление кастомных полей
    // в ключах массива доступна запись как field_id так и field_code
    //справа в значениях доступна запись как единичного значения, так и массива значений
    //если записывается массив, можно записывать как без ключей, так и с ключами (enum_id или enum_code)
    $custom_fields = 
    [
        729349 => [1249127 => '2', 1249131 => '4'], //пример 1 field_id -> массив значений с енумами
        729349 => ['2','4'], //пример 2 field_id -> массив значений без енумов
    ];
    $company = $amo->company;
    $company->debug(true); // Режим отладки
    $company1 = clone $company;
    $company1['id'] = 24180715;
    $company1['custom_fields_values'] = $custom;
    $company2 = clone $company;
    $company2['id'] = 24190891;
    $company2['custom_fields_values'] = $custom;

    $result = $amo->company->apiv4Update([$company1, $company2]);

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
