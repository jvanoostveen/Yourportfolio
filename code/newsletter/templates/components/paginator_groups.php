<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created Feb 3, 2007
 */
?>

<div id="paginator">
<?PHP

if( isset($data['params'] ) && !empty($data['params']) )
{
	$params = '&'.$data['params'];
} else {
	$params = '';
}
$threshold = 15;

$inactive_page = "<a href=\"#\" onclick=\"showPage(%d,'%s');\" class=\"paginator\">%d</a>";
$active_page = "<span class=\"active_page\">%d</span>";
  
if( isset($data['num_pages'])&& $data['num_pages'] > 1 && $data['num_pages'] <= $threshold )
{
	for( $i=1; $i<=$data['num_pages']; $i++)
	{
		if( $data['page'] != $i )
		{
			echo sprintf($inactive_page, $i, $params,$i);
		} else {
			echo sprintf($active_page, $i);
		}
		echo '&nbsp;';
	}
} else if( $data['num_pages'] > $threshold ) {
	for( $i=1; $i<=3; $i++ )
	{
		if( $data['page'] != $i )
		{
			echo sprintf($inactive_page, $i, $params,$i);
		} else {
			echo sprintf($active_page, $i);
		}
		echo '&nbsp;';
	}
	
	echo '...&nbsp;';

	if( $data['page'] <= 3 )
	{
		$start = 4;
		$end = 6;
	} else if( $data['page'] >= $data['num_pages']-2 ) {
		$start = $data['num_pages'] - 5;
		$end = $data['num_pages'] - 3;
	} else {
		$start = $data['page'] - 1;
		$end = $data['page'] + 1;
	}
	
	for( $i=$start; $i <= $end; $i++ )
	{
		if( $i > 3 && $i < ($data['num_pages']-2) )
		{
			if( $data['page'] != $i )
			{
				echo sprintf($inactive_page, $i, $params,$i);
			} else {
				echo sprintf($active_page, $i);
			}
			echo '&nbsp;';
		}
	}
	
	echo '...&nbsp;';
	
	for( $i=0; $i<3; $i++)
	{
		$p = $data['num_pages'] - (2 - $i );
		
		if( $data['page'] != $p )
		{
			echo sprintf($inactive_page, $p, $params,$p);
		} else {
			echo sprintf($active_page, $p);
		}
		echo '&nbsp;';
	}			
}

?>
</div>