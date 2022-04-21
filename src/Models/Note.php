<?php

namespace AmoCRM2\Models;

use AmoCRM2\Models\Traits\SetDateCreate;
use AmoCRM2\Models\Traits\SetLastModified;

/**
 * Class Note
 *
 * Класс модель для работы с Примечаниями
 *
 * @package AmoCRM2\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Note extends AbstractModel
{
    use SetDateCreate, SetLastModified;

    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'id',
        'element_id',
        'element_type',
        'note_type',
        'date_create',
        'last_modified',
        'request_id',
        'text',
        'responsible_user_id',
        'created_user_id',
        'order',
        'entity_id',
        'created_by',
        'params'
    ];

    /**
     * @link https://developers.amocrm.ru/rest_api/notes_type.php
     * @type array Типы примечаний
     */
    protected $types = [
        self::DEAL_CREATED => 'Сделка создана',
        self::CONTACT_CREATED => 'Контакт создан',
        self::DEAL_STATUS_CHANGED => 'Статус сделки изменен',
        self::COMMON => 'Обычное примечание',
        self::ATTACHMENT => 'Файл',
        self::CALL => 'Звонок приходящий от iPhone-приложений',
        self::EMAIL_MESSAGE => 'Письмо',
        self::EMAIL_ATTACHMENT => 'Письмо с файлом',
        self::CALL_IN => 'Входящий звонок',
        self::CALL_OUT => 'Исходящий звонок',
        self::COMPANY_CREATED => 'Компания создана',
        self::TASK_RESULT => 'Результат по задаче',
        self::SYSTEM => 'Системное сообщение',
        self::SMS_IN => 'Входящее смс',
        self::SMS_OUT => 'Исходящее смс',
    ];

    const DEAL_CREATED = 1;
    const CONTACT_CREATED = 2;
    const DEAL_STATUS_CHANGED = 3;
    const COMMON = 4;
    const ATTACHMENT = 5;
    const CALL = 6;
    const EMAIL_MESSAGE = 7;
    const EMAIL_ATTACHMENT = 8;
    const CALL_IN = 10;
    const CALL_OUT = 11;
    const COMPANY_CREATED = 12;
    const TASK_RESULT = 13;
    const SYSTEM = 25;
    const SMS_IN = 102;
    const SMS_OUT = 103;

    /**
     * @const int Типа задачи Контакт
     */
    const TYPE_CONTACT = 1;

    /**
     * @const int Типа задачи Сделка
     */
    const TYPE_LEAD = 2;

    /** @const int Типа задачи Компания */
    const TYPE_COMPANY = 3;

    /** @const int Типа задачи Задача */
    const TYPE_TASK = 4;

    /** @const int Типа задачи Покупатель */
    const TYPE_CUSTOMER = 12;

    /**
     * Список примечаний
     *
     * Метод для получения списка примечаний с возможностью фильтрации и постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 500 примечаний.
     *
     * @link https://developers.amocrm.ru/rest_api/notes_list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters, $modified = null)
    {
        $response = $this->getRequest('/private/api/v2/json/notes/list', $parameters, $modified);

        return isset($response['notes']) ? $response['notes'] : [];
    }
    /**
     * Список примечаний по типу сущности, метод в4
     *
     * Метод позволяет получить примечания по типу сущности с возможностью фильтрации и постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 250 примечаний.
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/events-and-notes#notes-list
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiv4List($entity, $parameters = [])
    {
        $response = $this->getRequest('/api/v4/'.$entity.'/notes', $parameters);

        return isset($response['_embedded']['notes']) ? $response['_embedded']['notes'] : [];
    }

    /**
     * Список примечаний по конкретной сущности, по ID сущности, метод в4
     *
     * Метод позволяет получить примечания по ID родительской сущности.
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/events-and-notes#notes-entity-list
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiv4One($entity_type, $entity_id, $parameters = [])
    {
        $response = $this->getRequest('/api/v4/'.$entity_type.'/'.$entity_id.'/notes', $parameters);

        return isset($response['_embedded']['notes']) ? $response['_embedded']['notes'] : [];
    }

    /**
     * Добавление примечания
     *
     * Метод позволяет добавлять примечание по одному или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/notes_set.php
     * @param array $notes Массив примечаний для пакетного добавления
     * @return int|array Уникальный идентификатор примечания или массив при пакетном добавлении
     */
    public function apiAdd($notes = [])
    {
        if (empty($notes)) {
            $notes = [$this];
        }

        $parameters = [
            'notes' => [
                'add' => [],
            ],
        ];

        foreach ($notes AS $note) {
            $parameters['notes']['add'][] = $note->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/notes/set', $parameters);

        if (isset($response['notes']['add'])) {
            $result = array_map(function($item) {
                return $item['id'];
            }, $response['notes']['add']);
        } else {
            return [];
        }

        return count($notes) == 1 ? array_shift($result) : $result;
    }

    /**
     * Добавление примечаний, метод в4
     *
     * Метод позволяет добавлять примечания
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/events-and-notes#notes-add
     * @param string $notes Массив примечаний на добавление
     * @throws \AmoCRM\Exception
     */
    public function apiv4Add($entity_type, $entity_id, $notes = [])
    {
        $parameters = [];

        if (empty($notes))
        {
            $notes = [$this];
        }

        foreach ($notes AS $note) 
        {
            $updated_values = $note->getValues();

            $parameters[] = $updated_values; 
        }

        $response = $this->postv4Request('/api/v4/'.$entity_type.'/'.$entity_id.'/notes', $parameters);

        return isset($response['_embedded']['notes']) ? $response['_embedded']['notes'] : [];
    }

    /**
     * Обновление примечания
     *
     * Метод позволяет обновлять данные по уже существующим примечаниям
     *
     * @link https://developers.amocrm.ru/rest_api/notes_set.php
     * @param int $id Уникальный идентификатор примечания
     * @param string $modified Дата последнего изменения данной сущности
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiUpdate($id, $modified = 'now')
    {
        $this->checkId($id);

        $parameters = [
            'notes' => [
                'update' => [],
            ],
        ];

        $lead = $this->getValues();
        $lead['id'] = $id;
        $lead['last_modified'] = strtotime($modified);

        $parameters['notes']['update'][] = $lead;

        $response = $this->postRequest('/private/api/v2/json/notes/set', $parameters);

        return empty($response['notes']['update']['errors']);
    }

    /**
     * Редактирование примечаний, метод в4
     *
     * Метод позволяет редактировать примечания
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/events-and-notes#notes-edit
     * @param string $notes Массив примечаний на обновление
     * @throws \AmoCRM\Exception
     */
    public function apiv4Update($entity_type, $entity_id, $notes = [])
    {
        $parameters = [];

        if (empty($notes))
        {
            $notes = [$this];
        }

        foreach ($notes AS $note) 
        {
            $updated_values = $note->getValues();

            $parameters[] = $updated_values; 
        }

        $response = $this->patchRequest('/api/v4/'.$entity_type.'/'.$entity_id.'/notes', $parameters);

        return isset($response['_embedded']['notes']) ? $response['_embedded']['notes'] : [];
    }
}
