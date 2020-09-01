<!<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="http://static.poethan.cn/js/jquery.min.js"></script>
    <title>查询医生</title>
</head>
<body>
<form href="<?=ENV::getDomain()?>doctorteam/getTeamByUserId" method="post">
    <label>用户id：<input name="userId" value="<?=$userId?>"></label>
    <label>用户名：<input name="userName" value="<?=$userName?>"></label>
    <button type="submit">提交</button>
</form>
<?php
echo empty($res) ? '' : $res;
?>
</body>
<script>
    $(document).ready(function(){
        $('input').focus(function(){
            $('input').each(function(index,val){
                $(val).val("");
            });
        });
    });
</script>
</html>