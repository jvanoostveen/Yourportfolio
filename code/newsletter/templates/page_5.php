<script type="text/javascript">

var root_url = '<?=dirname($_SERVER['SCRIPT_NAME'])?>';

</script>

<DIV id="menu">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="30"><img src="<?=$canvas->showIcon('menu_header')?>" width="30" height="28" class="special"></td>
		<td class="namebar" valign="middle">
		<!-- photographer name -->
		<a href="http://<?=DOMAIN?><?=(SUB_DOMAIN) ? '/'.SUB_DOMAIN : ''?>" target="_blank" class="fg_white txt_no_underline"><?=$canvas->filter($yourportfolio->photographer_name)?></a>
		<!-- end photographer name -->
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" style="border-left: 1px solid black;"><div id="menu_holder"><div id="menu_content">
	<!-- menu -->
	<? require(NL_TEMPLATES . 'menu.php'); ?>
</div></div>

<? if (!$system->isIE()) : ?>
<script>
var tablepage = false;
resize();
</script>
<? endif; ?>
	
	</td>
</tr>
<tr>
	<td width="1" height="1" class="horizontalline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" border="0" class="special"></td>
</tr>
</table>
</DIV>


<DIV id="canvas_top">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="1" height="28"><img src="design/img/white_spacer.gif" width="1" height="28" alt="spacer"></td>
		<td class="namebar" width="300">
			<img src="<?=$settings['page_icon']?>" alt="<?=$settings['page_title']?>" align="absmiddle" width="31" height="28" border="0"><a class="fg_white txt_no_underline"><?=$settings['page_title']?></a>
		</td>
		<td class="namebar" align="left">
			<span class="small" id="total_count"><?PHP if ( isset($settings['page_center_title'])) : echo $settings['page_center_title']; endif;?></span>
		</td>
		<td class="namebar" align="right">
		<?PHP
			if( isset( $components['topBar'] ) )
			{
				if( is_array($components['topBar']) && count($components['topBar']) > 0)
				{
					foreach( $components['topBar'] as $t )
					{
						require(NL_TEMPLATES.'components/'.$t);
					}
				} else if( !is_array($components['topBar']) ){
					require(NL_TEMPLATES.'components/'.$components['topBar']);
				}
			}
		?>&nbsp;
		</td>
		<td width="22" class="namebar" align="right"><img src="design/img/sync-white.gif" width="16" height="16" id="saving1" style="visibility: hidden;"></td>
		<td width="10"><img src="design/img/black_spacer.gif" width="10" height="28" class="special" class="special"></td>
		<td width="4" valign="top"><img src="design/img/round_right.gif" width="4" height="28" class="special"></td>
		<td width="1" valign="top"><img src="design/img/round_row.gif" width="1" height="28" class="special"></td>
	</tr>
	</table>
</DIV>

<DIV id="canvas_div_nl<?=($page_name == 'addresses/edit' ? '_addresses' : '')?>">
		<!-- current view -->



