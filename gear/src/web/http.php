<?php
class HTTP extends Container{
    public function start(){
        try{
            $this->parseUri();
        }catch (Exception $e){

        }
    }

    public function send(){

    }
}