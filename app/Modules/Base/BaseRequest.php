<?php

namespace App\Modules\Base;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * By default it returns false, change it to
         * something like this if u are checking authentication
         */
        return Auth::check();
    }

    /**
     * Regras para a validação da requisição
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Seta os nomes para os atributos da requisição
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }

    /**
     * Seta mensagens para combinações de atributos e regras
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * Função executada após realizar a validação.
     * É usada para setar os nomes dos atributos.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->setAttributeNames($this->attributeNames());
    }

    /**
     * {@inheritdoc}
     */
    protected function createDefaultValidator(ValidationFactory $factory)
    {
        if (method_exists($this, 'attributeNames')) {
            $attributes = $this->attributeNames();
        } else {
            $attributes = $this->attributes();
        }

        return $factory->make(
            $this->validationData(), $this->container->call([$this, 'rules']),
            $this->messages(), $attributes
        );
    }

    /**
     * É necessário sobrescrever a função de
     * \Illuminate\Foundation\Http\FormRequest
     * para traduzir a mensagem.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new AuthorizationException('Permissões insuficientes.');
    }

    /**
     * Lança uma resposta uma HttpResponseException como json
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'code'    => 'INVALID_REQUEST',
                'message' => $validator->errors()->first(),
                'field'   => $validator->errors()->keys()[0],
            ], 422)
        );
    }
}
