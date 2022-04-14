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

    // Список сделок
    $leads = $amo->lead->apiv4List(
    [
        //список всех возможных параметров ф-ии
        'id' => 000000, // поиск по id сделки
        'query' => '8005553535', // поисковый запрос
        'with' => ['catalog_elements', 'is_price_modified_by_robot', 'loss_reason', 'contacts', 'companies', 'only_deleted', 'source_id'], // доп параметры
        'limit' => 250, // кол-во шт на странице
        'page' => 1, // номер страницы
        'order[created_at|updated_at|id]' => 'asc|desc', //сортировка доступна по 3 типам полей created_at или updated_at или id и по 2 значениям asc или desc
        'filter[...]' => '...'|[], // https://www.amocrm.ru/developers/content/crm_platform/filters-api по фильтрации
    ]);

    //Сделка по id, доступны доп параметры через with (необязательно)
    $lead = $amo->lead->apiv4One(000000, ['with' => []]);

    // Добавление сделки
    $lead = $amo->lead;
    $lead['name'] = 'Тестовая сделка через в4';
    $lead['status_id'] = 28525342;
    $lead['price'] = 1;
    $lead->addv4CustomField(167411, 'Спецпроект'); // добавляет кастомное поле
    $lead->addTags(['тест1', 'тест2']); //добавляет тэги
    $lead->linkContact(88107275, false); //привязывает контакт (1 параметр - id контакта, 2 параметр is_main - по умолчанию true)
    $lead->linkCompany(88638419); // привязывает компанию
    $result = $lead->apiv4Add();

    //Добавление сделки комплексом с контактом и компанией (ни контакт, ни компания не обязательные)
    //создаем обьект контакта со всеми необходимыми полями
    $contact = $amo->contact;
    $contact['name'] = 'Тестовый через комплекс';
    //создаем обьект компании со всеми необходимыми полями
    $company = $amo->company;
    $company['name'] = 'Тестовая  через комплекс';
    //создаем обьект сделки со всеми необходимыми полями
    $lead = $amo->lead;
    $lead['name'] = 'Тестовая сделка через в4 комплекс';
    $lead['status_id'] = 28525342;
    $lead['price'] = 1;
    $lead->addTags(['тест1', 'тест2']);
    $lead->addEntities($contact, $company); //привязываем обьекты
    $result = $lead->apiv4Complex();

    // Обновление сделки
    $lead = $amo->lead;
    $lead['id'] = 24180715;
    $lead['name'] = 'Тестовая сделка 1: новое название';
    $result = $lead->apiv4Update();

    // Или массовое обновление:
    $lead = $amo->lead;
    $lead->debug(true); // Режим отладки
    $lead1 = clone $lead;
    $lead1['id'] = 24180715;
    $lead1['name'] = 'Тестовая сделка 1: новое название';
    $lead2 = clone $lead;
    $lead2['id'] = 24190891;
    $lead2['name'] = 'Тестовая сделка 2: новое название';

    $result = $amo->lead->apiv4Update([$lead1, $lead2]);

    // доступно массовое обновление кастомных полей
    // в ключах массива доступна запись как field_id так и field_code
    //справа в значениях доступна запись как единичного значения, так и массива значений
    //если записывается массив, можно записывать как без ключей, так и с ключами (enum_id или enum_code)
    $custom_fields = 
    [
        'UTM_SOURCE' => 'test1', //пример 1 field_code -> значение
        729349 => [1249127 => '2', 1249131 => '4'], //пример 2 field_id -> массив значений с енумами
        729349 => ['2','4'], //пример 3 field_id -> массив значений без енумов
    ];
    $lead = $amo->lead;
    $lead->debug(true); // Режим отладки
    $lead1 = clone $lead;
    $lead1['id'] = 24180715;
    $lead1['custom_fields_values'] = $custom_fields;
    $lead2 = clone $lead;
    $lead2['id'] = 24190891;
    $lead2['custom_fields_values'] = $custom_fields;

    $result = $amo->lead->apiv4Update([$lead1, $lead2]);

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

    // Список сделок
    // Метод для получения списка сделок с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 500 сделок.

    print_r($amo->lead->apiList([
        'query' => 'Илья',
    ]));

    // С доп. фильтрацией по (изменено с)
    print_r($amo->lead->apiList([
        'query' => 'Илья',
        'limit_rows' => 1,
    ], '-100 DAYS'));

    // получение сделки с доп сущностями по версии v4
    $lead = $amo->lead->apiv4List(['id' => $lead_id, 'with' => ['catalog_elements']]);

    // Добавление и обновление сделок
    // Метод позволяет добавлять сделки по одному или пакетно,
    // а также обновлять данные по уже существующим сделкам.

    $lead = $amo->lead;
    $lead->debug(true); // Режим отладки
    $lead['name'] = 'Тестовая сделка';
    $lead['date_create'] = '-2 DAYS';
    $lead['status_id'] = 10525225;
    $lead['price'] = 3000;
    $lead['responsible_user_id'] = 697344;
    $lead['tags'] = ['тест1', 'тест2'];
    $lead['visitor_uid'] = '12345678-52d2-44c2-9e16-ba0052d9f6d6';
    $lead->addCustomField(167379, [
        [388733, 'Стартап'],
    ]);
    $lead->addCustomField(167381, [
        [388743, '6 месяцев'],
    ]);
    $lead->addCustomField(167411, 'Спецпроект');

    $id = $lead->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $lead1 = clone $lead;
    $lead1['name'] = 'Тестовая сделка 1';
    $lead2 = clone $lead;
    $lead2['name'] = 'Тестовая сделка 2';

    $ids = $amo->lead->apiAdd([$lead1, $lead2]);
    print_r($ids);

    // Обновление сделок
    $lead = $amo->lead;
    $lead->debug(true); // Режим отладки
    $lead['name'] = 'Тестовая сделка 3';

    $lead->apiUpdate((int)$id, 'now');

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}
