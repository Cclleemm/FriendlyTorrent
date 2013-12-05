<?php
	/**
	 * get one Transmission transfer data array
	 *
	 * @param $transfer hash of the transfer
	 * @param $fields array of fields needed
	 * @return array or false
	 */
	function getTransmissionTransfer($transfer, $fields=array() , $instance) {
		//$fields = array("id", "name", "eta", "downloadedEver", "hashString", "fileStats", "totalSize", "percentDone",
		//			"metadataPercentComplete", "rateDownload", "rateUpload", "status", "files", "trackerStats", "uploadedEver" )
		$required = array('hashString');
		$afields = array_merge($required, $fields);

		$rpc = $instance;
		$response = $rpc->get(array(), $afields);
		$torrentlist = $response['arguments']['torrents'];

		if (!empty($torrentlist)) {
			foreach ($torrentlist as $aTorrent) {
				if ( $aTorrent['hashString'] == $transfer )
					return $aTorrent;
			}
		}
		return false;
	}

	/**
	 * checks if transfer is Transmission
	 *
	 * @param $transfer hash of the transfer
	 * @return boolean
	 */
	function isTransmissionTransfer($transfer, $instance) {
		$aTorrent = getTransmissionTransfer($transfer, array(), $instance);
		return is_array($aTorrent);
	}

	/**
	 * This method retrieves the current ID in transmission for the transfer that matches the $hash hash
	 *
	 * @return transmissionTransferId
	 */
	function getTransmissionTransferIdByHash($hash, $instance) {
		$transmissionTransferId = false;
		$rpc = $instance;
		$response = $rpc->get(array(), array('id','hashString'));
		if ( $response['result'] != "success" ) rpc_error("Getting ID for Hash failed: ".$response['result']);
		$torrentlist = $response['arguments']['torrents'];
		foreach ($torrentlist as $aTorrent) {
			if ( $aTorrent['hashString'] == $hash ) {
				$transmissionTransferId = $aTorrent['id'];
				break;
			}
		}
		return $transmissionTransferId;
	}

	/**
	 * This method starts the Transmission transfer with the matching hash
	 *
	 * @return void
	 */
	function startTransmissionTransfer($hash,$startPaused=false,$params=array(), $instance) {

		$rpc = $instance;
		$transmissionId = getTransmissionTransferIdByHash($hash, $instance);
		$response = $rpc->set($transmissionId, array_merge(array("seedRatioMode" => 1), $params) );
		$response = $rpc->start($transmissionId);
		if ( $response['result'] != "success" ) {
			rpc_error("Start failed", "", "", $response['result']);
			return false;
		}
		return true;
	}

	/**
	 * This method stops the Transmission transfer with the matching hash
	 *
	 * @return boolean
	 */
	function stopTransmissionTransfer($hash, $instance) {

			$transmissionId = getTransmissionTransferIdByHash($hash, $instance);
			$response = $instance->stop($transmissionId);
			if ( $response['result'] != "success" ) return false;

			return true;
	}

	/**
	 * convertTime
	 *
	 * @param $seconds
	 * @return common time-delta-string
	 */
	function convertTime($seconds) {
		// sanity-check
		if ($seconds < -1) $seconds=0-$seconds;
		// one week is enough
		if ($seconds >= 604800) return '-';
		// format time-delta
		$periods = array (/* 31556926, 2629743, 604800,*/ 86400, 3600, 60, 1);
		$seconds = floatval($seconds);
		$values = array();
		$leading = true;
		foreach ($periods as $period) {
			$count = floor($seconds / $period);
			if ($leading) {
				if ($count == 0)
					continue;
				$leading = false;
			}
			array_push($values, ($count < 10) ? "0".$count : $count);
			$seconds = $seconds % $period;
		}
		return (empty($values)) ? "?" : implode(':', $values);
	}

	/**
	 * This method deletes the Transmission transfer with the matching hash, without removing the data
	 *
	 * @return void
	 * TODO: test delete :)
	 */
	function deleteTransmissionTransfer($hash, $deleteData = false, $rpc) {

			$transmissionId = getTransmissionTransferIdByHash($hash, $rpc);
			$response = $rpc->remove($transmissionId,$deleteData);
			if ( $response['result'] != "success" )
				return false;

			return true;
	}

	/**
	 * convertTimeText
	 *
	 * @param $seconds
	 * @return textual remaining time
	 */
	function convertTimeText($seconds) {
		$hour_fmt = convertTime($seconds);
		if ($hour_fmt == '-')
			return '-';
		$parts = explode(':',$hour_fmt);
		if (count($parts) >= 4)
			return $parts[0]."d.";
		elseif (count($parts) == 3)
			return $parts[0]."h.";
		elseif (count($parts) == 2)
			return $parts[0]."m.";
		else
			return $parts[0]."s.";
	}

	/**
	 * Returns a string in format of TB, GB, MB, or kB depending on the size
	 *
	 * @param $inBytes
	 * @return string
	 */
	function formatBytesTokBMBGBTB($inBytes) {
		if(!is_numeric($inBytes)) return "";
		if ($inBytes > 1099511627776)
			return round($inBytes / 1099511627776, 2) . " TB";
		elseif ($inBytes > 1073741824)
			return round($inBytes / 1073741824, 2) . " GB";
		elseif ($inBytes > 1048576)
			return round($inBytes / 1048576, 1) . " MB";
		elseif ($inBytes > 1024)
			return round($inBytes / 1024, 1) . " kB";
		else
			return $inBytes . " B";
	}
?>
