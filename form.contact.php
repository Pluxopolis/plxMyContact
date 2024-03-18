<?php if(!defined('PLX_ROOT')) exit;
# Nom du Plugin
$plugName = basename(__DIR__);
# récupération d'une instance de plxShow
$plxShow = $this; # plxShow::getInstance();
$plxPlugin = $plxShow->plxMotor->plxPlugins->getInstance($plugName);
$captcha = $plxPlugin->getParam('captcha')=='' ? '1' : $plxPlugin->getParam('captcha');
if($captcha) $plxShow->plxMotor->plxCapcha = new plxCapcha();

# Si le fichier de langue n'existe pas
$lang = $plxShow->plxMotor->aConf['default_lang'];
if(!file_exists(PLX_PLUGINS . $plugName . '/lang/' . $lang . '.php')) {
	echo '<p>' . sprintf($plxPlugin->getLang('L_LANG_UNAVAILABLE'), PLX_PLUGINS . $plugName . '/lang/' . $lang . '.php') . '</p>';
	return;
}

$error=array();


if(!empty($_POST)) {

	$name=plxUtils::unSlash($_POST['name']);
	$mail=plxUtils::unSlash($_POST['mail']);
	$subject = '';
	if($plxPlugin->getParam('append_subject')) {
		$subject = plxUtils::unSlash($_POST['subject']) . ' ';
	}
	$content=plxUtils::unSlash($_POST['content']);

	# pour compatibilité avec le plugin plxMyCapchaImage
	if(strlen($_SESSION['capcha'])<=10)
		$_SESSION['capcha']=sha1($_SESSION['capcha']);

	if(trim($name)=='')
		$error[] = $plxPlugin->getLang('L_ERR_NAME');
	if(!plxUtils::checkMail($mail))
		$error[] = $plxPlugin->getLang('L_ERR_EMAIL');
	if(trim($content)=='')
		$error[] = $plxPlugin->getLang('L_ERR_CONTENT');
	if($captcha != 0 AND $_SESSION['capcha'] != sha1($_POST['rep']))
		$error[] = $plxPlugin->getLang('L_ERR_ANTISPAM');
	if(!$error) {
		if(plxUtils::sendMail($name, $mail, $plxPlugin->getParam('email'), plxUtils::unSlash($plxPlugin->getParam('subject')) . $subject, $content, 'text', $plxPlugin->getParam('email_cc'), $plxPlugin->getParam('email_bcc'))) {
			$_SESSION[$plugName . 'success'] = true;
			header( 'Location: ' . $plxShow->plxMotor->racine . $plxShow->plxMotor->path_url );
			exit;
		}else{
			$error[] = $plxPlugin->getLang('L_ERR_SENDMAIL');
		}
	}
} else {
	$name='';
	$mail='';
	$subject = '';
	$content='';
}
$holderParam = $plxPlugin->getParam('placeholder');
$labelParam = $plxPlugin->getParam('label');
?>
<div id="form_contact">
	<?php if($error): ?>
	<p class="contact_error"><?= implode('<br>', $error) ?></p>
	<?php endif; ?>
	<?php if(isset($_SESSION[$plugName . 'success'])): unset($_SESSION[$plugName . 'success']); ?>
	<p class="contact_success"><?= plxUtils::strCheck($plxPlugin->getParam('thankyou_' . $plxPlugin->default_lang)) ?></p>
	<?php else: ?>
	<?php if($plxPlugin->getParam('mnuText_' . $plxPlugin->default_lang)): ?>
	<p class="text_contact">
	<?= $plxPlugin->getParam('mnuText_' . $plxPlugin->default_lang) ?>
	</p>
	<?php endif; ?>
	<form action="#form" method="post">
		<fieldset>
		<p>
			<?php if($labelParam) : ?>
			<label for="name"><?php $plxPlugin->lang('L_FORM_NAME') ?>&nbsp;:</label>
			<?php endif; ?>
			<?php $placeholder = ($holderParam ? 'placeholder="' . plxUtils::strCheck($plxPlugin->getLang('L_FORM_NAME')) . '" ' : '') ?>
			<input <?= $placeholder ?>required id="name" name="name" type="text" size="30" value="<?= plxUtils::strCheck($name) ?>" maxlength="30" />
		</p>
		<p>
			<?php if($labelParam) : ?>
			<label for="mail"><?php $plxPlugin->lang('L_FORM_MAIL') ?>&nbsp;:</label>
			<?php endif; ?>
			<?php $placeholder = ($holderParam ? 'placeholder="' . plxUtils::strCheck($plxPlugin->getLang('L_FORM_MAIL')) . '" ' : '') ?>
			<input <?= $placeholder ?>required id="mail" name="mail" type="mail" size="30" value="<?= plxUtils::strCheck($mail) ?>" />
		</p>
		<?php if($plxPlugin->getParam('append_subject')) : ?>
		<p>
			<?php if($labelParam) : ?>
			<label for="subject"><?php $plxPlugin->lang('L_FORM_SUBJECT') ?>&nbsp;:</label>
			<?php endif; ?>
			<?php $placeholder = ($holderParam ? 'placeholder="' . $plxPlugin->getLang('L_FORM_SUBJECT') . '" ' : '') ?>
			<input <?= $placeholder ?>id="subject" name="subject" type="text" size="30" value="<?= plxUtils::strCheck($subject) ?>" maxlength="30" />
		</p>
		<?php endif; ?>
		<p>
			<?php if($labelParam) : ?>
			<label for="message"><?php $plxPlugin->lang('L_FORM_CONTENT') ?>&nbsp;:</label>
			<?php endif; ?>
			<?php $placeholder = ($holderParam ? 'placeholder="' . plxUtils::strCheck($plxPlugin->getLang('L_FORM_CONTENT')) . '" ' : '') ?>
			<textarea <?= $placeholder ?>required id="message" name="content" cols="60" rows="12"><?= plxUtils::strCheck($content) ?></textarea>
		</p>
		<?php if($captcha): ?>
		<p>
		<label for="id_rep"><strong><?php $plxPlugin->lang('L_FORM_ANTISPAM') ?></strong></label>
		<?php $plxShow->capchaQ(); ?>
		<input id="id_rep" name="rep" type="text" size="2" maxlength="1" style="width: auto; display: inline;" autocomplete="off" />
		</p>
		<?php endif; ?>
		<p>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_FORM_BTN_SEND') ?>" />
			<input type="reset" name="reset" value="<?php $plxPlugin->lang('L_FORM_BTN_RESET') ?>" />
		</p>
		</fieldset>
	</form>
	<?php endif; ?>
</div>
