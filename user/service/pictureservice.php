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
    public function getPicture($id):HttpResult{
        $data = $this->pictureDao->getPictureOne($id);
        return HttpResult::OK($data);
    }

    public function getAll($id){
        return $this->pictureDao->getPictureOne($id);
    }

}