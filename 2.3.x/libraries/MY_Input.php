<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Extending Input library.
 *
 * @package    Core
 * @author     icyleaf
 * @version	   1.0
 *
 *
 * function 'ip_location' is convertion from Discuz! forum system, 
 * its file path: 'include/misc.func.php'.
 *
 *
 * !!!REQUEST!!!
 * 1. qqwry ip data file, you could download it form http://update.cz88.Net/soft/qqwry.rar
 * or using Discuz! ip tiny data file(path: upload/ipdata/tinyipdata.dat).
 *
 * 2. qqwry file renamed 'fullipdata.dat' in 'application/vender/qqwry' directory, 
 * or Discuz! ip tiny data file moved it to 'application/vender/qqwry' directory.
 * (Discuz! ip tiny data file now in 'svn/trunk/vender/qqwry' directory)
 *
 * 3. return result string may be messy code because of qqwry ip data file encode is GB2312.
 */
class Input extends Input_Core {

	public function format_smiles($string)
	{
		$smile_path = url::base().'media/images/smiles/pixel/';
		$img_style = 'margin: 0pt 0.2ex; vertical-align: middle;';
		if ( !empty($string) )
		{
			$string = str_ireplace(':)', '<img src="'.$smile_path.'smile.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace(':-)', '<img src="'.$smile_path.'cool.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace(':o', '<img src="'.$smile_path.'scared.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace(':-o', '<img src="'.$smile_path.'fright.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace(':(', '<img src="'.$smile_path.'frown.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace('}:[', '<img src="'.$smile_path.'satan.gif" style="'.$img_style.'" />', $string);
			$string = str_replace(':D', '<img src="'.$smile_path.'grin.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace(':p', '<img src="'.$smile_path.'tongue.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace('^_^', '<img src="'.$smile_path.'evilgrin.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace('^-^', '<img src="'.$smile_path.'evilgrin.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace('>_<', '<img src="'.$smile_path.'surprised.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace('T T', '<img src="'.$smile_path.'cry.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace('-o-', '<img src="'.$smile_path.'shy.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace('- -', '<img src="'.$smile_path.'speechless.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace('= =!', '<img src="'.$smile_path.'impatient.gif" style="'.$img_style.'" />', $string);
			$string = str_ireplace('囧', '<img src="'.$smile_path.'orz.gif" style="'.$img_style.'" />', $string);
		}
		return $string;
	}

	/**
	 * Find ip location
	 *
	 * @param string $ip 
	 * @param boolean $isFull 
	 * @return string
	 */
	public function ip_location($ip='', $isFull=FALSE)
	{
		$ip = empty($ip) ? $this->ip_address() : $ip;		
		
		$location = '';
	
		if(preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) {
	
			$iparray = explode('.', $ip);
	
			if($iparray[0] == 10 || $iparray[0] == 127 || ($iparray[0] == 192 && $iparray[1] == 168) || ($iparray[0] == 172 && ($iparray[1] >= 16 && $iparray[1] <= 31))) {
				$location = '- LAN';
			} elseif($iparray[0] > 255 || $iparray[1] > 255 || $iparray[2] > 255 || $iparray[3] > 255) {
				$location = '- Invalid IP Address';
			} else {
				
				if( $dbfile=Kohana::find_file('vendor/qqwry',($isFull) ? 'fullipdata' : 'tinyipdata', FALSE, 'dat') )
				{
					if ( $isFull )
					{
						$location = $this->convertip_full($ip, $dbfile);
					}
					else
					{
						$location = $this->convertip_tiny($ip, $dbfile);
					}
				}
			}
		}
	
		return $location;
	}
	
	/**
	 * Using tiny data file to find ip location
	 *
	 * @param string $ip 
	 * @param string $ipdatafile 
	 * @return string
	 */
	private function convertip_tiny($ip, $ipdatafile) {
	
		static $fp = NULL, $offset = array(), $index = NULL;
	
		$ipdot = explode('.', $ip);
		$ip = pack('N', ip2long($ip));
	
		$ipdot[0] = (int)$ipdot[0];
		$ipdot[1] = (int)$ipdot[1];
	
		if($fp === NULL && $fp = @fopen($ipdatafile, 'rb')) {
			$offset = unpack('Nlen', fread($fp, 4));
			$index = fread($fp, $offset['len'] - 4);
		} elseif($fp == FALSE) {
			return '- Invalid IP data file';
		}
	
		$length = $offset['len'] - 1028;
		$start  = unpack('Vlen', $index[$ipdot[0] * 4] . $index[$ipdot[0] * 4 + 1] . $index[$ipdot[0] * 4 + 2] . $index[$ipdot[0] * 4 + 3]);
	
		for ($start = $start['len'] * 8 + 1024; $start < $length; $start += 8) {
	
			if ($index{$start} . $index{$start + 1} . $index{$start + 2} . $index{$start + 3} >= $ip) {
				$index_offset = unpack('Vlen', $index{$start + 4} . $index{$start + 5} . $index{$start + 6} . "\x0");
				$index_length = unpack('Clen', $index{$start + 7});
				break;
			}
		}
	
		fseek($fp, $offset['len'] + $index_offset['len'] - 1024);
		if($index_length['len']) {
			return '- '.fread($fp, $index_length['len']);
		} else {
			return '- Unknown';
		}
	}
	
	/**
	 * Using full data file to find ip location
	 *
	 * @param string $ip 
	 * @param string $ipdatafile 
	 * @return string
	 */
	private function convertip_full($ip, $ipdatafile) {
	
		if(!$fd = @fopen($ipdatafile, 'rb')) {
			return '- Invalid IP data file';
		}
	
		$ip = explode('.', $ip);
		$ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];
	
		if(!($DataBegin = fread($fd, 4)) || !($DataEnd = fread($fd, 4)) ) return;
		@$ipbegin = implode('', unpack('L', $DataBegin));
		if($ipbegin < 0) $ipbegin += pow(2, 32);
		@$ipend = implode('', unpack('L', $DataEnd));
		if($ipend < 0) $ipend += pow(2, 32);
		$ipAllNum = ($ipend - $ipbegin) / 7 + 1;
	
		$BeginNum = $ip2num = $ip1num = 0;
		$ipAddr1 = $ipAddr2 = '';
		$EndNum = $ipAllNum;
	
		while($ip1num > $ipNum || $ip2num < $ipNum) {
			$Middle= intval(($EndNum + $BeginNum) / 2);
	
			fseek($fd, $ipbegin + 7 * $Middle);
			$ipData1 = fread($fd, 4);
			if(strlen($ipData1) < 4) {
				fclose($fd);
				return '- System Error';
			}
			$ip1num = implode('', unpack('L', $ipData1));
			if($ip1num < 0) $ip1num += pow(2, 32);
	
			if($ip1num > $ipNum) {
				$EndNum = $Middle;
				continue;
			}
	
			$DataSeek = fread($fd, 3);
			if(strlen($DataSeek) < 3) {
				fclose($fd);
				return '- System Error';
			}
			$DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
			fseek($fd, $DataSeek);
			$ipData2 = fread($fd, 4);
			if(strlen($ipData2) < 4) {
				fclose($fd);
				return '- System Error';
			}
			$ip2num = implode('', unpack('L', $ipData2));
			if($ip2num < 0) $ip2num += pow(2, 32);
	
			if($ip2num < $ipNum) {
				if($Middle == $BeginNum) {
					fclose($fd);
					return '- Unknown';
				}
				$BeginNum = $Middle;
			}
		}
	
		$ipFlag = fread($fd, 1);
		if($ipFlag == chr(1)) {
			$ipSeek = fread($fd, 3);
			if(strlen($ipSeek) < 3) {
				fclose($fd);
				return '- System Error';
			}
			$ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
			fseek($fd, $ipSeek);
			$ipFlag = fread($fd, 1);
		}
	
		if($ipFlag == chr(2)) {
			$AddrSeek = fread($fd, 3);
			if(strlen($AddrSeek) < 3) {
				fclose($fd);
				return '- System Error';
			}
			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					return '- System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}
	
			while(($char = fread($fd, 1)) != chr(0))
			$ipAddr2 .= $char;
	
			$AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
			fseek($fd, $AddrSeek);
	
			while(($char = fread($fd, 1)) != chr(0))
			$ipAddr1 .= $char;
		} else {
			fseek($fd, -1, SEEK_CUR);
			while(($char = fread($fd, 1)) != chr(0))
			$ipAddr1 .= $char;
	
			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					return '- System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}
			while(($char = fread($fd, 1)) != chr(0))
			$ipAddr2 .= $char;
		}
		fclose($fd);
	
		if(preg_match('/http/i', $ipAddr2)) {
			$ipAddr2 = '';
		}
		$ipaddr = "$ipAddr1 $ipAddr2";
		$ipaddr = preg_replace('/CZ88\.NET/is', '', $ipaddr);
		$ipaddr = preg_replace('/^\s*/is', '', $ipaddr);
		$ipaddr = preg_replace('/\s*$/is', '', $ipaddr);
		if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
			$ipaddr = '- Unknown';
		}
	
		return '- '.$ipaddr;
	}
}

?>