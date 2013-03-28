<?PHP

$name_img = '';
$address_img = '';
$created_img = '';

$case = 'load';

if (isset($data['show_new']) && $data['show_new'])
	$case = 'loadnew';

if (isset($data['unused_only']) && $data['unused_only'])
	$case = 'loadunused';

if( isset( $filter ) )
{
	$f = "&case=$case&f=$filter";
} else {
	$f = "&case=$case";
}


$varname = $ordering['field'] . '_img';
$$varname = '&nbsp;<img border="0" alt="sort" src="design/img/btn_sort_'.strtolower($ordering['dir']).'.gif"/>';

?>
<table class="listing" cellspacing="0" cellpadding="0" width="100%">
<tbody id="addresses">
<tr>
	<th width="20"><img src="<?=IMAGES?>spacer.gif" width="20" height="20"></th>
	<th width="40%"><a class="black" href="#" onclick="sortDispatcher('name','addr','<?=$f?>')"><?=gettext('naam')?><?=$name_img?></a></th>
	<th width="40%"><a class="black" href="#" onclick="sortDispatcher('address','addr','<?=$f?>')"><?=gettext('e-mailadres')?><?=$address_img?></a></th>
	<th width="20%" nowrap><a class="black" href="#" onclick="sortDispatcher('created','addr','<?=$f?>')"><?=gettext('datum toegevoegd')?><?=$created_img?></a></th>
	<th width="30">&nbsp;</th>
</tr>
