<?php

/**
 * @RequestMapping("/pic")
 */
class PictureService
{
    private $pictureDao;
    public function __construct(PictureDao $pictureDao){
        $this->pictureDao = $pictureDao;
    }

    /**
     * @GetMapping("/get")
     */
    public function getPicture($id){
        return $this->pictureDao->getPictureOne($id);
    }

    public function getAll($id){
        return $this->pictureDao->getPictureOne($id);
    }

}