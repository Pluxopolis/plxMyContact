<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	$plxPlugin->setParam('mnuDisplay', $_POST['mnuDisplay'], 'numeric');
	$plxPlugin->setParam('mnuName', $_POST['mnuName'], 'string');
	$plxPlugin->setParam('mnuPos', $_POST['mnuPos'], 'numeric');
	$plxPlugin->setParam('mnuText', $_POST['mnuText'], 'string');	
	$plxPlugin->setParam('email', $_POST['email'], 'string');
	$plxPlugin->setParam('email_cc', $_POST['email_cc'], 'string');
	$plxPlugin->setParam('email_bcc', $_POST['email_bcc'], 'string');
	$plxPlugin->setParam('subject', $_POST['subject'], 'string');
	$plxPlugin->setParam('thankyou', $_POST['thankyou'], 'string');
	$plxPlugin->setParam('template', $_POST['template'], 'string');
	$plxPlugin->setParam('captcha', $_POST['captcha'], 'numeric');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxMyContact');
	exit;
}
$mnuDisplay =  $plxPlugin->getParam('mnuDisplay')=='' ? 1 : $plxPlugin->getParam('mnuDisplay');
$mnuName =  $plxPlugin->getParam('mnuName')=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_NAME') : $plxPlugin->getParam('mnuName');
$mnuPos =  $plxPlugin->getParam('mnuPos')=='' ? 2 : $plxPlugin->getParam('mnuPos');
$mnuText =  $plxPlugin->getParam('mnuText')=='' ? '' : $plxPlugin->getParam('mnuText');
$email = $plxPlugin->getParam('email')=='' ? '' : $plxPlugin->getParam('email');
$email_cc = $plxPlugin->getParam('email_cc')=='' ? '' : $plxPlugin->getParam('email_cc');
$email_bcc = $plxPlugin->getParam('email_bcc')=='' ? '' : $plxPlugin->getParam('email_bcc');
$subject = $plxPlugin->getParam('subject')=='' ? $plxPlugin->getLang('L_DEFAULT_OBJECT') : $plxPlugin->getParam('subject');
$thankyou = $plxPlugin->getParam('thankyou')=='' ? $plxPlugin->getLang('L_DEFAULT_THANKYOU') : $plxPlugin->getParam('thankyou');
$template = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');
$captcha = $plxPlugin->getParam('captcha')=='' ? '1' : $plxPlugin->getParam('captcha');

# On récupère les templates des pages statiques
$files = plxGlob::getInstance(PLX_ROOT.'themes/'.$plxAdmin->aConf['style']);
if ($array = $files->query('/^static(-[a-z0-9-_]+)?.php$/')) {
	foreach($array as $k=>$v)
		$aTemplates[$v] = $v;
}

?>

<h2><?php echo $plxPlugin->getInfo('title') ?></h2>
<?php
if(function_exists('mail')) {
	echo '<p style="color:green"><strong>'.$plxPlugin->getLang('L_MAIL_AVAILABLE').'</strong></p>';
} else {
	echo '<p style="color:#ff0000"><strong>'.$plxPlugin->getLang('L_MAIL_NOT_AVAILABLE').'</strong></p>';
}
?>
<br />
<form id="form_plxmycontact" action="parametres_plugin.php?p=plxMyContact" method="post">
	<fieldset>
		<p class="field"><label for="id_mnuDisplay"><?php echo $plxPlugin->lang('L_MENU_DISPLAY') ?>&nbsp;:</label></p>
		<?php plxUtils::printSelect('mnuDisplay',array('1'=>L_YES,'0'=>L_NO),$mnuDisplay); ?>
		<p class="field"><label for="id_mnuName"><?php $plxPlugin->lang('L_MENU_TITLE') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('mnuName',$mnuName,'text','20-20') ?>
		<p class="field"><label for="id_mnuPos"><?php $plxPlugin->lang('L_MENU_POS') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('mnuPos',$mnuPos,'text','2-5') ?>
		<p class="field"><label for="id_mnuText"><?php $plxPlugin->lang('L_MENU_TEXT') ?>&nbsp;:</label></p>
		<?php plxUtils::printArea('mnuText',$mnuText,'text','5-10') ?>
		<p class="field"><label for="id_email"><?php $plxPlugin->lang('L_EMAIL') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('email',$email,'text','50-120') ?>
		<p class="field"><label for="id_email_cc"><?php $plxPlugin->lang('L_EMAIL_CC') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('email_cc',$email_cc,'text','50-120') ?>
		<p class="field"><label for="id_email_bcc"><?php $plxPlugin->lang('L_EMAIL_BCC') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('email_bcc',$email_bcc,'text','50-120') ?>
		<p class="field"><label for="id_subject"><?php $plxPlugin->lang('L_EMAIL_SUBJECT') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('subject',$subject,'text','100-120') ?>
		<p class="field"><label for="id_thankyou"><?php $plxPlugin->lang('L_THANKYOU_MESSAGE') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('thankyou',$thankyou,'text','100-120') ?>
		<p class="field"><label for="id_captcha"><?php echo $plxPlugin->lang('L_CAPTCHA') ?>&nbsp;:</label></p>
		<?php plxUtils::printSelect('captcha',array('1'=>L_YES,'0'=>L_NO),$captcha); ?>
		<p class="field"><label for="id_template"><?php $plxPlugin->lang('L_TEMPLATE') ?>&nbsp;:</label></p>
		<?php plxUtils::printSelect('template', $aTemplates, $template) ?>
		<p>
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
		</p>
		<p><?php $plxPlugin->lang('L_COMMA') ?></p>
	</fieldset>
</form>
