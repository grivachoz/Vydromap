<?php

	$dbhost="";
	$dbuname="";
	$dbpass="";
	$dbname="";
	include_once("db.php");

	function getXml($tags){
		global $db;

		$res = $db->sql_query("SELECT * FROM teaching_map");

		while($row = $db->sql_fetchrow($res)){
			$coords = explode(",", $row['coords']);
			$objects.="
				<ym:GeoObject>
			        <name>
		        		".$row['title']."
			        </name>
			        <description>
			        	<div style='padding-left: 2px; padding-bottom: 5px;'>
			        		".htmlspecialchars(nl2br($row['description']))."
			        	</div>
						<a class='ui red circular label'></a>
			        	<a class='ui green circular label'></a>
			        </description>
			        <Point>
			          <pos>".$coords[1]." ".$coords[0]."</pos>
			        </Point>
			        <ym:style>".$row['color']."</ym:style>
			    </ym:GeoObject>
			";
		}

		$output='<?xml version="1.0" encoding="utf-8"?>
			<ym:ymaps xmlns:ym="http://maps.yandex.ru/ymaps/1.x">
				<Representation xmlns="http://maps.yandex.ru/representation/1.x">
					<Template xmlns:gml="http://www.opengis.net/gml" gml:id="balloon">
						<text>
							<div class="ui segment">
								<strong>$[name]</strong>
								<div>$[description]</div>
							</div>
						</text>
					</Template>
				</Representation>
			  <ym:GeoObjectCollection xmlns="http://www.opengis.net/gml">
			    <featureMembers>
			    	'.$objects.'
			    </featureMembers>
			    <gml:name xmlns:gml="http://www.opengis.net/gml">N.Maps — news</gml:name>
			    <ym:style>#userobject</ym:style>
			  </ym:GeoObjectCollection>
			</ym:ymaps>
		';
		return $output;
	}

	function saveMark($name, $title, $description, $coords, $color, $tags){
		global $db;

		$res = $db->sql_query("INSERT INTO teaching_map VALUES('',CURRENT_TIMESTAMP,'$name','$title','$description','$coords','$color','$tags')");
		if(!$res){
			return 'false';
		}else{
			return $db->sql_nextid();
		}
	}

	switch ($_GET['task']) {
		case 'save':
			echo saveMark('',$_GET['title'],$_GET['description'],$_GET['coords'],$_GET['color'],'');
			break;
		
		default:
			header("Content-Type: text/xml");
			echo getXml();
			break;
	}

?>