<?php
/**
 * Locale helper
 *
 * @package    Ush_Locale
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class ush_locale_Core
{
	/**
	 * @param   string	 ISO-639 language code
	 */
	public static function language($iso639)
	{
		$iso_array = array (
			"aa" => "Afaraf",
			"ab" => "Аҧсуа",
			"ae" => "Avesta",
			"af" => "Afrikaans",
			"ak" => "Akan",
			"am" => "አማርኛ",
			"an" => "Aragonés",
			"ar" => "العربية",
			"as" => "অসমীয়া",
			"av" => "авар мацӀ, магӀарул мацӀ",
			"ay" => "Aymar aru",
			"az" => "Azərbaycan dili",
			"ba" => "башҡорт теле",
			"be" => "Беларуская",
			"bg" => "Български",
			"bh" => "भोजपुरी",
			"bi" => "Bislama",
			"bm" => "Bamanankan",
			"bn" => "বাংলা",
			"bo" => "བོད་ཡིག",
			"br" => "Brezhoneg",
			"bs" => "Bosanski",
			"ca" => "Català",
			"ce" => "нохчийн мотт",
			"ch" => "Chamoru",
			"co" => "corsu, lingua corsa",
			"cr" => "ᓀᐦᐃᔭᐍᐏᐣ",
			"cs" => "česky, čeština",
			"cu" => "ѩзыкъ словѣньскъ",
			"cv" => "чӑваш чӗлхи",
			"cy" => "Cymraeg",
			"da" => "Dansk",
			"de" => "Deutsch",
			"dr" => "دری",
			"dv" => "ދިވެހި",
			"dz" => "རྫོང་ཁ",
			"ee" => "Eʋegbe",
			"el" => "Ελληνικά",
			"en" => "English",
			"eo" => "Esperanto",
			"es" => "Español",
			"et" => "Eesti",
			"eu" => "Euskara",
			"fa" => "فارسی",
			"ff" => "Fulfulde, Pulaar, Pular",
			"fi" => "Suomi",
			"fj" => "Vosa Vakaviti",
			"fo" => "Føroyskt",
			"fr" => "Français",
			"fy" => "Frysk",
			"ga" => "Gaeilge",
			"gd" => "Gàidhlig",
			"gl" => "Galego",
			"gn" => "Avañe'ẽ",
			"gu" => "ગુજરાતી",
			"gv" => "Gaelg, Gailck",
			"ha" => "Hausa, هَوُسَ",
			"he" => "עברית",
			"hi" => "हिन्दी, हिंदी",
			"ho" => "Hiri Motu",
			"hr" => "hrvatski",
			"ht" => "Kreyòl ayisyen",
			"hu" => "Magyar",
			"hy" => "Հայերեն",
			"hz" => "Otjiherero",
			"ia" => "Interlingua",
			"id" => "Bahasa Indonesia",
			"ie" => "Interlingue",
			"ig" => "Igbo",
			"ii" => "ꆇꉙ",
			"ik" => "Iñupiaq, Iñupiatun",
			"io" => "Ido",
			"is" => "Íslenska",
			"it" => "Italiano",
			"iu" => "ᐃᓄᒃᑎᑐᑦ",
			"ja" => "日本語 (にほんご／にっぽんご)",
			"jv" => "Basa Jawa",
			"ka" => "ქართული",
			"kg" => "KiKongo",
			"ki" => "Gĩkũyũ",
			"kj" => "Kuanyama",
			"kk" => "Қазақ тілі",
			"kl" => "kalaallisut, kalaallit oqaasii",
			"km" => "ភាសាខ្មែរ",
			"kn" => "ಕನ್ನಡ",
			"ko" => "한국어 (韓國語), 조선말 (朝鮮語)",
			"kr" => "Kanuri",
			"ks" => "कश्मीरी, كشميري‎",
			"ku" => "Kurdî, كوردی‎",
			"kv" => "Коми",
			"kw" => "Kernewek",
			"ky" => "кыргыз тили",
			"la" => "Latine",
			"lb" => "Lëtzebuergesch",
			"lg" => "Luganda",
			"li" => "Limburgs",
			"ln" => "Lingála",
			"lo" => "ພາສາລາວ",
			"lt" => "Lietuvių",
			"lu" => "Luba-Katanga",
			"lv" => "Latviešu",
			"mg" => "Malagasy fiteny",
			"mh" => "Kajin M̧ajeļ",
			"mi" => "te reo Māori",
			"mk" => "Македонски",
			"ml" => "മലയാളം",
			"mn" => "Монгол",
			"mr" => "मराठी",
			"ms" => "bahasa Melayu, بهاس ملايو‎",
			"mt" => "Malti",
			"my" => "ဗမာစာ",
			"na" => "Ekakairũ Naoero",
			"nb" => "Norsk bokmål",
			"nd" => "isiNdebele",
			"ne" => "नेपाली",
			"ng" => "Owambo",
			"nl" => "Nederlands",
			"nn" => "Norsk nynorsk",
			"no" => "Norsk",
			"nr" => "isiNdebele",
			"nv" => "Diné bizaad, Dinékʼehǰí",
			"ny" => "chiCheŵa, chinyanja",
			"oc" => "Occitan",
			"oj" => "ᐊᓂᔑᓈᐯᒧᐎᓐ",
			"om" => "Afaan Oromoo",
			"or" => "ଓଡ଼ିଆ",
			"os" => "Ирон æвзаг",
			"pa" => "ਪੰਜਾਬੀ, پنجابی‎",
			"pi" => "पाऴि",
			"pl" => "Polski",
			"ps" => "پښتو",
			"pt" => "Português",
			"qu" => "Runa Simi, Kichwa",
			"rm" => "rumantsch grischun",
			"rn" => "kiRundi",
			"ro" => "română",
			"ru" => "Русский",
			"rw" => "Ikinyarwanda",
			"sa" => "संस्कृतम्",
			"sc" => "sardu",
			"sd" => "सिन्धी, سنڌي، سندھی‎",
			"se" => "Davvisámegiella",
			"sg" => "yângâ tî sängö",
			"si" => "සිංහල",
			"sk" => "Slovenčina",
			"sl" => "Slovenščina",
			"sm" => "gagana fa'a Samoa",
			"sn" => "chiShona",
			"so" => "Soomaaliga, af Soomaali",
			"sq" => "Shqip",
			"sr" => "Српски",
			"ss" => "SiSwati",
			"st" => "Sesotho",
			"su" => "Basa Sunda",
			"sv" => "Svenska",
			"sw" => "Kiswahili",
			"ta" => "தமிழ்",
			"te" => "తెలుగు",
			"tg" => "тоҷикӣ, toğikī, تاجیکی‎",
			"th" => "ไทย",
			"ti" => "ትግርኛ",
			"tk" => "Türkmen, Түркмен",
			"tl" => "Wikang Tagalog, ᜏᜒᜃᜅ᜔ ᜆᜄᜎᜓᜄ᜔",
			"tn" => "Setswana",
			"to" => "faka Tonga",
			"tq" => "tlhIngan Hol (Klingon)",
			"tr" => "Türkçe",
			"ts" => "Xitsonga",
			"tt" => "татарча, tatarça, تاتارچا‎",
			"tw" => "Twi",
			"ty" => "Reo Mā`ohi",
			"ug" => "Uyƣurqə, ئۇيغۇرچە‎",
			"uk" => "Українська",
			"ur" => "اردو",
			"uz" => "O'zbek, Ўзбек, أۇزبېك‎",
			"ve" => "Tshivenḓa",
			"vi" => "Tiếng Việt",
			"vo" => "Volapük",
			"wa" => "Walon",
			"wo" => "Wollof",
			"xh" => "isiXhosa",
			"yi" => "ייִדיש",
			"yo" => "Yorùbá",
			"za" => "Saɯ cueŋƅ, Saw cuengh",
			"zh" => "中文 (Zhōngwén), 汉语, 漢語",
			"zu" => "isiZulu",
		);

		if (array_key_exists($iso639, $iso_array))
		{
			return $iso_array[$iso639];
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * @param   string	 ISO-3166 country code
	 */
	public static function country($iso3166)
	{
		$iso_array = array(
			"AD" => "Andorra",
			"AE" => "United Arab Emirates",
			"AF" => "Afghanistan",
			"AG" => "Antigua and Barbuda",
			"AI" => "Anguilla",
			"AL" => "Albania",
			"AM" => "Armenia",
			"AN" => "Netherlands Antilles",
			"AO" => "Angola",
			"AQ" => "Antarctica",
			"AR" => "Argentina",
			"AS" => "American Samoa",
			"AT" => "Austria",
			"AU" => "Australia",
			"AW" => "Aruba",
			"AX" => "Aland Islands",
			"AZ" => "Azerbaijan",
			"BA" => "Bosnia and Herzegovina",
			"BB" => "Barbados",
			"BD" => "Bangladesh",
			"BE" => "Belgium",
			"BF" => "Burkina Faso",
			"BG" => "Bulgaria",
			"BH" => "Bahrain",
			"BI" => "Burundi",
			"BJ" => "Benin",
			"BM" => "Bermuda",
			"BN" => "Brunei Darussalam",
			"BO" => "Bolivia",
			"BR" => "Brazil",
			"BS" => "Bahamas",
			"BT" => "Bhutan",
			"BV" => "Bouvet Island",
			"BW" => "Botswana",
			"BY" => "Belarus",
			"BZ" => "Belize",
			"CA" => "Canada",
			"CC" => "Cocos (Keeling) Islands",
			"CD" => "Congo, The Democratic Republic of the",
			"CF" => "Central African Republic",
			"CG" => "Congo",
			"CH" => "Switzerland",
			"CI" => "Cote d'Ivoire",
			"CK" => "Cook Islands",
			"CL" => "Chile",
			"CM" => "Cameroon",
			"CN" => "China",
			"CO" => "Colombia",
			"CR" => "Costa Rica",
			"CU" => "Cuba",
			"CV" => "Cape Verde",
			"CX" => "Christmas Island",
			"CY" => "Cyprus",
			"CZ" => "Czech Republic",
			"DE" => "Germany",
			"DJ" => "Djibouti",
			"DK" => "Denmark",
			"DM" => "Dominica",
			"DO" => "Dominican Republic",
			"DZ" => "Algeria",
			"EC" => "Ecuador",
			"EE" => "Estonia",
			"EG" => "Egypt",
			"EH" => "Western Sahara",
			"ER" => "Eritrea",
			"ES" => "Spain",
			"ET" => "Ethiopia",
			"FI" => "Finland",
			"FJ" => "Fiji",
			"FK" => "Falkland Islands (Malvinas)",
			"FM" => "Micronesia, Federated States of",
			"FO" => "Faroe Islands",
			"FR" => "France",
			"GA" => "Gabon",
			"GB" => "United Kingdom",
			"GD" => "Grenada",
			"GE" => "Georgia",
			"GF" => "French Guiana",
			"GG" => "Guernsey",
			"GH" => "Ghana",
			"GI" => "Gibraltar",
			"GL" => "Greenland",
			"GM" => "Gambia",
			"GN" => "Guinea",
			"GP" => "Guadeloupe",
			"GQ" => "Equatorial Guinea",
			"GR" => "Greece",
			"GS" => "South Georgia and the South Sandwich Islands",
			"GT" => "Guatemala",
			"GU" => "Guam",
			"GW" => "Guinea-Bissau",
			"GY" => "Guyana",
			"HK" => "Hong Kong",
			"HM" => "Heard Island and McDonald Islands",
			"HN" => "Honduras",
			"HR" => "Croatia",
			"HT" => "Haiti",
			"HU" => "Hungary",
			"ID" => "Indonesia",
			"IE" => "Ireland",
			"IL" => "Israel",
			"IM" => "Isle of Man",
			"IN" => "India",
			"IO" => "British Indian Ocean Territory",
			"IQ" => "Iraq",
			"IR" => "Iran, Islamic Republic of",
			"IS" => "Iceland",
			"IT" => "Italy",
			"JE" => "Jersey",
			"JM" => "Jamaica",
			"JO" => "Jordan",
			"JP" => "Japan",
			"KE" => "Kenya",
			"KG" => "Kyrgyzstan",
			"KH" => "Cambodia",
			"KI" => "Kiribati",
			"KM" => "Comoros",
			"KN" => "Saint Kitts and Nevis",
			"KP" => "Korea, Democratic People's Republic of",
			"KR" => "Korea, Republic of",
			"KW" => "Kuwait",
			"KY" => "Cayman Islands",
			"KZ" => "Kazakhstan",
			"LA" => "Lao People's Democratic Republic",
			"LB" => "Lebanon",
			"LC" => "Saint Lucia",
			"LI" => "Liechtenstein",
			"LK" => "Sri Lanka",
			"LR" => "Liberia",
			"LS" => "Lesotho",
			"LT" => "Lithuania",
			"LU" => "Luxembourg",
			"LV" => "Latvia",
			"LY" => "Libyan Arab Jamahiriya",
			"MA" => "Morocco",
			"MC" => "Monaco",
			"MD" => "Moldova, Republic of",
			"ME" => "Montenegro",
			"MG" => "Madagascar",
			"MH" => "Marshall Islands",
			"MK" => "Macedonia",
			"ML" => "Mali",
			"MM" => "Myanmar",
			"MN" => "Mongolia",
			"MO" => "Macao",
			"MP" => "Northern Mariana Islands",
			"MQ" => "Martinique",
			"MR" => "Mauritania",
			"MS" => "Montserrat",
			"MT" => "Malta",
			"MU" => "Mauritius",
			"MV" => "Maldives",
			"MW" => "Malawi",
			"MX" => "Mexico",
			"MY" => "Malaysia",
			"MZ" => "Mozambique",
			"NA" => "Namibia",
			"NC" => "New Caledonia",
			"NE" => "Niger",
			"NF" => "Norfolk Island",
			"NG" => "Nigeria",
			"NI" => "Nicaragua",
			"NL" => "Netherlands",
			"NO" => "Norway",
			"NP" => "Nepal",
			"NR" => "Nauru",
			"NU" => "Niue",
			"NZ" => "New Zealand",
			"OM" => "Oman",
			"PA" => "Panama",
			"PE" => "Peru",
			"PF" => "French Polynesia",
			"PG" => "Papua New Guinea",
			"PH" => "Philippines",
			"PK" => "Pakistan",
			"PL" => "Poland",
			"PM" => "Saint Pierre and Miquelon",
			"PN" => "Pitcairn",
			"PR" => "Puerto Rico",
			"PS" => "Palestinian Territory",
			"PT" => "Portugal",
			"PW" => "Palau",
			"PY" => "Paraguay",
			"QA" => "Qatar",
			"RE" => "Reunion",
			"RO" => "Romania",
			"RS" => "Serbia",
			"RU" => "Russian Federation",
			"RW" => "Rwanda",
			"SA" => "Saudi Arabia",
			"SB" => "Solomon Islands",
			"SC" => "Seychelles",
			"SD" => "Sudan",
			"SE" => "Sweden",
			"SG" => "Singapore",
			"SH" => "Saint Helena",
			"SI" => "Slovenia",
			"SJ" => "Svalbard and Jan Mayen",
			"SK" => "Slovakia",
			"SL" => "Sierra Leone",
			"SM" => "San Marino",
			"SN" => "Senegal",
			"SO" => "Somalia",
			"SR" => "Suriname",
			"ST" => "Sao Tome and Principe",
			"SV" => "El Salvador",
			"SY" => "Syrian Arab Republic",
			"SZ" => "Swaziland",
			"TC" => "Turks and Caicos Islands",
			"TD" => "Chad",
			"TF" => "French Southern Territories",
			"TG" => "Togo",
			"TH" => "Thailand",
			"TJ" => "Tajikistan",
			"TK" => "Tokelau",
			"TL" => "Timor-Leste",
			"TM" => "Turkmenistan",
			"TN" => "Tunisia",
			"TO" => "Tonga",
			"TR" => "Turkey",
			"TT" => "Trinidad and Tobago",
			"TV" => "Tuvalu",
			"TW" => "Taiwan",
			"TZ" => "Tanzania, United Republic of",
			"UA" => "Ukraine",
			"UG" => "Uganda",
			"UM" => "United States Minor Outlying Islands",
			"US" => "United States",
			"UY" => "Uruguay",
			"UZ" => "Uzbekistan",
			"VA" => "Holy See (Vatican City State)",
			"VC" => "Saint Vincent and the Grenadines",
			"VE" => "Venezuela",
			"VG" => "Virgin Islands, British",
			"VI" => "Virgin Islands, U.S.",
			"VN" => "Vietnam",
			"VU" => "Vanuatu",
			"WF" => "Wallis and Futuna",
			"WS" => "Samoa",
			"YE" => "Yemen",
			"YT" => "Mayotte",
			"ZA" => "South Africa",
			"ZM" => "Zambia",
			"ZW" => "Zimbabwe"
		);

		if (array_key_exists($iso3166, $iso_array))
		{
			return $iso_array[$iso3166];
		}
		else
		{
			return Kohana::lang('ui_admin.unknown');
		}
	}

	/**
	 * checks the i18n folder to see what folders we have available
	 * @param boolean Force reloading locale cache
	 */
	public static function get_i18n($refresh = FALSE)
	{
		// If we had cached locales return those
		if (! $refresh)
		{
			$locales = Cache::instance()->get('locales');
			if ( $locales )
			{
				return $locales;
			}
		}

		$locales = array();

		// i18n path
		$i18n_path = APPPATH.'i18n/';

		// i18n folder
		$i18n_folder = @ opendir($i18n_path);

		if ( !$i18n_folder )
			return false;

		while ( ($i18n_dir = readdir($i18n_folder)) !== false )
		{
			if ( is_dir($i18n_path.$i18n_dir) && is_readable($i18n_path.$i18n_dir) )
			{
				// Strip out .  and .. and any other stuff
				if ( $i18n_dir{0} == '.' || $i18n_dir == '..'
				 	|| $i18n_dir ==  '.DS_Store' || $i18n_dir == '.git')
					continue;

				$locale = explode("_", $i18n_dir);
				if ( count($locale) < 2 AND ! ush_locale::language($locale[0]))
					continue;

				$locales[$i18n_dir] = ush_locale::language($locale[0]) ? ush_locale::language($locale[0]) : $locale[0];
				$locales[$i18n_dir] .= isset($locale[1]) ? " (".$locale[1].")" : "";
			}
		}

		if ( is_dir( $i18n_dir ) )
			@closedir( $i18n_dir );

		Cache::instance()->set('locales', $locales, array('locales'), 604800);

		return $locales;
	}
	
	/**
	 * Detect language from GET param, session or settings.
	 * @param string 
	 */
	public static function detect_language($language = FALSE)
	{
		// Locale form submitted?
		if (isset($_GET['l']) && !empty($_GET['l']))
		{
			Session::instance()->set('locale', $_GET['l']);
		}

		// Has a locale session been set?
		if (Session::instance()->get('locale',FALSE))
		{
			// Change current locale
			Kohana::config_set('locale.language', $_SESSION['locale']);
		}
	}
}
