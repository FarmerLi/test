<html>
<head>
	<title>test ueditor</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>编辑器完整版实例</title>
	<script type="text/javascript" src="ueditor.config.js"></script>
	<script type="text/javascript" src="ueditor.all.js"></script>
</head>
<body>
	<?php 
		if (!empty($_POST)) {
			var_dump($_POST);
		}
	?>
	<form action="#" method="POST">
		<textarea name="name" id="name" style="height: 200px; width: 700px"></textarea>
		<textarea name="name2" id="name2" style="height: 200px; width: 700px"></textarea>
		<textarea name="name3" id="name3" style="height: 200px; width: 700px"></textarea>
		<input type="submit" value="submit" />
	</form>
	<script type="text/javascript">
			UE.getEditor('name');
			UE.getEditor('name2');
			UE.getEditor('name3');
	</script>
</body>
</html>