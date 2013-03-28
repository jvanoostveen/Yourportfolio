
<!--
### DEBUG INFO ###
number of queries: <?=$db->queries."\n"?>

performed queries:
<? foreach($db->queries_full as $query) : /* start query loop */ ?>
<?=$query."\n"?>
<? endforeach; /* end query loop */ ?>

outer template: <?=$canvas->template.'.php'?> 
inner template: <?=$canvas->inner_template.'.php'?> 

run time: <?=$run_time." seconds\n"?>

browser: <?=$_SERVER['HTTP_USER_AGENT']?> 
### END DEBUG INFO ###
-->
