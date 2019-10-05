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

class logManagerLevel {
	const LOGLEVEL = array(100 => 'debug', 200 => 'info', 300 => 'warning', 400 => 'error');

	public static function all() {
		return self::LOGLEVEL;
	}

	public static function getId($loglevel) {
		foreach (self::LOGLEVEL as $key => $value) {
			if ($value==$loglevel) return $key;
		}
	}

}

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
		$order = 0;

		foreach (logManagerLevel::all() as $loglevel) {
			$cmd = $this->getCmd(null, $loglevel);
			if (!is_object($cmd)) {
				$cmd = new logmanagerCmd();
				$cmd->setLogicalId($loglevel);
				$cmd->setIsVisible(1);
				$cmd->setOrder($order);
				$cmd->setName(ucfirst($loglevel));
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
}

class logmanagerCmd extends cmd {
	public function dontRemoveCmd() {
		return true;
	}

	public function execute($_options = array()) {

		if (!is_array($_options)) {
			log::add('logmanager', 'error', __('Options invalides',__FILE__));
			return;
		}
		if (!isset($_options['message'])) {
			log::add('logmanager', 'info', __('Message absent',__FILE__));
			return;
		}
		$message = trim($_options['message']);
		if (trim($message) == '') {
			log::add('logmanager', 'info', __('Message vide',__FILE__));
			return;
		}

		$eqlogic = $this->getEqLogic();
		$logName = $eqlogic->getName();
		$logLevel = $this->getLogicalId();
		$logLevelId = logManagerLevel::getId($logLevel);
		$eventLevel = intval($eqlogic->getConfiguration('eventlevel', 9999));
		$logLevelConfig = log::getLogLevel($logName);

		log::add('logmanager', 'debug', "Log new message with level {$logLevel} in {$logName} with config {$logLevelConfig}");
		try {
			log::add($logName, $logLevel, $message);
		} catch (\Throwable $th) {
			log::add('logmanager', 'error', "Erreur lors du log du message '{$message} dans le log '{$logName}': {$th->getMessage()}");
		}

		log::add('logmanager', 'debug', "New event? {$logLevelId} >= {$eventLevel}");
		if ($logLevelId>=$eventLevel && $logLevelId>=$logLevelConfig) {
			log::add('logmanager', 'debug', "Sending event for {$logLevel}");
			jeedom::event("lm-{$logLevel}");
		}
	}
}
