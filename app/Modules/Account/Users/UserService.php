<?php

namespace App\Modules\Account\Users;

use App\Modules\Base\BaseService;
use Illuminate\Support\Facades\DB;
use App\Modules\Account\Exceptions\AccountException;
use App\Modules\Account\Users\Resources\UserCollection;
use App\Modules\Account\Users\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService extends BaseService
{
    protected User $user;
    protected string $token;
    public function __construct()
    {
        $this->setModel(User::class);
        $this->setResource(UserResource::class);
        $this->setCollection(UserCollection::class);
    }

    public function get(array $query_params = [], bool $no_wrapper = false)
    {
        $query_params['relations'] = [
            'image', 'profile'
        ];

        return parent::get($query_params, $no_wrapper);
    }

    public function store(array $data)
    {
        try {
            DB::beginTransaction();

            if (!isset($data['active'])) {
                $data['active'] = true;
            }

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user = $this->model->create($data);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return new UserResource($user->load('profile'));
    }

    public function update(array $data, string $id)
    {
        try {
            DB::beginTransaction();

            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }

            $user = $this->model->findOrFail($id);

            $user->update($data);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return $user;
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            if (auth()->user() && ($id == auth()->user()->id)) {
                throw new AccountException(500, "Não é possível remover o próprio usuário.");
            }

            $user = $this->model->findOrFail($id);

            $user->delete();

            $result = $user->deleted_at->toDateTimeString();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return $result;
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();

            $user = $this->model->onlyTrashed()->findOrFail($id);

            $query = $this->model->where('email', $user->email);

            if ($query->exists()) {
                throw new AccountException(400, 'Já existe um usuário ativo com o e-mail "' . $user->email . '". Não é possível realizar a restauração.');
            }

            $result = $user->restore();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return $result;
    }

    public function getAuthenticatedUser(User $user = null)
    {
        $token = !empty($user) ?
            JWTAuth::setToken(JWTAuth::fromUser($user)) :
            JWTAuth::getToken();

        $id = JWTAuth::getPayload($token)['sub'];

        $user = new AuthenticatedUser($id);

        return $user->toArray();
    }
}
