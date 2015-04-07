<?php

//	functions.php

// sql 
function Xsql($sql)
{
//	echo "$sql<br>"; #DEBUG
	global $db;
	$querytype="x" . substr($sql,0,15);
	$isselect=strpos(strtoupper($querytype),"SELECT");
	$queryresult = mysqli_query($db,$sql);

	if ( !$isselect )	
	{
		$dummy=array();
		return $dummy;
	}

	$row=array();
	while ($currentrow = $queryresult->fetch_assoc()) 
 		$row[]=$currentrow;

	return $row;
}

# ====================[ DBInsert ]==================== #

function DBInsert($arrfields,$dbuser,$bladzijde='NORMAL')
{
/*
	gebruik van array om velden op te vullen 
	opmerkingen
		* JAAR en MAAND worden automatisch berekend
		* LOCKER is altijd leeg bij creatie van een nieuw record
		
	$arrfields=array(
		"DATUM"=>$datum,
		"TITEL"=>$titel,
		"TEKST"=>$tekst,
		"TAG"=>$tag,
		"PERSOON"=>$persoon,
		"CONF"=>$conf,
		"FOTOALBUM"=>$fotoalbum,
		"LOCKER"=>'',
		"LOCKERALBUM"=>''
	);	
	
	$bladzijde : NORMAL of DAGNOTITIE
*/

	if(!is_array($arrfields))
		exit("ERROR : array expected in function DBInsert");
	
	if(empty($dbuser))
		exit("ERROR : incorrect tablename in function DBInsert");
	
	$today=date("Y-m-d");

	// fields in array
	$datum=(isset($arrfields["DATUM"])) ? $arrfields["DATUM"] : $today;
	$titel=(isset($arrfields["TITEL"])) ? $arrfields["TITEL"] : "-- zonder titel --";
	$tekst=(isset($arrfields["TEKST"])) ? $arrfields["TEKST"] : "";
	$notitie=(isset($arrfields["NOTITIE"])) ? $arrfields["NOTITIE"] : "";
	list($jaar,$maand,$dag)=explode("-",$datum);
	$tag=(isset($arrfields["TAG"])) ? $arrfields["TAG"] : "";
	$persoon=(isset($arrfields["PERSOON"])) ? $arrfields["PERSOON"] : "";
	$conf=(isset($arrfields["CONF"])) ? $arrfields["CONF"] : 0;
	$confa=(isset($arrfields["CONFA"])) ? $arrfields["CONFA"] : 0;
	$confb=(isset($arrfields["CONFB"])) ? $arrfields["CONFB"] : 0;	
	$fotoalbum=(isset($arrfields["FOTOALBUM"])) ? $arrfields["FOTOALBUM"] : "";
	$locker='';
	$lockeralbum='';

	// format fields for database
	$id=0;
	$titel=addslashes($titel);
	$tekst=addslashes($tekst);
	$notitie=addslashes($notitie);

	if($bladzijde=='NORMAL')
	{
		# is er al een DAGNOTITIE met een lege bladzijde, dan UPDATE
		$sqlpag="SELECT * FROM $dbuser WHERE datum='$datum'";
		$ressqlpag=Xsql($sqlpag);			
		if(!empty($ressqlpag))
		{
			$pagid=$ressqlpag[0]["id"];
			$paginatekst=trim($ressqlpag[0]["tekst"]);
			if(empty($paginatekst))
			{
				$sql="UPDATE $dbuser SET tekst='$tekst',titel='$titel' WHERE id=$pagid";
			}
			else
			{
				$sql="INSERT INTO $dbuser VALUES ($id,'$datum','$titel','$tekst','$notitie','$jaar','$maand','$tag','$persoon',$conf,$confa,$confb,'$fotoalbum','$locker','$lockeralbum')";		
			}
		}
		else		
			$sql="INSERT INTO $dbuser VALUES ($id,'$datum','$titel','$tekst','$notitie','$jaar','$maand','$tag','$persoon',$conf,$confa,$confb,'$fotoalbum','$locker','$lockeralbum')";
	}
	
	if($bladzijde=='DAGNOTITIE')
	{
		$titel="Dagnotities";
		# is er al een dagnotitie voor deze datum
		$sql="SELECT * FROM $dbuser WHERE datum='$datum'";
		$ressql=Xsql($sql);			
		$pretext=(substr($tekst,0,1)==":") ? "" : ":-:";
		
		if(empty($ressql))
			$sql="INSERT INTO $dbuser VALUES ($id,'$datum','$titel','','$pretext $tekst','$jaar','$maand','$tag','$persoon',$conf,$confa,$confb,'$fotoalbum','$locker','$lockeralbum')";
		else
		{
			$id=$ressql[0]["id"];
			$newtekst=addslashes($ressql[0]["notitie"]) . "\r\n\r\n$pretext " . $tekst;
			$sql="UPDATE $dbuser SET notitie='$newtekst' WHERE id=$id";
		}
	}
	
	Xsql($sql);			

	$sql="SELECT LAST_INSERT_ID() as lastid";
	$ressql=Xsql($sql);
	$lastid=$ressql[0]["lastid"];
	return $lastid;
}

# ====================[ GetTextbetween ]==================== #
// GetTextbetween() returns the substring beginning after firstchar and ending at secondchar
// ex. GetTextbetween("{SQL-","-SQL}",$fielddetails)
function GetTextbetween($delimleft,$delimright, $text)
{
	if ( !strpos("@".$text,$delimleft) && !strpos("@".$text,$delimright) )
		return "";
		
	$startpos=strpos($text,$delimleft)+strlen($delimleft);
	$endpos=strpos($text,$delimright,$startpos);
	$retval=substr($text,$startpos,$endpos-$startpos);
	return $retval;
}


# ====================[ ShowError ]==================== #
function ShowError($type,$message)
{
		global $module;
		global $cssfile; 

		echo "
		<div class='error center'>
		<div style='font-size: 2em; margin-bottom: 2em;'>$type</div>
		$message
		</div>

		<div class='center'>
		<a href='javascript:history.go(-1)'>probeer opnieuw</a> | 
		<a href='index.php'>home</a></div>
		
		</div>
		";
		
		include("footer.php");
		exit;
}

# ====================[ debug ]==================== #
function debug($var,$continue=true)
{
	echo "<pre>";
	print_r($var);
	echo "</pre>";
	if(!$continue)
		exit;

	return;
}

?>