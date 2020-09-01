<?php
Interface IHttp
{
    public function init(string $host, $port, $root);
    public function start();
    public function getResponse(string $path, $request):string;
    public function getDynamicResponse(string $path, $request):string;
    public function getStaticResponse(string $path):string;
}