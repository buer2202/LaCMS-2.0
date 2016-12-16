<?php
namespace App\Repositories;

use App\User;
use Hash;

class UserRepository extends BaseRepository
{
    public function model()
    {
        return User::class;
    }

    /**
     * 修改密码
     * @param int $id 用户id
     * @param string passwordCurrent 当前密码
     * @param string passwordNew 新密码
     * @return boolean 更新结果
     */
    public function updatePassword($id, $passwordCurrent, $passwordNew)
    {
        $user = $this->model->find($id);

        if(!Hash::check($passwordCurrent, $user->password)) {
            $this->error = '当前密码不正确';
            return false;
        }

        $user->password = Hash::make($passwordNew);
        return $user->save();
    }
}