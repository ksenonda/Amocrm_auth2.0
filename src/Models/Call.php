<?php

namespace AmoCRM2\Models;

/**
 * Class Call
 *
 * Класс модель для работы со звонками
 *
 * @package AmoCRM2\Models
 * @author ksenonda <ksenonda@rambler.ru>
 * @link https://github.com/ksenonda/Amocrm_auth2.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Call extends AbstractModel
{
	/**
     * @var array Список доступный полей для модели
     */
    protected $fields = [
        'direction',
        'uniq',
        'duration',
        'source',
        'link',
        'phone',
        'call_result',
        'call_status',
        'responsible_user_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'request_id',    
    ];

    /**
     * Добавление звонка, метод в4
     *
     * Данный метод позволяет пакетно добавлять звонки в карточки сущностей
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/calls-api
     * @param array $calls Массив звонков для пакетного добавления
     * @return array Массив данных по звонку(звонкам)
     */
    public function apiv4Add($calls = [])
    {
        if (empty($calls)) 
        {
            $calls = [$this];
        }

        $parameters = [];

        foreach ($calls as $call) 
        {
            $parameters[] = $call->getValues();    
        }

        $response = $this->postv4Request('/api/v4/calls', $parameters);

        return isset($response['_embedded']['calls']) ? $response['_embedded']['calls'] : [];
    }
    
}