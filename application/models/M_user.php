<?php
class M_user extends CI_Model
{

    public function update_password($user_id, $password)
    {
        return $this->db
            ->where('id', $user_id)
            ->update('users', [
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);
    }
}
