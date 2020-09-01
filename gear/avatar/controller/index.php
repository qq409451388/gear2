<?php

/**
 * Class Index
 * @RequestController("/index")
 */
class Index extends BaseController
{
    /**
     * @RequestMapping('/index')
     */
    public function demo(){
        return "<h1>Hello Gear!</h1>";
    }

    private function getMenus(){
        return json_decode(file_get_contents(USERCONFIG_DIR.'/menu.json'), true);
    }

    public function main(){
        return $this->show(['menus' => $this->getMenus()], 'index/main');
    }

    public function getUserInfo($request){
        $userId = $request->get('userId', 0);
        $userName = $request->get('userName', '');
        $userInfo = [];
        if(!$request->isEmpty()) {
            if(!empty($userName)){
                $userId = $this->userSvc->getUserIdByUserName($userName);
            }
            $userInfo = $this->userSvc->getUserInfoByUserId($userId);
            $userInfo = EzString::convertArrayToUnicode($userInfo);
        }
        $response = [
            'userId' => $userId,
            'userName' => $userName,
            'res' => EzString::_dump($userInfo, 'html', ['userid', 'username'])
        ];

        return $this->show($response, 'user/getUserInfo');
    }
}