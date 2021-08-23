<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
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

} catch (\AmoCRM2\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
