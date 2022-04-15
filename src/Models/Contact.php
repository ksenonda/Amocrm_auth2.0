<?php

namespace AmoCRM2\Models;

use AmoCRM2\Models\Traits\SetNote;
use AmoCRM2\Models\Traits\SetTags;
use AmoCRM2\Models\Traits\SetDateCreate;
use AmoCRM2\Models\Traits\SetLastModified;
use AmoCRM2\Models\Traits\SetLinkedLeadsId;

/**
 * Class Contact
 *
 * Класс модель для работы с Контактами
 *
 * @package AmoCRM2\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Contact extends AbstractModel
{
    use SetNote, SetTags, SetDateCreate, SetLastModified, SetLinkedLeadsId;

    /**
     * @var array Список доступный полей для модели
     */
    protected $fields = [
        'id',
        'name',
        'first_name',
        'last_name',
        'responsible_user_id',
        'created_by',
        'created_user_id',
        'updated_by',
        'modified_user_id',
        'created_at',
        'date_create',
        'updated_at',
        'last_modified',
        'custom_fields_values',
        'request_id',
        'linked_leads_id',
        'company_name',
        'linked_company_id',
        'tags',
        'notes',
        '_embedded'
    ];

    /**
     * Список контактов
     *
     * Метод для получения списка контактов с возможностью фильтрации и постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 500 контактов.
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters, $modified = null)
    {
        $response = $this->getRequest('/private/api/v2/json/contacts/list', $parameters, $modified);

        return isset($response['contacts']) ? $response['contacts'] : [];
    }
    /**
     * Список контактов, метод в4
     *
     * Метод для получения списка контактов с возможностью фильтрации и постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 250 контактов
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/contacts-api#contacts-list
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiv4List($parameters, $modified = null)
    {
        $response = $this->getRequest('/api/v4/contacts', $parameters, $modified);

        return isset($response['_embedded']['contacts']) ? $response['_embedded']['contacts'] : [];
    }
    /**
     * Получение контакта по id, метод в4
     *
     * Метод позволяет получить данные конкретного контакта по ID
     * 
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/contacts-api#contact-detail
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiv4One($id, $parameters = [])
    {
        $response = $this->getRequest('/api/v4/contacts/'.$id, $parameters);

        return isset($response) ? $response : [];;
    }
    /**
     * Добавление контактов
     *
     * Метод позволяет добавлять контакты по одному или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_set.php
     * @param array $contacts Массив контактов для пакетного добавления
     * @return int|array Уникальный идентификатор контакта или массив при пакетном добавлении
     */
    public function apiAdd($contacts = [])
    {
        if (empty($contacts)) {
            $contacts = [$this];
        }

        $parameters = [
            'contacts' => [
                'add' => [],
            ],
        ];

        foreach ($contacts AS $contact) {
            $parameters['contacts']['add'][] = $contact->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/contacts/set', $parameters);

        if (isset($response['contacts']['add'])) {
            $result = array_map(function ($item) {
                return $item['id'];
            }, $response['contacts']['add']);
        } else {
            return [];
        }

        return count($contacts) == 1 ? array_shift($result) : $result;
    }

    /**
     * Добавление контакта, метод в4
     *
     * Метод позволяет добавлять контакт по одному или пакетно
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/contacts-api#contacts-add
     * @param array $contacts Массив контактов для пакетного добавления
     * @return array Массив данных по контакту(контактам)
     */
    public function apiv4Add($contacts = [])
    {
        if (empty($contacts)) 
        {
            $contacts = [$this];
        }

        $parameters = [];

        foreach ($contacts as $contact) 
        {
            $values = $contact->getValues();
            if (isset($values['tags']))
            {
                $values['_embedded']['tags'] = $this->handleTags($values['tags']);
            }
            $parameters[] = $values;    
        }

        $response = $this->postv4Request('/api/v4/contacts', $parameters);

        return isset($response['_embedded']['contacts']) ? $response['_embedded']['contacts'] : [];
    }

    /**
     * Обновление контактов
     *
     * Метод позволяет обновлять данные по уже существующим контактам
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_set.php
     * @param int $id Уникальный идентификатор контакта
     * @param string $modified Дата последнего изменения данной сущности
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiUpdate($id, $modified = 'now')
    {
        $this->checkId($id);

        $parameters = [
            'contacts' => [
                'update' => [],
            ],
        ];

        $contact = $this->getValues();
        $contact['id'] = $id;
        $contact['last_modified'] = strtotime($modified);

        $parameters['contacts']['update'][] = $contact;

        $response = $this->postRequest('/private/api/v2/json/contacts/set', $parameters);

        return empty($response['contacts']['update']['errors']);
    }
    /**
     * Обновление контактов
     *
     * Метод позволяет обновлять данные по уже существующим контактам
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/contacts-api#contacts-edit
     * @param string $modified Дата последнего изменения данной сущности
     * @throws \AmoCRM\Exception
     */
    public function apiv4Update($contacts = [], $modified = 'now')
    {
        if (empty($contacts))
        {
            $contacts = [$this];
        }

        $parameters = [];

        foreach ($contacts AS $contact) 
        {
            $updated_values = $contact->getValues();

            $id = (int)$updated_values['id'];

            $this->checkId($id);

            if (isset($updated_values['custom_fields_values']))
            {
                $updated_values['custom_fields_values'] = $this->handleCustomFields($updated_values['custom_fields_values']);
            }
            if (isset($updated_values['tags']))
            {
                $updated_values['_embedded']['tags'] = $this->handleTags($updated_values['tags']);
            }

            $updated_values['updated_at'] = strtotime($modified);
            $parameters[] = $updated_values; 
        }

        $response = $this->patchRequest('/api/v4/contacts', $parameters, $modified);

        return isset($response['_embedded']['contacts']) ? $response['_embedded']['contacts'] : [];
    }

    /**
     * Связи между сделками и контактами
     *
     * Метод для получения списка связей между сделками и контактами
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_links.php
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiLinks($parameters, $modified = null)
    {
        $response = $this->getRequest('/private/api/v2/json/contacts/links', $parameters, $modified);

        return isset($response['links']) ? $response['links'] : [];
    }

    /**
     * Чат по id контакта
     *
     * Метод для получения списка чатов контакта
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/contacts-api#contacts-chat-list
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiv4ChatByContactId($contact_id)
    {
        $response = $this->getRequest('/api/v4/contacts/chats', ['contact_id' => (int)$contact_id]);

        return isset($response['_embedded']['chats']) ? $response['_embedded']['chats'] : [];
    }

    /**
     * Контакт по id чата
     *
     * Метод для получения списка контактов чата
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/contacts-api#contacts-chat-list
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiv4ContactByChatId($chat_id)
    {
        $response = $this->getRequest('/api/v4/contacts/chats', ['chat_id' => $chat_id]);

        return isset($response['_embedded']['chats']) ? $response['_embedded']['chats'] : [];
    }

    /**
     * Привязка чатов к контактам
     *
     * Метод позволяет привязать чат к контакту. Чат может быть привязан только к 1 контакту
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/contacts-api#contacts-chat-connect
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiv4LinkChat($contact_id, $chat_id)
    {
        $response = $this->postv4Request('/api/v4/contacts/chats', ['contact_id' => (int)$contact_id, 'chat_id' => $chat_id]);

        return isset($response['_embedded']['chats']) ? $response['_embedded']['chats'] : [];
    }
}
