
<!--
### DEBUG INFO ###
number of queries: <?=$db->queries."\n"?>

performed queries:
<? foreach($db->queries_full as $query) : /* start query loop */ ?>
<?=$query."\n"?>
<? endforeach; /* end query loop */ ?>

outer template: <?=$canvas->template.".php"?> 
inner template: <?=$canvas->inner_template.".php"?> 

run time: <?=$run_time." seconds\n"?>

<? if (function_exists('memory_get_usage')) : ?>
memory current usage: <?=(memory_get_usage() / pow(1024, 2))?>MB
<? endif; ?>
<? if (function_exists('memory_get_peak_usage')) : ?>
memory peak usage: <?=(memory_get_peak_usage() / pow(1024, 2))?>MB 
<? endif; ?>

browser: <?=$_SERVER['HTTP_USER_AGENT']?> 
### END DEBUG INFO ###
-->
