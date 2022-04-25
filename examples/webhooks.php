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

    // Список Webhooks
    // Метод для получения списка Webhooks.

    $webhooks = $amo->webhooks->apiv4List();
    // доступна фильтрация по url вебхука
    $webhooks = $amo->webhooks->apiv4List(['filter[destination]' => 'https://ingeni.app/0/support/telegram/']);

    // подписка на события
    $result = $amo->webhooks->apiv4Subscribe('https://ingeni.app/modules/webhook/', ['responsible_lead']);

    //отписка от всех событий
    $result = $amo->webhooks->apiv4Unsubscribe('https://ingeni.app/modules/webhook/');

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

    // Список Webhooks
    // Метод для получения списка Webhooks.

    print_r($amo->webhooks->apiList());

    // Добавление Webhooks
    // Метод для добавления Webhooks на одно событие.

    print_r($amo->webhooks->apiSubscribe('http://example.com/', 'status_lead'));

    // Добавление Webhooks
    // Метод для добавления Webhooks на несколько событий.

    print_r($amo->webhooks->apiSubscribe('http://example.com/', [
        'add_contact',
        'update_contact',
        'delete_contact'
    ]));

    // Удаления Webhooks
    // Метод для удаления Webhooks на одно событие.

    print_r($amo->webhooks->apiUnsubscribe('http://example.com/', 'status_lead'));

    // Удаления Webhooks
    // Метод для удаления Webhooks на несколько событий.

    print_r($amo->webhooks->apiUnsubscribe('http://example.com/', [
        'add_contact',
        'update_contact',
        'delete_contact'
    ]));

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
