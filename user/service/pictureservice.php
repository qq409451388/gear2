<?php

/**
 * @RequestMapping("/pic")
 */
class PictureService
{
    private $pictureDao;

    /**
     * PictureService constructor.
     * @param PictureDao $pictureDao
     */
    public function __construct(PictureDao $pictureDao){
        $this->pictureDao = $pictureDao;
    }

    /**
     * @GetMapping("get")
     */
    public function getPicture($id):EzHttpResponse{
        $data = $this->pictureDao->getPictureOne($id);
        return EzHttpResponse::OK($data);
    }

    public function getAll($id){
        return $this->pictureDao->getPictureOne($id);
    }

}
