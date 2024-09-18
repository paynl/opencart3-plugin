<?php

$_['text_apitoken'] = 'API token';
$_['text_serviceid'] = 'Sales location';

// Text
$_['text_payment'] = 'Zahlung';

$_['button_save'] = 'Speichern';
$_['button_cancel'] = 'Stornieren';

$_['text_success'] = 'Einstellungen gespeichert';

//errors
$_['error_not_activated'] = "Diese Zahlungsmethode ist für diesen Dienst nicht aktiviert. Gehe zu
<a target='paynl' href='https://admin.pay.nl/programs/programs'>https://admin.pay.nl/programs/programs</a>, um dies anzupassen.";
$_['error_api_error'] = 'Die Pay. API hat den folgenden Fehler ausgegeben: ';
$_['error_error_occurred'] = 'Ein Fehler ist aufgetreten: ';
$_['error_no_apitoken'] = 'Sie müssen einen API token eingeben. Sie finden Ihre API token unter:
    <a href="https://my.pay.nl/programs/programs">https://my.pay.nl/programs/programs</a>';
$_['error_no_serviceid'] = 'Sie müssen eine SL-code eingeben. Sie finden Ihre SL-code unter:
    <a href="https://my.pay.nl/programs/programs">https://my.pay.nl/programs/programs</a>. Eine SL-code beginnt immer mit SL-';

//texts
$_['text_register'] = 'Sie haben noch kein Konto bei Pay? klicken Sie ';
$_['text_link_register'] = 'hier';
$_['link_register'] = 'https://signup.pay.nl/';
$_['text_after_register'] = ' um sich zu registrieren.';

$_['text_general_settings'] = 'Pay. Allgemeine Einstellungen';
$_['text_method_settings'] = 'Einstellungen für die Zahlungsmethode';

$_['text_confirm_start_tooltip'] = 'Bestätigen Sie die Bestellung beim Start der Transaktion, d.h. bevor die Zahlung erfolgt ist. Die Bestätigungs-E-Mail wird dann umgehend versendet';
$_['text_confirm_start'] = 'Bestätigen Sie die Bestellung, wenn Sie die Transaktion starten';
$_['text_send_statusupdates'] = 'Statusaktualisierungen senden';
$_['text_send_statusupdates_tooltip'] = 'Senden Sie dem Benutzer eine E-Mail, wenn sich der Status der Bestellung ändert';

$_['text_gateway'] = 'Failover gateway';
$_['text_gateway_tooltip'] = 'Nur ändern, wenn sie von Pay dazu aufgefordert werden.';

$_['text_prefix'] = 'Präfix der Bestellbeschreibung';
$_['text_prefix_tooltip'] = 'Ändern Sie hier das Präfix der Bestellbeschreibung. Wenn dieses Feld leer ist, ist die Beschreibung die Bestellnummer.';

$_['text_follow_payment_method'] = 'Follow payment method';
$_['text_follow_payment_method_tooltip'] = 'Dadurch wird sichergestellt, dass die Bestellung mit der tatsächlichen Zahlungsmethode aktualisiert wird, die zum Abschluss der Bestellung verwendet wurde. Diese kann von der ursprünglich gewählten Zahlungsart abweichen'; // phpcs:ignore
$_['text_advanced_settings'] = 'Erweiterte Einstellungen';

$_['text_coc'] = 'Feld mit der Nummer der Handelskammer anzeigen';
$_['text_coc_tooltip'] = 'Wenn diese Option aktiviert ist, hat der Kunde die Möglichkeit, seine Handelskammernummer einzugeben im Checkout';
$_['text_coc_disabled'] = 'nein';
$_['text_coc_enabled'] = 'Ja, als optionales Feld';
$_['text_coc_required'] = 'Ja, als Pflichtfeld';

