<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $amo = new \AmoCRM2\Client($account_link, $token);

    // Список контактов
    // Метод для получения списка контактов с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 500 контактов.

    print_r($amo->contact->apiList([
        'query' => 'Илья',
    ]));

    // С доп. фильтрацией по (изменено с)
    print_r($amo->contact->apiList([
        'query' => 'Илья',
        'limit_rows' => 1,
    ], '-100 DAYS'));

    // получение контакта с доп сущностями по версии v4
    $contact = $amo->contact->apiv4List(['id' => $contact_id, 'with' => ['customers']]);

    // Добавление и обновление контактов
    // Метод позволяет добавлять контакты по одному или пакетно,
    // а также обновлять данные по уже существующим контактам.

    $contact = $amo->contact;
    $contact->debug(true); // Режим отладки
    $contact['name'] = 'ФИО';
    $contact['request_id'] = '123456789';
    $contact['date_create'] = '-2 DAYS';
    $contact['responsible_user_id'] = 697344;
    $contact['company_name'] = 'ООО Тестовая компания';
    $contact['tags'] = ['тест1', 'тест2'];
    $contact->addCustomField(448, [
        ['+79261112233', 'WORK'],
    ]);

    $id = $contact->apiAdd();
    print_r($id);

    // Или массовое добавление:
    $contact1 = clone $contact;
    $contact1['name'] = 'ФИО 1';
    $contact2 = clone $contact;
    $contact2['name'] = 'ФИО 2';

    $ids = $amo->contact->apiAdd([$contact1, $contact2]);
    print_r($ids);

    // Обновление контактов
    $contact = $amo->contact;
    $contact->debug(true); // Режим отладки
    $contact['name'] = 'ФИО 3';

    $contact->apiUpdate((int)$id, 'now');

    // Или массовое обновление:
    $contact = $amo->contact;
    $contact->debug(true); // Режим отладки
    $contact1 = clone $contact;
    $contact1['id'] = 24180715;
    $contact1['name'] = 'Тестовый контакт 1: новое название';
    $contact2 = clone $contact;
    $contact2['id'] = 24190891;
    $contact2['name'] = 'Тестовый контакт 2: новое название';

    $result = $amo->contact->apiv4Update([$contact1, $contact2]);

    // доступно массовое обновление кастомных полей
    // в ключах массива доступна запись как field_id так и field_code
    //справа в значениях доступна запись как единичного значения, так и массива значений
    //если записывается массив, можно записывать как без ключей, так и с ключами (enum_id или enum_code)
    $custom_fields = 
    [
        729349 => [1249127 => '2', 1249131 => '4'], //пример 1 field_id -> массив значений с енумами
        729349 => ['2','4'], //пример 2 field_id -> массив значений без енумов
    ];
    $contact = $amo->contact;
    $contact->debug(true); // Режим отладки
    $contact1 = clone $contact;
    $contact1['id'] = 24180715;
    $contact1['custom_fields_values'] = $custom;
    $contact2 = clone $contact;
    $contact2['id'] = 24190891;
    $contact2['custom_fields_values'] = $custom;

    $result = $amo->contact->apiv4Update([$contact1, $contact2]);

    // Связи между сделками и контактами
    print_r($amo->contact->apiLinks([
        'limit_rows' => 3
    ]));

} catch (\AmoCRM2\Exception $e) {
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}
