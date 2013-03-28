<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created Mar 26, 2007
 */

$link = '';

if(isset($data['group']) ) 
{
	$link = '&group='.$data['group'][0]['group_id'];
}

if( isset($data['filter']) )
{
	$link .= '&filter='.$data['filter'];
}

?>
<div style="float: right;"><a href="newsletter_download.php?page=<?=$data['page']?><?=$link?>"><img height="border="0" height="20" src="design/iconsets/default/item_download.gif" alt="<?=_('Download als CSV bestand')?>" /></a></div>
