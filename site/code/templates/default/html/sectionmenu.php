<? if (!empty($section->items)) : /* has items to show */ ?>
<? foreach($section->items as $menu_item) : /* loop items */ ?>
<div class="menuitem"><a href="<?=$canvas->url($album, $section, $menu_item)?>" class="txt_small <?=($canvas->open_item == $menu_item->id)? '' : 'un' ?>selected"><?=$canvas->filter($menu_item->getName())?></a></div>
<? endforeach; /* end loop items */ ?>
<? endif; /* end has items to show */ ?>
