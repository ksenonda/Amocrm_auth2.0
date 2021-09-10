<?php

namespace AmoCRM2\Models;

/**
 * Class Lead
 *
 * Класс модель для работы с сейлсботом
 *
 * @package AmoCRM2\Models
 * @author ksenonda <ksenonda@rambler.ru>
 * @link https://github.com/ksenonda
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Salesbot extends AbstractModel
{
    /**
     * @var array Список доступный полей для модели
     */
    protected $fields = [
        'bot_id',
        'entity_id',
        'entity_type'
    ];

    /**
     * Обновление сделки
     *
     * Метод позволяет обновлять данные по уже существующим сделкам
     *
     * @link https://www.amocrm.ru/developers/content/api/salesbot-api
     * @param int $bot_id Уникальный идентификатор бота
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM2\Exception
     */
    public function apiRun($bot_id)
    {
        $this->checkId($bot_id);

        $parameters = [];

        $salesbot = $this->getValues();
        $salesbot['bot_id'] = $bot_id;

        $parameters[] = $salesbot;

        $response = $this->postRequest('/api/v2/salesbot/run', $parameters);

        return empty($response['leads']['update']['errors']);
    }
}
