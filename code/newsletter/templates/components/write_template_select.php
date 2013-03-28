<?PHP
/*
 * Project: yourportfolio / newsletter
 *
 * @author Christiaan Ottow
 * @created Dec 12, 2006
 */
?>
<table class="templateSelectTable" cellspacing="0" cellpadding="0" align="center">
<tr>
<td>

<div class="templateSelect">
<?PHP
	
	if( isset($data['letter']) )
	{
		$active_template = $data['letter']['template_id'];
	} else {
		$active_template = 1;
	}
	
	if( isset($data['templates'] ) && !empty($data['templates']) )
	{
		foreach( $data['templates'] as $t )
		{
			$class = ($t['template_id'] == $active_template ? 'templateActive' : 'templatePreview');
			
			?>
			<div id="template_<?=$t['template_id']?>" class="<?=$class?>" onclick="selectTemplate(<?=$t['template_id']?>, this)" onmouseover="this.style.cursor = 'pointer'" onmouseout="this.style.cursor = ''" >
				<img src="design/img/newsletter/<?=$t['template_id']?>.jpg" width="75"/><br/>
				<?=$t['name']?><br/>
				<?=$t['created']?><br/>
			</div>
			<?PHP
			if( $class == 'templateActive' )
			{
				?><script type="text/javascript">
				selected_template = $('template_<?=$t['template_id']?>');
				</script><?PHP
			}
		}
	}
?>

</div>

</td>
</tr>
</table>
