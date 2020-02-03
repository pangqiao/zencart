<?php
define('NAVBAR_TITLE', 'Mijn verlanglijstje delen');
define('HEADING_TITLE', 'Mijn verlanglijstje delen');

define('TEXT_DESCRIPTION', 'Om je verlanglijstje te delen met anderen vul je het email adres van diegene in het veld hieronder in. Als je een persoonlijke tekst wilt gebruiken, vervang dan de aanwezige tekst door de boodschap die je mee wilt sturen.');
//'To share your Wish List with friends and family, simply fill in the name and address boxes below. If you\'d like to write your own message, just erase what\'s in the box below and type in the text you want to send.');

define('TEXT_PRIVACY_EMAIL', STORE_NAME . ' gebruikt het email adres alleen om het bericht te versturen, het email adres wordt niet bewaard en dus ook niet voor andere doeleinden gebruikt.');
define('TEXT_MESSAGE_FROM', 'Je naam wordt gebruikt in het "Van:" veld bij de email die wordt verzonden.');

define('FORM_TITLE', 'Email een vriend(in) mijn verlanglijstje');
define('FORM_LABEL_EMAIL', 'email adres vriend(in)');
define('FORM_LABEL_MESSAGE', 'Persoonlijk bericht');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');

define('FORM_DEFAULT_BODY', 'Hallo,' . "\n\n" . 'Bij %s heb ik een verlanglijstje gemaakt. Kijk er even naar en maak zelf ook een verlanglijstje als je wilt.' . "\n\n" . 'Groetjes,' . "\n" . '%s');

define('TEXT_SUCCESS', 'De email is verstuurd.');

define('TEXT_EMAIL_SUBJECT', '%s\'s verlanglijstje bij %s');

define('TEXT_EMAIL_LINK', 'Om %s\'s verlanglijstje te bekijken gebruik je onderstaande link (of kopieer en plak de link in je web browser):' . "\n" . '%s');
define('TEXT_EMAIL_SIGNATURE', '%s (%s) verstuurde deze email op verzoek van %s.' . "\n\n");

define('TEXT_ERROR_EMAIL', 'Het email adres lijkt ongeldig te zijn.');