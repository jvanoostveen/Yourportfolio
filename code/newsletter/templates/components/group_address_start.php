<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created Jan 13, 2007
 */
 
$name_img = '';
$address_img = '';

$varname = $ordering['field'] . '_img';
$$varname = '&nbsp;<img border="0" alt="sort" src="design/img/btn_sort_'.strtolower($ordering['dir']).'.gif"/>';

?>

<table class="listing" cellpadding="0" cellspacing="0" width="100%" style="margin: 0; padding: 0">
<tbody  id="addresses">
<tr height="21">
	<th><input type="checkbox" style="margin-left: 10px;" onclick="toggleAll()" name="all" /></th>
	<th><a class="black" id="sort_name" href="#" onclick="sortDispatcher('name','group','<?=$extra?>');"><?=gettext('naam')?><?=$name_img?></a></th>
	<th><a class="black" id="sort_address" href="#" onclick="sortDispatcher('address','group','<?=$extra?>');"><?=gettext('e-mailadres')?><?=$address_img?></a></th>
</tr>
