<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>


	<script>
		const userData = @json($userData);
		const data = { message: userData };
		window.opener.postMessage(data, '*');
		// window.opener.location.href = 'http://localhost:3000/login';
		window.close();
	</script>
</body>
</html>