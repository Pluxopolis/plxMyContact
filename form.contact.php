<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# récuperation d'une instance de plxShow
$plxShow = plxShow::getInstance();
$plxShow->plxMotor->plxCapcha = new plxCapcha();
$plxPlugin = $plxShow->plxMotor->plxPlugins->getInstance('plxMyContact');

$error=false;
$success=false;

$captcha = $plxPlugin->getParam('captcha')=='' ? '1' : $plxPlugin->getParam('captcha');

if(!empty($_POST)) {
	$name=plxUtils::unSlash($_POST['name']);
	$mail=plxUtils::unSlash($_POST['mail']);
	$content=plxUtils::unSlash($_POST['content']);
	if(trim($name)=='')
		$error = $plxPlugin->getLang('L_ERR_NAME');
	elseif(!plxUtils::checkMail($mail))
		$error = $plxPlugin->getLang('L_ERR_EMAIL');
	elseif(trim($content)=='')
		$error = $plxPlugin->getLang('L_ERR_CONTENT');
	elseif($captcha AND $_POST['rep2'] != sha1($_POST['rep']))
		$error = $plxPlugin->getLang('L_ERR_ANTISPAM');
	if(!$error) {
		if(plxUtils::sendMail($name,$mail,$plxPlugin->getParam('email'),$plxPlugin->getParam('subject'),$content,'text',$plxPlugin->getParam('email_cc'),$plxPlugin->getParam('email_bcc')))
			$success = $plxPlugin->getParam('thankyou_'.$plxPlugin->default_lang);
		else
			$error = $plxPlugin->getLang('L_ERR_SENDMAIL');
	}
} else {
	$name='';
	$mail='';
	$content='';
}

?>

<div id='form_contact'>
	<?php if($error): ?>
	<p class='contact_error'><?php echo $error ?></p>
	<?php endif; ?>
	<?php if($success): ?>
	<p class='contact_success'><?php echo plxUtils::strCheck($success) ?></p>
	<?php else: ?>
	<?php if($plxPlugin->getParam('mnuText_'.$plxPlugin->default_lang)): ?>
	<div class='text_contact'>
	<?php echo $plxPlugin->getParam('mnuText_'.$plxPlugin->default_lang) ?>
	</div>
	<?php endif; ?>
	<form action='#form_contact' method='post'>
		<fieldset>
		<p>
			<input id='name' name='name' type='text' size='30' value='<?php echo plxUtils::strCheck($name) ?>' maxlength='30' required="required" placeholder="<?php $plxPlugin->lang('L_FORM_NAME') ?>" />
			<input id="mail" name="mail" type="email" required="required" size="30" value="<?php echo plxUtils::strCheck($mail) ?>" placeholder="<?php $plxPlugin->lang('L_FORM_MAIL') ?>" />
		</p>
		<textarea id='message' name='content' cols='60' rows='12' required="required" placeholder="<?php $plxPlugin->lang('L_FORM_CONTENT') ?>"><?php echo plxUtils::strCheck($content) ?></textarea>
		<?php if($captcha): ?>
		<p><label for='id_rep'><strong><?php $plxPlugin->lang('L_FORM_ANTISPAM') ?></strong>&nbsp;:</label></p>
		<?php echo $plxShow->capchaQ() ?>&nbsp;:&nbsp;<input id='id_rep' name='rep' type='text' size='10' required="required" autocomplete="off" />
		<input name='rep2' type='hidden' value='<?php echo $plxShow->capchaR() ?>' />
		<?php endif; ?>
		<p>
			<input type='submit' name='submit' value='<?php $plxPlugin->lang('L_FORM_BTN_SEND') ?>' />
			<input type='reset' name='reset' value='<?php $plxPlugin->lang('L_FORM_BTN_RESET') ?>' />
		</p>
		</fieldset>
	</form>
	<?php endif; ?>
</div>
