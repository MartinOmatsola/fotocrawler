<?php

/*
 *  @author Martin Okorodudu <webmaster@fotocrib.com>
 *
 * 	Feel free to extend, optimize .............
 */


include_once 'utils.php';

class Fotocrawler {
	
	//stores visited urls
	private $_visited;

	private $_tree;
	

	public function __construct() {
		$this->_visited = array();
		$this->_tree = array();
	}


	/**
	 *  Saves all image files on a webpage to a directory on the local machine
	 *  @param string $url The url to be crawled
	 *  @param string $dir The directory to store the harvested photos 
	 *  @param string $ext The format of the photos, one of "png", "jpg", "gif"
	 *  @param int $flag Determines the crawl depth
	 */
	function crawl($url, $dir, $ext='jpg', $flag=0) {
		if (!is_dir($dir)) {
			die("directory $dir does not exist");
		}
		if ($ext != 'png' && $ext != 'gif' && $ext != 'jpg') {
			die("format not supported, enter one of jpg, png or gif");
		}	
		$term = "." . $ext;
		$urls = array();
			
		$lines = file($url);
		foreach ($lines as $line) {
			$img_url = $this->getImageURL($line); 
			if ($img_url) {
				$img_url = $this->handleRelative($url, $img_url);
				$urls[] = myImageCreate($img_url);
			}
			//check for url and recurse
			if ($flag > 0) {
				$link = $this->getURL($line);
				if ($link && !array_key_exists($link, $this->_visited)) {
					$subdir = $dir . basename($link) . '/';
					$this->_tree[basename($url)][] = $link; 
					mkdir($subdir);
					$this->crawl($link, $subdir, $ext, $flag - 1);
				}
			}
		}
		
		//record visited url
		$this->_visited[basename($url)] = array(
											'count' => count($urls),
											'dir' => $dir
											); 

		foreach ($urls as $img) {
			myImageSave($img, $dir . generateName() . $term);
		}
		unset($urls);
		unset($lines);
	}


	function saveReportAsHtml($filename) {
		$html = '<html>
					<head><title>Fotocrawler Report</title></head>
						<body>
						<center>
							<table style="width:400px;border-style:solid;border-width:thin;font-family:tahoma;color:#1A1A63">
								<tr>
									<td style="text-align:center;background:Lavender;font-size:20px">URL</td>
									<td style="text-align:center;background:Lavender;font-size:20px">#Images</td>
								</tr>';
		foreach ($this->_visited as $domain => $info) {
			$html.= '<tr>
						<td style="text-align:center;background:#DDDDDD"><a href="../'. $info['dir'] .'"><i>'. $domain .'</i></a></td>
						<td style="text-align:center;background:#EEEEEE">' . $info['count'] . '</td>
					</tr>';
		}


		$html.= '			</table>
						</center>
						</body>
				</html>';
		file_put_contents($filename . '.html', $html);
	}


	function getVisited() {
		return $this->_visited;
	}
	

	function getTree() {
		return $this->_tree;
	}


	/**
	 *  Returns a full url from its relative url
	 *  @param string $url The domain name
	 *  @param string $rel The relative url 
	 */
	private function handleRelative($url, $rel) {
		if (strpos($rel, "http://") !== 0) {
			return $url . '/' . $rel;			
		}
		return $rel;
	}


	/**
	 *  Returns the url of an image from a string of html code.
	 *  If an image link is not present, the empty string is returned.
	 *  @param string $line The html line to be processed
	 */
	private function getImageURL($line) {
		$start = strpos($line, "img src=") + 9;
		$stop1 = stripos($line, ".png") + 4;
		$stop2 = stripos($line, ".jpg") + 4;
		$stop3 = stripos($line, ".gif") + 4;
		if ($start > 9) {
			if ($stop1 > 4) {
				return substr($line, $start, $stop1 - $start);
			}
			elseif ($stop2 > 4) {
				return substr($line, $start, $stop2 - $start);
			}
			elseif ($stop3 > 4) {
				return substr($line, $start, $stop3 - $start);
			}
		}
		return "";
	}


	/**
	 *  Returns the url a string of html code.
	 *  If a url is not present, the empty string is returned.
	 *  @param string $link The html line to be processed
	 */
	private function getURL($link) {
		$shift = strpos($link, "href=") + 6;
		if ($shift > 6 && !eregi("(.css)|(.xml)", $link)) {
			$pos = strpos(substr($link, $shift, strlen($link)), '"');
			return substr($link, $shift, $pos);
		}
		return "";
	}
}
?>
