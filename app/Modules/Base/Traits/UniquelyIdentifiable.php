<?php

namespace App\Modules\Base\Traits;

use Ramsey\Uuid\Uuid;

/**
 * Trait para utilizar uuid como primary keys
 */
trait UniquelyIdentifiable
{
    /**
     * Função executada quando o model é inicializado
     * Desabilita o auto-increment da primary-key e seta ela como string
     */
    protected function initializeUniquelyIdentifiable()
    {
        $this->incrementing = false;
        $this->keyType      = 'string';
    }

    /**
     * Função executada quando o model é bootado
     * Ao salvar no banco, a primary key usada é um uuid gerado
     */
    public static function bootUniquelyIdentifiable()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
            }
        });
    }
}
