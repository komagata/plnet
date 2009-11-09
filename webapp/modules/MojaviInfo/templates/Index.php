<?php echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<title>Mojavi Info</title>
<style type="text/css">
<!--
body {
	font-family:helvetica, 'ＭＳ Ｐゴシック', Osaka, sans-serif;
	text-align: center;
	font-size: 12px;
}

h2 {
	font-size: 16px;
}

th {
	padding: 3px;
	background: #373737;
	color: #ffffff;
}

td {
	padding: 3px;
	vertical-align: top;
	background-color: #EFEFEF;
}

.container {
	border: 1px solid #373737;
	width: 100%;
	font-size: 12px;
}

.name {
	font-weight: bold;
}

.none {
	font-style: italic;
}

.red {
	color: red;
}

h1 {
	padding: 20px;
	background: #373737;
	color: #ffffff;
	font-size: 26px;
}

#contents {
	width: 600px;
	text-align: left;
	margin-left:auto;
	margin-right:auto;
}

#footer {
	text-align: center;
	margin: 10px;
}
-->
</style>
</head>

<body>
<div id="contents">
<h1>Mojavi<span class="red">Info</span></h1>

<h2>Configuration</h2>
<table class="container">
	<tr>
		<th>Name</th>
		<th>Value</th>
	</tr>
<?php foreach ($template["configs"] as $configName => $configValue): ?>
	<tr>
		<td class="name"><?php echo $configName; ?></td>
		<td><?php echo $configValue; ?></td>
	</tr>
<?php endforeach; ?>
</table>

<h2>Modules</h2>
<table class="container">
	<tr>
		<th>Module</th><th>Action</th><th>View</th><th>Template</th>
	</tr>
<?php foreach ($template["modules"] as $moduleName => $module): ?>
	<tr>
		<td class="name">
			<?php echo $moduleName; ?>
		</td>
		<td>
	<?php foreach ($module["actions"] as $action): ?>
			<?php echo $action; ?><br />
	<?php endforeach; ?>
		</td>
		<td>
	<?php foreach ($module["views"] as $view): ?>
			<?php echo $view; ?><br />
	<?php endforeach; ?>
		</td>
		<td>
	<?php foreach ($module["templates"] as $templ): ?>
			<?php echo $templ; ?><br />
	<?php endforeach; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<h2>Global Filter</h2>
<table class="container">
	<tr><th>Class Name</th></tr>
<?php foreach ($template["globalFilterList"] as $number => $globalFilter): ?>
	<tr><td><span class="name"><?php echo $number+1; ?>.</span> <?php echo $globalFilter; ?></td></tr>
<?php endforeach; ?>
</table>

<h2>Logger</h2>
<table class="container">
	<tr>
		<th>Logger</th><th>Priority</th><th>Exit Priority</th><th>Appender</th>
	</tr>
<?php foreach ($template["loggers"] as $logger): ?>
	<tr>
		<td class="name">
			<?php echo $logger["name"]; ?>
		</td>
		<td>
			<?php echo $logger["priority"]; ?>
		</td>
		<td>
			<?php echo $logger["exit"]; ?>
		</td>
		<td>
	<?php foreach ($logger["appenders"] as $appneder): ?>
			<?php echo $appneder; ?><br />
	<?php endforeach; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<h2>Authorization Handler</h2>
<table class="container">
	<tr><th>Class Name</th></tr>
	<tr><td><?php echo $template["authorizationHandler"]; ?></td></tr>
</table>

<h2>User</h2>
<table class="container">
	<tr><th>User</th><th>User Container</th></tr>
	<tr><td><?php echo $template["user"]; ?></td><td><?php echo $template["userContainer"]; ?></td></tr>
</table>

<h2>Session Handler</h2>
<table class="container">
	<tr><th>Class Name</th></tr>
	<tr>
		<td>
<?php if($template["sessionHandler"] === "none"): ?>
			<span class="none"><?php echo $template["sessionHandler"]; ?></span>
<?php else: ?>
			<?php echo $template["sessionHandler"]; ?>
<?php endif; ?>
		</td>
	</tr>
</table>

<div id="footer">MojaviInfo <?php echo MOJAVIINFO_VERSION; ?> by <a href="http://p0t.jp" alt="Masaki Komagata">Masaki Komagata</a></div>
</div>
</body>
</html>
