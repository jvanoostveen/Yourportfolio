<?PHP
/*
 * Project: yourportfolio / newsletter
 *
 * @author Christiaan Ottow
 * @created Dec 5, 2006
 */
?>
<DIV id="canvas_bottom_high">
<script>
var pxextra = 40;
</script>

<div style="background-color: #e1e1e1; height: 40px; border: 1px solid #000000;" id="quickadd_div">
	<div style="padding-top: 5px; float: left;" id="quickadd_innercontent">
		<table><tr>
		<td width="10"><img src="<?=IMAGES?>spacer.gif" width="10" height="1"></td>
		<td><input type="text" name="name" size="35" id="new_name" onfocus="checkFocus('name')" onblur="checkBlur('name')" onkeyup="checkSubmitNew()" value="<?=gettext('Naam')?>"></td>
		<td width="10"><img src="<?=IMAGES?>spacer.gif" width="10" height="1"></td>
		<td><input type="text" name="addr" size="35" id="new_addr" onfocus="checkFocus('addr')" onblur="checkBlur('addr')" onkeyup="checkSubmitNew()" value="<?=gettext('E-mail adres')?>">
		<td width="10"><img src="<?=IMAGES?>spacer.gif" width="10" height="1"></td>
		<td><div class="button"><a class="upload" href="javascript:void(0);" onclick="formSubmit()"><?=gettext('voeg toe')?></a></div></td>
		<td width="10"><img src="<?=IMAGES?>spacer.gif" width="10" height="1"></td>	
		<td>
			<select id="group_id_select" name="group_id">
			<option value="">-</option>
			<? if (!empty($data['groups'])) : ?>
			<? foreach ($data['groups'] as $group) : ?>
			<option value="<?=$group['group_id']?>"><?=$canvas->filter($group['name'])?></option>
			<? endforeach; ?>
			<? endif; ?>
			</select>
		</td>
		<td width="10"></td>
		</tr>
		</table>
	</div>
	<div style="padding-top: 10px;float:left;" id="quickadd_link_div"><a href="#" onclick="javascript:massAdd();" class="blacklink"><?=gettext('meer...')?></a></div>
</div>
<div style="display: none; visibility: hidden;" id="quickadd_2_div">
<a href="#" onclick="javascript:massAdd();" class="blacklink"><?=gettext('terug')?></a>
</div>