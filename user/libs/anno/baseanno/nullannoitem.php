<?php
class NullAnnoItem implements AnnoItem
{
    public function getService(){
        return '';    
    }

    public function getMethod(){
        return '';    
    }
}
