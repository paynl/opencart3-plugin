<?php

$_['text_apitoken'] = 'jeton API';
$_['text_serviceid'] = 'Service';

// Text
$_['text_payment'] = 'Paiement';

$_['button_save'] = 'Sauvegarder';
$_['button_cancel'] = 'Annuler';

$_['text_success'] = 'Paramètres sauvegardés';

//errors
$_['error_not_activated'] = "Ce mode de paiement n'est pas activé pour ce site, rendez-vous sur 
    <a target='paynl' href='https://my.pay.nl/programs/programs'>https://my.pay.nl/programs/programs</a> pour modifier votre site";
$_['error_api_error'] = 'Le Pay. Api a répondu avec l\'erreur suivante : ';
$_['error_error_occurred'] = 'Une erreur est survenue: ';
$_['error_no_apitoken'] = 'Vous devez saisir un jeton API, vous pouvez retrouver vos jeton API sur: 
    <a href="https://admin.pay.nl/company/tokens">https://admin.pay.nl/company/tokens</a>';
$_['error_no_serviceid'] = 'Vous avez un SL-code demandé et vous avez un ID de service op: 
    <a href="https://my.pay.nl/company/tokens">https://my.pay.nl/company/tokens</a>. Un SL-code commence altijd avec SL-';

//texts
$_['text_register'] = 'Vous n\'êtes pas inscrit chez Pay? Inscrivez-vous ';
$_['text_link_register'] = 'ici';
$_['link_register'] = 'https://signup.pay.nl/';
$_['text_after_register'] = '!';

$_['text_general_settings'] = 'Pay. Réglages généraux';
$_['text_method_settings'] = 'Paramètres du mode de paiement';

$_['text_confirm_start_tooltip'] = 'Confirmez la commande au début de la transaction, avant que la transaction ne soit payée. L\'e-mail de confirmation sera envoyé immédiatement';
$_['text_confirm_start'] = 'Confirmer la commande au début de la transaction';
$_['text_send_statusupdates'] = 'Envoyer des mises à jour de statut';
$_['text_send_statusupdates_tooltip'] = 'Envoyer au client un e-mail à chaque fois que le statut de la commande change';

$_['text_gateway'] = 'Failover gateway';
$_['text_gateway_tooltip'] = 'Ne remplissez ceci que lorsque Pay. vous fournit une passerelle à remplir ici.';

$_['text_prefix'] = 'Préfixe de description de la commande';
$_['text_prefix_tooltip'] = 'Modifiez le préfixe de description de la commande ici. Si elle est laissée vide, la description de la commande sera simplement le numéro de commande.';

$_['text_follow_payment_method'] = 'Follow payment method';
$_['text_follow_payment_method_tooltip'] = 'Cela garantira que la commande est mise à jour avec le mode de paiement réel utilisé pour finaliser la commande. Celui-ci peut différer du mode de paiement initialement sélectionné'; // phpcs:ignore
$_['text_advanced_settings'] = 'Réglages avancés';

$_['text_coc'] = 'Afficher le champ du numéro de la Chambre de Commerce';
$_['text_coc_tooltip'] = 'Lorsqu\'il est activé, le client aura la possibilité de saisir son numéro de chambre de commerce avant de terminer la transaction.';
$_['text_coc_disabled'] = 'Non';
$_['text_coc_enabled'] = 'Oui, en option';
$_['text_coc_required'] = 'Oui, au besoin';

$_['text_vat'] = 'Afficher le champ du numéro de TVA pour les clients professionnels';
$_['text_vat_tooltip'] = 'Lorsqu\'il est activé, le client aura la possibilité de saisir son numéro de TVA avant de terminer la transaction.';
$_['text_vat_disabled'] = 'Désactivé';
$_['text_vat_enabled'] = 'En option pour les clients professionnels';
$_['text_vat_required'] = 'Obligatoire pour les clients professionnels';

$_['text_dob'] = 'Afficher le champ date de naissance';
$_['text_dob_tooltip'] = 'Lorsqu\'il est activé, le client peut en outre saisir sa date de naissance avant de terminer la transaction.';
$_['text_dob_disabled'] = 'Non';
$_['text_dob_enabled'] = 'Oui, en option';
$_['text_dob_required'] = 'Oui, au besoin';

$_['text_display_icon'] = 'Icône d\'affichage';
$_['text_display_icon_tooltip'] = 'Sélectionnez si vous souhaitez afficher une icône et la taille';

$_['text_custom_exchange_url'] = 'URL d\'échange alternative';
$_['text_custom_exchange_url_tooltip'] = 'Utilisez votre propre gestionnaire d\'échange. Les demandes seront envoyées sous forme de GET.<br/>
    Exemple: https://www.yourdomain.nl/exchange_handler?action=#action#&order_id=#order_id#';

$_['text_testmode'] = 'Mode d\'essai';
$_['text_testmode_tooltip'] = 'Activez ou désactivez le mode test pour tester les échanges entre Pay. et votre boutique en ligne';

$_['text_status_pending'] = 'Statut de la commande en attente de paiement';
$_['text_status_pending_tooltip'] = 'Le statut de la commande lorsque le paiement est commencé, mais pas encore terminé';
$_['text_status_complete'] = 'Statut de la commande, paiement réussi';
$_['text_status_complete_tooltip'] = 'Le statut de la commande lorsque le paiement est réussi';
$_['text_status_canceled'] = 'Order status canceled';
$_['text_status_canceled_tooltip'] = 'Statut de la commande annulé';
$_['text_minimum_amount'] = 'Montant minimum de commande';
$_['text_maximum_amount'] = 'Montant maximum de la commande';
$_['text_payment_instructions'] = 'Instructions';
$_['text_payment_instructions_tooltip'] = 'Si vous souhaitez donner des instructions au client, vous pouvez les donner ici';

$_['entry_order_status'] = 'Statut de la commande';
$_['entry_geo_zone']     = 'Zone géographique';
$_['entry_status']       = 'Statut';
$_['entry_sort_order']   = 'Ordre de tri';

$_['text_customer_type'] = 'Type de client autorisé';
$_['text_customer_type_tooltip'] = 'Sélectionnez le type de client pour lequel vous souhaitez pouvoir accéder au mode de paiement.';
$_['text_both'] = 'Les deux';
$_['text_private'] = 'Privé';
$_['text_business'] = 'Entreprise';

$_['text_extension'] = 'Pay.';

$_['text_enabled'] = 'marche';
$_['text_disabled'] = 'arrêt';

$version = '1.7.1';
$css = 'position: relative;top:0px;display: inline;left: 10px;';
$paynl_logo = '<a href="https://www.pay.nl" target="paynl"><img style="width: 30px;" 
    src="view/image/payment/main_pay_logo.png" alt="PAY." title="PAY." /></a>' . '<div style="' . $css . '">Version: ' . $version . '</div>';

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
    'klarnakp',
    'kunstencultuurkaart',
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
    'stadspasamsterdam',
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
