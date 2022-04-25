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

    // Список примечаний
    $notes = $amo->note->apiv4List('leads', 
        [
            'limit' => 250, // кол-во шт на странице
            'page' => 1, // номер страницы
            'filter[id]' => 40206119, // id примечания - можно массив из id
            'filter[entity_id]' => [6730231], // id сущности
            'filter[note_type]' => 'common', // тип примечания можно []
            'filter[updated_at][from]' => 1549870000, // изменено с - по
            'filter[updated_at][to]' => 1549874640,
            'order[updated_at|id]' => 'asc|desc', //сортировка доступна по 2 типам полей updated_at или id и по 2 значениям asc или desc
        ]);

    // Список примечаний по id 1 сущности
    $notes = $amo->note->apiv4One('leads', 6730231, 
        [
            'filter[id]' => 40206119, 
            'filter[note_type]' => 'common', 
            'filter[updated_at][from]' => 1549870000, 
            'filter[updated_at][to]' => 1549874640, 
            'order[updated_at|id]' => 'asc|desc'
        ]);

    // добавление примечания
    $note = $amo->note;
    $note['note_type'] = 'common';
    $note['params'] = ['text' => $comment];
    $note->apiv4Add('leads', (int)$lead_id);

    // редактирование примечания
    $note = $amo->note;
    $note['note_type'] = 'common';
    $note['params'] = ['text' => $comment];
    $note->apiv4Update('leads', (int)$lead_id); 

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

    // Список примечаний
    // Метод для получения списка примечаний с возможностью фильтрации и постраничной выборки.
    // Ограничение по возвращаемым на одной странице (offset) данным - 500 записей.

    print_r($amo->note->apiList([
        'type' => 'lead',
        'limit_rows' => 5,
        'query' => 'mail',
    ]));

    // С доп. фильтрацией по (изменено с)
    print_r($amo->note->apiList([
        'type' => 'lead',
        'limit_rows' => 5,
        'query' => 'mail',
    ], '-100 DAYS'));

    // Создадим тестовый контакт к которому привяжем примечание
    $note = $amo->contact;
    $note['name'] = 'ФИО';
    $noteId = $note->apiAdd();

    // Добавление и обновление примечаний
    // Метод позволяет добавлять примечание по одному или пакетно,
    // а также обновлять данные по уже существующим примечаниям

    $note = $amo->note;
    $note->debug(true); // Режим отладки
    $note['element_id'] = $noteId;
    $note['element_type'] = \AmoCRM\Models\Note::TYPE_CONTACT; // 1 - contact, 2 - lead
    $note['note_type'] = \AmoCRM\Models\Note::COMMON; // @see https://developers.amocrm.ru/rest_api/notes_type.php
    $note['text'] = 'Текст примечания';

    $id = $note->apiAdd();
    print_r($id);

    // Или массовое добавление
    $note1 = clone $note;
    $note1['text'] = 'Текст примечания 1';
    $note2 = clone $note;
    $note2['name'] = 'Текст примечания 2';

    $ids = $amo->note->apiAdd([$note1, $note2]);
    print_r($ids);

    // Обновление задач
    $note = $amo->note;
    $note->debug(true); // Режим отладки
    $note['element_id'] = $noteId;
    $note['element_type'] = \AmoCRM\Models\Note::TYPE_CONTACT; // 1 - contact, 2 - lead
    $note['note_type'] = \AmoCRM\Models\Note::COMMON; // @see https://developers.amocrm.ru/rest_api/notes_type.php
    $note['text'] = 'Апдейт примечания';

    $note->apiUpdate((int)$id, 'now');

    // Или массовое обновление:
    $note = $amo->note;
    $note->debug(true); // Режим отладки
    $note1 = clone $note;
    $note1['id'] = 24180715;
    $note1['name'] = 'Тестовый контакт 1: новое название';
    $note2 = clone $note;
    $note2['id'] = 24190891;
    $note2['name'] = 'Тестовый контакт 2: новое название';

    $result = $amo->note->apiv4Update([$note1, $note2]);

} 
catch (\AmoCRM2\Exception $e) 
{
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
