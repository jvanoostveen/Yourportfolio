<div id="newGroupDiv" class="popupDiv">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="heading" width="1" valign="top" id="cell1"><img src="design/img/round_row.gif" width="1" height="28" class="special"></td>
	
		<td class="heading" width="30" valign="top" id="cell2"><img src="design/iconsets/default/menu_header.gif" class="special"></td>
	
		<td class="namebar" width="100%"><?=gettext('Nieuwe groep')?></td>
	
		<td class="namebar" align="right"><img src="design/img/sync-white.gif" width="16" height="16" id="progress" style="visibility: hidden;"></td>
	
		<td class="heading" width="27" valign="top"><a href="javascript:void(0)" onclick="showPopup()"><img src="design/iconsets/default/close.gif" width="27" height="28" class="special" border="0"></a></td>
	
		<td class="heading" width="1" valign="top"><img src="design/img/round_row.gif" width="1" height="28" class="special"></td>
	</tr>
	<tr>
		<td colspan="6">
			<div width="100%" class="popupInnerDiv">
			
				<div style="position: absolute; top: 20px; left: 20px; padding: 0; font-weight: normal;">
					<?=gettext('Naam')?>:		<input type="text" size="28" name="groupname" id="groupField" onkeypress="handleKeyPress(event)" maxlength="40">
					<br><br>
					<input type="checkbox" name="groupvisible" id="groupVisible"> <?=gettext('Groep is zichtbaar op website')?>
				</div> 
				<div class="button" style="position: absolute; bottom: 15px; right: 10px;">
					<a class="upload" href="javascript:void(0)" onclick="makeGroup()">
						<?=gettext('Bewaar')?>
					</a>
				</div>
				
			</div>
		</td>
	</tr>
	</table>

</div>
