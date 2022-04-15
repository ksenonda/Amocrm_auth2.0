<?php

namespace AmoCRM2\Models;

use AmoCRM2\Models\Traits\SetTags;
use AmoCRM2\Models\Traits\SetNextDate;

/**
 * Class Customer
 *
 * Класс модель для работы с Покупателями
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Customer extends AbstractModel
{
    use SetTags, SetNextDate;

    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'id',
        'name',
        'next_price',
        'next_date',
        'responsible_user_id',
        'main_user_id',
        'periodicity',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'closest_task_at',
        'is_deleted',
        'custom_fields_values',
        'tags',
        'request_id',
        '_embedded'
    ];

    /**
     * Список покупателей
     *
     * Метод для получения покупателей аккаунта
     *
     * @link https://developers.amocrm.ru/rest_api/customers/list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters)
    {
        $response = $this->getRequest('/private/api/v2/json/customers/list', $parameters);

        return isset($response['customers']) ? $response['customers'] : [];
    }

    /**
     * Список покупателей, метод в4
     *
     * Метод для получения покупателей аккаунта
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/customers-api#customers-list
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiv4List($parameters)
    {
        $response = $this->getRequest('/api/v4/customers', $parameters);

        return isset($response['_embedded']['customers']) ? $response['_embedded']['customers'] : [];
    }

    /**
     * Получение покупателя по id, метод в4
     *
     * Метод позволяет получить данные конкретного покупателя по ID
     * 
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/customers-api#customer-detail
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiv4One($id, $parameters = [])
    {
        $response = $this->getRequest('/api/v4/customers/'.$id, $parameters);

        return isset($response) ? $response : [];;
    }

    /**
     * Добавление покупателей
     *
     * Метод позволяет добавлять покупателей по одному или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/customers/set.php
     * @param array $customers Массив покупателей для пакетного добавления
     * @return int|array Уникальный идентификатор покупателя или массив при пакетном добавлении
     */
    public function apiAdd($customers = [])
    {
        if (empty($customers)) {
            $customers = [$this];
        }

        $parameters = [
            'customers' => [
                'add' => [],
            ],
        ];

        foreach ($customers AS $customer) {
            $parameters['customers']['add'][] = $customer->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/customers/set', $parameters);

        if (isset($response['customers']['add'])) {
            $result = array_map(function ($item) {
                return $item['id'];
            }, $response['customers']['add']['customers']);
        } else {
            return [];
        }

        return count($customers) == 1 ? array_shift($result) : $result;
    }

    /**
     * Добавление покупателей, метод в4
     *
     * Метод позволяет добавлять покупателей по одному или пакетно
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/customers-api#customers-add
     * @param array $customers Массив покупателей для пакетного добавления
     * @return int|array Уникальный идентификатор покупателя или массив при пакетном добавлении
     */
    public function apiv4Add($customers = [])
    {
        if (empty($customers))
        {
            $customers = [$this];
        }

        $parameters = [];

        foreach ($customers as $customer) 
        { 
            $values = $customer->getValues();
            if (isset($values['custom_fields_values']))
            {
                $values['custom_fields_values'] = $this->handleCustomFields($values['custom_fields_values']);
            }
            if (isset($values['tags']))
            {
                $values['_embedded']['tags'] = $this->handleTags($values['tags']);
            }
            if (isset($values['next_date']))
            {
                $values['next_date'] = (int)$values['next_date'];
            }
            else
            {
                $values['next_date'] = NULL;
            }
            $parameters[] = $values;
        }

        $response = $this->postv4Request('/api/v4/customers', $parameters, $modified);

        return isset($response['_embedded']['customers']) ? $response['_embedded']['customers'] : [];
    }

    /**
     * Обновление покупателей
     *
     * Метод позволяет обновлять данные по уже существующим покупателям
     *
     * @link https://developers.amocrm.ru/rest_api/customers/set.php
     * @param int $id Уникальный идентификатор покупателя
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiUpdate($id)
    {
        $this->checkId($id);

        $parameters = [
            'customers' => [
                'update' => [],
            ],
        ];

        $customer = $this->getValues();
        $customer['id'] = $id;

        $parameters['customers']['update'][] = $customer;

        $response = $this->postRequest('/private/api/v2/json/customers/set', $parameters);

        return isset($response['customers']) ? true : false;
    }

    /**
     * Обновление покупателей, метод в4
     *
     * Метод позволяет обновлять данные по уже существующим покупателям
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/customers-api#customers-edit
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiv4Update($customers = [])
    {
        if (empty($customers))
        {
            $customers = [$this];
        }

        $parameters = [];

        foreach ($customers as $customer) 
        {
            $updated_values = $customer->getValues();

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
            if (isset($updated_values['next_date']))
            {
                $updated_values['next_date'] = (int)$updated_values['next_date'];
            }

            $parameters[] = $updated_values; 
        }

        $response = $this->patchRequest('/api/v4/customers', $parameters);

        return isset($response['_embedded']['customers']) ? $response['_embedded']['customers'] : [];
    }
}
