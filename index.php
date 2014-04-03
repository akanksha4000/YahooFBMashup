<?php
				$appid="taKe2ffV34HQHxHFF4q31DxH.u1jCpIMGOTha3gWS2hcbPF9dc2cJsFAB9i4fmYVkV_L";
				$units="";
				$tempUnit =  $_GET["tempUnit"];
				$resultCount=0;
				$xmlText = new SimpleXMLElement('<weather/>');
				$city="";
				
				
				// Make the request
				if($_GET["type"] == "zip")
					$request="http://where.yahooapis.com/v1/concordance/usps/" . $_GET["location"] . "?appid=".$appid;
					//$request="http://where.yahooapis.com/v1/concordance/usps/90007?appid=".$appid;
				else
					$request="http://where.yahooapis.com/v1/places\$and(.q('" . $_GET["location"] . "'),.type(7));start=0;count=1?appid=".$appid;
				
				$xml=@simplexml_load_file(urlencode($request));
				if($_GET["type"] == "zip") {
					if(empty($xml->woeid)) {
							$xmlText->addChild('error', "Weather information cannot be found!");
							Header('Content-type: text/xml');
							echo $xmlText->asXML();
						}
					else {
						$woeid= $xml->woeid;
						loadYahooWeatherXML($woeid,$xmlText,$resultCount,$tempUnit);
						Header('Content-type: text/xml');
						echo $xmlText->asXML();
					}
				}
				else {
					if(empty($xml->place)) {
							$xmlText->addChild('error', "Weather information cannot be found!");
							Header('Content-type: text/xml');
							echo $xmlText->asXML();
						}
					else{
						$woeid= $xml->place[0]->woeid;
						loadYahooWeatherXML($woeid,$xmlText,$resultCount,$tempUnit);
						Header('Content-type: text/xml');
						echo $xmlText->asXML();
					}
				}	
				
				function loadYahooWeatherXML($woeid,&$xmlText,&$resultCount,&$tempUnit) {
				$units="";
				
								
				$request2 = "http://weather.yahooapis.com/forecastrss?w=".$woeid."&u=".$tempUnit;
				$finalxml=simplexml_load_file(urlencode($request2));	
				if($finalxml->channel->item->title != "City not found") {
				//$weather = $xmlText->addChild('weather');
				$xmlText->addChild('feed', "http://weather.yahooapis.com/forecastrss?w=".$woeid."&amp;u=".$tempUnit);
				
				
				$channel=$finalxml->channel;
	
				//link
				$link = $channel->link;
				if($link=="")
						$xmlText->addChild('link', "N/A");
				else
					$xmlText->addChild('link', $link);
				
				//location
				$yweather=$channel->children('http://xml.weather.yahoo.com/ns/rss/1.0')->location; 
				$locationNode = $xmlText->addChild('location');
				foreach($yweather->attributes() as $a => $b) {
						if($a=="city") {
							if($b=="")
								$b="N/A";
							$locationNode->addAttribute('city',$b);
						}
						if($a=="region") {
							if($b=="")
								$b="N/A";
							$locationNode->addAttribute('region',$b);
						}
						if($a=="country") {
							if($b=="")
								$b="N/A";
							$locationNode->addAttribute('country',$b);
						}
				}
				
				
				//units
				$yweather=$channel->children('http://xml.weather.yahoo.com/ns/rss/1.0')->units; 
				$unitsNode = $xmlText->addChild('units');
				foreach($yweather->attributes() as $a => $b) {
						if($a=="temperature") {
							if($b=="")
								$b="N/A";
							$unitsNode->addAttribute('temperature',$b);
						}
				}
				
				//yweather condition
				$yweather=$channel->item->children('http://xml.weather.yahoo.com/ns/rss/1.0')->condition; 
				$conditionNode = $xmlText->addChild('condition');
				
				foreach($yweather->attributes() as $a => $b) {
						if($a=="temp") {
							if($b=="")
								$b="N/A";
							$conditionNode->addAttribute('temp',$b);
						}
						if($a=="text") {
							if($b=="")
								$b="N/A";
							$conditionNode->addAttribute('text',$b);
						}
				}					
				
				//image
				$imgDesc = $channel->item->description;
				if($imgDesc=="")
					$xmlText->addChild('img', "N/A");
				else {
					$regExSrc='/src="(.*?)"/i';
					preg_match($regExSrc,$imgDesc,$imgSrc);
					$xmlText->addChild('img', $imgSrc[1]);
				}
											
				//yweather forecast
				foreach(($channel->item->children('http://xml.weather.yahoo.com/ns/rss/1.0')->forecast) as $yweather) {
				//$yweather=$channel->item->children('http://xml.weather.yahoo.com/ns/rss/1.0')->forecast; 
					$forecastNode = $xmlText->addChild('forecast');
					foreach($yweather->attributes() as $a => $b) {
							if($a=="text") {
								if($b=="")
									$b="N/A";
								$forecastNode->addAttribute('text',$b);
							}
							if($a=="high") {
								if($b=="")
									$b="N/A";
								$forecastNode->addAttribute('high',$b);
							}
							if($a=="day") {
								if($b=="")
									$b="N/A";
								$forecastNode->addAttribute('day',$b);
							}
							if($a=="low") {
								if($b=="")
									$b="N/A";
								$forecastNode->addAttribute('low',$b);
							}
						}
					}
				
				}	
				else {
					$xmlText->addChild('error', $finalxml->channel->item->title . "! Description:" . $finalxml->channel->item->description);
				}
				}
		    ?>