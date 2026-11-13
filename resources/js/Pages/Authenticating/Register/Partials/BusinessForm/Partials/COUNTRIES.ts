export type Country = {
  name: string;
  currencies: {
    name: string;
    symbol?: string;
  }[];
  languages: string[];
  postalCode?: {
    format: string;
    regex?: string;
  };
  /** Flag file name (ISO 3166-1 alpha-2 code); see Utilities/flagUrl.ts. */
  flag: string;
  emoji: string;
  countryCallingCode: string;
  tax?: number;
};
/**

json
Lao kip
Laos
dalasi
Gambia
South African rand
Namibian dollar
Namibia
Mozambican metical
Mozambique
Central African CFA franc
Cameroon
Ukrainian hryvnia
Ukraine
Mauritanian ouguiya
Mauritania
West African CFA franc
Mali
Zimbabwean dollar
Zimbabwe
Azerbaijani manat
Azerbaijan
Euro
Cyprus
Egyptian pound
Egypt
Norwegian krone
Norway
West African CFA franc
Guinea-Bissau
Saudi riyal
Saudi Arabia
Danish krone
Denmark
Indian rupee
India
Afghan afghani
Afghanistan
Romanian leu
Romania
Serbian dinar
Serbia
New Zealand dollar
New Zealand
Iraqi dinar
Iraq
Ghanaian cedi
Ghana
Tunisian dinar
Tunisia
Angolan kwanza
Angola
Euro
Lithuania
New Taiwan dollar
Taiwan
North Korean won
North Korea
United States dollar
Puerto Rico
South Korean won
South Korea
United States dollar
Ecuador
Costa Rican colón
Costa Rica
Papua New Guinean kina
Papua New Guinea
Bulgarian lev
Bulgaria
United States dollar
United States
Euro
Germany
Moroccan dirham
Morocco
Uruguayan peso
Uruguay
Guatemalan quetzal
Guatemala
Bangladeshi taka
Bangladesh
Indonesian rupiah
Indonesia
Bahraini dinar
Bahrain
West African CFA franc
Niger
Euro
Netherlands
Uzbekistani soʻm
Uzbekistan
Kyrgyzstani som
Kyrgyzstan
Syrian pound
Syria
Malawian kwacha
Malawi
Argentine peso
Argentina
Nepalese rupee
Nepal
Polish złoty
Poland
Mongolian tögrög
Mongolia
Australian dollar
Australia
Tanzanian shilling
Tanzania
Yemeni rial
Yemen
Central African CFA franc
Equatorial Guinea
Liberian dollar
Liberia
Venezuelan bolívar soberano
Venezuela
Qatari riyal
Qatar
Central African CFA franc
Gabon
Albanian lek
Albania
Philippine peso
Philippines
Euro
Slovakia
West African CFA franc
Senegal
Burmese kyat
Myanmar
Brazilian real
Brazil
Vietnamese đồng
Vietnam
Icelandic króna
Iceland
Zambian kwacha
Zambia
Belarusian ruble
Belarus
West African CFA franc
Burkina Faso
Tajikistani somoni
Tajikistan
Eritrean nakfa
Eritrea
Central African CFA franc
Republic of the Congo
Guinean franc
Guinea
Mauritian rupee
Mauritius
Lebanese pound
Lebanon
Euro
Latvia
Hong Kong dollar
Hong Kong
Pakistani rupee
Pakistan
Croatian kuna
Croatia
Euro
Italy
Jamaican dollar
Jamaica
Euro
Greece
Sri Lankan rupee
Sri Lanka
Euro
Slovenia
West African CFA franc
Ivory Coast
West African CFA franc
Togo
Canadian dollar
Canada
Nicaraguan córdoba
Nicaragua
Chinese yuan
China
Euro
Belgium
South African rand
Swazi lilangeni
Eswatini
Singapore dollar
Singapore
Bosnia and Herzegovina convertible mark
Bosnia and Herzegovina
Chilean peso
Chile
Cuban peso
Cuban convertible peso
Cuba
Congolese franc
DR Congo
Malaysian ringgit
Malaysia
Euro
Spain
British pound
United Kingdom
Bolivian boliviano
Bolivia
South African rand
Lesotho loti
Lesotho
United States dollar
Cambodian riel
Cambodia
Burundian franc
Burundi
lari
Georgia
Kuwaiti dinar
Kuwait
Armenian dram
Armenia
United States dollar
Timor-Leste
Thai baht
Thailand
Euro
Portugal
Omani rial
Oman
Rwandan franc
Rwanda
Dominican peso
Dominican Republic
Honduran lempira
Honduras
Euro
Finland
Jordanian dinar
Israeli new shekel
Egyptian pound
Palestine
Euro
Estonia
Paraguayan guaraní
Paraguay
Moldovan leu
Moldova
Kazakhstani tenge
Kazakhstan
Ugandan shilling
Uganda
Sudanese pound
Sudan
Japanese yen
Japan
Haitian gourde
Haiti
Botswana pula
Botswana
Euro
Ireland
Turkish lira
Turkey
South Sudanese pound
South Sudan
West African CFA franc
Benin
Peruvian sol
Peru
Hungarian forint
Hungary
Czech koruna
Czechia
South African rand
South Africa
United Arab Emirates dirham
United Arab Emirates
Iranian rial
Iran
Colombian peso
Colombia
Euro
Kosovo
United States dollar
El Salvador
denar
North Macedonia
Central African CFA franc
Central African Republic
Trinidad and Tobago dollar
Trinidad and Tobago
Central African CFA franc
Chad
Russian ruble
Russia
Euro
France
Somali shilling
Somalia
Ethiopian birr
Ethiopia
Swiss franc
Switzerland
Swedish krona
Sweden
Algerian dinar
Algeria
United States dollar
Panamanian balboa
Panama
Turkmenistan manat
Turkmenistan
Mexican peso
Mexico
Libyan dinar
Libya
Jordanian dinar
Jordan
Nigerian naira
Nigeria
Malagasy ariary
Madagascar
Sierra Leonean leone
Sierra Leone
Kenyan shilling
Kenya


 */
