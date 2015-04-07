<?
class view
{
public $datim;
public $datetoday;

public function startView() 
{
	$this->datim = date("Y-m-d H:i");
	$this->datetoday = date("Y-m-d");
}

}

?>