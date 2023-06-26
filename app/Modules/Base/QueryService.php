<?php

namespace App\Modules\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QueryService
{
    /**
     * @var \Illuminate\Database\Eloquent\Model $model
     */
    public $model;

    protected $key_words = [
        'deleted',
        'sort',
        'paginate',
        'skip',
        'take',
        'page',
        'relations',
        'exists'
    ];

    protected $filters = [];

    protected $sorts = [];

    protected $paginate = false;

    protected $skip = 0;

    protected $page = 0;

    protected $take = 25;

    protected $deleted = false;

    protected $relations = [];

    protected $exists = [];

    public function __construct(Model $model, array $query_params = [])
    {
        $this->model = $model;
        $this->handleQueryParams($query_params);
    }

    /**
     * Direciona os parâmetros da url para tratamento de acordo com sua chave
     *
     * @param array $query_params
     *
     * @return void
     */
    private function handleQueryParams(array $query_params)
    {
        foreach ($query_params as $key => $value) {

            if (!in_array($key, $this->key_words)) {
                $this->setFilters($key, $value);
            } else {
                switch ($key) {
                    case 'sort':
                        $this->setSorts($value);
                        break;
                    case 'relations':
                        $this->setRelations($value);
                        break;
                    case 'paginate':
                        $this->setPaginate($value);
                        break;
                    case 'deleted':
                        $this->setDeleted($value);
                        break;
                    case 'take':
                        $this->setTake($value);
                        break;
                    case 'skip':
                        $this->setSkip($value);
                        break;
                    case 'exists':
                        $this->setExists($value);
                        break;
                }
            }
        }
    }

    private function setFilters(string $key, $value)
    {
        $values = explode('%2C', urlencode($value));

        foreach ($values as $value) {
            $this->setFilter($key, $value);
        }
    }

    private function setFilter($key, $value)
    {
        $value = urldecode($value);

        if ($this->hasComparator($value)) {
            $value = [
                'value'    => $this->parseComparator($value),
                'operator' => $this->parseOperator($value)
            ];
        }

        if (empty($this->filters[$key])) {
            $this->filters[$key] = [$value];
        } else {
            $this->filters[$key][] = $value;
        }
    }

    protected function hasComparator($value)
    {
        $token = substr($value, 0, 1);

        return ($token == '<' || $token == '>');
    }

    private function parseOperator(string $key)
    {
        $token = substr($key, 0, 1);

        if ($token == '<' || $token == '>') {

            $double_token = substr($key, 0, 2);

            if (
                $double_token == '<=' ||
                $double_token == '>=' ||
                $double_token == '<>'
            ) {
                return $double_token;
            }

            return $token;
        }
    }

    private function parseComparator(string $value)
    {
        return substr($value, strlen($this->parseOperator($value)));
    }

    private function setSorts(string $columns)
    {
        $columns = explode('%2C', urlencode($columns));

        foreach ($columns as $column) {
            switch (substr($column, 0, 1)) {
                case '-':
                    $direction = 'desc';
                    $column = str_replace('-', '', $column);
                    break;
                case '+':
                    $column = str_replace('+', '', $column);
                    //no break
                default:
                    $direction = 'asc';
                    break;
            }

            $this->sorts[$column] = $direction;
        }
    }

    private function setPaginate($value)
    {
        $this->paginate = $value == 'true' ? true : false;
    }

    private function setDeleted($value)
    {
        $this->deleted = $value == 'true' ? true : false;
    }

    private function setTake($value)
    {
        if (!is_numeric($value)) {
            return;
        }

        $this->take = $value;
    }

    private function setSkip($value)
    {
        if (!is_numeric($value)) {
            return;
        }

        $this->skip = $value;
    }

    public function getById($id)
    {
        $model = $this->model;

        return $model->findOrFail($id);
    }

    public function get(array $custom_filters = [], array $relationships = [])
    {
        $model = $this->model;


        $this->applyRelations($model);

        $this->checkSoftDelete($model);

        $this->applyExternalFunctions($model, $relationships);

        $this->applyFilters($model);

        $this->applyExists($model);

        $this->applyExternalFunctions($model, $custom_filters);

        $this->applySorts($model);

        $model = $this->applyPagination($model);

        return $model;
    }

    private function applyPagination(&$model)
    {
        if ($this->paginate) {
            $model = $model->paginate($this->take, ['*'], 'page', $this->page);
        } else {
            $model = $model->get();
        }

        return $model;
    }

    public function checkSoftDelete(&$model)
    {
        if ($this->deleted) {
            if (in_array(SoftDeletes::class, class_uses($model))) {
                $model->withTrashed();
            }
        }
    }

    public function applyExternalFunctions(&$model, array $functions)
    {
        foreach ($functions as $function) {
            $model = call_user_func($function, $model);
        }
    }

    private function applyFilters(&$model)
    {
        foreach ($this->filters as $key => $value) {
            foreach ($value as $filter) {
                $this->addFilter($model, $key, $filter);
            }
        }
    }

    private function addFilter(&$model, $key, $value)
    {
        if (strpos($key, ',') !== false) {
            $keys = explode(',', $key);

            if ($this->isComparative($value)) {
                foreach ($keys as $key_order => $key) {
                    if ($key_order == 0) {
                        $model = $model->where($key, $value['operator'], $value['value']);
                    } else {
                        $model = $model->orWhere($key, $value['operator'], $value['value']);
                    }
                }
            } elseif ($this->isLike($value)) {
                foreach ($keys as $key_order => $key) {
                    if ($key_order == 0) {
                        $model = $model->where($key, 'like', $value);
                    } else {
                        $model = $model->orWhere($key, 'like', $value);
                    }
                }
            } else {
                foreach ($keys as $key_order => $key) {
                    if ($key_order == 0) {
                        $model = $model->where($key, $value);
                    } else {
                        $model = $model->orWhere($key, $value);
                    }
                }
            }

        } else {
            if ($this->isComparative($value)) {
                $model = $model->where($key, $value['operator'], $value['value']);
            } elseif ($this->isLike($value)) {
                $model = $model->where($key, 'like', $value);
            } else {
                $model = $model->where($key, $value);
            }
        }

    }

    private function isComparative($value)
    {
        return is_array($value) && !empty($value['operator']);
    }


    private function applySorts(&$model)
    {
        foreach ($this->sorts as $column => $direction) {
            $model = $model->orderBy($column, $direction);
        }
    }

    /**
     * Retorna se o primeiro ou o último caracter é "%"
     *
     * @param string $value
     *
     * @return bool
     */
    private function isLike(string $value)
    {
        return (substr($value, 0, 1)   == '%' ||
            substr($value, -1, 1)  == '%'
        );
    }

    private function setRelations($value)
    {
        foreach ($value as $relation) {
            $this->relations[] = $relation;
        }
    }

    private function applyRelations(&$model)
    {
        if (!empty($this->relations)) {
            $model = call_user_func_array([$model, 'with'], $this->relations);
        }
    }

    private function setExists($value)
    {
        foreach ($value as $column) {
            $this->exists[] = $column;
        }
    }

    private function applyExists(&$model)
    {
        foreach ($this->exists as $column) {
            $model = $model->whereNotNull($column);
        }
    }
}
