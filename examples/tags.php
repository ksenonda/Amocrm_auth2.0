<?php

require_once __DIR__ . '/../vendor/autoload.php';

try 
{
    $amo = new \AmoCRM2\Client($account_link, $token); 
    // Список тэгов
    // Метод для получения списка тэгов с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 250 шт.
    //{entity_type:leads|contacts|companies|customers}

    print_r($amo->tags->apiList('leads', ['query' => 'test', 'limit' => 250, 'offset' => 0]));

    // С доп. фильтрацией по (изменено с)
    print_r($amo->tags->apiList('leads', ['filter[id]' => 1234, 'filter[name]' => 'test'], '- 100 days'));


} catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}