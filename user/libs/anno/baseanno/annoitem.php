<?php
Interface AnnoItem
{
    public function getService();
    public function getMethod();
    public function isValid():bool;
}