//export default
const COUNTRIES: Country[] = [
  {
    name: "Kenya",
    currencies: [{ name: "Kenyan shilling", symbol: "Sh" }],
    languages: ["English", "Swahili"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "ke",
    emoji: "🇰🇪",
    countryCallingCode: "+254",
  },
  {
    name: "Sierra Leone",
    currencies: [{ name: "Sierra Leonean leone", symbol: "Le" }],
    languages: ["English"],
    flag: "sl",
    emoji: "🇸🇱",
    countryCallingCode: "+232",
  },
  {
    name: "Madagascar",
    currencies: [{ name: "Malagasy ariary", symbol: "Ar" }],
    languages: ["French", "Malagasy"],
    postalCode: { format: "###", regex: "^(\\d{3})$" },
    flag: "mg",
    emoji: "🇲🇬",
    countryCallingCode: "+261",
  },
  {
    name: "Nigeria",
    currencies: [{ name: "Nigerian naira", symbol: "₦" }],
    languages: ["English"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "ng",
    emoji: "🇳🇬",
    countryCallingCode: "+234",
    tax: 0.075
  },
  {
    name: "Jordan",
    currencies: [{ name: "Jordanian dinar", symbol: "د.ا" }],
    languages: ["Arabic"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "jo",
    emoji: "🇯🇴",
    countryCallingCode: "+962",
  },
  {
    name: "Libya",
    currencies: [{ name: "Libyan dinar", symbol: "ل.د" }],
    languages: ["Arabic"],
    flag: "ly",
    emoji: "🇱🇾",
    countryCallingCode: "+218",
  },
  {
    name: "Mexico",
    currencies: [{ name: "Mexican peso", symbol: "$" }],
    languages: ["Spanish"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "mx",
    emoji: "🇲🇽",
    countryCallingCode: "+52",
    tax: 0.16,
  },
  {
    name: "Turkmenistan",
    currencies: [{ name: "Turkmenistan manat", symbol: "m" }],
    languages: ["Russian", "Turkmen"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "tm",
    emoji: "🇹🇲",
    countryCallingCode: "+993",
  },
  {
    name: "Panama",
    currencies: [
      { name: "Panamanian balboa", symbol: "B/." },
      { name: "United States dollar", symbol: "$" },
    ],
    languages: ["Spanish"],
    flag: "pa",
    emoji: "🇵🇦",
    countryCallingCode: "+507",
  },
  {
    name: "Algeria",
    currencies: [{ name: "Algerian dinar", symbol: "د.ج" }],
    languages: ["Arabic"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "dz",
    emoji: "🇩🇿",
    countryCallingCode: "+213",
    tax: 0.19,
  },
  {
    name: "Sweden",
    currencies: [{ name: "Swedish krona", symbol: "kr" }],
    languages: ["Swedish"],
    postalCode: { format: "SE-### ##", regex: "^(?:SE)*(\\d{5})$" },
    flag: "se",
    emoji: "🇸🇪",
    countryCallingCode: "+46",
  },
  {
    name: "Switzerland",
    currencies: [{ name: "Swiss franc", symbol: "Fr." }],
    languages: ["French", "Swiss German", "Italian", "Romansh"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "ch",
    emoji: "🇨🇭",
    countryCallingCode: "+41",
  },
  {
    name: "Ethiopia",
    currencies: [{ name: "Ethiopian birr", symbol: "Br" }],
    languages: ["Amharic"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "et",
    emoji: "🇪🇹",
    countryCallingCode: "+251",
    tax: 0.15,
  },
  {
    name: "Somalia",
    currencies: [{ name: "Somali shilling", symbol: "Sh" }],
    languages: ["Arabic", "Somali"],
    postalCode: { format: "@@  #####", regex: "^([A-Z]{2}\\d{5})$" },
    flag: "so",
    emoji: "🇸🇴",
    countryCallingCode: "+252",
  },
  {
    name: "France",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["French"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "fr",
    emoji: "🇫🇷",
    countryCallingCode: "+33",
  },
  {
    name: "Russia",
    currencies: [{ name: "Russian ruble", symbol: "₽" }],
    languages: ["Russian"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "ru",
    emoji: "",
    countryCallingCode: "",
    tax: 0.20,
  },
  {
    name: "Chad",
    currencies: [{ name: "Central African CFA franc", symbol: "Fr" }],
    languages: ["Arabic", "French"],
    flag: "td",
    emoji: "🇹🇩",
    countryCallingCode: "+235",
  },
  {
    name: "Trinidad and Tobago",
    currencies: [{ name: "Trinidad and Tobago dollar", symbol: "$" }],
    languages: ["English"],
    flag: "tt",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Central African Republic",
    currencies: [{ name: "Central African CFA franc", symbol: "Fr" }],
    languages: ["French", "Sango"],
    flag: "cf",
    emoji: "🇨🇫",
    countryCallingCode: "+236",
  },
  {
    name: "North Macedonia",
    currencies: [{ name: "denar", symbol: "den" }],
    languages: ["Macedonian"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "mk",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "El Salvador",
    currencies: [{ name: "United States dollar", symbol: "$" }],
    languages: ["Spanish"],
    postalCode: { format: "CP ####", regex: "^(?:CP)*(\\d{4})$" },
    flag: "sv",
    emoji: "🇸🇻",
    countryCallingCode: "+503",
    tax: 0.13,
  },
  {
    name: "Kosovo",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Albanian", "Serbian"],
    flag: "xk",
    emoji: "",
    countryCallingCode: "+383",
  },
  {
    name: "Colombia",
    currencies: [{ name: "Colombian peso", symbol: "$" }],
    languages: ["Spanish"],
    flag: "co",
    emoji: "🇨🇴",
    countryCallingCode: "+57",
    tax: 0.19,
  },
  {
    name: "Iran",
    currencies: [{ name: "Iranian rial", symbol: "﷼" }],
    languages: ["Persian (Farsi)"],
    postalCode: { format: "##########", regex: "^(\\d{10})$" },
    flag: "ir",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "United Arab Emirates",
    currencies: [{ name: "United Arab Emirates dirham", symbol: "د.إ" }],
    languages: ["Arabic"],
    flag: "ae",
    emoji: "🇦🇪",
    countryCallingCode: "+971",
    tax: 0.05,
  },
  {
    name: "South Africa",
    currencies: [{ name: "South African rand", symbol: "R" }],
    languages: [
      "Afrikaans",
      "English",
      "Southern Ndebele",
      "Northern Sotho",
      "Southern Sotho",
      "Swazi",
      "Tswana",
      "Tsonga",
      "Venda",
      "Xhosa",
      "Zulu",
    ],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "za",
    emoji: "🇿🇦",
    countryCallingCode: "+27",
  },
  {
    name: "Czechia",
    currencies: [{ name: "Czech koruna", symbol: "Kč" }],
    languages: ["Czech", "Slovak"],
    postalCode: { format: "### ##", regex: "^(\\d{5})$" },
    flag: "cz",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Hungary",
    currencies: [{ name: "Hungarian forint", symbol: "Ft" }],
    languages: ["Hungarian"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "hu",
    emoji: "🇭🇺",
    countryCallingCode: "+36",
  },
  {
    name: "Peru",
    currencies: [{ name: "Peruvian sol", symbol: "S/ " }],
    languages: ["Aymara", "Quechua", "Spanish"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "pe",
    emoji: "🇵🇪",
    countryCallingCode: "+51",
    tax: 0.18,
  },
  {
    name: "Benin",
    currencies: [{ name: "West African CFA franc", symbol: "Fr" }],
    languages: ["French"],
    flag: "bj",
    emoji: "🇧🇯",
    countryCallingCode: "+229",
  },
  {
    name: "South Sudan",
    currencies: [{ name: "South Sudanese pound", symbol: "£" }],
    languages: ["English"],
    flag: "ss",
    emoji: "🇸🇸",
    countryCallingCode: "+211",
  },
  {
    name: "Turkey",
    currencies: [{ name: "Turkish lira", symbol: "₺" }],
    languages: ["Turkish"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "tr",
    emoji: "🇹🇷",
    countryCallingCode: "+90",
    tax: 0.19,
  },
  {
    name: "Ireland",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["English", "Irish"],
    flag: "ie",
    emoji: "🇮🇪",
    countryCallingCode: "+353",
  },
  {
    name: "Botswana",
    currencies: [{ name: "Botswana pula", symbol: "P" }],
    languages: ["English", "Tswana"],
    flag: "bw",
    emoji: "🇧🇼",
    countryCallingCode: "+267",
    tax: 0.14,
  },
  {
    name: "Haiti",
    currencies: [{ name: "Haitian gourde", symbol: "G" }],
    languages: ["French", "Haitian Creole"],
    postalCode: { format: "HT####", regex: "^(?:HT)*(\\d{4})$" },
    flag: "ht",
    emoji: "🇭🇹",
    countryCallingCode: "+509",
  },
  {
    name: "Japan",
    currencies: [{ name: "Japanese yen", symbol: "¥" }],
    languages: ["Japanese"],
    postalCode: { format: "###-####", regex: "^(\\d{7})$" },
    flag: "jp",
    emoji: "🇯🇵",
    countryCallingCode: "+81",
    tax: 0.10,
  },
  {
    name: "Sudan",
    currencies: [{ name: "Sudanese pound" }],
    languages: ["Arabic", "English"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "sd",
    emoji: "🇸🇩",
    countryCallingCode: "+249",
  },
  {
    name: "Uganda",
    currencies: [{ name: "Ugandan shilling", symbol: "Sh" }],
    languages: ["English", "Swahili"],
    flag: "ug",
    emoji: "🇺🇬",
    countryCallingCode: "+256",
    tax: 0.18,
  },
  {
    name: "Kazakhstan",
    currencies: [{ name: "Kazakhstani tenge", symbol: "₸" }],
    languages: ["Kazakh", "Russian"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "kz",
    emoji: "🇰🇿",
    countryCallingCode: "+7",
    tax: 0.12,
  },
  {
    name: "Moldova",
    currencies: [{ name: "Moldovan leu", symbol: "L" }],
    languages: ["Moldavian"],
    postalCode: { format: "MD-####", regex: "^(?:MD)*(\\d{4})$" },
    flag: "md",
    emoji: "🇲🇩",
    countryCallingCode: "+373",
  },
  {
    name: "Paraguay",
    currencies: [{ name: "Paraguayan guaraní", symbol: "₲" }],
    languages: ["Guaraní", "Spanish"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "py",
    emoji: "🇵🇾",
    countryCallingCode: "+595",
  },
  {
    name: "Estonia",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Estonian"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "ee",
    emoji: "🇪🇪",
    countryCallingCode: "+372",
  },
  {
    name: "Palestine",
    currencies: [
      { name: "Egyptian pound", symbol: "E£" },
      { name: "Israeli new shekel", symbol: "₪" },
      { name: "Jordanian dinar", symbol: "JD" },
    ],
    languages: ["Arabic"],
    flag: "ps",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Finland",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Finnish", "Swedish"],
    postalCode: { format: "#####", regex: "^(?:FI)*(\\d{5})$" },
    flag: "fi",
    emoji: "🇫🇮",
    countryCallingCode: "+358",
  },
  {
    name: "Honduras",
    currencies: [{ name: "Honduran lempira", symbol: "L" }],
    languages: ["Spanish"],
    postalCode: { format: "@@####", regex: "^([A-Z]{2}\\d{4})$" },
    flag: "hn",
    emoji: "🇭🇳",
    countryCallingCode: "+504",
  },
  {
    name: "Dominican Republic",
    currencies: [{ name: "Dominican peso", symbol: "$" }],
    languages: ["Spanish"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "do",
    emoji: "🇩🇴",
    countryCallingCode: "+1 809",
  },
  {
    name: "Rwanda",
    currencies: [{ name: "Rwandan franc", symbol: "Fr" }],
    languages: ["English", "French", "Kinyarwanda"],
    flag: "rw",
    emoji: "🇷🇼",
    countryCallingCode: "+250",
  },
  {
    name: "Oman",
    currencies: [{ name: "Omani rial", symbol: "ر.ع." }],
    languages: ["Arabic"],
    postalCode: { format: "###", regex: "^(\\d{3})$" },
    flag: "om",
    emoji: "🇴🇲",
    countryCallingCode: "+968",
    tax: 0.05,
  },
  {
    name: "Portugal",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Portuguese"],
    postalCode: { format: "####-###", regex: "^(\\d{7})$" },
    flag: "pt",
    emoji: "🇵🇹",
    countryCallingCode: "+351",
  },
  {
    name: "Thailand",
    currencies: [{ name: "Thai baht", symbol: "฿" }],
    languages: ["Thai"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "th",
    emoji: "🇹🇭",
    countryCallingCode: "+66",
    tax: 0.07,
  },
  {
    name: "Timor-Leste",
    currencies: [{ name: "United States dollar", symbol: "$" }],
    languages: ["Portuguese", "Tetum"],
    flag: "tl",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Armenia",
    currencies: [{ name: "Armenian dram", symbol: "֏" }],
    languages: ["Armenian"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "am",
    emoji: "🇦🇲",
    countryCallingCode: "+374",
    tax: 0.20,
  },
  {
    name: "Kuwait",
    currencies: [{ name: "Kuwaiti dinar", symbol: "د.ك" }],
    languages: ["Arabic"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "kw",
    emoji: "🇰🇼",
    countryCallingCode: "+965",
    tax: 0,
  },
  {
    name: "Georgia",
    currencies: [{ name: "lari", symbol: "₾" }],
    languages: ["Georgian"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "ge",
    emoji: "🇬🇪",
    countryCallingCode: "+995",
    tax: 0.18,
  },
  {
    name: "Burundi",
    currencies: [{ name: "Burundian franc", symbol: "Fr" }],
    languages: ["French", "Kirundi"],
    flag: "bi",
    emoji: "🇧🇮",
    countryCallingCode: "+257",
  },
  {
    name: "Cambodia",
    currencies: [
      { name: "Cambodian riel", symbol: "៛" },
      { name: "United States dollar", symbol: "$" },
    ],
    languages: ["Khmer"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "kh",
    emoji: "🇰🇭",
    countryCallingCode: "+855",
  },
  {
    name: "Lesotho",
    currencies: [
      { name: "Lesotho loti", symbol: "L" },
      { name: "South African rand", symbol: "R" },
    ],
    languages: ["English", "Sotho"],
    postalCode: { format: "###", regex: "^(\\d{3})$" },
    flag: "ls",
    emoji: "🇱🇸",
    countryCallingCode: "+266",
  },
  {
    name: "Bolivia",
    currencies: [{ name: "Bolivian boliviano", symbol: "Bs." }],
    languages: ["Aymara", "Guaraní", "Quechua", "Spanish"],
    flag: "bo",
    emoji: "",
    countryCallingCode: "",
    tax: 0.13,
  },
  {
    name: "United Kingdom",
    currencies: [{ name: "British pound", symbol: "£" }],
    languages: ["English"],
    postalCode: {
      format: "@# #@@|@## #@@|@@# #@@|@@## #@@|@#@ #@@|@@#@ #@@|GIR0AA",
      regex:
        "^(([A-Z]\\d{2}[A-Z]{2})|([A-Z]\\d{3}[A-Z]{2})|([A-Z]{2}\\d{2}[A-Z]{2})|([A-Z]{2}\\d{3}[A-Z]{2})|([A-Z]\\d[A-Z]\\d[A-Z]{2})|([A-Z]{2}\\d[A-Z]\\d[A-Z]{2})|(GIR0AA))$",
    },
    flag: "gb",
    emoji: "🇬🇧",
    countryCallingCode: "+44",
    tax: 0.20,
  },
  {
    name: "Spain",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Spanish"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "es",
    emoji: "🇪🇸",
    countryCallingCode: "+34",
  },
  {
    name: "Malaysia",
    currencies: [{ name: "Malaysian ringgit", symbol: "RM" }],
    languages: ["English", "Malay"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "my",
    emoji: "🇲🇾",
    countryCallingCode: "+60",
  },
  {
    name: "DR Congo",
    currencies: [{ name: "Congolese franc", symbol: "FC" }],
    languages: ["French", "Kikongo", "Lingala", "Tshiluba", "Swahili"],
    flag: "cd",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Cuba",
    currencies: [
      { name: "Cuban convertible peso", symbol: "$" },
      { name: "Cuban peso", symbol: "$" },
    ],
    languages: ["Spanish"],
    postalCode: { format: "CP #####", regex: "^(?:CP)*(\\d{5})$" },
    flag: "cu",
    emoji: "🇨🇺",
    countryCallingCode: "+53",
  },
  {
    name: "Chile",
    currencies: [{ name: "Chilean peso", symbol: "$" }],
    languages: ["Spanish"],
    postalCode: { format: "#######", regex: "^(\\d{7})$" },
    flag: "cl",
    emoji: "🇨🇱",
    countryCallingCode: "+56",
    tax: 0.19,
  },
  {
    name: "Bosnia and Herzegovina",
    currencies: [{ name: "Bosnia and Herzegovina convertible mark" }],
    languages: ["Bosnian", "Croatian", "Serbian"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "ba",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Singapore",
    currencies: [{ name: "Singapore dollar", symbol: "$" }],
    languages: ["Chinese", "English", "Malay", "Tamil"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "sg",
    emoji: "🇸🇬",
    countryCallingCode: "+65",
    tax: 0.08,
  },
  {
    name: "Eswatini",
    currencies: [
      { name: "Swazi lilangeni", symbol: "L" },
      { name: "South African rand", symbol: "R" },
    ],
    languages: ["English", "Swazi"],
    postalCode: { format: "@###", regex: "^([A-Z]\\d{3})$" },
    flag: "sz",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Belgium",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["German", "French", "Dutch"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "be",
    emoji: "🇧🇪",
    countryCallingCode: "+32",
  },
  {
    name: "China",
    currencies: [{ name: "Chinese yuan", symbol: "¥" }],
    languages: ["Chinese"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "cn",
    emoji: "🇨🇳",
    countryCallingCode: "+86",
  },
  {
    name: "Nicaragua",
    currencies: [{ name: "Nicaraguan córdoba", symbol: "C$" }],
    languages: ["Spanish"],
    postalCode: { format: "###-###-#", regex: "^(\\d{7})$" },
    flag: "ni",
    emoji: "🇳🇮",
    countryCallingCode: "+505",
  },
  {
    name: "Canada",
    currencies: [{ name: "Canadian dollar", symbol: "$" }],
    languages: ["English", "French"],
    postalCode: {
      format: "@#@ #@#",
      regex:
        "^([ABCEGHJKLMNPRSTVXY]\\d[ABCEGHJKLMNPRSTVWXYZ]) ?(\\d[ABCEGHJKLMNPRSTVWXYZ]\\d)$ ",
    },
    flag: "ca",
    emoji: "🇨🇦",
    countryCallingCode: "+1",
    tax: 0.05,
  },
  {
    name: "Togo",
    currencies: [{ name: "West African CFA franc", symbol: "Fr" }],
    languages: ["French"],
    flag: "tg",
    emoji: "🇹🇬",
    countryCallingCode: "+228",
  },
  {
    name: "Ivory Coast",
    currencies: [{ name: "West African CFA franc", symbol: "Fr" }],
    languages: ["French"],
    flag: "ci",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Slovenia",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Slovene"],
    postalCode: { format: "SI- ####", regex: "^(?:SI)*(\\d{4})$" },
    flag: "si",
    emoji: "🇸🇮",
    countryCallingCode: "+386",
  },
  {
    name: "Sri Lanka",
    currencies: [{ name: "Sri Lankan rupee", symbol: "Rs  රු" }],
    languages: ["Sinhala", "Tamil"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "lk",
    emoji: "🇱🇰",
    countryCallingCode: "+94",
  },
  {
    name: "Greece",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Greek"],
    postalCode: { format: "### ##", regex: "^(\\d{5})$" },
    flag: "gr",
    emoji: "🇬🇷",
    countryCallingCode: "+30",
  },
  {
    name: "Jamaica",
    currencies: [{ name: "Jamaican dollar", symbol: "$" }],
    languages: ["English", "Jamaican Patois"],
    flag: "jm",
    emoji: "🇯🇲",
    countryCallingCode: "+1 876",
  },
  {
    name: "Italy",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Italian"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "it",
    emoji: "🇮🇹",
    countryCallingCode: "+39",
  },
  {
    name: "Croatia",
    currencies: [{ name: "Croatian kuna", symbol: "kn" }],
    languages: ["Croatian"],
    postalCode: { format: "HR-#####", regex: "^(?:HR)*(\\d{5})$" },
    flag: "hr",
    emoji: "🇭🇷",
    countryCallingCode: "+385",
  },
  {
    name: "Pakistan",
    currencies: [{ name: "Pakistani rupee", symbol: "₨" }],
    languages: ["English", "Urdu"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "pk",
    emoji: "🇵🇰",
    countryCallingCode: "+92",
    tax: 0.18
  },
  {
    name: "Hong Kong",
    currencies: [{ name: "Hong Kong dollar", symbol: "$" }],
    languages: ["English", "Chinese"],
    flag: "hk",
    emoji: "🇭🇰",
    countryCallingCode: "+852",
    tax: 0,
  },
  {
    name: "Latvia",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Latvian"],
    postalCode: { format: "LV-####", regex: "^(?:LV)*(\\d{4})$" },
    flag: "lv",
    emoji: "🇱🇻",
    countryCallingCode: "+371",
  },
  {
    name: "Lebanon",
    currencies: [{ name: "Lebanese pound", symbol: "ل.ل" }],
    languages: ["Arabic", "French"],
    postalCode: {
      format: "#### ####|####",
      regex: "^(\\d{4}(\\d{4})?)$",
    },
    flag: "lb",
    emoji: "🇱🇧",
    countryCallingCode: "+961",
  },
  {
    name: "Mauritius",
    currencies: [{ name: "Mauritian rupee", symbol: "₨" }],
    languages: ["English", "French", "Mauritian Creole"],
    flag: "mu",
    emoji: "🇲🇺",
    countryCallingCode: "+230",
    tax: 0.15,
  },
  {
    name: "Guinea",
    currencies: [{ name: "Guinean franc", symbol: "Fr" }],
    languages: ["French"],
    flag: "gn",
    emoji: "🇬🇳",
    countryCallingCode: "+224",
  },
  {
    name: "Republic of the Congo",
    currencies: [{ name: "Central African CFA franc", symbol: "Fr" }],
    languages: ["French", "Kikongo", "Lingala"],
    flag: "cg",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Eritrea",
    currencies: [{ name: "Eritrean nakfa", symbol: "Nfk" }],
    languages: ["Arabic", "English", "Tigrinya"],
    flag: "er",
    emoji: "🇪🇷",
    countryCallingCode: "+291",
  },
  {
    name: "Tajikistan",
    currencies: [{ name: "Tajikistani somoni", symbol: "ЅМ" }],
    languages: ["Russian", "Tajik"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "tj",
    emoji: "🇹🇯",
    countryCallingCode: "+992",
  },
  {
    name: "Burkina Faso",
    currencies: [{ name: "West African CFA franc", symbol: "Fr" }],
    languages: ["French"],
    flag: "bf",
    emoji: "🇧🇫",
    countryCallingCode: "+226",
  },
  {
    name: "Belarus",
    currencies: [{ name: "Belarusian ruble", symbol: "Br" }],
    languages: ["Belarusian", "Russian"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "by",
    emoji: "🇧🇾",
    countryCallingCode: "+375",
  },
  {
    name: "Zambia",
    currencies: [{ name: "Zambian kwacha", symbol: "ZK" }],
    languages: ["English"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "zm",
    emoji: "🇿🇲",
    countryCallingCode: "+260",
  },
  {
    name: "Iceland",
    currencies: [{ name: "Icelandic króna", symbol: "kr" }],
    languages: ["Icelandic"],
    postalCode: { format: "###", regex: "^(\\d{3})$" },
    flag: "is",
    emoji: "🇮🇸",
    countryCallingCode: "+354",
  },
  {
    name: "Vietnam",
    currencies: [{ name: "Vietnamese đồng", symbol: "₫" }],
    languages: ["Vietnamese"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "vn",
    emoji: "",
    countryCallingCode: "",
    tax: 0.10,
  },
  {
    name: "Brazil",
    currencies: [{ name: "Brazilian real", symbol: "R$" }],
    languages: ["Portuguese"],
    postalCode: { format: "#####-###", regex: "^(\\d{8})$" },
    flag: "br",
    emoji: "🇧🇷",
    countryCallingCode: "+55",
  },
  {
    name: "Myanmar",
    currencies: [{ name: "Burmese kyat", symbol: "Ks" }],
    languages: ["Burmese"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "mm",
    emoji: "🇲🇲",
    countryCallingCode: "+95",
  },
  {
    name: "Senegal",
    currencies: [{ name: "West African CFA franc", symbol: "Fr" }],
    languages: ["French"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "sn",
    emoji: "🇸🇳",
    countryCallingCode: "+221",
  },
  {
    name: "Slovakia",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Slovak"],
    postalCode: { format: "###  ##", regex: "^(\\d{5})$" },
    flag: "sk",
    emoji: "🇸🇰",
    countryCallingCode: "+421",
  },
  {
    name: "Philippines",
    currencies: [{ name: "Philippine peso", symbol: "₱" }],
    languages: ["English", "Filipino"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "ph",
    emoji: "🇵🇭",
    countryCallingCode: "+63",
    tax: 0.12,
  },
  {
    name: "Albania",
    currencies: [{ name: "Albanian lek", symbol: "L" }],
    languages: ["Albanian"],
    flag: "al",
    emoji: "🇦🇱",
    countryCallingCode: "+355",
  },
  {
    name: "Gabon",
    currencies: [{ name: "Central African CFA franc", symbol: "Fr" }],
    languages: ["French"],
    flag: "ga",
    emoji: "🇬🇦",
    countryCallingCode: "+241",
  },
  {
    name: "Qatar",
    currencies: [{ name: "Qatari riyal", symbol: "ر.ق" }],
    languages: ["Arabic"],
    flag: "qa",
    emoji: "🇶🇦",
    countryCallingCode: "+974",
    tax: 0,
  },
  {
    name: "Venezuela",
    currencies: [{ name: "Venezuelan bolívar soberano", symbol: "Bs.S." }],
    languages: ["Spanish"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "ve",
    emoji: "",
    countryCallingCode: "",
    tax: 0.16,
  },
  {
    name: "Liberia",
    currencies: [{ name: "Liberian dollar", symbol: "$" }],
    languages: ["English"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "lr",
    emoji: "🇱🇷",
    countryCallingCode: "+231",
  },
  {
    name: "Equatorial Guinea",
    currencies: [{ name: "Central African CFA franc", symbol: "Fr" }],
    languages: ["French", "Portuguese", "Spanish"],
    flag: "gq",
    emoji: "🇬🇶",
    countryCallingCode: "+240",
    tax: 15,
  },
  {
    name: "Yemen",
    currencies: [{ name: "Yemeni rial", symbol: "﷼" }],
    languages: ["Arabic"],
    flag: "ye",
    emoji: "🇾🇪",
    countryCallingCode: "+967",
  },
  {
    name: "Tanzania",
    currencies: [{ name: "Tanzanian shilling", symbol: "Sh" }],
    languages: ["English", "Swahili"],
    flag: "tz",
    emoji: "",
    countryCallingCode: "",
    tax: 0.18,
  },
  {
    name: "Australia",
    currencies: [{ name: "Australian dollar", symbol: "$" }],
    languages: ["English"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "au",
    emoji: "🇦🇺",
    countryCallingCode: "+61",
    tax: 0.10,
  },
  {
    name: "Mongolia",
    currencies: [{ name: "Mongolian tögrög", symbol: "₮" }],
    languages: ["Mongolian"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "mn",
    emoji: "🇲🇳",
    countryCallingCode: "+976",
  },
  {
    name: "Poland",
    currencies: [{ name: "Polish złoty", symbol: "zł" }],
    languages: ["Polish"],
    postalCode: { format: "##-###", regex: "^(\\d{5})$" },
    flag: "pl",
    emoji: "🇵🇱",
    countryCallingCode: "+48",
  },
  {
    name: "Nepal",
    currencies: [{ name: "Nepalese rupee", symbol: "₨" }],
    languages: ["Nepali"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "np",
    emoji: "🇳🇵",
    countryCallingCode: "+977",
  },
  {
    name: "Argentina",
    currencies: [{ name: "Argentine peso", symbol: "$" }],
    languages: ["Guaraní", "Spanish"],
    postalCode: { format: "@####@@@", regex: "^([A-Z]\\d{4}[A-Z]{3})$" },
    flag: "ar",
    emoji: "🇦🇷",
    countryCallingCode: "+54",
  },
  {
    name: "Malawi",
    currencies: [{ name: "Malawian kwacha", symbol: "MK" }],
    languages: ["English", "Chewa"],
    flag: "mw",
    emoji: "🇲🇼",
    countryCallingCode: "+265",
  },
  {
    name: "Syria",
    currencies: [{ name: "Syrian pound", symbol: "£" }],
    languages: ["Arabic"],
    flag: "sy",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Kyrgyzstan",
    currencies: [{ name: "Kyrgyzstani som", symbol: "с" }],
    languages: ["Kyrgyz", "Russian"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "kg",
    emoji: "🇰🇬",
    countryCallingCode: "+996",
  },
  {
    name: "Uzbekistan",
    currencies: [{ name: "Uzbekistani soʻm", symbol: "so'm" }],
    languages: ["Russian", "Uzbek"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "uz",
    emoji: "🇺🇿",
    countryCallingCode: "+998",
    tax: 0.12,
  },
  {
    name: "Netherlands",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Dutch"],
    postalCode: { format: "#### @@", regex: "^(\\d{4}[A-Z]{2})$" },
    flag: "nl",
    emoji: "🇳🇱",
    countryCallingCode: "+31",
  },
  {
    name: "Niger",
    currencies: [{ name: "West African CFA franc", symbol: "Fr" }],
    languages: ["French"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "ne",
    emoji: "🇳🇪",
    countryCallingCode: "+227",
  },
  {
    name: "Bahrain",
    currencies: [{ name: "Bahraini dinar", symbol: ".د.ب" }],
    languages: ["Arabic"],
    postalCode: { format: "####|###", regex: "^(\\d{3}\\d?)$" },
    flag: "bh",
    emoji: "🇧🇭",
    countryCallingCode: "+973",
    tax: 0.10,
  },
  {
    name: "Indonesia",
    currencies: [{ name: "Indonesian rupiah", symbol: "Rp" }],
    languages: ["Indonesian"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "id",
    emoji: "🇮🇩",
    countryCallingCode: "+62",
    tax: 0.11,
  },
  {
    name: "Bangladesh",
    currencies: [{ name: "Bangladeshi taka", symbol: "৳" }],
    languages: ["Bengali"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "bd",
    emoji: "🇧🇩",
    countryCallingCode: "+880",
  },
  {
    name: "Guatemala",
    currencies: [{ name: "Guatemalan quetzal", symbol: "Q" }],
    languages: ["Spanish"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "gt",
    emoji: "🇬🇹",
    countryCallingCode: "+502",
    tax: 0.12,
  },
  {
    name: "Uruguay",
    currencies: [{ name: "Uruguayan peso", symbol: "$" }],
    languages: ["Spanish"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "uy",
    emoji: "🇺🇾",
    countryCallingCode: "+598",
    tax: 0.22,
  },
  {
    name: "Morocco",
    currencies: [{ name: "Moroccan dirham", symbol: "د.م." }],
    languages: ["Arabic", "Berber"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "ma",
    emoji: "🇲🇦",
    countryCallingCode: "+212",
    tax: 0.20,
  },
  {
    name: "Germany",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["German"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "de",
    emoji: "🇩🇪",
    countryCallingCode: "+49",
  },
  {
    name: "United States",
    currencies: [{ name: "United States dollar", symbol: "$" }],
    languages: ["English"],
    postalCode: { format: "#####-####", regex: "^\\d{5}(-\\d{4})?$" },
    flag: "us",
    emoji: "🇺🇸",
    countryCallingCode: "+1",
  },
  {
    name: "Bulgaria",
    currencies: [{ name: "Bulgarian lev", symbol: "лв" }],
    languages: ["Bulgarian"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "bg",
    emoji: "🇧🇬",
    countryCallingCode: "+359",
    tax: 0.20,
  },
  {
    name: "Papua New Guinea",
    currencies: [{ name: "Papua New Guinean kina", symbol: "K" }],
    languages: ["English", "Hiri Motu", "Tok Pisin"],
    postalCode: { format: "###", regex: "^(\\d{3})$" },
    flag: "pg",
    emoji: "🇵🇬",
    countryCallingCode: "+675",
  },
  {
    name: "Costa Rica",
    currencies: [{ name: "Costa Rican colón", symbol: "₡" }],
    languages: ["Spanish"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "cr",
    emoji: "🇨🇷",
    countryCallingCode: "+506",
    tax: 0.13,
  },
  {
    name: "Ecuador",
    currencies: [{ name: "United States dollar", symbol: "$" }],
    languages: ["Spanish"],
    postalCode: { format: "@####@", regex: "^([a-zA-Z]\\d{4}[a-zA-Z])$" },
    flag: "ec",
    emoji: "🇪🇨",
    countryCallingCode: "+593",
    tax: 0.12,
  },
  {
    name: "South Korea",
    currencies: [{ name: "South Korean won", symbol: "₩" }],
    languages: ["Korean"],
    postalCode: {
      format: "SEOUL ###-###",
      regex: "^(?:SEOUL)*(\\d{6})$",
    },
    flag: "kr",
    emoji: "",
    countryCallingCode: "",
    tax: 0.10,
  },
  {
    name: "Puerto Rico",
    currencies: [{ name: "United States dollar", symbol: "$" }],
    languages: ["English", "Spanish"],
    postalCode: { format: "#####-####", regex: "^(\\d{9})$" },
    flag: "pr",
    emoji: "🇵🇷",
    countryCallingCode: "+1 787",
    tax: 0.115
  },
  {
    name: "North Korea",
    currencies: [{ name: "North Korean won", symbol: "₩" }],
    languages: ["Korean"],
    postalCode: { format: "###-###", regex: "^(\\d{6})$" },
    flag: "kp",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Taiwan",
    currencies: [{ name: "New Taiwan dollar", symbol: "$" }],
    languages: ["Chinese"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "tw",
    emoji: "🇹🇼",
    countryCallingCode: "+886",
    tax: 0.05,
  },
  {
    name: "Lithuania",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Lithuanian"],
    postalCode: { format: "LT-#####", regex: "^(?:LT)*(\\d{5})$" },
    flag: "lt",
    emoji: "🇱🇹",
    countryCallingCode: "+370",
  },
  {
    name: "Angola",
    currencies: [{ name: "Angolan kwanza", symbol: "Kz" }],
    languages: ["Portuguese"],
    flag: "ao",
    emoji: "🇦🇴",
    countryCallingCode: "+244",
  },
  {
    name: "Tunisia",
    currencies: [{ name: "Tunisian dinar", symbol: "د.ت" }],
    languages: ["Arabic"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "tn",
    emoji: "🇹🇳",
    countryCallingCode: "+216",
    tax: 0.19,
  },
  {
    name: "Ghana",
    currencies: [{ name: "Ghanaian cedi", symbol: "₵" }],
    languages: ["English"],
    flag: "gh",
    emoji: "🇬🇭",
    countryCallingCode: "+233",
  },
  {
    name: "Iraq",
    currencies: [{ name: "Iraqi dinar", symbol: "ع.د" }],
    languages: ["Arabic", "Aramaic", "Sorani"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "iq",
    emoji: "🇮🇶",
    countryCallingCode: "+964",
  },
  {
    name: "New Zealand",
    currencies: [{ name: "New Zealand dollar", symbol: "$" }],
    languages: ["English", "Māori", "New Zealand Sign Language"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "nz",
    emoji: "🇳🇿",
    countryCallingCode: "+64",
    tax: 0.15,
  },
  {
    name: "Serbia",
    currencies: [{ name: "Serbian dinar", symbol: "дин." }],
    languages: ["Serbian"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "rs",
    emoji: "🇷🇸",
    countryCallingCode: "+381",
  },
  {
    name: "Romania",
    currencies: [{ name: "Romanian leu", symbol: "lei" }],
    languages: ["Romanian"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "ro",
    emoji: "🇷🇴",
    countryCallingCode: "+40",
  },
  {
    name: "Afghanistan",
    currencies: [{ name: "Afghan afghani", symbol: "؋" }],
    languages: ["Dari", "Pashto", "Turkmen"],
    flag: "af",
    emoji: "🇦🇫",
    countryCallingCode: "+93",
  },
  {
    name: "India",
    currencies: [{ name: "Indian rupee", symbol: "₹" }],
    languages: ["English", "Hindi", "Tamil"],
    postalCode: { format: "######", regex: "^(\\d{6})$" },
    flag: "in",
    emoji: "🇮🇳",
    countryCallingCode: "+91",
  },
  {
    name: "Denmark",
    currencies: [{ name: "Danish krone", symbol: "kr" }],
    languages: ["Danish"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "dk",
    emoji: "🇩🇰",
    countryCallingCode: "+45",
  },
  {
    name: "Saudi Arabia",
    currencies: [{ name: "Saudi riyal", symbol: "ر.س" }],
    languages: ["Arabic"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "sa",
    emoji: "🇸🇦",
    countryCallingCode: "+966",
    tax: 0.15,
  },
  {
    name: "Guinea-Bissau",
    currencies: [{ name: "West African CFA franc", symbol: "Fr" }],
    languages: ["Portuguese", "Upper Guinea Creole"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "gw",
    emoji: "",
    countryCallingCode: "",
  },
  {
    name: "Norway",
    currencies: [{ name: "Norwegian krone", symbol: "kr" }],
    languages: ["Norwegian Nynorsk", "Norwegian Bokmål", "Sami"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "no",
    emoji: "🇳🇴",
    countryCallingCode: "+47",
  },
  {
    name: "Egypt",
    currencies: [{ name: "Egyptian pound", symbol: "£" }],
    languages: ["Arabic"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "eg",
    emoji: "🇪🇬",
    countryCallingCode: "+20",
    tax: 0.14,
  },
  {
    name: "Cyprus",
    currencies: [{ name: "Euro", symbol: "€" }],
    languages: ["Greek", "Turkish"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "cy",
    emoji: "🇨🇾",
    countryCallingCode: "+357",
  },
  {
    name: "Azerbaijan",
    currencies: [{ name: "Azerbaijani manat", symbol: "₼" }],
    languages: ["Azerbaijani", "Russian"],
    postalCode: { format: "AZ ####", regex: "^(?:AZ)*(\\d{4})$" },
    flag: "az",
    emoji: "🇦🇿",
    countryCallingCode: "+994",
    tax: 0.18,
  },
  {
    name: "Zimbabwe",
    currencies: [{ name: "Zimbabwean dollar", symbol: "$" }],
    languages: [
      "Chibarwe",
      "English",
      "Kalanga",
      "Khoisan",
      "Ndau",
      "Northern Ndebele",
      "Chewa",
      "Shona",
      "Sotho",
      "Tonga",
      "Tswana",
      "Tsonga",
      "Venda",
      "Xhosa",
      "Zimbabwean Sign Language",
    ],
    flag: "zw",
    emoji: "🇿🇼",
    countryCallingCode: "+263",
  },
  {
    name: "Mali",
    currencies: [{ name: "West African CFA franc", symbol: "Fr" }],
    languages: ["French"],
    flag: "ml",
    emoji: "🇲🇱",
    countryCallingCode: "+223",
  },
  {
    name: "Mauritania",
    currencies: [{ name: "Mauritanian ouguiya", symbol: "UM" }],
    languages: ["Arabic"],
    flag: "mr",
    emoji: "🇲🇷",
    countryCallingCode: "+222",
    tax: 0.16,
  },
  {
    name: "Ukraine",
    currencies: [{ name: "Ukrainian hryvnia", symbol: "₴" }],
    languages: ["Ukrainian"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "ua",
    emoji: "🇺🇦",
    countryCallingCode: "+380",
    tax: 0.20,
  },
  {
    name: "Cameroon",
    currencies: [{ name: "Central African CFA franc", symbol: "Fr" }],
    languages: ["English", "French"],
    flag: "cm",
    emoji: "🇨🇲",
    countryCallingCode: "+237",
  },
  {
    name: "Mozambique",
    currencies: [{ name: "Mozambican metical", symbol: "MT" }],
    languages: ["Portuguese"],
    postalCode: { format: "####", regex: "^(\\d{4})$" },
    flag: "mz",
    emoji: "🇲🇿",
    countryCallingCode: "+258",
  },
  {
    name: "Namibia",
    currencies: [
      { name: "Namibian dollar", symbol: "$" },
      { name: "South African rand", symbol: "R" },
    ],
    languages: [
      "Afrikaans",
      "German",
      "English",
      "Herero",
      "Khoekhoe",
      "Kwangali",
      "Lozi",
      "Ndonga",
      "Tswana",
    ],
    flag: "na",
    emoji: "🇳🇦",
    countryCallingCode: "+264",
  },
  {
    name: "Gambia",
    currencies: [{ name: "dalasi", symbol: "D" }],
    languages: ["English"],
    flag: "gm",
    emoji: "🇬🇲",
    countryCallingCode: "+220",
  },
  {
    name: "Laos",
    currencies: [{ name: "Lao kip", symbol: "₭" }],
    languages: ["Lao"],
    postalCode: { format: "#####", regex: "^(\\d{5})$" },
    flag: "la",
    emoji: "",
    countryCallingCode: "",
  },
];

export default COUNTRIES;