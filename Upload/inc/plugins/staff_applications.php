<?php
/*
 * MyBB: Staff Applications
 *
 * File: staff_applications.php
 *
 * Authors: Callofgfx & updated by Vintagedaddyo
 *
 * MyBB Version: 1.8
 *
 * Plugin Version: 1.1
 *
 */
 
// Disallow direct access to this file for security reasons

if(!defined("IN_MYBB"))
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");

// hooks

$plugins->add_hook('global_end', 'staff_applications_page');
$plugins->add_hook('global_start', 'staff_applications_index');

$plugins->add_hook('admin_load', 'staff_applications_admin');
$plugins->add_hook('admin_tools_menu', 'staff_applications_admin_tools_menu');
$plugins->add_hook('admin_tools_action_handler', 'staff_applications_admin_tools_action_handler');
$plugins->add_hook('admin_tools_permissions', 'staff_applications_admin_permissions');

$plugins->add_hook('admin_home_menu', 'staff_applications_admin_home_menu');

// plugin info

function staff_applications_info()
{
	global $db, $lang;

	 $lang->load('staff_applications');

    $lang->staff_applications_info_description = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="AZE6ZNZPBPVUL">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $db->escape_string($lang->staff_applications_info_description);	  
		
	return array(
		"name"			=> $lang->staff_applications_info_name,
		"description"	=> $lang->staff_applications_info_description,
		"website"		=> $lang->staff_applications_info_website,
		"author"		=> $lang->staff_applications_info_author,
		"authorsite"	=> $lang->staff_applications_info_authorsite,
		"version"		=> "1.1",
		"guid" 			=> "3ecf053ec78d876e5e717ec830d8e508",
		"compatibility" => "18*"
	);
}

// plugin activate

