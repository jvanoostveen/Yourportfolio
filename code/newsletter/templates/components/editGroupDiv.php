<div id="editGroupDiv" class="popupDiv">

	<table width="100%" style="padding: 0; margin: 0; " cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="heading" width="1" valign="top"><img src="design/img/round_row.gif" width="1" height="28" class="special"></td>
		<td class="heading" width="30" valign="top"><img src="design/iconsets/default/menu_header.gif" class="special"></td>
		<td class="namebar" width="100%"><?=gettext('Groep bewerken')?></td>
		<td class="namebar" align="right"><img src="design/img/sync-white.gif" width="16" height="16" id="progress2" style="visibility: hidden;"></td>
		<td class="heading" width="27" valign="top"><a href="javascript:void(0)" onclick="showPopup()"><img src="design/iconsets/default/close.gif" width="27" height="28" class="special" border="0"></a></td>
		<td class="heading" width="1" valign="top"><img src="design/img/round_row.gif" width="1" height="28" class="special"></td>
	</tr>
	<tr>
		<td colspan="6">
			<div width="100%" class="popupInnerDiv">
				<div style="position: absolute; top: 20px; left: 20px; font-weight: normal;">
					<?=gettext('Naam')?>:		<input type="text" name="groupname" size="29" id="editGroupField" onkeypress="handleKeyPress(event)" maxlength="40">
				</div> 
			
				<div style="position: absolute; top: 47px; left: 20px; font-weight: normal;" id="groupVisibleDiv">
					
				</div> 
		
				<div class="button button_2" style="position: absolute; bottom: 30px; left: 20px;">
					<a class="upload" href="javascript:void(0)" onclick="deleteGroup()">
						<?=gettext('Groep verwijderen')?>
					</a>
				</div>
				<div style="position: absolute; bottom: 8px; left: 20px;">
				<input type="checkbox" id="delete_contents_check" value="Y"> <label for="delete_contents_check"><?=gettext('verwijder adressen in groep')?></label>
				</div>
			
				<div class="button" style="position: absolute; bottom: 30px; right: 10px;">
					<a class="upload" href="javascript:void(0)" onclick="saveGroupMeta()" accesskey="s">
						<?=gettext('Bewaar')?>
					</a>
					<input type="hidden" id="editGroupId" name="group_id">
				</div>
			</div>		
		</td>
	</tr>
	</table>

<form name="deleteForm" method="post" action="newsletter_groups.php?case=delete">
	<input type="hidden" name="id" id="deleteFormGroupId">
	<input type="hidden" name="del_contents" id="deleteFormDelContents">
</form>
<form name="saveForm" method="post" action="newsletter_groups.php?case=saveMeta">
	<input type="hidden" name="id" id="saveFormGroupId">
	<input type="hidden" name="name" id="saveFormGroupName">
	<input type="hidden" name="visibility" id="saveFormGroupVisible">
</form>

</div>