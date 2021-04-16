<?php

namespace AmoCRM2\Models;

use AmoCRM2\Models\Traits\SetNote;
use AmoCRM2\Models\Traits\SetTags;
use AmoCRM2\Models\Traits\SetDateCreate;
use AmoCRM2\Models\Traits\SetLastModified;

/**
 * Class Event
 *
 * Класс модель для работы со списком событий
 *
 * @package AmoCRM\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Event extends AbstractModel
{
    use SetNote, SetTags, SetDateCreate, SetLastModified;

    /**
     * @var array Список доступных полей для модели
     */
    protected $fields = [
        'with',
        'page',
        'limit',
        'filter',
        'filter[id]',
        'filter[created_at]',
        'filter[created_by]',
        'filter[entity]',
        'filter[entity_id]',
        'filter[type]',
        'filter[value_before]',
        'filter[value_after]'
    ];

    /**
     * Список событий
     *
     * Метод для получения списка событийс возможностью фильтрации и постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 100 событий
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/events-and-notes
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters, $modified = null)
    {
        $response = $this->getRequest('/api/v4/events', $parameters, $modified);

        return isset($response['_embedded']['events']) ? $response['_embedded']['events'] : [];
    }
    public function apiTypes($parameters)
    {
        $response = $this->getRequest('/api/v4/events/types', $parameters);

        return isset($response['_embedded']['events_types']) ? $response['_embedded']['events_types'] : [];
    }   
}
