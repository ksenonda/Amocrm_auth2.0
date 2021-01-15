<?php

namespace AmoCRM2;

use AmoCRM2\Models\ModelInterface;
use AmoCRM2\Request\CurlHandle;
use AmoCRM2\Request\ParamsBag;
use AmoCRM2\Helpers\Fields;
use AmoCRM2\Helpers\Format;

/**
 * Class Client
 *
 * Основной класс для получения доступа к моделям amoCRM API
 *
 * @package AmoCRM
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 * @property \AmoCRM2\Models\Account $account +
 * @property \AmoCRM2\Models\Catalog $catalog
 * @property \AmoCRM2\Models\CatalogElement $catalog_element
 * @property \AmoCRM2\Models\Company $company
 * @property \AmoCRM2\Models\Contact $contact
 * @property \AmoCRM2\Models\Customer $customer
 * @property \AmoCRM2\Models\CustomersPeriods $customers_periods
 * @property \AmoCRM2\Models\CustomField $custom_field
 * @property \AmoCRM2\Models\Lead $lead
 * @property \AmoCRM2\Models\Links $links
 * @property \AmoCRM2\Models\Note $note
 * @property \AmoCRM2\Models\Pipelines $pipelines
 * @property \AmoCRM2\Models\Task $task
 * @property \AmoCRM2\Models\Transaction $transaction
 * @property \AmoCRM2\Models\Unsorted $unsorted
 * @property \AmoCRM2\Models\Webhooks $webhooks
 * @property \AmoCRM2\Models\Widgets $widgets
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Client
{
    /**
     * @var Fields|null Экземпляр Fields для хранения номеров полей
     */
    public $fields = null;

    /**
     * @var ParamsBag|null Экземпляр ParamsBag для хранения аргументов
     */
    public $parameters = null;

    /**
     * @var CurlHandle Экземпляр CurlHandle для повторного использования
     */
    private $curlHandle;

    /**
     * Client constructor
     *
     * @param string $domain Поддомен или домен amoCRM
     * @param string $login Логин amoCRM
     * @param string $apikey Ключ пользователя amoCRM
     * @param string|null $proxy Прокси сервер для отправки запроса
     */
    public function __construct($domain, $token, $proxy = null)
    {

        $this->parameters = new ParamsBag();
        $this->parameters->addAuth('domain', $domain);
        $this->parameters->addAuth('token', $token);

        if ($proxy !== null) {
            $this->parameters->addProxy($proxy);
        }

        $this->fields = new Fields();

        $this->curlHandle = new CurlHandle();
    }

    /**
     * Возвращает экземпляр модели для работы с amoCRM API
     *
     * @param string $name Название модели
     * @return ModelInterface
     * @throws ModelException
     */
    public function __get($name)
    {
        $classname = '\\AmoCRM2\\Models\\' . Format::camelCase($name);

        if (!class_exists($classname)) {
            throw new ModelException('Model not exists: ' . $name);
        }

        // Чистим GET и POST от предыдущих вызовов
        $this->parameters->clearGet()->clearPost();

        return new $classname($this->parameters, $this->curlHandle);
    }
}
