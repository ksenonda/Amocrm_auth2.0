<?php

require_once __DIR__ . '/../vendor/autoload.php';

try 
{
    $amo = new \AmoCRM2\Client($account_link, $token);

    // Список событий
    // Метод для получения списка событий с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 100 событий.
    //https://www.amocrm.ru/developers/content/crm_platform/events-and-notes

    $events = $amo->lead->apiList(
    [
        'with' => '...', //(str) доступные значения поля: contact_name/lead_name/company_name/catalog_element_name/customer_name
        'page' => 000, // (int) номер страницы выборки
        'limit' => 000, // (int) кол-во событий в запросе - макс.100
        'filter[id]' => '...'/[], //(string|array) - фильтр по id события
        'filter[created_at]' => 000/[],//(int|array) - фильтр по дате создания события
        // можно задавать временной интервал

            'filter[created_at][from]' => 000,
            'filter[created_at][to]' => 000,

        'filter[created_by]' => 000/[],//(int|array) - фильтр по пользователю - в массиве можно задавать до 10 пользователей
        'filter[entity]' => '...'/[],//(string|array) - доступные значения поля: lead/contact/company/customer/task/catalog_{CATALOG_ID}
        'filter[entity_id]' => 000/[],//(int|array) - фильтр по id сущности - до 10 сущностей
        'filter[type]' => '...'/[],//(string|array) - доступные типы см. https://www.amocrm.ru/developers/content/crm_platform/events-and-notes#events-type
        'filter[value_before]' => '...'/[],//(string|array) - доступные значения: 
        //примеры
        //leads_statuses - изменение статуса и/или воронки
        
            'filter[value_before][leads_statuses][0][pipeline_id]' => 13513,
            'filter[value_before][leads_statuses][0][status_id]' => 17079863,
        
        //responsible_user_id - изменение ответственного
            'filter[value_before][responsible_user_id]' => 32321,

        //custom_field_values - изменение кастомных полей
            'filter[value_before][custom_field_values]' => 145,
            'filter[type]' => 'custom_field_57832_value_changed',

        //value - изменение значения - работает с типами nps_rate_added/sale_field_changed/name_field_changed/ltv_field_changed/custom_field_value_changed
            'filter[value_before][value]' => 155,
            'filter[type]' => 'sale_field_changed',
            'filter[entity]' => 'lead',

        'filter[value_after]' => '...'/[],//(string|array) - все что актуально для value_before
    ]);



} catch (\AmoCRM2\Exception $e) {
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}