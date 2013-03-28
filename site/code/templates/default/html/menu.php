<? if (!empty($yourportfolio->albums)) : /* albums to show */ ?>
<? foreach($yourportfolio->albums as $menu_album) : /* loop albums */ ?>
<div class="menuitem"><a href="<?=$canvas->url($menu_album)?>" class="txt_normal <?=($canvas->open_album == $menu_album->id) ? '':'un'?>selected"><?=$canvas->filter($menu_album->getName())?></a></div>
<? endforeach; /* end loop albums */ ?>
<? endif; /* end albums to show */ ?>
