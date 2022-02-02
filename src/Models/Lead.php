<?php

namespace AmoCRM2\Models;

use AmoCRM2\Models\Traits\SetNote;
use AmoCRM2\Models\Traits\SetTags;
use AmoCRM2\Models\Traits\SetDateCreate;
use AmoCRM2\Models\Traits\SetLastModified;

/**
 * Class Lead
 *
 * Класс модель для работы со Сделками
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Lead extends AbstractModel
{
    use SetNote, SetTags, SetDateCreate, SetLastModified;

    /**
     * @var array Список доступный полей для модели
     */
    protected $fields = [
        'id',
        'name',
        'status_id',
        'pipeline_id',
        'created_user_id',
        'created_by',
        'modified_user_id',
        'updated_by',
        'closed_at',
        'date_create',
        'created_at',
        'updated_at',
        'last_modified',
        'loss_reason_id',
        'responsible_user_id',
        'custom_fields_values',
        'price',
        'request_id',
        'linked_company_id',
        'tags',
        'visitor_uid',
        'notes',       
    ];

    /**
     * Список сделок
     *
     * Метод для получения списка сделок с возможностью фильтрации и постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 500 сделок
     *
     * @link https://developers.amocrm.ru/rest_api/leads_list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters, $modified = null)
    {
        $response = $this->getRequest('/private/api/v2/json/leads/list', $parameters, $modified);

        return isset($response['leads']) ? $response['leads'] : [];
    }
    public function apiv4List($parameters, $modified = null)
    {
        $response = $this->getRequest('/api/v4/leads', $parameters, $modified);

        return isset($response['_embedded']['leads']) ? $response['_embedded']['leads'] : [];
    }

    /**
     * Добавление сделки
     *
     * Метод позволяет добавлять сделки по одной или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/leads_set.php
     * @param array $leads Массив сделок для пакетного добавления
     * @return int|array Уникальный идентификатор сделки или массив при пакетном добавлении
     */
    public function apiAdd($leads = [])
    {
        if (empty($leads)) {
            $leads = [$this];
        }

        $parameters = [
            'leads' => [
                'add' => [],
            ],
        ];

        foreach ($leads AS $lead) {
            $parameters['leads']['add'][] = $lead->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/leads/set', $parameters);

        if (isset($response['leads']['add'])) {
            $result = array_map(function($item) {
                return $item['id'];
            }, $response['leads']['add']);
        } else {
            return [];
        }

        return count($leads) == 1 ? array_shift($result) : $result;
    }

    /**
     * Обновление сделки
     *
     * Метод позволяет обновлять данные по уже существующим сделкам
     *
     * @link https://developers.amocrm.ru/rest_api/leads_set.php
     * @param int $id Уникальный идентификатор сделки
     * @param string $modified Дата последнего изменения данной сущности
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiUpdate($id, $modified = 'now')
    {
        $this->checkId($id);

        $parameters = [
            'leads' => [
                'update' => [],
            ],
        ];

        $lead = $this->getValues();
        $lead['id'] = $id;
        $lead['last_modified'] = strtotime($modified);

        $parameters['leads']['update'][] = $lead;

        $response = $this->postRequest('/private/api/v2/json/leads/set', $parameters);

        return empty($response['leads']['update']['errors']);
    }

    public function apiv4Update(array $leads, $modified = 'now')
    {

        $parameters = [];

        foreach ($leads AS $lead) 
        {
            $updated_values = $lead->getValues();

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

        $response = $this->patchRequest('/api/v4/leads', $parameters, $modified);

        return isset($response['_embedded']['leads']) ? $response['_embedded']['leads'] : [];
    }

    /**
     * Список причин отказов
     *
     * Метод для получения списка причин отказов
     *
     * @return array Ответ amoCRM API
     */
    public function lossReasons()
    {
        $response = $this->getRequest('/api/v4/leads/loss_reasons');

        return isset($response['_embedded']['loss_reasons']) ? $response['_embedded']['loss_reasons'] : [];
    }
}
