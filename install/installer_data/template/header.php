<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<style>
body {
  background-color: #ffffff;
  color: #000000;
  font-family: Arial, Helvetica, Verdana, sans-serif;
  font-weight: normal;
}

table.items {
  background-color: #eeeeee;
}

td {
  font-family: Arial, Helvetica, Verdana, sans-serif;
  font-weight: normal;
}

td.itembox {
  border-bottom: 1px solid #c0c0c0;
  margin: 5px;
  width: 350px;
}

span.title {
  border-bottom: 1px solid #c0c0c0;
  display: block;
  font-weight: bold;
  size: 16px;
  margin-bottom: 7px;
}

td.separator {
  background-color: #ffffff;
}

.content {
  color: #444444;
  font-size: 11px;
}

a:link, a:active, a:visited {
  color: #992222;
  text-decoration: none;
  font-size: 11px;
}

a:hover {
  text-decoration: underline;
  font-size: 11px;
}

p.unsubscribe {
  font-size: 11px;
  align: center;
}
</style>
</head>
<body>
<? if ($IN_MAIL) : ?>

<center><a href="<?=$PREVIEW_URL?>" class="preview">Klik hier als de nieuwsbrief niet goed weergegeven wordt.</a></center><br>

<? endif; ?>
<h2>Nieuwsbrief standaard template</h2>

<table width="600" cellpadding="0" cellspacing="0" class="items">
