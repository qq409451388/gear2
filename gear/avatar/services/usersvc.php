<?php
class UserSvc
{
    public function getDb():IDbSe{
        return DB::get("db_Hdf");
    }
    public function getUserIdByUserName($userName){
        $sql = 'select userid from user where username = :userName;';
        return $this->getDb()->queryValue($sql, [':userName' => $userName], 'fld_UserId');
    }

    public function getUserInfoByUserId($userId){
        $sql = 'select * from user where userid = :userId;';
        return $this->getDb()->findOne($sql, [':userId' => $userId]);
    }
}