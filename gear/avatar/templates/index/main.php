<!<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="http://static.poethan.cn/js/jquery.min.js"></script>
    <title>工具包</title>
    <style>
        #headway {width: 100%;height:50px;}
        #headway div {width:300px;height:50px;margin:0px auto;line-height: 50px;font:16px red;}
        #headway input{border-radius: 5px;border: 1px solid #d2d2d2;width:80%;height:40px;folat:left;}
        #menu {width:10%;min-height:50%;float:left;border: 1px solid #d2d2d2;}
        #menu .center {cursor:pointer;border: 1px solid #f2f2f2;}
        #menu .center:hover {border: 1px solid deepskyblue;}
        #content {float: left;margin-left: 2%;border:1px solid #d2d2d2;}
    </style>
</head>
<body>
    <div id="headway">
        <div><input id='searchI' placeholder="输入以搜索..." type="serach"></div>
    </div>
    <div id="menu">
        <div style="text-align: center;"><h1>功能菜单</h1></div>
        <?php
            foreach($menus as $menu){
                echo "<center class='center' onclick=showFunc('".Env::getDomain().$menu['func']."')><h2>{$menu['name']}</h2></center>";
            }
        ?>
    </div>
    <iframe id="content" width="80%" height="80%" border="0" frameborder="no"></iframe>
</body>
<script>
    function showFunc(func){
        var ele = document.getElementById("content");
        ele.setAttribute("src", func);
    }
    $(document).ready(function(){
        $('#searchI').on('keyup',function(){
            $('#menu center h2:contains('+$(this).val()+')').parent().show();
            $('#menu center h2:not(:contains('+$(this).val()+'))').parent().hide();
        });
    });
</script>
</html>