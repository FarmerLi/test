<html>
<head>
    <title></title>
<script type="text/javascript" src="http://ts.logis.cn/public/js/jquery.js"></script>
</head>
<body>
<a id="xx" href="http://baidu.com" target="_blank">xxx</a>
<button>xxx</button>
<script type="text/javascript">
$(function (){
    $('#xx').bind('click', function () {
        return false;
    });
});
</script>
</body>

</html>