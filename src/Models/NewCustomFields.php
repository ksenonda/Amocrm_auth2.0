<?php

namespace AmoCRM2\Models;

/**
 * Class NewCustomFields
 *
 * Класс модель для работы с Дополнительными полями
 *
 * @package AmoCRM2\Models
 * @author mihasichechek <mihasichechek@gmail.com>
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class NewCustomFields extends AbstractModel
{
	/**
     * Список кастомных полей
     *
     * Метод для получения списка кастомных полей с возможностью постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 50 шт
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/custom-fields
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
	/**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'id',
        'name',
        'type',
        'code',
        'sort',
        'entity_type',
        'is_predefined',
        'is_deletable',
        'is_visible',
        'is_required',
        'settings',
        'remind',
        'enums',
        'nested',
        'is_api_only',
        'group_id',
        'required_statuses',
        'tracking_callback',
    ];
    public function apiList($name, $parameters = null, $modified = null)
    {
        $response = $this->getRequest('/api/v4/'.$name.'/custom_fields', $parameters, $modified);

        return isset($response['_embedded']['custom_fields']) ? $response['_embedded']['custom_fields'] : [];
    }
    public function apiOne($name, $id)
    {
        $response = $this->getRequest('/api/v4/'.$name.'/custom_fields/'.$id);

        return isset($response) ? $response : [];
    }
    public function apiAdd($name, $id, $values_array)
    {
        $data = [];
        foreach ($values_array as $key => $value) 
        {
            if (is_numeric($key))
            {
              $data[] = ['field_code' => $key,
                        'values' => 
                        [
                          ['value' => $value]
                        ]
                      ];
            }
            else
            {
              $data[] = ['field_id' => $key,
                        'values' => 
                        [
                          ['value' => $value]
                        ]
                      ];
            }
        }
        $parameters = [
                        ['id' => $id,
                        'custom_fields_values' => $data
                        ]
                    ];
        $response = $this->patchRequest('/api/v4/'.$name, $parameters);

        return isset($response) ? $response : [];
    }
}