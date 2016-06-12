<?php
if(!defined('PHPQUERY_LOADER')) {
	include('../../index.html');
	die();
}

class YouTube
{
	protected $videoInfoLink = 'http://www.youtube.com/get_video_info?video_id={%ID%}&asv=3&el=detailpage&hl=en_US';
	protected $videoID = null;
	protected $info = array();
	protected $url = array();
	
	public $viewCount = 0;
	public $uid = 0;
	public $thumbnail = 0;
	public $title = null;
	public $duration = 0; //in seconds
	public $formats = array();
	
	public function __construct($ytID)
	{
		$this->videoID = $this->parseID($ytID);
		$this->videoInfoLink = str_replace('{%ID%}', $this->videoID, $this->videoInfoLink);
	}
	
	public function getInfo($curlCalleable)
	{
		
		$ytData = $curlCalleable($this->videoInfoLink);
		parse_str($ytData, $info);
		$this->info = $info;
		
		if(!isset($info['url_encoded_fmt_stream_map'])) return false;
		
		// general information
		$this->title = $this->info['title'];
		$this->viewCount = $this->info['view_count'];
		$this->uid = $this->info['uid'];
		$this->thumbnail = $this->info['thumbnail_url'];
		$this->duration = $this->info['length_seconds'];
		
		$map = array();
		$formats = explode(',', $info['url_encoded_fmt_stream_map']);
		foreach($formats as $format)
		{
			parse_str($format, $map);
			$this->formats[] = array(
					'type' => explode(';',$map['type'])[0],
					'quality' => $map['quality'],
					'url' => $map['url']
			);
		}
		return true;
	}
	
	public function formatSeconds($seconds)
	{
		$mins = 0;
		$hours = 0;
		$out = '';
		if($seconds >= 60){
			$mins = floor($seconds/60);
			$seconds = $seconds % 60;
		}
		if($mins >= 60)
		{
			$hours = floor($mins/60);
			$mins = $mins % 60;
		}
		return ($hours<10 ? '0'.$hours : $hours).':'.($mins<10 ? '0'.$mins : $mins).':'.($seconds<10 ? '0'.$seconds : $seconds);
	}
	
	protected function parseID($url)
	{
		$matches = null;
		preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
		if(isset($matches[1]) && !empty($matches[1])) return $matches[1]; else return $id;
	}
}