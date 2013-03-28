<? if (isset($item)) : /* there is an item to display */ ?>
<? $preview	= $item->getFile('preview'); ?>

<? if (!$preview->isEmpty()) : /* has image */ ?><div><img src="<?=$system->base_url?><?=$preview->basepath.$preview->sysname?>" alt="<?=$canvas->filter($item->getName())?>"></div><? endif; /* end has image */ ?>
<h2><?=$canvas->filter($item->getName())?></h2>

<div>
<br>
<? $yourportfolio->parseCustomFields(); ?>
<? foreach ($yourportfolio->custom_fields as $custom_field) : /* loop thru custom fields */ ?>
<? $item_custom_data = $item->getCustomData($custom_field['key']); ?>
<? if (!empty($item_custom_data)) : ?>
<?=$canvas->filter($custom_field['label'])?>: <b><?=$canvas->filter($item_custom_data)?></b><br>
<? endif; ?>
<? endforeach; /* end loop thru custom fields */ ?>
<p><?=$canvas->filter($item->getText())?></p>
</div>
<? endif; /* end item */ ?>
<br />
