<?php

require_once __DIR__ . '/../vendor/autoload.php';

try 
{
    $amo = new \AmoCRM2\Client($account_link, $token);

    // Заполнение и очистка кастомных полей на примере сделки
    $lead = $amo->lead;
    $lead['id'] = 24449959;

    $lead->addv4CustomField(726703, 1577836800); // поле с типом дата|дата-время|день рождения заполняется timestamp 
    $lead->addv4CustomField(726703, '2021-06-22T09:11:33+00:00'); // или RFC 3339

    $lead->addv4CustomField(727807, '1000'); // поле с типом text|numeric|textarea|price|streetaddress|tracking_data заполняется строкой (или допустимо числом для numeric)

    $lead->addv4CustomField(729609, true); // поле с типом checkbox только bool

    $lead->addv4CustomField(730917, 'https://ingeni.app'); // поле с типом url заполняется строкой, можно без https, тогда амо сама допишет http
    $lead->addv4CustomField(730917, 'ingeni.app');

    $lead->addv4CustomField(729349, ['1', '3']); // поле с типом select|multiselect|radiobutton|category можно подать только значения
    $lead->addv4CustomField(729349, [1249127 => '2', 1249131 => '4']); // можно подать с енумами

    $lead->addv4CustomField('UTM_SOURCE', 'test1'); // поля можно подавать не только по id, но и по code

    $lead->addv4CustomField(21307, '88005553535', 'WORK'); // поля типа multitext можно подавать с енумом
    $lead->addv4CustomField(21307, '88005553535'); // и без него

    $result = $lead->apiv4Update();



} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s', $e->getCode(), $e->getMessage());
}
