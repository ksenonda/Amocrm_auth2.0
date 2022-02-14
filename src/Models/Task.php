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
        $this->values['complete_till'] = strtotime($date);

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

    public function apiv4List($parameters)
    {
        /*$new_params = [];

        if (isset($parameters['page']))
        {
            $new_params['page'] = $parameters['page'];
            unset($parameters['page']);
        }
        if (isset($parameters['limit']))
        {
            $new_params['limit'] = $parameters['limit'];
            unset($parameters['limit']);
        }
        if (isset($parameters['order']))
        {
            $new_params['order'] = $parameters['order'];
            unset($parameters['order']);
        }
        if (!empty($parameters))
        {
            foreach ($parameters as $key => $parameter) 
            {
                $new_params['filter'][$key][] = $parameter;
            }
        }*/
        $response = $this->getRequest('/api/v4/tasks', $parameters);

        return isset($response['_embedded']['tasks']) ? $response['_embedded']['tasks'] : [];
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
    public function apiv4Update(array $tasks, $modified = 'now')
    {
        $parameters = [];

        foreach ($tasks as $task) 
        {
            $updated_values = $task->getValues();

            $id = (int)$updated_values['id'];

            $this->checkId($id);

            $updated_values['updated_at'] = strtotime($modified);
            $parameters[] = $updated_values; 
        }

        $response = $this->patchRequest('/api/v4/tasks', $parameters, $modified);

        return isset($response['_embedded']['tasks']) ? $response['_embedded']['tasks'] : [];
    }
}
