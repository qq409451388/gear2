<?php

class User extends BaseController
{
    public function getToken(Request $request){
        $userId = $request->get("userId");
        $hostId = $request->get("hostId");
        return AuthUntils::generateToken(["userId"=>$userId, "hostId"=>$hostId]);
    }

    public function verifyToken(Request $request){
        $token = $request->get("token");
        $userId = $request->get("userId");
        $hostId = $request->get("hostId");
        $sourceData = ["userId"=>$userId, "hostId"=>$hostId];
        return json_encode(AuthUntils::verifyToken($sourceData, $token));
    }
}