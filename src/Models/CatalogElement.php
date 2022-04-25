<?php

namespace AmoCRM2\Models;

/**
 * Class CatalogElement
 *
 * Класс модель для работы с Элементами каталога
 *
 * @package AmoCRM2\Models
 * @author mb@baso-it.ru
 * @author dotzero <mail@dotzero.ru>
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CatalogElement extends AbstractModel
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'catalog_id',
        'name',
        'id',
        'request_id',
        'custom_fields_values'
    ];

    /**
     * Список элементов каталога
     *
     * Метод для получения элементов каталога аккаунта.
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     * @throws \AmoCRM\Exception
     */
    public function apiList($parameters = [])
    {
        $response = $this->getRequest('/private/api/v2/json/catalog_elements/list', $parameters);

        return isset($response['catalog_elements']) ? $response['catalog_elements'] : [];
    }

    /**
     * Доступные элементы списка, метод в4
     *
     * Метод позволяет получить доступные элементы списка
     * @link https://www.amocrm.ru/developers/content/crm_platform/catalogs-api#list-elements-list
     * @return array Ответ amoCRM API
     */
    public function apiv4List($catalog_id, $parameters = [])
    {
        $response = $this->getRequest('/api/v4/catalogs/'.$catalog_id.'/elements', $parameters);

        return isset($response['_embedded']['elements']) ? $response['_embedded']['elements'] : [];
    }

    /**
     * Получение элемента списка по ID, метод в4
     *
     * Метод позволяет получить элемент списка по его ID
     * @link https://www.amocrm.ru/developers/content/crm_platform/catalogs-api#list-elements-detail
     * @return array Ответ amoCRM API
     */
    public function apiv4One($catalog_id, $element_id, $parameters = [])
    {
        $response = $this->getRequest('/api/v4/catalogs/'.$catalog_id.'/elements/'.$element_id, $parameters);

        return isset($response) ? $response : [];
    }

    /**
     * Добавление элементов каталога
     *
     * Метод позволяет добавлять элементы каталога по одному или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/set.php
     * @param array $elements Массив каталогов для пакетного добавления
     * @return int|array Уникальный идентификатор элемента каталога или массив при пакетном добавлении
     */
    public function apiAdd($elements = [])
    {
        if (empty($elements)) {
            $elements = [$this];
        }

        $parameters = [
            'catalog_elements' => [
                'add' => [],
            ],
        ];

        foreach ($elements AS $element) {
            $parameters['catalog_elements']['add'][] = $element->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/catalog_elements/set', $parameters);

        if (isset($response['catalog_elements']['add']['catalog_elements'])) {
            $result = array_map(function ($item) {
                return $item['id'];
            }, $response['catalog_elements']['add']['catalog_elements']);
        } else {
            return [];
        }

        return count($elements) == 1 ? array_shift($result) : $result;
    }

    /**
     * Добавление элементов списков, метод в4
     *
     * Метод позволяет добавлять элементы списков в аккаунт
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/catalogs-api#list-elements-add
     * @param array $elements Массив элементов для пакетного добавления
     * @return array Массив данных по элементу(элементам)
     */
    public function apiv4Add($catalog_id, $elements = [])
    {
        if (empty($elements))
        {
            $elements = [$this];
        }

        $parameters = [];

        foreach ($elements AS $element) 
        {
            $values = $element->getValues();

            if (isset($values['custom_fields_values']))
            {
                $values['custom_fields_values'] = $this->handleCustomFields($values['custom_fields_values']);
            }
            $parameters[] = $values;
        }

        $response = $this->postv4Request('/api/v4/catalogs/'.$catalog_id.'/elements', $parameters);

        return isset($response['_embedded']['elements']) ? $response['_embedded']['elements'] : [];
    }

    /**
     * Обновление элементов каталога
     *
     * Метод позволяет обновлять данные по уже существующим элементам каталога
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/set.php
     * @param int $id Уникальный идентификатор элемента каталога
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiUpdate($id)
    {
        $this->checkId($id);

        $parameters = [
            'catalog_elements' => [
                'update' => [],
            ],
        ];

        $catalog = $this->getValues();
        $catalog['id'] = $id;

        $parameters['catalog_elements']['update'][] = $catalog;

        $response = $this->postRequest('/private/api/v2/json/catalog_elements/set', $parameters);

        if (!isset($response['catalog_elements']['update']['errors'])) {
            return false;
        }

        return empty($response['catalog_elements']['update']['errors']);
    }

    /**
     * Редактирование элементов списков, метод в4
     *
     * Метод позволяет редактировать элементы списков
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/catalogs-api#list-elements-edit
     * @param array $elements Массив элементов для пакетного добавления
     * @return array Массив данных по элементу(элементам)
     */
    public function apiv4Update($catalog_id, $elements = [])
    {
        if (empty($elements))
        {
            $elements = [$this];
        }
        
        $parameters = [];

        foreach ($elements as $element) 
        {
            $updated_values = $element->getValues();

            if (isset($updated_values['custom_fields_values']))
            {
                $updated_values['custom_fields_values'] = $this->handleCustomFields($updated_values['custom_fields_values']);
            }

            $parameters[] = $updated_values; 
        }

        $response = $this->patchRequest('/api/v4/catalogs/'.$catalog_id.'/elements', $parameters);

        return isset($response['_embedded']['elements']) ? $response['_embedded']['elements'] : [];
    }

    /**
     * Удаление элементов каталога
     *
     * Метод позволяет удалять данные по уже существующим элементам каталога
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/set.php
     * @param int $id Уникальный идентификатор элемента каталога
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiDelete($id)
    {
        $this->checkId($id);

        $parameters = [
            'catalog_elements' => [
                'delete' => [$id],
            ],
        ];

        $response = $this->postRequest('/private/api/v2/json/catalog_elements/set', $parameters);

        if (!isset($response['catalog_elements']['delete']['errors'])) {
            return false;
        }

        return empty($response['catalog_elements']['delete']['errors']);
    }

    /**
     * Удаление элементов каталога
     *
     * Метод позволяет удалять данные по уже существующим элементам каталога
     *
     * @link https://developers.amocrm.com/rest_api/catalog_elements/set.php
     * @param array $ids Уникальные идентификаторы элементов каталога
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiDeleteBatch(array $ids)
    {
        array_walk($ids, function($id){
            $this->checkId($id);
        });

        $parameters = [
            'catalog_elements' => [
                'delete' => $ids,
            ],
        ];

        $response = $this->postRequest('/private/api/v2/json/catalog_elements/set', $parameters);

        if (!isset($response['catalog_elements']['delete']['errors'])) {
            return false;
        }

        return empty($response['catalog_elements']['delete']['errors']);
    }
}
