<?php
class PictureDao
{
    public function getPictureOne($id){
        return DB::get("user")->findOne("select * from picture where id = :id limit 1", [':id' => $id]);
    }
}