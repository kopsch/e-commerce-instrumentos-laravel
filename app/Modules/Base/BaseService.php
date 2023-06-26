<?php

namespace App\Modules\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

/**
 * Serviço para tratar, persistir e consultar dados do banco
 */
abstract class BaseService
{
    /**
     * Define o modelo do eloquent do serviço
     *
     * @param string $model
     *
     * @return void
     */
    protected function setModel(string $model)
    {
        $this->model = new $model;
    }

    /**
     * Retorna o modelo eloquent do serviço
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Define o json resource do serviço
     *
     * @param \Illuminate\Http\Resources\Json\JsonResource
     *
     * @return void
     */
    protected function setResource(string $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Retorna o json resource do serviço
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function getResource(Model $model): JsonResource
    {
        if (!isset($this->resource)) {
            throw new \Exception('JsonResource não declarado na classe "' . get_class($this) . '".');
        }

        $resource = $this->resource;

        return new $resource($model);
    }

    /**
     * Define o modelo do eloquent do serviço
     *
     * @param string $collection
     *
     * @return void
     */
    protected function setCollection(string $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Retorna o resource collection do serviço
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function getCollection($eloquent_collection): ResourceCollection
    {
        if (!isset($this->collection)) {
            throw new \Exception('ResourceCollection não declarado na classe "' . get_class($this) . '".');
        }

        $collection = $this->collection;

        return new $collection($eloquent_collection);
    }
    /**
     * Retorna um registro do modelo de acordo com o id parametrizado
     *
     * @param string $id
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function getById(string $id, array $query_params = [])
    {
        return $this->getResource(
            with(new QueryService($this->model, $query_params))->getById($id)
        );
    }

    /**
     * Retorna registros
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function get(array $query_params = [], bool $no_wrapper = false)
    {
        if ($no_wrapper) {
            $a = with(new QueryService($this->model, $query_params))->get();
            return $a;
        }

        $b = with(new QueryService($this->model, $query_params))->get();
        $a = $this->getCollection($b);

        return $a;
    }

    /**
     * Persiste os dados de um modelo a partir dos dados inseridos
     *
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model;
     */
    public function store(array $data)
    {
        try {
            DB::beginTransaction();
                $model = $this->model->create($data);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $model;
    }

    /**
     * Atualiza os dados de um modelo a partir dos dados inseridos
     *
     * @param array $data
     * @param string $id
     *
     * @return \Illuminate\Database\Eloquent\Model;
     */
    public function update(array $data, string $id)
    {
        try {
            DB::beginTransaction();

            $model = $this->model->findOrFail($id);

            $model->update($data);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $model;
    }

    /**
     * Verifica se já existe um modelo no banco de acordo com os dados inseridos
     *
     * @param string $column
     * @param mixed $value
     *
     * @return bool
     */
    public function modelExists(string $column, $value)
    {
        return $this->model->where($column, $value)->exists();
    }


    /**
     * Remove registros do modelo do banco de dados
     *
     * @param string $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $model = $this->model->findOrFail($id);

            $model->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return $model;
    }

    /**
     * Restaura registros do modelo do banco de dados
     *
     * @param string $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function restore(string $id)
    {
        try {
            DB::beginTransaction();

            // A função withTrashed trás até os registros
            // removidos com soft delete
            $model = $this->model->withTrashed()->findOrFail($id);

            $model->restore();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return $model;
    }
}
