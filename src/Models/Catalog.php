<?php

namespace AmoCRM2\Models;

/**
 * Class Catalog
 *
 * Класс модель для работы с Каталогами
 *
 * @package AmoCRM2\Models
 * @author mb@baso-it.ru
 * @author dotzero <mail@dotzero.ru>
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Catalog extends AbstractModel
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'name',
        'id',
        'request_id',
        'type',
        'sort',
        'can_add_elements',
        'can_link_multiple',
        'custom_fields_values'
    ];

    /**
     * Список каталогов
     *
     * Метод для получения списка каталогов аккаунта.
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/list.php
     * @param null|int $id Выбрать элемент с заданным ID
     * @return array Ответ amoCRM API
     */
    public function apiList($id = null)
    {
        $parameters = [];

        if ($id !== null) {
            $parameters['id'] = $id;
        }

        $response = $this->getRequest('/private/api/v2/json/catalogs/list', $parameters);

        return isset($response['catalogs']) ? $response['catalogs'] : [];
    }

    /**
     * Список каталогов, метод в4
     *
     * Метод для получения списка каталогов аккаунта.
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/catalogs-api#lists-list
     * @param null|int $parameters - page и limit
     * @return array Ответ amoCRM API
     */
    public function apiv4List($parameters = [])
    {
        $response = $this->getRequest('/api/v4/catalogs', $parameters);

        return isset($response['_embedded']['catalogs']) ? $response['_embedded']['catalogs'] : [];
    }

    /**
     * Получение списка по id, метод в4
     *
     * Метод позволяет получить данные конкретного списка по ID
     * 
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/catalogs-api#list-detail
     * @param array $id ID запрашиваемого списка
     * @return array Ответ amoCRM API
     */
    public function apiv4One($id)
    {
        $response = $this->getRequest('/api/v4/catalogs/'.$id);

        return isset($response) ? $response : [];;
    }


    /**
     * Добавление каталогов
     *
     * Метод позволяет добавлять каталоги по одному или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/set.php
     * @param array $catalogs Массив каталогов для пакетного добавления
     * @return int|array Уникальный идентификатор каталога или массив при пакетном добавлении
     */
    public function apiAdd($catalogs = [])
    {
        if (empty($catalogs)) {
            $catalogs = [$this];
        }

        $parameters = [
            'catalogs' => [
                'add' => [],
            ],
        ];

        foreach ($catalogs AS $catalog) {
            $parameters['catalogs']['add'][] = $catalog->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/catalogs/set', $parameters);

        if (isset($response['catalogs']['add']['catalogs'])) {
            $result = array_map(function ($item) {
                return $item['id'];
            }, $response['catalogs']['add']['catalogs']);
        } else {
            return [];
        }

        return count($catalogs) == 1 ? array_shift($result) : $result;
    }

    /**
     * Добавление списка, метод в4
     *
     * Метод позволяет добавлять списки пакетно
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/catalogs-api#lists-add
     * @param array $catalogs Массив списков для пакетного добавления
     * @return array Массив данных по списку(спискам)
     */
    public function apiv4Add($catalogs = [])
    {
        if (empty($catalogs)) 
        {
            $catalogs = [$this];
        }

        $parameters = [];

        foreach ($catalogs as $catalog) 
        {
            $parameters[] = $catalog->getValues();    
        }

        $response = $this->postv4Request('/api/v4/catalogs', $parameters);

        return isset($response['_embedded']['catalogs']) ? $response['_embedded']['catalogs'] : [];
    }

    /**
     * Обновление каталогов
     *
     * Метод позволяет обновлять данные по уже существующим каталогам
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/set.php
     * @param int $id Уникальный идентификатор каталога
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiUpdate($id)
    {
        $this->checkId($id);

        $parameters = [
            'catalogs' => [
                'update' => [],
            ],
        ];

        $catalog = $this->getValues();
        $catalog['id'] = $id;

        $parameters['catalogs']['update'][] = $catalog;

        $response = $this->postRequest('/private/api/v2/json/catalogs/set', $parameters);

        if (!isset($response['catalogs']['update']['errors'])) {
            return false;
        }

        return empty($response['catalogs']['update']['errors']);
    }

    /**
     * Обновление списка, метод в4
     *
     * Метод позволяет обновлять данные по уже существующим спискам
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/catalogs-api#lists-edit
     * @param string $modified Дата последнего изменения данной сущности
     * @throws \AmoCRM\Exception
     */
    public function apiv4Update($catalogs = [])
    {
        if (empty($catalogs)) 
        {
            $catalogs = [$this];
        }

        $parameters = [];

        foreach ($catalogs as $catalog) 
        {
            $parameters[] = $catalog->getValues();    
        }

        $response = $this->patchRequest('/api/v4/catalogs', $parameters, $modified);

        return isset($response['_embedded']['catalogs']) ? $response['_embedded']['catalogs'] : [];
    }

    /**
     * Удаление каталогов
     *
     * Метод позволяет удалять данные по уже существующим каталогам
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/set.php
     * @param int $id Уникальный идентификатор каталога
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiDelete($id)
    {
        $this->checkId($id);

        $parameters = [
            'catalogs' => [
                'delete' => [$id],
            ],
        ];

        $response = $this->postRequest('/private/api/v2/json/catalogs/set', $parameters);

        if (!isset($response['catalogs']['delete']['errors'])) {
            return false;
        }

        return empty($response['catalogs']['delete']['errors']);
    }
}
