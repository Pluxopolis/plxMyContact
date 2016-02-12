<?php
/**
 * Plugin plxMyContact
 * @author	Stephane F
 **/
class plxMyContact extends plxPlugin {

	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		# déclaration des hooks
		$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
		$this->addHook('AdminTopBottom', 'AdminTopBottom');
		if(plxUtils::checkMail($this->getParam('email'))) {
			$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
			$this->addHook('plxShowConstruct', 'plxShowConstruct');
			$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
			$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
			$this->addHook('SitemapStatics', 'SitemapStatics');
		}

	}

	public function AdminTopEndHead() {
		if(basename($_SERVER['SCRIPT_NAME'])=='parametres_plugin.php') {
			echo '<link href="'.PLX_PLUGINS.'plxMyContact/tabs/style.css" rel="stylesheet" type="text/css" />'."\n";
		}
	}

	/**
	 * Méthode de traitement du hook plxShowConstruct
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowConstruct() {

		# infos sur la page statique
		$string  = "if(\$this->plxMotor->mode=='".$this->getParam('url')."') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".addslashes($this->getParam('mnuName_'.$this->default_lang))."',
			'menu'		=> '',
			'url'		=> 'contact',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		echo "<?php ".$string." ?>";
	}

	/**
	 * Méthode de traitement du hook plxMotorPreChauffageBegin
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxMotorPreChauffageBegin() {

		$template = $this->getParam('template')==''?'static.php':$this->getParam('template');

		$string = "
		if(\$this->get && preg_match('/^".$this->getParam('url')."\/?/',\$this->get)) {
			\$this->mode = '".$this->getParam('url')."';
			\$prefix = str_repeat('../', substr_count(trim(PLX_ROOT.\$this->aConf['racine_statiques'], '/'), '/'));
			\$this->cible = \$prefix.'plugins/plxMyContact/form';
			\$this->template = '".$template."';
			return true;
		}
		";

		echo "<?php ".$string." ?>";
	}

	/**
	 * Méthode de traitement du hook plxShowStaticListEnd
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowStaticListEnd() {

		# ajout du menu pour accèder à la page de contact
		if($this->getParam('mnuDisplay')) {
			echo "<?php \$class = \$this->plxMotor->mode=='".$this->getParam('url')."'?'active':'noactive'; ?>";
			echo "<?php array_splice(\$menus, ".($this->getParam('mnuPos')-1).", 0, '<li><a class=\"static '.\$class.'\" href=\"'.\$this->plxMotor->urlRewrite('?".$this->getParam('url')."').'\" title=\"".addslashes($this->getParam('mnuName_'.$this->default_lang))."\">".addslashes($this->getParam('mnuName_'.$this->default_lang))."</a></li>'); ?>";
		}

	}

	/**
	 * Méthode qui rensigne le titre de la page dans la balise html <title>
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowPageTitle() {
		echo '<?php
			if($this->plxMotor->mode == "'.$this->getPAram('url').'") {
				echo "'.$this->getParam('mnuName_'.$this->default_lang).'" - "'.plxUtils::strCheck($this->plxMotor->aConf["title"]).'";
				return true;
			}
		?>';
	}

	/**
	 * Méthode qui référence la page de contact dans le sitemap
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function SitemapStatics() {
		echo '<?php
		echo "\n";
		echo "\t<url>\n";
		echo "\t\t<loc>".$plxMotor->urlRewrite("?'.$this->getParam('url').'")."</loc>\n";
		echo "\t\t<changefreq>monthly</changefreq>\n";
		echo "\t\t<priority>0.8</priority>\n";
		echo "\t</url>\n";
		?>';
	}

	/**
	 * Méthode qui affiche un message si l'adresse email du contact n'est pas renseignée
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AdminTopBottom() {

		$string = '
		if($plxAdmin->plxPlugins->aPlugins["plxMyContact"]->getParam("email")=="") {
			echo "<p class=\"warning\">Plugin MyContact<br />'.$this->getLang("L_ERR_EMAIL").'</p>";
			plxMsg::Display();
		}';
		echo '<?php '.$string.' ?>';

	}

}
?>