function staff_applications_activate()
{
	global $mybb, $db, $lang;
	 $lang->load('staff_applications');
	
	// create settings group

	$insertarray = array(
		'name' => 'staff_applications', 
		'title' => $lang->staff_applications_settinggroup_title, 
		'description' => $lang->staff_applications_settinggroup_description, 
		'disporder' => 100, 
		'isdefault' => 0
	);

	$gid = $db->insert_query("settinggroups", $insertarray);
	
	// setting 1

	$insertarray = array(
		'name' => 'staff_applications_groups',
		'title' => $lang->staff_applications_setting_1_title,
		'description' => $lang->staff_applications_setting_1_title,
		'optionscode' => 'text',
		'value' => '0',
		'disporder' => 1,
		'gid' => $gid
	);

	$db->insert_query("settings", $insertarray);
	
	// setting 2

	$insertarray = array(
		'name' => 'staff_applications_staffgroups',
		'title' => $lang->staff_applications_setting_2_title,
		'description' => $lang->staff_applications_setting_2_description,
		'optionscode' => 'text',
		'value' => '6,3,4',
		'disporder' => 2,
		'gid' => $gid
	);

	$db->insert_query("settings", $insertarray);
	
	// setting 3

	$insertarray = array(
		'name' => 'staff_applications_users',
		'title' => $lang->staff_applications_setting_3_title,
		'description' => $lang->staff_applications_setting_3_description,
		'optionscode' => 'text',
		'value' => '1',
		'disporder' => 3,
		'gid' => $gid
	);

	$db->insert_query("settings", $insertarray);
	
	// template

	$templatearray = array(
		"tid" => "0",
		"title" => "staff_applications_page",
		"template" => $db->escape_string('
<html>
<head>
<title>{$title}</title>
{$headerinclude}
</head>
<body>
{$header}
<br />
<form action="index.php?action=staff_applications" method="post">
<input type="hidden" name="postcode" value="{$mybb->post_code}">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><span class="smalltext"><strong>{$title}</strong></span></td>
</tr>
<tr>
<td class="trow1" width="50%" align="left">
<fieldset class="trow2" style="height: 130px;">
<legend><strong>{$lang->staff_applications_staff_group}</strong></legend>
	<select id="groups" name="groups">
		{$staff_groups}
	</select>
<br />
<br />
<span class="smalltext">{$lang->staff_applications_staff_group_desc}</span>
</fieldset>
</td>
<td class="trow1" width="50%" align="left">
<fieldset class="trow2" style="height: 130px;">
<legend><strong>{$lang->staff_applications_staff_reason}</strong></legend>
<span class="smalltext">{$lang->staff_applications_staff_reason_description}</span>
<br />
<textarea name="reason" id="reason" rows="2" cols="50" tabindex="2">
</textarea>
<br />
<span class="smalltext">{$lang->staff_applications_staff_reason_desc}</span>
<br />
</fieldset>
</td>
</tr>
<tr>
<td class="trow2" colspan="2" align="center"><span class="smalltext"><strong>{$lang->staff_applications_info}</strong></span></td>
</tr>
<tr>
<td class="tfoot" colspan="2" align="center"><input type="submit" class="button" name="apply" value="{$lang->staff_applications_apply_for}"></td>
</tr>
</table>
</form>
{$footer}
</body>
</html>
'),
		"sid" => "-1",
		);
	$db->insert_query("templates", $templatearray);
	
	// create table 'staff_applications'

	$db->write_query("CREATE TABLE `".TABLE_PREFIX."staff_applications` (
	  `aid` int(10) NOT NULL auto_increment,
	  `gid` smallint(5) UNSIGNED NOT NULL,
	  `uid` bigint(30) UNSIGNED NOT NULL default 0,
	  `username` varchar(100) NOT NULL default '',
	  `reason` text NOT NULL default '',
	  `date` bigint(30) UNSIGNED NOT NULL default 0,
	  PRIMARY KEY  (`aid`)
		) ENGINE=MyISAM");
	
	rebuild_settings();
	
	// edit templates

	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';

	find_replace_templatesets('header', '#'.preg_quote('{$lang->toplinks_help}</a></li>').'#', "{\$lang->toplinks_help}</a></li><li class=\"staff_app\"><a href=\"{\$mybb->settings['bburl']}/index.php?action=staff_applications\" class=\"staff_app\" style=\"background: url({\$mybb->settings['bburl']}/images/staff_applications.png) no-repeat;\" border=\"0\" alt=\"\" />{\$lang->staff_applications}</a></li>");
}

// plugin deactivate 

function staff_applications_deactivate()
{
	global $mybb, $db, $lang;
	
	$db->delete_query('templates', 'title IN (\'staff_applications_page\') AND sid=\'-1\'');
	
	// Delete all settings

	$db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN(
		'staff_applications_groups',
		'staff_applications_staffgroups'
	)");
	
	// Delete settings group

	$db->delete_query("settinggroups", "name = 'staff_applications'");
	
	// drop table 'staff_applications' if exists

	if($db->table_exists('staff_applications'))
	{ 
		$db->drop_table('staff_applications');
	}
	
	// remove edits

	require_once MYBB_ROOT."inc/adminfunctions_templates.php";

	find_replace_templatesets("header", '#'.preg_quote('<li class="staff_app"><a href="{$mybb->settings[\'bburl\']}/index.php?action=staff_applications" class="staff_app" style="background: url({$mybb->settings[\'bburl\']}/images/staff_applications.png) no-repeat;" border="0" alt="" />{$lang->staff_applications}</a></li>').'#', '', 0);
}

// index lang

function staff_applications_index()
{
	global $lang;
	
	$lang->load('staff_applications');
}

// page

function staff_applications_page()
{
	global $mybb, $lang, $cache, $db, $templates, $staff_groups, $header, $footer, $headerinclude, $title, $theme;
	
	if ($mybb->input['action'] != 'staff_applications')
		return;
		
	$accepted_groups = explode(",", $mybb->settings['staff_applications_groups']);
	$usergroups = array_merge((array)$mybb->user['usergroup'], (array)$mybb->user['additionalgroups']);

	if (count(array_intersect($usergroups, $accepted_groups)) == 0 && $mybb->settings['staff_applications_groups'] != 0) {
		error_no_permission();
	}
	
	$lang->load('staff_applications');
	
	if ($mybb->request_method == 'post')
	{
		if ($mybb->user['uid'] <= 0)
			error();
		
		if (!verify_post_check($mybb->input['postcode'], true))
			error_no_permission();
			
		if (!$mybb->input['reason'])
			error($lang->staff_applications_no_reason);
			
		// check if user group chosen exists

		$query = $db->simple_select('usergroups', 'title', 'gid='.intval($mybb->input['groups']));
		$group = $db->fetch_field($query, 'title');
		
		if (!$group || !in_array(intval($mybb->input['groups']), explode(',', $mybb->settings['staff_applications_staffgroups'])))
			error($lang->staff_applications_invalid_gid);
			
		// insert application in to applications table

		$db->insert_query('staff_applications', array(
			'uid' => intval($mybb->user['uid']),
			'username' => $db->escape_string($mybb->user['username']),
			'reason' => $db->escape_string($mybb->input['reason']),
			'gid' => intval($mybb->input['groups']),
			'date' => time()
		));
		
		// send private message
		
		// send pm

		require_once(MYBB_ROOT."/inc/datahandlers/pm.php");

		$pmhandler = new PMDataHandler();

		$subject = $lang->staff_applications_pm_new_title;
		$message = $lang->sprintf($lang->staff_applications_pm_new_message, $group);

		$pm = array(
			"subject" => $db->escape_string($subject),
			"message" => $db->escape_string($message),
			"icon" => 0,
			"fromid" => intval($mybb->user['uid']),
			"do" => '',
			"pmid" => ''
		);

		$pm['toid'] = explode(',',$mybb->settings['staff_applications_users']);

		$pm['options'] = array(
			"savecopy" => "no",
			"saveasdraft" => 0,
			"signature" => "no",
			"disablesmilies" => "no",
		);

		$pmhandler->admin_override = 1;
		$pmhandler->set_data($pm);

		if(!$pmhandler->validate_pm())
		{
			// error sending PM

			$error_pm = $lang->staff_applications_error_pm;
		}
		else
			$pminfo = $pmhandler->insert_pm();
		
		redirect('index.php?action=staff_applications', $lang->staff_applications_applied_message, $lang->staff_applications_applied_title);
	}
		
	$title = $lang->staff_applications;
	
	$staff_groups_ids = explode(',',$db->escape_string($mybb->settings['staff_applications_staffgroups']));
	$staff_groups_ids = implode('\',\'',$staff_groups_ids);
	
	$query = $db->simple_select('usergroups', '*', 'gid IN (\''.$staff_groups_ids.'\')');
	while($group = $db->fetch_array($query)) {
		$staff_groups .= "<option value=\"".intval($group['gid'])."\">".htmlspecialchars_uni($group['title'])."</option>";
	}
	
	eval("\$template = \"".$templates->get("staff_applications_page")."\";");
	
	output_page($template);
	
	exit;
}

// admin menu

function staff_applications_admin_home_menu(&$sub_menu)
{
	global $lang, $db;
	
	$lang->load("staff_applications", false, true);
	
	$unmanaged_applications = $db->fetch_field($db->simple_select("staff_applications", "COUNT(aid) as apps"), "apps");
	
	$sub_menu[] = array('id' => 'staff_applications', 'title' => $lang->sprintf($lang->staff_applications_index, $unmanaged_applications), 'link' => 'index.php?module=tools/staff_applications');
}

function staff_applications_admin_tools_menu(&$sub_menu)
{
	global $lang, $db;
	
	$lang->load("staff_applications", false, true);
	
	$unmanaged_applications = $db->fetch_field($db->simple_select("staff_applications", "COUNT(aid) as apps"), "apps");
	
	$sub_menu[] = array('id' => 'staff_applications', 'title' => $lang->sprintf($lang->staff_applications_index, $unmanaged_applications), 'link' => 'index.php?module=tools/staff_applications');
}

// handler

function staff_applications_admin_tools_action_handler(&$actions)
{
	$actions['staff_applications'] = array('active' => 'staff_applications', 'file' => 'staff_applications');
}

// perms

function staff_applications_admin_permissions(&$admin_permissions)
{
  	global $db, $mybb, $lang;
	
	$lang->load("staff_applications", false, true);
  
	$admin_permissions['staff_applications'] = $lang->staff_applications_canmanage;
}

// admin

function staff_applications_admin()
{
	global $db, $lang, $mybb, $page, $run_module, $action_file, $mybbadmin, $plugins;
	
	$lang->load("staff_applications", false, true);
	
	if($run_module == 'tools' && $action_file == 'staff_applications')
	{
		if (!$mybb->input['action']) {
			$page->add_breadcrumb_item($lang->staff_applications_home, 'index.php?module=tools/staff_applications');
			
			$page->output_header($lang->staff_applications_home);
			
			$sub_tabs['staff_applications_home'] = array(
				'title'			=> $lang->staff_applications_home,
				'link'			=> 'index.php?module=tools/staff_applications',
				'description'	=> $lang->staff_applications_home_description
			);
			
			$page->output_nav_tabs($sub_tabs, 'staff_applications_home');
			
			$per_page = 10;
			if($mybb->input['page'] && intval($mybb->input['page']) > 1)
			{
				$mybb->input['page'] = intval($mybb->input['page']);
				$start = ($mybb->input['page']*$per_page)-$per_page;
			}
			else
			{
				$mybb->input['page'] = 1;
				$start = 0;
			}
			
			$query = $db->simple_select("staff_applications", "COUNT(aid) as applications");
			$total_rows = $db->fetch_field($query, "applications");
		
			echo "<br />".draw_admin_pagination($mybb->input['page'], $per_page, $total_rows, "index.php?module=tools/staff_applications&amp;page={page}");
			
			$field = array();
			
			// table

			$table = new Table;
			$table->construct_header($lang->staff_applications_username, array('width' => '20%'));
			$table->construct_header($lang->staff_applications_group, array('width' => '20%'));
			$table->construct_header($lang->staff_applications_reason, array('width' => '20%'));
			$table->construct_header($lang->staff_applications_date, array('width' => '20%'));
			$table->construct_header($lang->staff_applications_approve, array('width' => '10%'));
			$table->construct_header($lang->staff_applications_unapprove, array('width' => '10%'));
			
			$query = $db->simple_select('staff_applications', '*', '', array('order_by' => 'date', 'order_dir' => 'DESC', 'limit' => "{$start}, {$per_page}"));
			while($g = $db->fetch_array($query)) {
				$group = $g;
				
				$query2 = $db->simple_select('usergroups', 'title', 'gid='.intval($group['gid']));
				$group_title = $db->fetch_field($query2, 'title');
				
				$table->construct_cell(htmlspecialchars_uni($group['username'])."<br /><small>".htmlspecialchars_uni($field['description'])."</small>");
				$table->construct_cell(htmlspecialchars_uni($group_title));
				$table->construct_cell(nl2br(htmlspecialchars_uni($group['reason'])));
				$table->construct_cell(my_date($mybb->settings['dateformat'], $group['date'], '', false).", ".my_date($mybb->settings['timeformat'], $group['date']));
				
				$form = new Form("index.php?module=tools/staff_applications&amp;action=approve", "post", 'staff_applications" onsubmit="return confirm(\''.staff_applications_jsspecialchars($lang->staff_applications_approve_confirm).'\');', 0, "", true);
				$html_data = $form->construct_return;
				$html_data .= $form->generate_hidden_field("aid", $group['aid']);
				$html_data .= "<input type=\"submit\" class=\"submit_button\" value=\"{$lang->staff_applications_approve}\" />";
				$html_data .= $form->end();
				
				$table->construct_cell($html_data, array('width' => '10%'));
				
				$form = new Form("index.php?module=tools/staff_applications&amp;action=unapprove", "post", 'staff_applications" onsubmit="return confirm(\''.staff_applications_jsspecialchars($lang->staff_applications_unapprove_confirm).'\');', 0, "", true);
				$html_data = $form->construct_return;
				$html_data .= $form->generate_hidden_field("aid", $group['aid']);
				$html_data .= "<input type=\"submit\" class=\"submit_button\" value=\"{$lang->staff_applications_unapprove}\" />";
				$html_data .= $form->end();
				
				$table->construct_cell($html_data, array('width' => '10%'));
				
				$table->construct_row();
			}
			
			if (!$group)
			{
				$table->construct_cell($lang->staff_applications_not_found, array('colspan' => 7));
				$table->construct_row();
			}
			
			$table->output($lang->staff_applications);
			
			$page->output_footer();
		}
		elseif ($mybb->input['action'] == 'approve') {
			
			if ((!($aid = intval($mybb->input['aid']))) || (!($application = $db->fetch_array($db->simple_select('staff_applications', '*', 'aid='.intval($mybb->input['aid']), array('limit' => 1))))))
			{
				flash_message($lang->staff_applications_invalid_aid, 'error');
				admin_redirect("index.php?module=tools/staff_applications");
			}
			if ($mybb->request_method == "post")
			{
				if(!isset($mybb->input['my_post_key']) || $mybb->post_code != $mybb->input['my_post_key'])
				{
					$mybb->request_method = "get";
					flash_message($lang->staff_applications_error, 'error');
					admin_redirect("index.php?module=tools/staff_applications");
				}
				
				$db->delete_query('staff_applications', 'aid='.$aid, 1);

				// join user to additional user group

				join_usergroup($application['uid'], $application['gid']);
				
				$lang->staff_applications_log_approved = $lang->sprintf($lang->staff_applications_log_approved, $application['username'], $application['gid']);
				
				log_admin_action($lang->staff_applications_log_approved);
				
				// get group title

				$group_title = $db->fetch_field($db->simple_select('usergroups', 'title', 'gid='.intval($application['gid'])), 'title');
				
				// send pm

				require_once(MYBB_ROOT."/inc/datahandlers/pm.php");

				$pmhandler = new PMDataHandler();
		
				$subject = $lang->sprintf($lang->staff_applications_pm_subject_approved, $group_title);
				$message = $lang->sprintf($lang->staff_applications_pm_message_approved, $group_title);
		
				$pm = array(
						"subject" => $db->escape_string($subject),
						"message" => $db->escape_string($message),
						"icon" => 0,
						"fromid" => intval($mybb->user['uid']),
						"do" => '',
						"pmid" => ''
				);
		
				$pm['toid'] = array($application['uid']);
		
				$pm['options'] = array(
					"savecopy" => "no",
					"saveasdraft" => 0,
					"signature" => "no",
					"disablesmilies" => "no",
				);
	
				$pmhandler->admin_override = 1;
				$pmhandler->set_data($pm);
	
				if(!$pmhandler->validate_pm())
				{
					// error sending PM

					$error_pm = $lang->staff_applications_error_pm;
				}
				else
					$pminfo = $pmhandler->insert_pm();
				
				flash_message($lang->staff_applications_approved, 'success');
				admin_redirect("index.php?module=tools/staff_applications");
			}
			else {
				$page->add_breadcrumb_item($lang->staff_applications_approve_application, 'index.php?module=tools/staff_applications');
		
				$page->output_header($lang->staff_applications_approve_application);
				
				$form = new Form("index.php?module=tools/staff_applications&amp;action=approve", 'post');
			
				echo $form->generate_hidden_field("aid", intval($mybb->input['aid']));
				
				echo "<div class=\"confirm_action\">\n";
				echo "<p>{$lang->staff_applications_approve_confirm}</p>\n";
				echo "<br />\n";
				echo "<p class=\"buttons\">\n";
				echo $form->generate_submit_button($lang->yes, array('class' => 'button_yes'));
				echo $form->generate_submit_button($lang->no, array("name" => "no", 'class' => 'button_no'));
				echo "</p>\n";
				echo "</div>\n";
				$form->end();
				
				$page->output_footer();
			}
		}
		elseif ($mybb->input['action'] == 'unapprove') {
			
			if ((!($aid = intval($mybb->input['aid']))) || (!($application = $db->fetch_array($db->simple_select('staff_applications', '*', 'aid='.intval($mybb->input['aid']), array('limit' => 1))))))
			{
				flash_message($lang->staff_applications_invalid_aid, 'error');
				admin_redirect("index.php?module=tools/staff_applications");
			}
			if ($mybb->request_method == "post")
			{
				if(!isset($mybb->input['my_post_key']) || $mybb->post_code != $mybb->input['my_post_key'])
				{
					$mybb->request_method = "get";
					flash_message($lang->staff_applications_error, 'error');
					admin_redirect("index.php?module=tools/staff_applications");
				}
				
				$db->delete_query('staff_applications', 'aid='.$aid, 1);
				
				// get group title

				$group_title = $db->fetch_field($db->simple_select('usergroups', 'title', 'gid='.intval($application['gid'])), 'title');
				
				// send pm

				require_once(MYBB_ROOT."/inc/datahandlers/pm.php");

				$pmhandler = new PMDataHandler();
		
				$subject = $lang->sprintf($lang->staff_applications_pm_subject_unapproved, $group_title);
				$message = $lang->sprintf($lang->staff_applications_pm_message_unapproved, $group_title);
		
				$pm = array(
						"subject" => $db->escape_string($subject),
						"message" => $db->escape_string($message),
						"icon" => 0,
						"fromid" => intval($mybb->user['uid']),
						"do" => '',
						"pmid" => ''
				);
		
				$pm['toid'] = array($application['uid']);
		
				$pm['options'] = array(
					"savecopy" => "no",
					"saveasdraft" => 0,
					"signature" => "no",
					"disablesmilies" => "no",
				);
	
				$pmhandler->admin_override = 1;
				$pmhandler->set_data($pm);
	
				if(!$pmhandler->validate_pm())
				{
					// error sending PM

					$error_pm = $lang->staff_applications_error_pm;
				}
				else
					$pminfo = $pmhandler->insert_pm();
				
				$lang->staff_applications_log_unapproved = $lang->sprintf($lang->staff_applications_log_unapproved, $application['username'], $application['gid']);
				
				log_admin_action($lang->staff_applications_log_unapproved);
				
				flash_message($lang->staff_applications_unapproved, 'success');
				admin_redirect("index.php?module=tools/staff_applications");
			}
			else {
				$page->add_breadcrumb_item($lang->staff_applications_unapprove_application, 'index.php?module=tools/staff_applications');
		
				$page->output_header($lang->staff_applications_unapprove_application);
				
				$form = new Form("index.php?module=tools/staff_applications&amp;action=unapprove", 'post');
			
				echo $form->generate_hidden_field("aid", intval($mybb->input['aid']));
				
				echo "<div class=\"confirm_action\">\n";
				echo "<p>{$lang->staff_applications_unapprove_confirm}</p>\n";
				echo "<br />\n";
				echo "<p class=\"buttons\">\n";
				echo $form->generate_submit_button($lang->yes, array('class' => 'button_yes'));
				echo $form->generate_submit_button($lang->no, array("name" => "no", 'class' => 'button_no'));
				echo "</p>\n";
				echo "</div>\n";
				$form->end();
				
				$page->output_footer();
			}
		}
	}
}

/**
 * Somewhat like htmlspecialchars_uni but for JavaScript strings
 * 
 * @param string: The string to be parsed
 * @return string: Javascript compatible string
 */

function staff_applications_jsspecialchars($str)
{
	// Converts & -> &amp; allowing Unicode

	// Parses out HTML comments as the XHTML validator doesn't seem to like them
	
	$string = preg_replace(array("#\<\!--.*?--\>#", "#&(?!\#[0-9]+;)#"), array('','&amp;'), $str);
	return strtr($string, array("\n" => '\n', "\r" => '\r', '\\' => '\\\\', '"' => '\x22', "'" => '\x27', '<' => '&lt;', '>' => '&gt;'));
}

?>