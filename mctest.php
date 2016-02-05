<?php
/**************************************************************************\
||========================================================================||
|| Minecraft Connect ||
|| Copyright 2016 ||
|| Version 0.3 ||
|| Made by fizz on the official MyBB board ||
|| http://community.mybb.com/user-36020.html ||
|| https://github.com/squez/Minecraft-Connect/ ||
|| I don't take responsibility for any errors caused by this plugin. ||
|| Always keep MyBB up to date and always keep this plugin up to date. ||
|| You may NOT redistribute this plugin, sell it, ||
|| remove copyrights, or claim it as your own in any way. ||
||========================================================================||
\*************************************************************************/

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'mctest.php');

require_once("./global.php");

#$lang->load("safelink");

// Add link in breadcrumb
add_breadcrumb('Minecraft Connect', "mctest.php");
if($mybb->settings['mcc_enabled'] != 1)
{
	$error = 'disabled';
	eval("\$minecraftconnect = \"".$templates->get("mcc_main")."\";");
	output_page($minecraftconnect);
	exit;
}

if($mybb->get_input('act') == 'login')
{
	if($mybb->request_method == 'post')
	{
		verify_post_check($mybb->get_input('my_post_key'));

		require('inc/plugins/MinecraftConnect/MCAuth.class.php');

		$username = $db->escape_string(trim($mybb->get_input('mcusername')));
		$pass = $db->escape_string($mybb->get_input('mcpassword'));
		$mc = new MCAuth($username);
		if($mc->validateInput())
		{
			// Authenticate the user with Mojang's API
			$auth = $mc->authenticate($username, $pass);
			if($auth == true)
			{
				$mcuser = $mc->getUsername();
				// if user authenticated, log them in to MyBB
				if($mc->login($mcuser))
					redirect('index.php', $lang->sprintf($lang->mcc_login_success, $mcuser));
				else
					redirect('mctest.php?act=login', $lang->mcc_login_fail);
			}
			else
				$error = $mc->getErr();
		}
		else
			$error = $mc->getErr();
	}
}

eval("\$minecraftconnect = \"".$templates->get("mcc_main")."\";");

output_page($minecraftconnect);

exit;