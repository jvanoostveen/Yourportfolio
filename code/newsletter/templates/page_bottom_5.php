		<!-- end current view name -->
</DIV>


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

<div style="position: relative;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="1" class="verticalline"><img src="design/img/black_spacer.gif" width="1" height="1"></td>
		<td class="namebar" colspan="2" align="right" width="2200">
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
		?></td>
		<td width="22" class="namebar" align="right"><img src="design/img/sync-white.gif" width="16" height="16" id="saving2" style="visibility: hidden;"></td>				
		<td width="10"><img src="design/img/black_spacer.gif" width="10" height="28" class="special"></td>
		<td width="4" valign="top"><img src="design/img/round_right_down.gif" width="4" height="28" class="special"></td>
		<td width="1" valign="top"><img src="design/img/round_row_down.gif" width="1" height="28" class="special"></td>
	</tr>
	</table>
</div>
</DIV>
