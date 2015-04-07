<?
class controller
{
public $datim;
public $datetoday;
public $includefiles;
public $config;

public function startController() 
{
	$this->datim = date("Y-m-d H:i");
	$this->datetoday = date("Y-m-d");
	$this->includefiles = true;
	$this->config=$this->readConfig();
}

# ========== PRIVATE functions ============= #

private function readConfig()
{
	$configtext=file_get_contents("custom/config.yaml.php");
	$configtext=substr($configtext,strpos($configtext,"?>")+2);
	return $configtext;
}

}

?>