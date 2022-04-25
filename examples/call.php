<?php

require_once __DIR__ . '/../vendor/autoload.php';

try 
{
    $amo = new \AmoCRM2\Client($account_link, $token); 

    // Добавление звонков, метод в4
    // Данный метод позволяет пакетно добавлять звонки в карточки сущностей

    $call = $amo->call;
    $call->debug(true); // Режим отладки
    // обязательные поля
    $call['phone'] = '78005553535'; // поиск производится по последним 10 цифрам номера
    $call['direction'] = 'inbound'|'outbound'; // входящий или исходящий
    $call['duration'] = 120; // длительность в сек
    $call['source'] = 'sipuni'; // источник
    // необязательные поля
    $call['link'] = 'http://example.com/audio.mp3'; // запись
    $call['uuid'] = '947669bc-ec58-450e-83e8-828a3e6fc354'; // id звонка
    $call['call_result'] = 'Успешный разговор'; // результат звонка, типа комментарий
    $call['call_status'] = 4; //Статус звонка. Доступные варианты: 1 – оставил сообщение, 2 – перезвонить позже, 3 – нет на месте, 4 – разговор состоялся, 5 – неверный номер, 6 – Не дозвонился, 7 – номер занят. 
    $call['responsible_user_id'] = 0; // кому звонок
    $call['created_by'] = 0;
    $call['updated_by'] = 0;
    $call['created_at'] = 1650870500;
    $call['updated_at'] = 1650870500;
    $result = $call->apiv4Add();

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
