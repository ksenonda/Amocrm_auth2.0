<?php

require_once __DIR__ . '/../vendor/autoload.php';

try 
{
    $amo = new \AmoCRM2\Client($account_link, $token);

    // Заполнение и очистка кастомных полей на примере сделки
    $lead = $amo->lead;
    $lead['id'] = 24449959;

    // поле с типом text|numeric|textarea|price|streetaddress|tracking_data заполняется строкой (или допустимо числом для numeric)
    $lead->addv4CustomField(727807, '1000'); 

    // поля можно подавать не только по id, но и по code
    $lead->addv4CustomField('UTM_SOURCE', 'test1'); 

    // поле с типом дата|дата-время|день рождения заполняется timestamp
    $lead->addv4CustomField(726703, 1577836800);  
    $lead->addv4CustomField(726703, '2021-06-22T09:11:33+00:00'); // или RFC 3339

    // поле с типом checkbox только bool
    $lead->addv4CustomField(729609, true); 

    // поле с типом url заполняется строкой, можно без https, тогда амо сама допишет http
    $lead->addv4CustomField(730917, 'https://ingeni.app'); 
    $lead->addv4CustomField(730917, 'ingeni.app');

    // поле с типом select|multiselect|radiobutton|category можно подать только значения
    $lead->addv4CustomField(729349, ['1', '3']); 
    $lead->addv4CustomField(729349, [1249127 => '2', 1249131 => '4']); // можно подать с енумами

    // поле с типом smart_address заполняется данными как multiselect, можно использовать enum_id или enum_code
    $lead->addv4CustomField(730945, ['address_line_1' => 'Николоямская улица 28/60', 'address_line_2' => 'кв. 120', 'city' => 'Москва', 'state' => 'Московская обл.', 'zip' => '109004', 'country' => 'RU']);
    $lead->addv4CustomField(730945, ['1' => 'Николоямская улица 28/60', '2' => 'кв. 120', '3' => 'Москва', '4' => 'Московская обл.', '5' => '109004', '6' => 'RU']);

    // поля типа multitext можно подавать с енумом
    $lead->addv4CustomField(21307, '88005553535', 'WORK'); 
    $lead->addv4CustomField(21307, '88005553535'); // и без него

    // поле с типом legal_entity заполняется массивом, в значении необходимо обязательно передать поля name, vat_id, kpp
    $lead->addv4CustomField(730941, [
                    "name" => "ООО Рога и копыта", //название организации - обязательное поле
                    "vat_id" => "123123123", // инн - обязательное поле
                    "kpp" => "23123123", // кпп - обязательное поле
                    "tax_registration_reason_code" => 213, // ОГРНИП
                    "entity_type" => 1, // 1 – Частное, 2 – Юридическое лицо
                    "address" => "Moscow", // адрес
                    "bank_code" => 12345, // бик
                    "external_uid" => "uuid" //идентификатор внешней системы
                ]);

    // поле любого типа можно очистить, подав NULL
    $lead->addv4CustomField(730941, NULL);

    $result = $lead->apiv4Update();

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}