$_['text_vat'] = 'Feld USt-IdNr für Geschäftskunden anzeigen';
$_['text_vat_tooltip'] = 'Wenn diese Option aktiviert ist, hat der Kunde die Möglichkeit, seine Umsatzsteuer-Identifikationsnummer einzugeben im Checkout';
$_['text_vat_disabled'] = 'Aus';
$_['text_vat_enabled'] = 'Optional für Geschäftskunden';
$_['text_vat_required'] = 'Erforderlich für Geschäftskunden';

$_['text_dob'] = 'Feld „Geburtsdatum“ anzeigen';
$_['text_dob_tooltip'] = 'Wenn diese Option aktiviert ist, hat der Kunde die Möglichkeit, seine Geburtsdatum einzugeben im Checkout';
$_['text_dob_disabled'] = 'nein';
$_['text_dob_enabled'] = 'Ja, als optionales Feld';
$_['text_dob_required'] = 'Ja, als Pflichtfeld';

$_['text_display_icon'] = 'Symbol anzeigen';
$_['text_display_icon_tooltip'] = 'Wählen Sie hier aus, ob und in welcher Größe ein Symbol angezeigt werden soll.';

$_['text_icon_style'] = 'Symbolstil';
$_['text_icon_style_tooltip'] = 'Wählen Sie hier aus, ob Sie die klassischen oder die neuesten Bilder verwenden möchten.';
$_['text_classic'] = 'Klassisch';
$_['text_newest'] = 'Neueste';

$_['text_testmode'] = 'Test modus';
$_['text_testmode_tooltip'] = 'Schalten Sie den Test modus ein oder aus, um den Austausch zwischen Pay. und Ihrem Webshop zu testen';

$_['text_status_pending'] = 'Bestellstatus wartet auf Zahlung';
$_['text_status_pending_tooltip'] = 'Der Status der Bestellung, wenn die Zahlung begonnen, aber noch nicht abgeschlossen ist';
$_['text_status_complete'] = 'Bestellstatus: Zahlung abgeschlossen';
$_['text_status_complete_tooltip'] = 'Der Status, den die Bestellung nach erfolgreichem Zahlungseingang erhalten soll';
$_['text_status_canceled'] = 'Bestellstatus storniert';
$_['text_status_canceled_tooltip'] = 'Der Status, den die Bestellung erhalten soll, nachdem die Zahlung storniert wurde';
$_['text_minimum_amount'] = 'Mindestbestellmenge';
$_['text_maximum_amount'] = 'Maximale Bestellmenge';
$_['text_payment_instructions'] = 'Anweisungen';
$_['text_payment_instructions_tooltip'] = 'Wenn Sie dem Kunden Anweisungen zeigen möchten, können Sie diese hier angeben';

$_['entry_order_status'] = 'Bestellstatus';
$_['entry_geo_zone']     = 'Geo Zone';
$_['entry_status']       = 'Status';
$_['entry_sort_order']   = 'Sortierreihenfolge';

$_['text_customer_type'] = 'Zulässiger Kundentyp';
$_['text_customer_type_tooltip'] = 'Wählen Sie aus, welcher Kundentyp die Zahlungsmethode verwenden kann.';
$_['text_both'] = 'Beide';
$_['text_private'] = 'Privat';
$_['text_business'] = 'Kommerziell';

$_['text_extension'] = 'Pay.';

$_['text_enabled'] = 'An';
$_['text_disabled'] = 'Aus';

$version = '1.7.1';
$css = 'position: relative;top:0px;display: inline;left: 10px;';
$paynl_logo = '<a href="https://www.pay.nl" target="paynl"><img style="width: 30px;" 
    src="view/image/payment/main_pay_logo.png" alt="Pay." title="Pay." /></a>' . '<div style="' . $css . '">Version: ' . $version . '</div>';

$arrPaymentMethods = array(
    'afterpay',
    'afterpayint',
    'alipay',
    'alipayplus',
    'alma',
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
    'kidsorteen',
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
