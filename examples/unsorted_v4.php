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

    // список неразобранного
    $unsorted = $amo->unsorted->apiv4List(
    [
        //список всех возможных параметров ф-ии
        'page' => 1, // номер страницы
        'limit' => 250, // кол-во шт на странице
        'filter[uid]' => '...'|[], // Фильтр по UID неразобранного
        'filter[category]' => ['sip', 'mail', 'forms', 'chats'], // Массив, содержащий в себе категории
        'filter[pipeline_id]' => $pipeline_id, // Фильтр по ID воронки
        'order[created_at|updated_at]' => 'asc|desc', //сортировка доступна по 2 типам полей created_at или updated_at и по 2 значениям asc или desc
    ]);

    // неразобранное по uid
    $unsorted = $amo->unsorted->apiv4One($uid);

    //добавление неразобранного

    //создаем обьект контакта со всеми необходимыми полями
    $contact = $amo->contact;
    $contact['name'] = 'Тестовый через комплекс';
    // если необходимо добавить кастомные поля в контакт (компанию или сделку) этим методом, необходимо добавить конструкцию
    $contact->addv4CustomField(1129447, $phone); 
    $fields = $contact->getValues();
    $contact['custom_fields_values'] = $contact->handleCustomFields($fields['custom_fields_values']);
    //создаем обьект компании со всеми необходимыми полями
    $company = $amo->company;
    $company['name'] = 'Тестовая  через комплекс';
    //создаем обьект сделки со всеми необходимыми полями
    $lead = $amo->lead;
    $lead['name'] = 'Тестовая сделка через в4 комплекс';
    $lead['status_id'] = 28525342;
    $lead['price'] = 1;
    // тип звонок
    $unsorted = $amo->unsorted;
    $unsorted['source_uid'] = $source_uid;
    $unsorted['source_name'] = $source_name;
    $unsorted['pipeline_id'] = $pipeline_id; // необязательный параметр
    $unsorted['created_at'] = $timestamp; // необязательный параметр
    $unsorted['metadata'] = 
    [
        'from' => $caller_phone, //От кого сделан звонок
        'phone' => $callee_phone, // Кому сделан звонок
        'called_at' => $timestamp, //Когда сделан звонок в формате Unix Timestamp
        'duration' => $duration, //Сколько длился звонок
        'link' => $link, //Ссылка на запись звонка
        'service_code' => $service_code, //Код сервиса, через который сделан звонок
        'is_call_event_needed' => true, //В случае передачи значения true, в карточку будет добавлено событие о входящем звонке
    ];
    $unsorted['_embedded'] = ['contacts' => [$contact], 'companies' => [$company], 'leads' => [$lead]]; // допускается только по 1 сущности каждого вида
    $unsorted['request_id'] = $request_id; // необязательный параметр
    $result = $unsorted->apiv4Add('sip');

    //тип форма
    $unsorted = $amo->unsorted;
    $unsorted['source_uid'] = $source_uid;
    $unsorted['source_name'] = $source_name;
    $unsorted['pipeline_id'] = $pipeline_id; // необязательный параметр
    $unsorted['created_at'] = $timestamp; // необязательный параметр
    $unsorted['metadata'] =
    [
        'form_id' => $form_id, //Идентификатор формы на стороне интеграции
        'form_name' => $form_name, //Название формы
        'form_page' => $form_page, //Страница, на которой установлена форма
        'ip' => $ip, //IP адрес, с которого поступила заявка
        'form_sent_at' => $form_sent_at, //Временная метка отправки данных через форму в формате Unix Timestamp
        'referer' => $referer, //Информация, откуда был переход на страницу, где расположена форма
    ];
    $unsorted['_embedded'] = ['contacts' => [$contact], 'companies' => [$company], 'leads' => [$lead]];
    $unsorted['request_id'] = $request_id; // необязательный параметр
    $result = $unsorted->apiv4Add('form');

    // принятие неразобранного
    $unsorted = $amo->unsorted;
    $unsorted['user_id'] = $responsible_user_id; // необязательный параметр
    $unsorted['status_id'] = $status_id; // необязательный параметр
    $result = $unsorted->apiv4Accept($uid);

    // отклонение неразобранного
    $unsorted = $amo->unsorted;
    $unsorted['user_id'] = $responsible_user_id; // необязательный параметр
    $result = $unsorted->apiv4Decline($uid);

    // привязка неразобранного
    $unsorted = $amo->unsorted;
    $unsorted['user_id'] = $responsible_user_id;
    $unsorted['link'] = ['entity_id' => $entity_id, 'entity_type' => $entity_type, 'metadata' => ['contact_id' => $contact_id]]; // link - обязательный параметр
    $result = $unsorted->apiv4Link($uid);

    // сводная информация по неразобранному - параметры не обязательны
    $info = $amo->unsorted->apiv4GetAllSummary(
    [
        'filter[uid]' => '...'|[], // Фильтр по UID неразобранного
        'filter[created_at][from]' => 1589176500,
        'filter[created_at][to]' => 1589176900,
        'filter[pipeline_id]' => $pipeline_id, // Фильтр по ID воронки
    ]);
} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}