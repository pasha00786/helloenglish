<?php
$path="/home/ashutosh/Desktop/culture_alley/audio/";
$obj=new SoxScripts($path);
$obj->kidsAppSentenceProcess();
class SoxScripts{
	var $lastPath;
	var $rootPath;
	function __construct($path){
		$this->lastPath=$path;
		$this->rootPath=$path;
	}
	function kidsAppProcess(){
		$this->checkFiles("mp3");
		$this->convertMp3ToWav();
		$this->trimSilence();
		$this->normailizeAudios();
		$this->downsampleWav();
		$this->convertWavToMp3();
	}
	function kidsAppSentenceProcess(){
		$this->checkFiles("mp3");
		$this->convertMp3ToWav();
		$this->normailizeAudios();
		$this->downsampleWav();
		$this->convertWavToMp3();
	}
	
	function multiplayerProcess(){
		// $this->checkFiles("mp3");
		// $this->convertMp3ToWav();
		$this->normailizeAudios();
		$this->downsampleWav();
		$this->convertToMono();
		$this->convertWavToMp3();
	}
	function advanceConversationProcess(){
		$this->checkFiles("wav");
		$this->normailizeAudios();
		$this->downsampleWav();
		$this->convertWavToMp3();
	}
	function wavenetToKidsAudios(){
		$this->checkFiles("wav");
		// $this->trimSilence();
		$this->normailizeAudios();
		$this->downsampleWav();
		$this->convertWavToMp3();	
	}
	function checkFiles($extension=null){
		$directory = $this->lastPath;
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		$isError=false;
		foreach ($scanned_directory as $key => $value) {
			$fileName=$directory.$value;
			if(strpos($fileName," ")>-1){
				echo "File Name Not Valid:". $value."\n";
				$isError=true;
			}
			if($extension!=null){
				if(pathinfo(strtolower($fileName),PATHINFO_EXTENSION) != $extension){
					echo "File Extension Not Valid:". $value."\n";
					$isError=true;
				}
			}
		}
		if($isError)
			exit();
	}
	function convertWavToMp3(){
		$directory = $this->lastPath;
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		$newFolderPath=$this->rootPath."NormAudiosMp3/";
		if(!is_dir($newFolderPath))
			mkdir($newFolderPath,0777,true);
		foreach ($scanned_directory as $key => $value) {
			exec("sox ".$directory.$value." ".$newFolderPath.basename($value,".wav").".mp3");	
		}
		$this->lastPath=$newFolderPath;
	}
	function convertMp3ToWav(){
		$directory = $this->lastPath;
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		$newFolderPath=$this->rootPath."WavAudios/";
		if(!is_dir($newFolderPath))
			mkdir($newFolderPath,0777,true);
		foreach ($scanned_directory as $key => $value) {
			exec("sox ".$directory.$value." ".$newFolderPath.basename($value,".mp3").".wav");	
		}
		$this->lastPath=$newFolderPath;
	}
	function trimSilence(){
		$directory = $this->lastPath;
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		$newFolderPath=$this->rootPath."SilenceTrimAudios/";
		if(!is_dir($newFolderPath))
			mkdir($newFolderPath,0777,true);
		foreach ($scanned_directory as $key => $value) {
			exec("sox ".$directory.$value." ".$newFolderPath.$value." silence 1 0.1 1% -1 2.0 1%");	
		}
		$this->lastPath=$newFolderPath;
	}
	function normailizeAudios(){
		$directory = $this->lastPath;
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		$newFolderPath=$this->rootPath."NormAudios/";
		if(!is_dir($newFolderPath))
			mkdir($newFolderPath,0777,true);
		foreach ($scanned_directory as $key => $value) {
			exec("sox --norm=-0.1 ".$directory.$value." ".$newFolderPath.$value);	
		}
		$this->lastPath=$newFolderPath;	
	}
	function downsampleWav($sampleRate=22050){
		$directory = $this->lastPath;
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		$newFolderPath=$this->rootPath."DownsampleWav/";
		if(!is_dir($newFolderPath))
			mkdir($newFolderPath,0777,true);
		foreach ($scanned_directory as $key => $value) {
			if(exec("soxi -r ".$directory.$value)>$sampleRate){
				exec("sox ".$directory.$value." ".$newFolderPath.$value." norm rate ".$sampleRate);	
				echo "Downsample"."\n";
			}
			else{
				exec("sox ".$directory.$value." ".$newFolderPath.$value);	
			}
		}
		$this->lastPath=$newFolderPath;		
	}
	function convertToMono(){
		$directory = $this->lastPath;
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		$newFolderPath=$this->rootPath."DownsampleMonoWav/";
		if(!is_dir($newFolderPath))
			mkdir($newFolderPath,0777,true);
		foreach ($scanned_directory as $key => $value) {
			exec("sox ".$directory.$value." ".$newFolderPath.$value." remix 1,2");
		}
		$this->lastPath=$newFolderPath;		
	}
	function increaseVolume($newVolume){
		$directory = $this->lastPath;
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		$newFolderPath=$this->rootPath."IncreaseVolume/";
		if(!is_dir($newFolderPath))
			mkdir($newFolderPath,0777,true);
		foreach ($scanned_directory as $key => $value) {
			exec("sox  -v ".$newVolume." ".$directory.$value." ".$newFolderPath.$value);
		}
		$this->lastPath=$newFolderPath;			
	}
	function decreaseVolume($newVolume){
		$directory = $this->lastPath;
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		$newFolderPath=$this->rootPath."DecreaseVolume/";
		if(!is_dir($newFolderPath))
			mkdir($newFolderPath,0777,true);
		foreach ($scanned_directory as $key => $value) {
			exec("sox  -v ".$newVolume." ".$directory.$value." ".$newFolderPath.$value);
		}
		$this->lastPath=$newFolderPath;			
	}
	function combinedAudio(){
		$directory = $this->lastPath;
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		$appendFiles="";
		foreach ($scanned_directory as $key => $value) {
			$appendFiles.=$directory.$value." ";
		}
		$newFolderPathOut=$directory."CombinedOutput/";
		if(!is_dir($newFolderPathOut))
			mkdir($newFolderPathOut,0777,true);
		exec("sox ".$appendFiles.$newFolderPathOut."output.wav");
		exec("sox ".$newFolderPathOut."output.wav ".$newFolderPathOut."output.mp3");

	}
}
