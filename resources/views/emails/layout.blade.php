<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body id="html_body">
		<table cellpadding="10">
			<tr>
				<td>
					<a href="{{ route('home') }}"><img src="{?  echo env('APP_DEBUG', '/') ?}/assets/emails/app_icon-p.png" width="50"></a>
				</td>
			</tr>
			<tr>
				<td>
					@yield('content')
				</td>
			</tr>
			<tr>
				<td>
					<font face="Arial" size="2"><strong><font color="#8bb140">Beamble</font></strong></font>
				</td>
			</tr>
		</table>
	</body>
</html>
