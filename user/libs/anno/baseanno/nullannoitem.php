<?php
class NullAnnoItem implements AnnoItem
{
    public function getService(){
        return '';    
    }

    public function getMethod(){
        return '';    
    }

    public function isValid(): bool{
        return !empty($this->getService()) && !empty($this->getMethod());
    }
}
