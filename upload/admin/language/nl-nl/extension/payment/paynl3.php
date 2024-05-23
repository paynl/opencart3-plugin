<?php

$_['text_apitoken'] = 'API token';
$_['text_serviceid'] = 'Verkooplocatie';

// Text
$_['text_payment'] = 'Betaling';

$_['button_save'] = 'Opslaan';
$_['button_cancel'] = 'Annuleren';

$_['text_success'] = 'Instellingen opgeslagen';

//errors
$_['error_not_activated'] = "Deze betaalmethode is niet geactiveerd voor deze dienst. Ga naar  
    <a target='paynl' href='https://admin.pay.nl/programs/programs'>https://admin.pay.nl/programs/programs</a> om dit aan te passen.";
$_['error_api_error'] = 'De Pay. Api gaf de volgende fout: ';
$_['error_error_occurred'] = 'Er is een fout opgetreden: ';
$_['error_no_apitoken'] = 'U moet een apitokeninvoeren, u vind uw apitokens op: 
    <a href="https://admin.pay.nl/company/tokens">https://admin.pay.nl/company/tokens</a>';
$_['error_no_serviceid'] = 'U moet een serviceId invoeren, u vind uw serviceId op: 
    <a href="https://admin.pay.nl/programs/programs">https://admin.pay.nl/programs/programs</a>. Een serviceId begint altijd met SL-';

//texts
$_['text_register'] = 'Nog geen account bij Pay.? Klik ';
$_['text_link_register'] = 'hier';
$_['link_register'] = 'https://www.pay.nl/registreren';
$_['text_after_register'] = ' om u aan te melden.';

$_['text_general_settings'] = 'Pay. Algemene instellingen';
$_['text_method_settings'] = 'Betaalmethode instellingen';

$_['text_confirm_start_tooltip'] = 'De order bevestigen bij het starten van de transactie, dus voordat er betaald is. De bevestigingsmail wordt dan ook meteen verstuurd';
$_['text_confirm_start'] = 'Order bevestigen bij starten transactie';
$_['text_send_statusupdates'] = 'Statusupdates versturen';
$_['text_send_statusupdates_tooltip'] = 'De gebruiker een email sturen als de status van de bestelling veranderd';

$_['text_gateway'] = 'Failover gateway';
$_['text_gateway_tooltip'] = 'Voer hier alleen iets in als wij van Pay. een gateway aan u doorgeven om hier in te vullen';

$_['text_prefix'] = 'Order omschrijving prefix';
$_['text_prefix_tooltip'] = 'Verander de order omschrijving prefix hier. Als dit leeg is, zal de omschrijving het ordernummer zijn.';

$_['text_advanced_settings'] = 'Geavanceerde instellingen';

$_['text_auto_void'] = 'Auto void';
$_['text_auto_void_tooltip'] = 'Geautoriseerde transacties automatisch vrijgeven (void) bij het annuleren van een bestelling.';

$_['text_auto_capture'] = 'Auto capture';
$_['text_auto_capture_tooltip'] = 'Schakel auto capture in voor gereserveerde transacties met status AUTHORIZE. De capture wordt uitgevoerd wanneer een bestelstatus wijzigt naar Completed.';

$_['text_refund_processing'] = 'Verwerking terugbetaling';
$_['text_refund_processing_tooltip'] = 'Verwerk terugbetalingen die gestart zijn vanuit Pay.';

$_['text_follow_payment_method'] = 'Follow payment method';
$_['text_follow_payment_method_tooltip'] = 'Dit zorgt ervoor dat de bestelling wordt bijgewerkt met de daadwerkelijke betaalmethode die is gebruikt om de bestelling te voltooien. Dit kan afwijken van de aanvankelijk gekozen betaalmethode'; // phpcs:ignore

$_['text_coc'] = 'Toon KVK nummer veld';
$_['text_coc_tooltip'] = 'Wanneer dit aan staat zal de klant een optie hebben om hun KVK nummer in te voeren voordat ze de transactie afmaken';
$_['text_coc_disabled'] = 'nee';
$_['text_coc_enabled'] = 'Ja, als optioneel veld';
$_['text_coc_required'] = 'Ja, als verplicht veld';

$_['text_vat'] = 'Toon BTW nummer veld voor zakelijke klanten';
$_['text_vat_tooltip'] = 'Wanneer dit aan staat zal de klant een optie hebben om hun BTW nummer in te voeren voordat ze de transactie afmaken';
$_['text_vat_disabled'] = 'Uit';
$_['text_vat_enabled'] = 'Optioneel voor zakelijke klanten';
$_['text_vat_required'] = 'Verplicht voor zakelijke klanten';

$_['text_dob'] = 'Toon geboortedatum veld';
$_['text_dob_tooltip'] = 'Wanneer dit aan staat zal de klant een optie hebben om hun geboortedatum in te voeren voordat ze de transactie afmaken';
$_['text_dob_disabled'] = 'Nee';
$_['text_dob_enabled'] = 'Ja, als optioneel veld';
$_['text_dob_required'] = 'Ja, als verplicht veld';

$_['text_display_icon'] = 'Icoon weergeven';
$_['text_display_icon_tooltip'] = 'Selecteer hier of je een icoon wilt weergeven en welke grootte.';

$_['text_custom_exchange_url'] = 'Alternatieve Exchange URL';
$_['text_custom_exchange_url_tooltip'] = 'Gebruik je eigen exchange-handler. Requests zullen verzonden worden al een GET.<br/>
    Voorbeeld: https://www.yourdomain.nl/exchange_handler?action=#action#&order_id=#order_id#';

