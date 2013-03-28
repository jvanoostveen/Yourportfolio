
		<!-- end current view name -->
	</td>
</tr>
<tr>
	<td>
		<?PHP
		if( isset($components['pre_bottomBar']) && count($components['pre_bottomBar']) > 0 )
		{
			foreach($components['pre_bottomBar'] as $t )
			{
				require(NL_TEMPLATES.'components/'.$t);
			}
		} else {
			?><DIV id="canvas_bottom"><?PHP
		}
		?>
	</td>
</tr>
<tr>
	<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td class="namebar" width="2000">
					<div style="position: relative; width: 100%">
						<?PHP
						if( isset( $components['bottomBar'] ) )
						{
							if( is_array($components['bottomBar']) && count($components['bottomBar']) > 0)
							{
								foreach( $components['bottomBar'] as $t )
								{
									require(NL_TEMPLATES.'components/'.$t);
								}
							} else if( !is_array($components['bottomBar']) ){
								require(NL_TEMPLATES.'components/'.$components['bottomBar']);
							}
						}
						?>
					</div>
					</td>
		
				<!-- end upload photo link -->
				</td>
				<td class="namebar">&nbsp;</td>
				<td width="22" class="namebar" align="right"><img src="<?=IMAGES?>spacer.gif" width="16" height="16" id="progress2"></td>
				<td width="10"><img src="design/img/black_spacer.gif" width="10" height="28" class="special"></td>
				<td width="4" valign="top"><img src="design/img/round_right_down.gif" width="4" height="28" class="special"></td>
				<td width="1" valign="top"><img src="design/img/round_row_down.gif" width="1" height="28" class="special"></td>
			</tr>
		</table>
	</td>
</tr>
</table>


	</td>
	<td width="12" height="1"><img src="<?=IMAGES?>spacer.gif" width="12" height="1"></td>
</tr>
</table>
