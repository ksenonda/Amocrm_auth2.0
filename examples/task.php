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

    // Список задач
    $tasks = $amo->task->apiv4List([
        'page' => 1,
        'limit' => 250,
        'filter[responsible_user_id]' => 6121351,
        'filter[is_completed]' => false,
        'filter[task_type]' => 2,
        'filter[entity_type]' => 'leads',
        'filter[entity_id]' => 24190891,
        'filter[id]' => 15302397,
        'filter[updated_at][from]' => 1618895258,
        'filter[updated_at][to]' => 1650442058,
        'order[created_at|complete_till|id]' => 'asc|desc', //сортировка доступна по 3 типам полей created_at или complete_till или id и по 2 значениям asc или desc
    ]);

    //Задача по id
    $task = $amo->task->apiv4One(15302397);

    // Добавление задачи
    $task = $amo->task;
    $task['responsible_user_id'] = 6121351;
    $task['entity_id'] = 24190891;
    $task['entity_type'] = 'leads';
    $task['is_completed'] = false;
    $task['task_type_id'] = 2248942;
    $task['text'] = 'Test'; // обязательный параметр
    $task['duration'] = 3600;
    $task['complete_till'] = 1653023258; // обязательный параметр
    $task['result'] = ['text' => 'ok'];
    $task['created_by'] = 0;
    $task['updated_by'] = 0;
    $task['created_at'] = 1650442058;
    $task['updated_at'] = 1650442058;
    $result = $task->apiv4Add();

    //Обновление задач
    $task = $amo->task;
    $task['id'] = 15879187; // обязательный параметр
    $task['responsible_user_id'] = 6121351;
    $task['entity_id'] = 24190891;
    $task['entity_type'] = 'leads';
    $task['is_completed'] = false;
    $task['task_type_id'] = 2248942;
    $task['text'] = 'Test';
    $task['duration'] = 3600;
    $task['complete_till'] = 1653023258;
    $task['result'] = ['text' => 'ok'];
    $task['created_by'] = 0;
    $task['updated_by'] = 0;
    $task['created_at'] = 1650442058;
    $task['updated_at'] = 1650442058;
    $result = $task->apiv4Update();

    //Быстрое завершение задачи - обязательный id и необязательный текст
    $task = $amo->task->apiv4Complete(15879187, 'complete');

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
    // Список задач
    // Метод для получения списка задач с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 500 задач.

    print_r($amo->task->apiList([
        'type' => 'task',
        'limit_rows' => 5,
        'query' => 'mail',
    ]));

    // С доп. фильтрацией по (изменено с)
    print_r($amo->task->apiList([
        'type' => 'task',
        'limit_rows' => 5,
        'query' => 'mail',
    ], '-100 DAYS'));

    // Добавление и обновление задач
    // Метод позволяет добавлять задачи по одной или пакетно,
    // а также обновлять данные по уже существующим задачам

    $task = $amo->task;
    $task->debug(true); // Режим отладки
    $task['element_id'] = 11029224;
    $task['element_type'] = 1;
    $task['date_create'] = '-2 DAYS';
    $task['task_type'] = 1;
    $task['text'] = "Текст\nзадачи";
    $task['responsible_user_id'] = 798027;
    $task['complete_till'] = '+1 DAY';

    $id = $task->apiAdd();
    print_r($id);

    // Или массовое добавление
    $task1 = clone $task;
    $task1['text'] = 'Текст задачи 1';
    $task2 = clone $task;
    $task2['name'] = 'Текст задачи 2';

    $ids = $amo->task->apiAdd([$task1, $task2]);
    print_r($ids);

    // Обновление задач
    $task = $amo->task;
    $task->debug(true); // Режим отладки

    $task->apiUpdate((int)$id, 'Текст задачи', 'now');

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
