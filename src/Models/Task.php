<?php

namespace AmoCRM2\Models;

use AmoCRM2\Models\Traits\SetDateCreate;
use AmoCRM2\Models\Traits\SetLastModified;

/**
 * Class Task
 *
 * Класс модель для работы с Задачами
 *
 * @package AmoCRM2\Models
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Task extends AbstractModel
{
    use SetDateCreate, SetLastModified;

    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [
        'id',
        'responsible_user_id',
        'entity_id',
        'element_id',
        'entity_type',
        'element_type',
        'is_completed',
        'task_type_id',
        'task_type',
        'text',
        'duration',
        'complete_till',
        'result',
        'created_by',
        'created_user_id',
        'created_at',
        'date_create',
        'updated_by',
        'updated_at',
        'last_modified',
        'status',
        'request_id', 
        'order'    
    ];

    /**
     * @const int Типа задачи Контакт
     */
    const TYPE_CONTACT = 1;

    /**
     * @const int Типа задачи Сделка
     */
    const TYPE_LEAD = 2;

    /** @const int Типа задачи Компания */
    const TYPE_COMPANY = 3;
    

    /** @const int Типа задачи Покупатель */
    const TYPE_CUSTOMER = 12;

    /**
     * Сеттер для дата до которой необходимо завершить задачу
     *
     * Если указано время 23:59, то в интерфейсах системы
     * вместо времени будет отображаться "Весь день"
     *
     * @param string $date Дата в произвольном формате
     * @return $this
     */
    public function setCompleteTill($date)
    {
        if (!is_int($date))
        {
           $this->values['complete_till'] = strtotime($date); 
        }
        return $this;
    }

    /**
     * Список задач
     *
     * Метод для получения списка задач с возможностью фильтрации и постраничной выборки.
     * Ограничение по возвращаемым на одной странице (offset) данным - 500 задач
     *
     * @link https://developers.amocrm.ru/rest_api/tasks_list.php
     * @param array $parameters Массив параметров к amoCRM API
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     * @return array Ответ amoCRM API
     */
    public function apiList($parameters, $modified = null)
    {
        $response = $this->getRequest('/private/api/v2/json/tasks/list', $parameters, $modified);

        return isset($response['tasks']) ? $response['tasks'] : [];
    }
    /**
     * Список задач, метод в4
     *
     * Метод позволяет получить список задач в аккаунте
     * Ограничение по возвращаемым на одной странице данным - 250 задач
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/tasks-api#tasks-list
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiv4List($parameters = [])
    {
        $response = $this->getRequest('/api/v4/tasks', $parameters);

        return isset($response['_embedded']['tasks']) ? $response['_embedded']['tasks'] : [];
    }

    /**
     * Получение задачи по ID, метод в4
     *
     * Метод позволяет получить данные конкретной задачи по ID
     * 
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/tasks-api#task-detail
     * @param array $parameters Массив параметров к amoCRM API
     * @return array Ответ amoCRM API
     */
    public function apiv4One($id)
    {
        $response = $this->getRequest('/api/v4/tasks/'.$id);

        return isset($response) ? $response : [];
    }

    /**
     * Добавление задачи
     *
     * Метод позволяет добавлять задачи по одной или пакетно
     *
     * @link https://developers.amocrm.ru/rest_api/tasks_set.php
     * @param array $tasks Массив задач для пакетного добавления
     * @return int|array Уникальный идентификатор задачи или массив при пакетном добавлении
     */
    public function apiAdd($tasks = [])
    {
        if (empty($tasks)) {
            $tasks = [$this];
        }

        $parameters = [
            'tasks' => [
                'add' => [],
            ],
        ];

        foreach ($tasks AS $task) {
            $parameters['tasks']['add'][] = $task->getValues();
        }

        $response = $this->postRequest('/private/api/v2/json/tasks/set', $parameters);

        if (isset($response['tasks']['add'])) {
            $result = array_map(function($item) {
                return $item['id'];
            }, $response['tasks']['add']);
        } else {
            return [];
        }

        return count($tasks) == 1 ? array_shift($result) : $result;
    }

    /**
     * Добавление задач, метод в4
     *
     * Метод позволяет добавлять задачи в аккаунт пакетно
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/tasks-api#tasks-add
     * @param array $tasks Массив задач для пакетного добавления
     * @return array Ответ amoCRM API
     */
    public function apiv4Add($tasks = [])
    {
        if (empty($tasks)) 
        {
            $tasks = [$this];
        }

        $parameters = [];

        foreach ($tasks as $task) 
        {
            $parameters[] = $task->getValues();
        }

        $response = $this->postv4Request('/api/v4/tasks', $parameters);

        return isset($response['_embedded']['tasks']) ? $response['_embedded']['tasks'] : [];
    }

    /**
     * Обновление задачи
     *
     * Метод позволяет обновлять данные по уже существующим задачам
     *
     * @link https://developers.amocrm.ru/rest_api/tasks_set.php
     * @param int $id Уникальный идентификатор задачи
     * @param string $text Текст задачи
     * @param string $modified Дата последнего изменения данной сущности
     * @return bool Флаг успешности выполнения запроса
     * @throws \AmoCRM\Exception
     */
    public function apiUpdate($id, $text, $modified = 'now')
    {
        $this->checkId($id);

        $parameters = [
            'tasks' => [
                'update' => [],
            ],
        ];

        $task = $this->getValues();
        $task['id'] = $id;
        $task['text'] = $text;
        $task['last_modified'] = strtotime($modified);

        $parameters['tasks']['update'][] = $task;

        $response = $this->postRequest('/private/api/v2/json/tasks/set', $parameters);

        return empty($response['tasks']['update']['errors']);
    }

    /**
     * Редактирование задач, метод в4
     *
     * Метод позволяет редактировать задачи пакетно
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/tasks-api#tasks-edit
     * @param array $tasks Массив задач для пакетного добавления
     * @return array Ответ amoCRM API
     */

    public function apiv4Update($tasks = [])
    {
        if (empty($tasks)) 
        {
            $tasks = [$this];
        }

        $parameters = [];

        foreach ($tasks as $task) 
        {
            $parameters[] = $task->getValues();
        }

        $response = $this->patchRequest('/api/v4/tasks', $parameters);

        return isset($response['_embedded']['tasks']) ? $response['_embedded']['tasks'] : [];
    }

    /**
     * Выполнение задачи, метод в4
     *
     * Метод для закрытия задач
     *
     * @link https://www.amocrm.ru/developers/content/crm_platform/tasks-api#tasks-complete
     * @param array $tasks Массив задач для пакетного добавления
     * @return array Ответ amoCRM API
     */

    public function apiv4Complete($task_id, $text = null)
    {
        $parameters = ['is_completed' => true, 'result' => ['text' => $text]];

        $response = $this->patchRequest('/api/v4/tasks/'.$task_id, $parameters);

        return isset($response) ? $response : [];
    }
}
