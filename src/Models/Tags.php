<?php

namespace AmoCRM2\Models;

/**
 * Class Tags
 *
 * Класс модель для работы с тэгами
 *
 * @package AmoCRM2\Models
 *
 */
class Tags extends AbstractModel
{
	/**
     * Список тэгов
     *
     * Метод для получения списка тэгов с возможностью постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 250 шт
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/tags-api#tags-list
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