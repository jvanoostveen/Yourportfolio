<?PHP
/*
 * Project: yourportfolio / newsletter
 *
 * @author Christiaan Ottow
 * @created Dec 5, 2006
 */
?>
<div id="search" style="float: right;">
	<?=_('zoek')?>: &nbsp;&nbsp;<input type="search" name="param" id="search_param" onfocus="checkFocus('search')" onblur="checkBlur('search')"  onkeyup="doSearch()" accesskey="f" class="search">
	<input type="hidden" name="filter" id="search_filter" value="<?=$data['filter']?>">
	<span id="search_reset" style="visibility: hidden;">
	<a href="#" onclick="resetSearch()">
		<img src="<?=IMAGES?>spacer.gif" width="12" id="search_reset" border="0">
	</a>
	</span>
</div>
