<script type="text/javascript">

var root_url = '<?=dirname($_SERVER['SCRIPT_NAME'])?>';
var tablepage = true;

</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 0;">
<tr>
	<td width="12" height="12"><img src="<?=IMAGES?>spacer.gif" width="12" height="12"></td>
	<td width="176" height="12"><img src="<?=IMAGES?>spacer.gif" width="176" height="12"></td>
	<td height="12"><img src="<?=IMAGES?>spacer.gif" width="1" height="12"></td>
	<td width="12" height="12"><img src="<?=IMAGES?>spacer.gif" width="12" height="12"></td>
</tr>
<tr>
	<td width="12" height="1"><img src="<?=IMAGES?>spacer.gif" width="12" height="1"></td>
	<td valign="top">

<!-- main table -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 0;">
<tr>
	<td width="1"><img src="<?=IMAGES?>round_row.gif" width="1" height="28" class="special"></td>
	<td width="200">
		<table width="200" border="0" cellpadding="0" cellspacing="0" style="border: 0;">
		<tr>
			<td width="30"><img src="<?=$canvas->showIcon('menu_header')?>" width="30" height="28" class="special"></td>
			<td class="bg_black fg_white" valign="middle">
			<!-- photographer name -->
			<a href="http://<?=DOMAIN?><?=(SUB_DOMAIN) ? '/'.SUB_DOMAIN : ''?>" target="_blank" class="fg_white txt_no_underline"><?=$canvas->filter($yourportfolio->photographer_name)?></a>
			<!-- end photographer name -->
			</td>
		</tr>
		</table>
	</td>
	<td width="1" class="namebar" height="28" align="left"><img src="design/img/white_spacer.gif" width="1" height="28"></td>
	<td class="namebar" width="300"><img src="<?=$settings['page_icon']?>" alt="<?=$settings['page_title']?>" align="absmiddle" width="31" height="28" border="0"><a class="fg_white txt_no_underline"><?=$settings['page_title']?></a></td>
	<td class="namebar" align="left">
		<span class="small" id="total_count"><?PHP 
			if( isset($settings['page_center_title'])) {
				 echo $settings['page_center_title'];
			} else {
				 echo '&nbsp;';
			}
		?></span>
	</td>	
	<td style="padding: 0; margin: 0;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border: 0;">
			<tr>
				
				<td class="namebar" align="right">
				<?PHP
					$included = false;
					if( isset( $components['topBar'] ) )
					{
						if( is_array($components['topBar']) && count($components['topBar']) > 0)
						{
							foreach( $components['topBar'] as $t )
							{
								$included = true;
								require(NL_TEMPLATES.'components/'.$t);
							}
						} else if( !is_array($components['topBar']) ){
							$included = true;
							require(NL_TEMPLATES.'components/'.$components['topBar']);
						}
					}
					
					if( !$included )
					{
						?>&nbsp;<?PHP
					}
					
				?>
				</td>
				<td width="22" class="namebar" align="right"><img src="design/img/sync-white.gif" width="16" height="16" id="saving1" style="visibility: hidden;"></td>				
				<td width="10"><img src="design/img/black_spacer.gif" width="10" height="28" class="special" class="special"></td>
				<td width="4" valign="top"><img src="design/img/round_right.gif" width="4" height="28" class="special"></td>
				<td width="1" valign="top"><img src="design/img/round_row.gif" width="1" height="28" class="special"></td>
			</tr>
			</table>	
	</td>	
</tr>
<tr>
	<td valign="top" colspan=2>
		<table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 1px solid #000000;">
		<tr>
			<td class="verticalline" width="1">
				<img src="<?=IMAGES?>spacer.gif" width="1" height="1" class="special">
			</td>
			<td>
			<!-- menu -->
			<? require(NL_TEMPLATES.'menu.php'); ?>
			<!-- end menu -->
			</td>
		</tr>
		</table>
	</td>
	<td valign="top" colspan="4">

		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="canvas_div" style="border: 0;">
			<tr>
				<td>
				</td>
			</tr>
			<tr>
				<td class="canvas_content" valign="top" id="contentcell" style="height: 500px;">
	<!-- current view -->

