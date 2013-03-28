<? if (!empty($album->sections)) : /* albums to show */ ?>
<? foreach($album->sections as $menu_section) : /* loop albums */ ?>
<div class="menuitem"><a href="<?=$canvas->url($album, $menu_section)?>" class="txt_normal <?=($canvas->open_section == $menu_section->id) ? '':'un'?>selected"><?=$canvas->filter($menu_section->getName())?></a></div>
<? endforeach; /* end loop albums */ ?>
<? endif; /* end albums to show */ ?>