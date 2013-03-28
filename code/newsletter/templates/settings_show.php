<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created May 8, 2007
 */
?>
<center><h3><?=gettext('Nieuwsbrief settings')?></h3></center>

<form name="settings" action="newsletter_settings.php" method="post" id="settingsForm">
<input type="hidden" name="case" value="save">

<table class="settings" align="center" cellpadding="3" cellspacing="0">

	<tr>
		<th><b><?=gettext('Instelling')?></b></th>
		<th><b><?=gettext('Waarde')?></b></th>
	</tr>
	<?PHP
		foreach( array_keys($data['settings']) as $key )
		{
			$s['name'] =  $key;
			$s['value'] = $data['settings'][$key]['value'];
			$s['type'] = $data['settings'][$key]['type'];
			
			?><tr>
				<td><?=$s['name']?></td>
				<td>
				<?PHP
					if( substr($s['type'],0,strlen('enum')) == 'enum') 
					{
						$subtype = 'enum';
					} else {
						$subtype = $s['type'];
					}
					
					switch( $subtype )
					{
						case 'enum':
							$rest = substr($s['type'], strlen('enum')+1, strlen($s['type']));
							$values = explode(',', $rest);
							?><select name="settings[<?=$s['name']?>]">
								<?PHP
								foreach($values as $val )
								{
									?><option value="<?=$val?>"<?=($s['value']==$val?' selected':'')?>><?=$val?></option>
									<?PHP
								}
								?></select>
								<?PHP
							break;
						case 'host':
						case 'email':
						case 'string':
						case 'integer':
						default:
							?><input type="text" size="20" name="settings[<?=$s['name']?>]" value="<?=$s['value']?>"/>
							<?PHP
							break;
					}
				?>
				</td>
			</tr>
			<?PHP
		}
	?>
</table>

</form>
