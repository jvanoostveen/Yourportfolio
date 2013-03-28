<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * preferences template
 *
 * @package yourportfolio
 * @subpackage PageHtml
 */
?>
<?=$canvas->filter($yourportfolio->feedback)?>

<form action="<?=$system->thisFile()?>" method="POST" name="theForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="admin">

<input type="hidden" name="adminForm[action]" value="advancedsettings_save">

<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="150" nowrap align="right">&nbsp;</td>
	<td width="100%">&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><b><?=gettext('yourportfolio voorkeuren')?></b></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[text_nodes]" value="0">
	<input type="checkbox" name="adminForm[text_nodes]" id="text_nodes" value="1"<?=($yourportfolio->settings['text_nodes']) ? ' checked' :''?>><label for="text_nodes"> <?=gettext('kan tekst nodes gebruiken')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[internal_links]" value="0">
	<input type="checkbox" name="adminForm[internal_links]" id="internal_links" value="1"<?=($yourportfolio->settings['internal_links']) ? ' checked' :''?>><label for="internal_links"> <?=gettext('kan interne links gebruiken')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[autopublish]" value="0">
	<input type="checkbox" name="adminForm[autopublish]" id="autopublish" value="1"<?=($yourportfolio->settings['autopublish']) ? ' checked' :''?>><label for="autopublish"> <?=gettext('auto-publish')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[moving_sets_position_to_one]" value="0">
	<input type="checkbox" name="adminForm[moving_sets_position_to_one]" id="moving_sets_position_to_one" value="1"<?=($yourportfolio->settings['moving_sets_position_to_one']) ? ' checked' :''?>><label for="moving_sets_position_to_one"> <?=gettext('verplaatsen node zet positie op 1')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><b><?=gettext('frontend')?></b></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<?=gettext('achtergrond kleur')?>: #<input type="text" name="adminForm[bg_colour]" id="bg_colour" value="<?=$canvas->edit_filter($yourportfolio->preferences['bg_colour'])?>" size="6" maxlength="6">
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[quicktime_check]" value="0">
	<input type="checkbox" name="adminForm[quicktime_check]" id="quicktime_check" value="1"<?=($yourportfolio->settings['quicktime_check']) ? ' checked' :''?>><label for="quicktime_check"> <?=gettext('controleer QuickTime versie')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[html_only]" value="0">
	<input type="checkbox" name="adminForm[html_only]" id="html_only" value="1"<?=($yourportfolio->settings['html_only']) ? ' checked' :''?>><label for="html_only"> <?=gettext('HTML site')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[mobile]" value="0">
	<input type="checkbox" name="adminForm[mobile]" id="mobile" value="1"<?=($yourportfolio->settings['mobile']) ? ' checked' :''?>><label for="mobile"> <?=gettext('Alternatieve mobile site')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[tablet]" value="0">
	<input type="checkbox" name="adminForm[tablet]" id="tablet" value="1"<?=($yourportfolio->settings['tablet']) ? ' checked' :''?>><label for="tablet"> <?=gettext('Alternatieve tablet site')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><b><?=gettext('XML & AMFPHP voorkeuren')?></b></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[xml_amf_hybrid]" value="0">
	<input type="checkbox" name="adminForm[xml_amf_hybrid]" id="xml_amf_hybrid" value="1"<?=($yourportfolio->settings['xml_amf_hybrid']) ? ' checked' :''?>><label for="xml_amf_hybrid"> <?=gettext('XML / AMFPHP hybride')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[xml_filter_items]" value="0">
	<input type="checkbox" name="adminForm[xml_filter_items]" id="xml_filter_items" value="1"<?=($yourportfolio->settings['xml_filter_items']) ? ' checked' :''?>><label for="xml_filter_items"> <?=gettext('XML')?>: <?=gettext('geen items exporteren')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[xml_filter_news]" value="0">
	<input type="checkbox" name="adminForm[xml_filter_news]" id="xml_filter_news" value="1"<?=($yourportfolio->settings['xml_filter_news']) ? ' checked' :''?>><label for="xml_filter_news"> <?=gettext('XML')?>: <?=gettext('geen nieuws exporteren')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><b><?=gettext('RSS')?></b></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[rss_news_only]" value="0">
	<input type="checkbox" name="adminForm[rss_news_only]" id="rss_news_only" value="1"<?=($yourportfolio->settings['rss_news_only']) ? ' checked' :''?>><label for="rss_news_only"> <?=gettext('alleen nieuws tonen')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><b><?=gettext('social media')?></b></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><?php echo _('Facebook user IDs'); ?>: <input type="text" name="adminForm[facebook_user_ids]" id="facebook_user_ids" value="<?php echo $canvas->edit_filter($yourportfolio->preferences['facebook_user_ids']); ?>" size="30" maxlength="250"></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><?php echo _('Facebook Platform application ID'); ?>: <input type="text" name="adminForm[facebook_app_id]" id="facebook_app_id" value="<?php echo $canvas->edit_filter($yourportfolio->preferences['facebook_app_id']); ?>" size="15" maxlength="250"></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><b><?=gettext('site voorkeuren')?></b></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[subusers]" value="0">
	<input type="checkbox" name="adminForm[subusers]" id="subusers" value="1"<?=($yourportfolio->settings['subusers']) ? ' checked' :''?>><label for="subusers"> <?=gettext('kan subgebruikers aanmaken')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[newsletter]" value="0">
	<input type="checkbox" name="adminForm[newsletter]" id="newsletter" value="1"<?=($yourportfolio->settings['newsletter']) ? ' checked' :''?>><label for="newsletter"> <?=gettext('heeft nieuwsbrief module')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<?=gettext('Google Analytics account')?>: <input type="text" name="adminForm[google_analytics_account]" id="google_analytics_account" value="<?=$canvas->edit_filter($yourportfolio->preferences['google_analytics_account'])?>" size="20" maxlength="75">
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<?=gettext('Google site verificatie')?>: <input type="text" name="adminForm[google_site_verification]" id="google_site_verification" value="<?=$canvas->edit_filter($yourportfolio->preferences['google_site_verification'])?>" size="50" maxlength="75">
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><b><?=gettext('gastenboek')?></b></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[guestbook]" value="0">
	<input type="checkbox" name="adminForm[guestbook]" id="guestbook" value="1"<?=($yourportfolio->settings['guestbook']) ? ' checked' :''?>><label for="guestbook"> <?=gettext('ondersteuning voor gastenboek')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[guestbook_approval]" value="0">
	<input type="checkbox" name="adminForm[guestbook_approval]" id="guestbook_approval" value="1"<?=($yourportfolio->settings['guestbook_approval']) ? ' checked' :''?>><label for="guestbook_approval"> <?=gettext('automatisch goedkeuren nieuwe gastenboek berichten')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><b><?=gettext('gebruikers voorkeuren management')?></b></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[can_add_albums]" value="0">
	<input type="checkbox" name="adminForm[can_add_albums]" id="can_add_albums" value="1"<?=($yourportfolio->settings['can_add_albums']) ? ' checked' :''?>><label for="can_add_albums"> <?=gettext('kan albums toevoegen')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[can_edit_types]" value="0">
	<input type="checkbox" name="adminForm[can_edit_types]" id="can_edit_types" value="1"<?=($yourportfolio->settings['can_edit_types']) ? ' checked' :''?>><label for="can_edit_types"> <?=gettext('kan album types wijzigen')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[restricted_albums]" value="0">
	<input type="checkbox" name="adminForm[restricted_albums]" id="restricted_albums" value="1"<?=($yourportfolio->settings['restricted_albums']) ? ' checked' :''?>><label for="restricted_albums"> <?=gettext('kan beveiligde albums/gebruikers aanmaken')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[unassigned_restricted_albums_for_all]" value="0">
	<input type="checkbox" name="adminForm[unassigned_restricted_albums_for_all]" id="unassigned_restricted_albums_for_all" value="1"<?=($yourportfolio->settings['unassigned_restricted_albums_for_all']) ? ' checked' :''?>><label for="unassigned_restricted_albums_for_all"> <?=gettext('beveiligde albums zonder user voor alle users')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><b><?=gettext('opties')?></b></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[tags]" value="0">
	<input type="checkbox" name="adminForm[tags]" id="tags" value="1"<?=($yourportfolio->settings['tags']) ? ' checked' :''?>><label for="tags"> <?=gettext('kan tags gebruiken')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><b><?=gettext('wijzig scherm weergave')?></b></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[sections_have_subname]" value="0">
	<input type="checkbox" name="adminForm[sections_have_subname]" id="sections_have_subname" value="1"<?=($yourportfolio->settings['sections_have_subname']) ? ' checked' :''?>><label for="sections_have_subname"> <?=gettext('sections hebben een subtitel')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[items_have_subname]" value="0">
	<input type="checkbox" name="adminForm[items_have_subname]" id="items_have_subname" value="1"<?=($yourportfolio->settings['items_have_subname']) ? ' checked' :''?>><label for="items_have_subname"> <?=gettext('items hebben een subtitel')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[news_templates]" value="0">
	<input type="checkbox" name="adminForm[news_templates]" id="news_templates" value="1"<?=($yourportfolio->settings['news_templates']) ? ' checked' :''?>><label for="news_templates"> <?=gettext('kan nieuws templates gebruiken')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<input type="hidden" name="adminForm[has_custom_fields]" value="0">
	<input type="checkbox" name="adminForm[has_custom_fields]" id="has_custom_fields" value="1"<?=($yourportfolio->settings['has_custom_fields']) ? ' checked' :''?>><label for="has_custom_fields"> <?=gettext('heeft eigen velden')?></label>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td><textarea name="adminForm[custom_fields]" cols="60" rows="7"><?=$canvas->edit_filter($yourportfolio->preferences['custom_fields'])?></textarea></td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
</form>