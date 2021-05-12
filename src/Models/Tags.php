<?php

namespace AmoCRM2\Models;

/**
 * Class Tags
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
class Tags extends AbstractModel
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
    protected $fields = 
    [
        'id',
        'name'
    ];
    public function apiList($entity, $parameters = null, $modified = null)
    {
        $response = $this->getRequest('/api/v4/'.$entity.'/tags', $parameters, $modified);

        return isset($response['_embedded']['tags']) ? $response['_embedded']['tags'] : [];
    }
}