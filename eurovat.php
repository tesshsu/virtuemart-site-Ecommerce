<?php

	
function EuroVAT($country){
		$europe=array(
			"AD"	=> "Andorra",
			"AL"	=> "Albania",
			"AT"	=> "Austria",
			"BA"	=> "Bosnia-Hercegovina",
			"BE"	=> "Belgium",
			"BG"	=> "Bulgaria",
			"BY"	=> "Belarus",
			"CY"	=> "Cyprus",
			"CZ"	=> "Czech Republic",
			"DE"	=> "Germany",
			"DK"	=> "Denmark",
			"EE"	=> "Estonia",
			"ES"	=> "Spain",
			"FI"	=> "Finland, Suomi",
			"FO"	=> "Faroe Islands (DK)",
			"FR"	=> "France",
			"GG"	=> "Guernsey (UK)",
			"GI"	=> "Gibraltar (UK)",
			"GR"	=> "Greece",
			"HR"	=> "Croatia",
			"HU"	=> "Hungary",
			"IE"	=> "Ireland",
			"IM"	=> "Isle of Man (UK)",
			"IS"	=> "Iceland",
			"IT"	=> "Italy",
			"JE"	=> "Jersey (UK)",
			"LI"	=> "Liechtenstein",
			"LT"	=> "Lithuania",
			"LU"	=> "Luxemburg",
			"LV"	=> "Latvia",
			"MC"	=> "Monaco",
			"MD"	=> "Moldova",
			"MK"	=> "Macedonia",
			"MT"	=> "Malta",
			"NL"	=> "Netherlands",
			"PL"	=> "Poland",
			"PT"	=> "Portugal",
			"RO"	=> "Romania",
			"SE"	=> "Sweden",
			"SI"	=> "Slovenia",
			"SJ"	=> "Svalbard and Jan Mayen Islands (NO)",
			"SK"	=> "Slovakia",
			"SM"	=> "San Marino",
			"TR"	=> "Turkey, West",
			"UK"	=> "United Kingdom",
			"GB"	=> "United Kingdom",
			"VA"	=> "Vatican City State (IT)",
			"YU"	=> "Yugoslavia Jугославиjа"
		);
		
		if (array_key_exists($country, $europe)) {
			return true;
		}
		else{
			return false;
		}
	}
