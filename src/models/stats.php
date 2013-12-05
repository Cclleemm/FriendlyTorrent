<?php

/* $Id$ */

/*******************************************************************************

 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html

*******************************************************************************/

/**
 * StatFile
 */
class StatFile extends Common
{
	// public fields

	// file
	var $theFile;

	// af-props
	var $running = "1";
	var $percent_done = "0.0";
	var $time_left = "";
	var $down_speed = "";
	var $up_speed = "";
	var $sharing = "";
	var $transferowner = "";
	var $seeds = "";
	var $peers = "";
	var $seedlimit = "";
	var $uptotal = "";
	var $downtotal = "";
	var $size = "";
	var $error = 0;
	var $errorString = "";
	var $eta = 0;
	var $cons = 0;
	var $peersList = array();
	var $files = array();

	// =========================================================================
	// public static methods
	// =========================================================================

	/**
	 * factory
	 *
	 * @param $transfer
	 * @param $user
	 * @return StatFile
	 */
	function getInstance($transfer, $user = '') {
		return new StatFile($transfer, $user);
	}

	// =========================================================================
	// ctor
	// =========================================================================

	/**
	 * ctor
	 *
	 * @param $transfer
	 * @param $user
	 * @return StatFile
	 */
	function StatFile($transfer, $user = "") {
		parent::__construct();
		$this->init($transfer, $user);
	}

	// =========================================================================
	// public methods
	// =========================================================================

	/**
	 * init stat-file
	 *
	 * @param $transfer
	 * @param $user
	 */
	function init($transfer, $user = '') {
		// file
		$this->theFile = $transfer.".stat";
		// set user
		if ($user != '')
			$this->transferowner = $user;
		// load file
		if ($this->bdd->getCache()->get($this->theFile)) {
			// read the stat file
			$data = $this->bdd->getCache()->get($this->theFile);
			// assign vars from content
			$content = @explode("\n", $data);
			$this->running = @ $content[0];
			$this->percent_done = @ $content[1];
			$this->time_left = @ $content[2];
			$this->down_speed = @ $content[3];
			$this->up_speed = @ $content[4];
			$this->transferowner = @ $content[5];
			$this->seeds = @ $content[6];
			$this->peers = @ $content[7];
			$this->sharing = @ $content[8];
			$this->seedlimit = @ $content[9];
			$this->uptotal = @ $content[10];
			$this->downtotal = @ $content[11];
			$this->size = @ $content[12];
			$this->eta = @ $content[13];
			$this->cons = @ $content[14];
			$this->peersList = unserialize(@ $content[15]);
			$this->files = unserialize(@ $content[16]);
			$this->error = @ $content[17];
			//complete stat file
			if((int)$this->size == 0) {
				$this->size = $this->getTransferSize($transfer);
				if((int)$this->size > 0) $this->write();
			}
		}
	}

	/**
	 * @return full size
	 */
	function getTransferSize($transfer) {
		$file = ROOT_DOWNLOADS.".tranferts/".$transfer;
		if ($fd = @fopen($file, "rd")) {
			$alltorrent = @fread($fd, @filesize($file));
			$array = BDecode($alltorrent);
			@fclose($fd);
		}
		return ((isset($array["info"]["piece length"])) && (isset($array["info"]["pieces"])))
			? $array["info"]["piece length"] * (strlen($array["info"]["pieces"]) / 20)
			: 0;
	}

	
	/**
	 * call this on start
	 *
	 * @return boolean
	 */
	function start() {
		// Reset all the var to new state (all but transferowner)
		$this->running = "-1";
		$this->percent_done = "0.0";
		$this->time_left = "Starting...";
		$this->down_speed = "";
		$this->up_speed = "";
		$this->sharing = "";
		$this->seeds = "";
		$this->peers = "";
		$this->seedlimit = "";
		$this->uptotal = "";
		$this->downtotal = "";
		// write to file
		return $this->write();
	}
	
	function finish() {
		// Reset all the var to new state (all but transferowner)
		$this->running = "-2";
		$this->time_left = "-";
		$this->percent_done= 100;
		// write to file
		return $this->write();
	}

	/**
	 * to clean a stat file (manualy stopped)
	 *
	 * @return boolean
	 */
	function stop() {
		$this->running = "0";
		if ($this->percent_done == 0)
			$this->running = "2"; //new
		elseif ($this->percent_done > 0) {
			$this->percent_done= 0 - $this->percent_done;
		}
		$this->time_left = "Stopped";
		$this->down_speed = "";
		$this->up_speed = "";
		$this->peers = "";
		// write to file
		return $this->write();
	}


	/**
	 * Common write Method
	 *
	 * @return boolean
	 */
	function write() {
		// content
		$content  = $this->running."\n";
		$content .= $this->percent_done."\n";
		$content .= $this->time_left."\n";
		$content .= $this->down_speed."\n";
		$content .= $this->up_speed."\n";
		$content .= $this->transferowner."\n";
		$content .= $this->seeds."\n";
		$content .= $this->peers."\n";
		$content .= $this->sharing."\n";
		$content .= $this->seedlimit."\n";
		$content .= $this->uptotal."\n";
		$content .= $this->downtotal."\n";
		$content .= $this->size."\n";
		$content .= $this->eta."\n";
		$content .= $this->cons."\n";
		$content .= serialize($this->peersList)."\n";
		$content .= serialize($this->files)."\n";
		$content .= $this->error;
		if (!$this->bdd->getCache()->get($this->theFile) or $content != $this->bdd->getCache()->get($this->theFile)) {
			$this->bdd->getCache()->set($this->theFile, $content, false, 0);
		}
		return true;
	}

}

?>
