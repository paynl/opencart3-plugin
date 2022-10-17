<?php
// Text
$_['text_payment'] = 'Betaling';

$_['button_save'] = 'Opslaan';
$_['button_cancel'] = 'Annuleren';

$_['text_success'] = 'Instellingen opgeslagen';

//errors
$_['error_not_activated'] = "Deze betaalmethode is niet geactiveerd voor deze dienst. Ga naar  <a target='paynl' href='https://admin.pay.nl/programs/programs'>https://admin.pay.nl/programs/programs</a> om dit aan te passen.";
$_['error_api_error'] = 'De PAY. Api gaf de volgende fout: ';
$_['error_error_occurred'] = 'Er is een fout opgetreden: ';
$_['error_no_apitoken'] = 'U moet een apitokeninvoeren, u vind uw apitokens op: <a href="https://admin.pay.nl/company/tokens">https://admin.pay.nl/company/tokens</a>';
$_['error_no_serviceid']= 'U moet een serviceId invoeren, u vind uw serviceId op: <a href="https://admin.pay.nl/programs/programs">https://admin.pay.nl/programs/programs</a>. Een serviceId begint altijd met SL-';

//texts
$_['text_register'] = 'Nog geen account bij PAY.? Klik ';
$_['text_link_register'] = 'hier';
$_['link_register'] = 'https://www.pay.nl/registreren';
$_['text_after_register'] = ' om u aan te melden.';

$_['text_general_settings'] = 'PAY. Algemene instellingen';
$_['text_method_settings'] = 'Betaalmethode instellingen';

$_['text_confirm_start_tooltip'] = 'De order bevestigen bij het starten van de transactie, dus voordat er betaald is. De bevestigingsmail wordt dan ook meteen verstuurd';
$_['text_confirm_start'] = 'Order bevestigen bij starten transactie';
$_['text_send_statusupdates'] = 'Statusupdates versturen';
$_['text_send_statusupdates_tooltip'] = 'De gebruiker een email sturen als de status van de bestelling veranderd';

$_['text_gateway'] = 'Failover gateway';
$_['text_gateway_tooltip'] = 'Voer hier alleen iets in als wij van PAY. een gateway aan u doorgeven om hier in te vullen';

$_['text_prefix'] = 'Order omschrijving prefix';
$_['text_prefix_tooltip'] = 'Verander de order omschrijving prefix hier. Als dit leeg is, zal de omschrijving het ordernummer zijn.';

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

$_['text_icon_style'] = 'Icoon stijl';
$_['text_icon_style_tooltip'] = 'Selecteer hier of u de klassieke of de nieuwste afbeeldingen wilt gebruiken.';
$_['text_classic'] = 'Klassiek';
$_['text_newest'] = 'Nieuwste';

$_['text_testmode'] = 'Testmode';
$_['text_testmode_tooltip'] = 'Zet de testmode aan of uit om de exchanges te testen tussen PAY. en uw webshop';

$_['text_status_pending']='Order status wacht op betaling';
$_['text_status_pending_tooltip']='De status van de order wanneer de betaling is gestart, maar nog niet afgerond';
$_['text_status_complete']='Order status betaling voltooid';
$_['text_status_complete_tooltip']='De status die het order moet krijgen nadat de betaling succesvol is ontvangen';
$_['text_status_canceled']='Order status geannuleerd';
$_['text_status_canceled_tooltip']='De status die het order moet krijgen nadat de betaling is geannuleerd';
$_['text_minimum_amount']='Minimaal order bedrag';
$_['text_maximum_amount']='Maximaal order bedrag';
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

$_['text_extension'] = 'PAY.';

$version = '1.5.1';
$css = 'position: relative;top:0px;display: inline;left: 10px;';
$paynl_logo = '<a href="https://www.pay.nl" target="paynl"><img style="width: 30px;" src="view/image/payment/main_pay_logo.png" alt="PAY." title="PAY." /></a>' .
    '<div style="' . $css . '">Version: ' . $version . '</div>';;

$arrPaymentMethods = array(
    'afterpay',
    'afterpayem',
    'afterpayint',
    'amex',
    'applepay',
    'biercheque',
    'billink',
    'bitcoin',
    'capayable',
    'capayablegespreid',
    'cartebleue',
    'cashly',
    'creditclick',
    'decadeaukaart',
    'eps',
    'fashioncheque',
    'fashiongiftcard',
    'focum',
    'gezondheidsbon',
    'giropay',
    'givacard',
    'good4fun',
    'googlepay',
    'ideal',
    'incasso',
    'klarnakp',
    'maestro',
    'mistercash',
    'multibanco',
    'mybank',
    'nexi',
    'overboeking',
    'payconiq',
    'paypal',
    'paysafecard',
    'phone',
    'podiumkadokaart',
    'postepay',
    'przelewy24',
    'sofortbanking',
    'spraypay',
    'tikkie',
    'trustly',
    'visamastercard',
    'vvvgiftcard',
    'webshopgiftcard',
    'wechatpay',
    'wijncadeau',
    'yehhpay',
    'yourgift'
);

foreach($arrPaymentMethods as $paymentMethod){
    $_['text_paynl_'.$paymentMethod] = $paynl_logo;
}