<h2><?=$canvas->filter($album->getName())?></h2>
<br />
<div><?=$canvas->filter($album->getText())?></div>
<br />
<div>
telefoon: <?=$canvas->filter($yourportfolio->phone)?><br />
fax: <?=$canvas->filter($yourportfolio->fax)?><br />
e-mail: <a href="mailto:<?=$yourportfolio->email?>" class="default txt_normal"><?=$canvas->filter($yourportfolio->email)?></a>
</div>