<?php

namespace AmoCRM2\Models;

/**
 * Class ShortLinks
 *
 * Класс модель для работы с Дополнительными полями
 *
 * @package AmoCRM2\Models
 * @author ksenonda <ksenonda@rambler.ru>
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class ShortLinks extends AbstractModel
{
	/**
     * 
     * Создание короткой ссылки
     * Метод для создания короткой ссылки в амо
     * Не более 250 ссылок за раз
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/short_links
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
	/**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'url',
        'metadata'
    ];

    public function apiv4Add($url, $entity_id, $entity_type = 'contacts')
    {
        $parameters = [
                        ['url' => $url,
                        'metadata' => ['entity_type' => $entity_type, 'entity_id' => $entity_id]
                        ]
                    ];
        $response = $this->postv4Request('/api/v4/short_links', $parameters);

        return isset($response['_embedded']['short_links']) ? $response['_embedded']['short_links'] : [];
    }
}