$_['text_current_ip'] = 'IP-adres van huidige gebruiker: ';
$_['text_test_ip'] = 'Test IP Adressen';
$_['text_test_ip_tooltip'] = "Forceer test mode voor de ingevulde IP adressen, scheid IP's met komma's voor meerdere IP's";

$_['text_logging'] = 'Logging';
$_['text_logging_tooltip'] = "Schakel logging in";

$_['text_testmode'] = 'Test mode';
$_['text_testmode_tooltip'] = 'Zet de test mode aan of uit om de exchanges te testen tussen Pay. en uw webshop';

$_['text_status_pending'] = 'Order status wacht op betaling';
$_['text_status_pending_tooltip'] = 'De status van de order wanneer de betaling is gestart, maar nog niet afgerond';
$_['text_status_complete'] = 'Order status betaling voltooid';
$_['text_status_complete_tooltip'] = 'De status die het order moet krijgen nadat de betaling succesvol is ontvangen';
$_['text_status_canceled'] = 'Order status geannuleerd';
$_['text_status_canceled_tooltip'] = 'De status die het order moet krijgen nadat de betaling is geannuleerd';
$_['text_status_refunded'] = 'Order status terugbetaald';
$_['text_status_refunded_tooltip'] = 'De status die het order moet krijgen nadat de betaling is terugbetaald';
$_['text_minimum_amount'] = 'Minimaal order bedrag';
$_['text_maximum_amount'] = 'Maximaal order bedrag';
$_['text_payment_instructions'] = 'Instructies';
$_['text_payment_instructions_tooltip'] = 'Als u instructies wilt tonen aan de klant, kunt u die hier hier aangeven';

$_['entry_order_status'] = 'Order Status';
$_['entry_geo_zone']     = 'Geo Zone';
$_['entry_status']       = 'Status';
$_['entry_sort_order']   = 'Sort Order';

$_['text_customer_type'] = 'Toegestaan ​​klanttype';
$_['text_customer_type_tooltip'] = 'Selecteer welk type klant de betaalmethode kan gebruiken.';
$_['text_both'] = 'Beide';
$_['text_private'] = 'Privé';
$_['text_business'] = 'Zakelijk';

$_['text_extension'] = 'Pay.';

$_['text_enabled'] = 'Aan';
$_['text_disabled'] = 'Uit';

$_['text_suggestions'] = 'Als je een idee hebt voor een functie die je graag terugziet, laat het ons weten!<br/>Na het indienen, wordt deze intern beoordeeld door ons ontwikkelteam.<br/<br/>Zo nodig, nemen wij contact op voor nadere informatie via het opgegeven mailadres<br/>Let op: dit formulier is niet voor Support aanvragen, neem hiervoor contact op met <a href="mailto:support@pay.nl" target="_blank">support@pay.nl</a>.'; // phpcs:ignore

$_['text_email_label'] = 'E-mail (optioneel)';
$_['text_email_error'] = 'Vul een geldig e-mailadres in.';

$_['text_message_label'] = 'Bericht';
$_['text_message_error'] = 'Vul een bericht in.';
$_['text_message_placeholder'] = 'Laat hier jouw wens of idee achter...';

$_['text_suggestions_submit'] = 'Verstuur';

$_['text_suggestions_success_modal'] = 'Verstuurd! Bedankt voor het delen van jouw input.';
$_['text_suggestions_fail_modal'] = 'E-mail kon niet worden verzonden. Probeer het later opnieuw.';

$version = '1.9.1';
$_['version'] = $version;
$css = 'position: relative;top:0px;display: inline;left: 10px;';
$paynl_logo = '<a href="https://www.pay.nl" target="paynl"><img style="width: 30px;" 
    src="view/image/payment/main_pay_logo.png" alt="Pay." title="Pay." /></a>' . '<div style="' . $css . '">Version: ' . $version . '</div>';

$arrPaymentMethods = array(
    'afterpay',
    'afterpayint',
    'alipay',
    'amazonpay',
    'amex',
    'applepay',
    'beautycadeau',
    'biercheque',
    'biller',
    'billink',
    'bioscoopbon',
    'blik',
    'bloemencadeau',
    'boekenbon',
    'capayablegespreid',
    'cartebleue',
    'cashly',
    'creditclick',
    'cult',
    'dankort',
    'decadeaukaart',
    'dinerbon',
    'eps',
    'fashioncheque',
    'fashiongiftcard',
    'festivalcadeau',
    'gezondheidsbon',
    'giropay',
    'givacard',
    'good4fun',
    'googlepay',
    'horsesandgifts',
    'huisentuincadeau',
    'ideal',
    'in3business',
    'incasso',
    'klarnakp',
    'maestro',
    'monizze',
    'mooigiftcard',
    'mistercash',
    'multibanco',
    'nexi',
    'onlinebankbetaling',
    'overboeking',
    'parfumcadeaukaart',
    'payconiq',
    'paypal',
    'paysafecard',
    'podiumcadeaukaart',
    'phone',
    'podiumcadeaukaart',
    'postepay',
    'przelewy24',
    'shoesandsneakers',
    'sodexo',
    'sofortbanking',
    'sofortbankingds',
    'sofortbankinghr',
    'spraypay',
    'trustly',
    'visamastercard',
    'vvvgiftcard',
    'webshopgiftcard',
    'wechatpay',
    'wijncadeau',
    'winkelcheque',
    'yourgift',
    'yourgreengift',
);

foreach ($arrPaymentMethods as $paymentMethod) {
    $_['text_paynl_' . $paymentMethod] = $paynl_logo;
}
