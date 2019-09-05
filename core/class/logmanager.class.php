<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class logmanager extends eqLogic {

	public function preInsert() {
		$this->setConfiguration('loglevel', '100');
		$this->setIsEnable(1);
	}

	public function postInsert() {

	}

	public function preSave() {
		$replaceChars = array(
			'á' => 'a', 'à' => 'a', 'â' => 'a', 'ä' => 'a',
			'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
			'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
			'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'ö' => 'o',
			'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u'
		);

		$name = strtolower($this->getName());
		$name = ltrim($name, '_');
		$name = strtr($name, $replaceChars);
		$name = preg_replace("/[^a-zA-Z_]/", "", $name);
		$this->setName($name);
	}

	public function postSave() {
		$loglevel = array('debug', 'info', 'warning', 'error');
		$order = 0;

		foreach ($loglevel as $log) {
			$cmd = $this->getCmd(null, $log);
			if (!is_object($cmd)) {
				$cmd = new logmanagerCmd();
				$cmd->setLogicalId($log);
				$cmd->setIsVisible(1);
				$cmd->setOrder($order);
				$cmd->setName(ucfirst($log));
				$cmd->setType('action');
				$cmd->setSubType('message');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setDisplay('title_disable', 1);
				$cmd->save();
			}
			++$order;
		}
		$logConfig = array($this->getConfiguration('loglevel', '100') => '1', 'default' => '0');
		config::save('log::level::'.$this->getName(), $logConfig);
	}

	public function preUpdate() {

	}

	public function postUpdate() {

	}

	public function preRemove() {

	}

	public function postRemove() {
		config::remove('log::level::'.$this->getName());
	}

	/*
	 * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
	  public function toHtml($_version = 'dashboard') {

	  }
	 */

	/*
	 * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
	public static function postConfig_<Variable>() {
	}
	 */

	/*
	 * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
	public static function preConfig_<Variable>() {
	}
	 */


	/*     * **********************Getteur Setteur*************************** */

}

class logmanagerCmd extends cmd {
	public function dontRemoveCmd() {
		return true;
	}

	public function execute($_options = array()) {

		if (!is_array($_options)) {
			log::add('logmanager', 'error', __('Options invalides',__FILE__));
		}
		if (!isset($_options['message'])) {
			log::add('logmanager', 'info', __('Message absent',__FILE__));
			return;
		}
		$message =  trim($_options['message']);
		if (trim($message) == '') {
			log::add('logmanager', 'info', __('Message vide',__FILE__));
			return;
		}

		$eqlogic = $this->getEqLogic();
		$loglevel = $this->getLogicalId();

		try {
			log::add($eqlogic->getName(), $loglevel, $message);
		} catch (\Throwable $th) {
			log::add('logmanager', 'error', $th->getMessage());
		}
	}
}
