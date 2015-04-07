<?
class model
{
public $datim;
public $datetoday;

public function startModel() 
{
	$this->datim = date("Y-m-d H:i");
	$this->datetoday = date("Y-m-d");
}

}

?>