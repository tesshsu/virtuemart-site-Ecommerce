<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


Class Sap_xml_handler {

	function create_SAP_XML($order_id) {

		//Récupération des infos de connexion à la DB
		include_once(dirname(__FILE__)."/configuration.php");

		$config = new JConfig();

		$db_sap = new PDO('mysql:host=localhost;dbname='.$config->db, $config->user, $config->password);

		//Récupération des données de la commande (1 ligne)
		$order_request = $db_sap->query('SELECT virtuemart_user_id, created_on, order_tax, order_shipment, order_language, order_payment, order_total, order_status, virtuemart_shipmentmethod_id, virtuemart_paymentmethod_id
										FROM h8q2p_virtuemart_orders
										WHERE virtuemart_order_id = '.$order_id);
		$order_data = $order_request->fetch();

		//Récupération de l'utilisateur lié a la commande
		$user_id = $order_data['virtuemart_user_id'];
		$payment_id = $order_data['virtuemart_paymentmethod_id'];
		$shipping_id = $order_data['virtuemart_shipmentmethod_id'];

		//Récupération des données du mode de paiement
		
		$payment_request = $db_sap->query('SELECT payment_name
										FROM h8q2p_virtuemart_paymentmethods_fr_fr
										WHERE virtuemart_paymentmethod_id = '.$payment_id);
		$payment_data = $payment_request->fetch();

		$payment_name = $payment_data['payment_name'];

		//Récupération des données du mode de livraison
		$shipping_request = $db_sap->query('SELECT shipment_name, shipment_desc
										FROM h8q2p_virtuemart_shipmentmethods_fr_fr
										WHERE virtuemart_shipmentmethod_id = '.$shipping_id);
		$shipping_data = $shipping_request->fetch();

		$shipping_name = $shipping_data['shipment_name'];
		$shipping_desc = $shipping_data['shipment_desc'];

		//Génération de l'user code
		$number=10;
		$code='INTFR';
		$generatedCode= str_pad($user_id, $number, "0", STR_PAD_LEFT).$code;

		if($generatedCode){
			$code_request = $db_sap->prepare('UPDATE h8q2p_virtuemart_userinfos
								SET user_code = :code
								WHERE virtuemart_user_id = :user_id');
			$code_request->bindParam(':code',$generatedCode, PDO::PARAM_STR);
			$code_request->bindParam(':user_id',$user_id, PDO::PARAM_STR);
			$code_request->execute();
		}

		//Récupération des données de l'utilisateur dans la commande (1/2 ligne)
		$order_user_info_data_ST; $order_user_info_data_BT;
		$user_info_request = $db_sap->query('SELECT * 
										FROM h8q2p_virtuemart_order_userinfos
										WHERE virtuemart_order_id = '.$order_id);

		while($order_user_info_data = $user_info_request->fetch()) {
			if($order_user_info_data['address_type'] == 'BT') {
				$order_user_info_data_BT = $order_user_info_data;
			} else if($order_user_info_data['address_type'] == 'ST' && $order_user_info_data['address_type_name'] !== null) {
				$order_user_info_data_ST = $order_user_info_data;
			}
		}

		if($order_user_info_data_ST == null || $order_user_info_data_ST == '') {
			$order_user_info_delivery = $order_user_info_data_BT;
		}  else {
			$order_user_info_delivery = $order_user_info_data_ST;
		}


		//Test si le groupe utilisateur est personnalisé 
		$user_group = 5;
		$has_discount = false;
		$distributeur_group;
		$discount_value = 0;

		$group_request = $db_sap->query('SELECT virtuemart_shoppergroup_id 
										FROM h8q2p_virtuemart_vmuser_shoppergroups
										WHERE virtuemart_user_id = '.$user_id);
		while($group_data = $group_request->fetch()) {
			//$user_group = $group_data['virtuemart_shoppergroup_id'];
			if($group_data['virtuemart_shoppergroup_id'] != 5) {
				$user_group = 7;
			}
			if($group_data['virtuemart_shoppergroup_id'] > 7 && $group_data['virtuemart_shoppergroup_id'] != 25 && $group_data['virtuemart_shoppergroup_id'] != 16) {
				$distributeur_group = $group_data['virtuemart_shoppergroup_id'];
				$has_discount = true;
			}
		}

		//Si il a une remise, on la récupère
		if($has_discount) {
			$group_request = $db_sap->query('SELECT calc_value 
										FROM h8q2p_virtuemart_calcs
										INNER JOIN h8q2p_virtuemart_calc_shoppergroups ON h8q2p_virtuemart_calcs.virtuemart_calc_id = h8q2p_virtuemart_calc_shoppergroups.virtuemart_calc_id
										WHERE virtuemart_shoppergroup_id = '.$distributeur_group);
			while($group_data = $group_request->fetch()) {
				$discount_value = $group_data['calc_value'];
			}
		}

		//Récupération des données de l'utilisateur(1/2 ligne)
		$user_request = $db_sap->query('SELECT * 
										FROM h8q2p_virtuemart_userinfos
										WHERE h8q2p_virtuemart_userinfos.virtuemart_user_id = '.$user_id);

		while($user_data = $user_request->fetch()) {
			if($user_data['address_type'] == 'BT') {
				$user_data_BT = $user_data;

			} else if($user_data['address_type'] == 'ST') {
				$user_data_ST = $user_data;
			}
		}

		if($user_data_ST == null || $user_data_ST == '') {
			$user_delivery = $user_data_BT;
		}  else {
			$user_delivery = $user_data_ST;
		}

		//Récupération des données du pays (1 ligne)
		$country_request = $db_sap->query('SELECT * 
										FROM h8q2p_virtuemart_countries
										WHERE virtuemart_country_id = '.$order_user_info_delivery['virtuemart_country_id']);
		$country_data = $country_request->fetch();

		$countrybt_request = $db_sap->query('SELECT * 
										FROM h8q2p_virtuemart_countries
										WHERE virtuemart_country_id = '.$order_user_info_data_BT['virtuemart_country_id']);
		$country_billing = $countrybt_request->fetch();

		if($country_data['country_2_code'] != $country_billing['country_2_code']) {
			$country_shipping = $country_data;
		} else {
			$country_shipping = $country_billing;
		}
		

		//Récupération des données de l'Etat (1 ligne)
		if($order_user_info_delivery['virtuemart_state_id'] != null) {
			$state_request = $db_sap->query('SELECT * 
											FROM h8q2p_virtuemart_states
											WHERE virtuemart_state_id = '.$order_user_info_delivery['virtuemart_state_id']);
			$state_data = $state_request->fetch();
			$state_id = $state_data['virtuemart_country_id'];
		} else {
			$state_id = 0;
		}
		
		//STATES que pour les USA et chine et japon
		if($state_id == 44 || $state_id == 223 || $state_id == 107) {
			$state = $state_data['state_3_code'];
		} else {
			$state = '';
		}

		//TVA Intra
		if($order_user_info_data_BT['eu_vat_id'] != null && $order_user_info_data_BT['eu_vat_id'] != ''){
			$FederalTaxID = $order_user_info_data_BT['eu_vat_id'];
		}
		else{
			$FederalTaxID = '';
		}

		//language
		$countryCode=array('HE'=>1,'EN'=>3,'PL'=>5,'GB'=>8,'DE'=>9,'DK'=>11,'NO'=>12,'IT'=>13,'HU'=>14,'CN'=>15,'NL'=>16,'FI'=>17,'EL'=>18,'PT'=>19,'SE'=>20,'FR'=>22,'ES'=>23,'RU'=>24,'CO'=>25,'CZ'=>26,'SK'=>27,'KO'=>28,'BR'=>29,'JA'=>30,'TW'=>35,'ZH'=>15);

		if($order_data['order_language'] != null){
			$code = explode("-", $order_data['order_language']);
			$LanguageCode = strtoupper($code[0]);
		}
		else{
			$LanguageCode='EN';
		}

		//La facture est français pour la france, anglais pour le reste
		if(!array_key_exists($LanguageCode,$countryCode)){
			$codelangue = 3;
		}
		else{
			if($countryCode[$LanguageCode] == 22) {
				$codelangue = 22;
			} else {
				$codelangue = 3;
			}
			//$codelangue=$countryCode[$LanguageCode];
		}

		//TESS:Find the language code to insert in SAP, if order language is Fr show 22 else show 3
        /*if($order_data['order_language'] != null && $order_data['order_language'] == 'fr_FR'){
			$codelangue = 22;
		}else{
			$codelangue = 3;
		}*/

		//Génération du type de TVA applicable (Ancien code de Farid)
		include_once(dirname(__FILE__)."/eurovat.php");
		if($order_data['order_tax'] > 0){
			$PayTermsGrpCode="\t\t\t\t\t<PayTermsGrpCode>131</PayTermsGrpCode>\n";
			$exonereTVA='';
			if($country_data['country_2_code']=='FR'){
				$U_OB1TYPETOTAL='TTC';
				$VatLiable='vLiable';
				$VatGroup='C4';
			}
			else{
				if(EuroVAT($country_data['country_2_code'])){
					$U_OB1TYPETOTAL='DDP';
					$VatLiable='vLiable';
					$VatGroup='C6';
				}
				else{
					$U_OB1TYPETOTAL='DAP';
					$VatLiable='vExempted';
					$VatGroup='C1';
				}
			}
		}
		else{
			$PayTermsGrpCode='';
			if($country_data['country_2_code']=='FR'){
				$U_OB1TYPETOTAL='HT';
				$VatLiable='vLiable';
				$VatGroup='C0';
				$exonereTVA="\t\t\t\t\t<U_OB1EXONERETVA>Y</U_OB1EXONERETVA>\n";
			}
			else{
				if(EuroVAT($country_data['country_2_code'])){
					$U_OB1TYPETOTAL='HT';
					$VatLiable='vEC';
					$VatGroup='E7';
				}
				else{
					$U_OB1TYPETOTAL='DAP';
					$VatLiable='vExempted';
					$VatGroup='C1';
				}
				$exonereTVA='';
			}
		}

		//Génération de l'adresse de base (Ancien code de Farid)
		$complete_name_BT=ucfirst($order_user_info_data_BT['last_name'])." ".ucfirst($order_user_info_data_BT['first_name']);
		$complete_name_ST=ucfirst($order_user_info_delivery['last_name'])." ".ucfirst($order_user_info_delivery['first_name']);
		$adressnameBT = $complete_name_BT;
		$adressnameST = $complete_name_ST;
		/*
		if($user_group == 7){ // Groupe PRO
			if($order_user_info_data_BT['address_type_name']=='-default-'){
				if($order_user_info_data_BT['company'] != ''){
					$adressnameBT=$order_user_info_data_BT['company'];
				}
				else{
					$adressnameBT=$complete_name_BT;
				}
			}
			else{
				if($order_user_info_data_BT['company'] != ''){
					$adressnameBT=$order_user_info_data_BT['company'].' '.$order_user_info_data_BT['address_type_name'];
				}
				else{
				$adressnameBT=$complete_name_BT.' '.$order_user_info_data_BT['address_type_name'];
				}
			}
			
			if($order_user_info_data_ST['address_type_name']=='-default-'){
				if($order_user_info_data_BT['company']!=''){
					$adressnameST=$order_user_info_data_BT['company'];
				}
				else{
					$adressnameST=$complete_name_ST;
				}
			}
			else{
				if($order_user_info_data_BT['company']!=''){
					$adressnameST=$order_user_info_data_BT['company'].' '.$order_user_info_data_ST['address_type_name'];
				}
				else{
					$adressnameST=$complete_name_ST.' '.$order_user_info_data_ST['address_type_name'];
				}
			}
			
		}
		else{
			if($order_user_info_data_BT['address_type_name']=='-default-'){
				$adressnameBT=$complete_name_BT;
			}
			else{
				$adressnameBT=$complete_name_BT.' '.$order_user_info_data_BT['address_type_name'];
			}
			
			if($order_user_info_data_ST['address_type_name']=='-default-'){
				$adressnameST=$complete_name_ST;
			}
			else{
				$adressnameST=$complete_name_ST.' '.$order_user_info_data_ST['address_type_name'];
			}
		}
		*/

		//Génération du bon email
		if($order_user_info_delivery['email'] != null && $order_user_info_delivery['email'] != '') {
			$user_email = $order_user_info_delivery['email'];
		} else if($order_user_info_data_ST['email'] != null && $order_user_info_data_ST['email'] != '') {
			$user_email = $order_user_info_data_ST['email'];
		} else {
			$user_email = $order_user_info_data_BT['email'];
		}

		//Génération du code de livraison (Ancien code de Farid)
		if(EuroVAT($country_data['country_2_code']) && $country_data['country_2_code'] != 'FR' && $user_group==5){
			$ExpenseCode=3;
		}
		elseif(EuroVAT($country_data['country_2_code']) && $country_data['country_2_code'] != 'FR' && $user_group == 7){
			$ExpenseCode=4;
		}
		elseif($country_data['country_2_code']=='FR' && $user_group==5 ){
			$ExpenseCode=1;
		}
		elseif($country_data['country_2_code']=='FR' && $user_group == 7 ){
			$ExpenseCode=2;
		}
		else{
			$ExpenseCode=5;
		}

		//AJOUT DE L'entreprise si existe
		if($order_user_info_data_BT['company'] != null && $order_user_info_data_BT['company'] != ''){
			$company_bt = ' - '. $order_user_info_data_BT['company'];
		} else if($order_user_info_data_BT['address_type_name'] == null){
			$company_bt = '';
		}
		//Modification de l'entreprise si adresse de livraison et que l'entreprise est différente
        if($order_user_info_data_ST['company'] != null && $order_user_info_data_ST['company'] != '' && $order_user_info_data_ST['address_type_name']!= null){
            $company_st = ' - '. $order_user_info_data_ST['company'];
        } else if($order_user_info_data_ST['address_type_name'] != null && $order_user_info_data_ST['company'] == null){
            $company_st = '';
        }else {
            $company_st = $company_bt;
        }

		//Ajout de l'id de paiement
		if($payment_id == 3) {
			$payment_octg = 143;
		} else {
			$payment_octg = 131;
		} 

		//Ajout de l'id de livraison 
		$is_colissimo = stripos($shipping_desc, 'colissimo');
		$is_free = stripos($shipping_desc, 'gratuit');
		$is_fedex = stripos($shipping_desc, 'fedex');

		if($is_colissimo !== false || $is_free !== false) {
			$shipping_octg = 2;
		} else {
			$shipping_octg = 1;
		}
        
        //Ajout de value de discount si il y a
		$order_items_request = $db_sap->query('SELECT * 
										FROM h8q2p_virtuemart_order_items
										WHERE virtuemart_order_id = '.$order_id);
		while($order_items_data = $order_items_request->fetch()) {
			if($order_items_data['product_item_price'] != $order_items_data['product_discountedPriceWithoutTax']) {
				$discount_value = ( 1 - ( $order_items_data['product_discountedPriceWithoutTax'] / $order_items_data['product_item_price'] ) )*100;
			}
		}

		//Suppression du + des N° de tel
		$phone_number_delivery = str_replace('+', '', $order_user_info_delivery['phone_1']);
		$mobile_number_delivery = str_replace('+', '', $order_user_info_delivery['phone_2']);

		$phone_number_billing = str_replace('+', '', $user_data_BT['phone_1']);
		$mobile_number_billing = str_replace('+', '', $user_data_BT['phone_2']);

		$XML_content="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
		$XML_content.="<OB>\n\t<CLIENT>\n\t\t<BOM>\n\t\t\t<BO>\n";
			$XML_content.="\t\t\t<AdmInfo>\n";
				$XML_content.="\t\t\t\t<Object>2</Object>\n";
				$XML_content.="\t\t\t\t<Version>2</Version>\n";
			$XML_content.="\t\t\t</AdmInfo>\n";
			$XML_content.="\t\t\t<BusinessPartners>\n";
				$XML_content.="\t\t\t\t<row>\n";
					$XML_content.="\t\t\t\t\t<CardType>cCustomer</CardType>\n";
					$XML_content.="\t\t\t\t\t<CardCode>".$user_data_BT['user_code']."</CardCode>\n";
					$XML_content.="\t\t\t\t\t<CardName>".$user_data_BT['last_name']." ".$user_data_BT['first_name']."</CardName>\n";
					$XML_content.="\t\t\t\t\t<GroupCode>100</GroupCode>\n";
					//$XML_content.="\t\t\t\t\t<Phone1>".$phone_number_billing."</Phone1>\n";
					$XML_content.="\t\t\t\t\t<Phone1>".$order_user_info_data_BT['phone_1']."</Phone1>\n"; // MODIFY TESS POUR PHONE 1
					$XML_content.="\t\t\t\t\t<Fax>".$user_data_BT['fax']."</Fax>\n";
					$XML_content.="\t\t\t\t\t<Cellular>".$mobile_number_billing."</Cellular>\n";
					$XML_content.="\t\t\t\t\t<EmailAddress>".$user_email."</EmailAddress>\n";
					$XML_content.="\t\t\t\t\t<FederalTaxID>".$FederalTaxID."</FederalTaxID>\n";
					$XML_content.="\t\t\t\t\t<LanguageCode>".$codelangue."</LanguageCode>\n";
					$XML_content.="\t\t\t\t\t<U_OB1TYPETOTAL>".$U_OB1TYPETOTAL."</U_OB1TYPETOTAL>\n";
					$XML_content.="\t\t\t\t\t<VatLiable>".$VatLiable."</VatLiable>\n";
					$XML_content.="\t\t\t\t\t<VatGroup>".$VatGroup."</VatGroup>\n";
					$XML_content.="\t\t\t\t\t<ContactPerson>".$user_data_BT['last_name']."</ContactPerson>\n"; // AJOUT BRUNO POUR ORGA BURO
					$XML_content.="\t\t\t\t\t<U_OB1REMCLT>".$discount_value."</U_OB1REMCLT>\n"; // AJOUT TESS POIR DISCOUNT VALUE
					$XML_content.=$exonereTVA;
					$XML_content.=$PayTermsGrpCode;
				$XML_content.="\t\t\t\t</row>\n";
			$XML_content.="\t\t\t</BusinessPartners>\n"; 

			//Ship-to
			$XML_content.="\t\t\t<ContactEmployees>\n";
				$XML_content.="\t\t\t\t<row>\n";
					$XML_content.="\t\t\t\t\t<Name>".$order_user_info_delivery['last_name']."</Name>\n";
					$XML_content.="\t\t\t\t\t<FirstName>".$order_user_info_delivery['first_name']."</FirstName>\n";
					$XML_content.="\t\t\t\t\t<Phone1>".$phone_number_delivery."</Phone1>\n";
					$XML_content.="\t\t\t\t\t<Fax>".$order_user_info_delivery['fax']."</Fax>\n";
					$XML_content.="\t\t\t\t\t<MobilePhone>".$mobile_number_delivery."</MobilePhone>\n";
					$XML_content.="\t\t\t\t\t<E_Mail>".$user_email."</E_Mail>\n";
				$XML_content.="\t\t\t\t</row>\n";
			$XML_content.="\t\t\t</ContactEmployees>\n";

			$XML_content.="\t\t\t<BPAddresses>\n";
				
				//Ship-to
				$XML_content.="\t\t\t\t<row>\n";
					$XML_content.="\t\t\t\t\t<AddressType>bo_ShipTo</AddressType>\n";
					$XML_content.="\t\t\t\t\t<AddressName>".$adressnameST."</AddressName>\n";
					$XML_content.="\t\t\t\t\t<AddressName3>".$company_st."</AddressName3>\n"; //Ajout nom société
					$XML_content.="\t\t\t\t\t<Street>".trim($order_user_info_delivery['address_1'])."</Street>\n";
					$XML_content.="\t\t\t\t\t<Block>".$order_user_info_delivery['address_2']."</Block>\n";
					$XML_content.="\t\t\t\t\t<ZipCode>".$order_user_info_delivery['zip']."</ZipCode>\n";
					$XML_content.="\t\t\t\t\t<City>".$order_user_info_delivery['city']."</City>\n";
					$XML_content.="\t\t\t\t\t<State>".$state."</State>\n";
					$XML_content.="\t\t\t\t\t<BuildingFloorRoom>".$order_user_info_delivery['phone_1']."</BuildingFloorRoom>\n"; //AJOUT PHONE TESS
					$XML_content.="\t\t\t\t\t<Country>".$country_shipping['country_2_code']."</Country>\n";
				$XML_content.="\t\t\t\t</row>\n";
				
				//Bill-to
				$XML_content.="\t\t\t\t<row>\n";
					$XML_content.="\t\t\t\t\t<AddressType>bo_BillTo</AddressType>\n";
					$XML_content.="\t\t\t\t\t<AddressName>".$adressnameBT."</AddressName>\n";
					$XML_content.="\t\t\t\t\t<AddressName3>".$company_bt."</AddressName3>\n"; //Ajout nom société
					$XML_content.="\t\t\t\t\t<Street>".trim($order_user_info_data_BT['address_1'])."</Street>\n";
					$XML_content.="\t\t\t\t\t<Block>".$order_user_info_data_BT['address_2']."</Block>\n";
					$XML_content.="\t\t\t\t\t<ZipCode>".$order_user_info_data_BT['zip']."</ZipCode>\n";
					$XML_content.="\t\t\t\t\t<City>".$order_user_info_data_BT['city']."</City>\n";
					$XML_content.="\t\t\t\t\t<State>".$state."</State>\n";
					$XML_content.="\t\t\t\t\t<BuildingFloorRoom>".$order_user_info_data_BT['phone_1']."</BuildingFloorRoom>\n"; //AJOUT PHONE TESS
					$XML_content.="\t\t\t\t\t<Country>".$country_billing['country_2_code']."</Country>\n";
				$XML_content.="\t\t\t\t</row>\n";			
				
			$XML_content.="\t\t\t</BPAddresses>\n";

			$XML_content.="\t\t\t</BO>\n\t\t</BOM>\n\t</CLIENT>\n";
			/************************end of CLIENT*************************/
			/**************************COMMANDE****************************/
		 	$XML_content.="\t<COMMANDE>\n\t\t<BOM>\n\t\t\t<BO>\n";
			
				$XML_content.="\t\t\t<AdmInfo>\n";
					$XML_content.="\t\t\t\t<Object>17</Object>\n";
					$XML_content.="\t\t\t\t<Version>2</Version>\n";
				$XML_content.="\t\t\t</AdmInfo>\n";
				
				$XML_content.="\t\t\t<Documents>\n";
					$XML_content.="\t\t\t\t<row>\n";
						$XML_content.="\t\t\t\t\t<DocType>dDocument_Items</DocType>\n";
						$XML_content.="\t\t\t\t\t<DocDate>".date("Ymd", strtotime($order_data['created_on']))."</DocDate>\n";
						$XML_content.="\t\t\t\t\t<DocDueDate>".date("Ymd", strtotime($order_data['created_on']))."</DocDueDate>\n";
						$XML_content.="\t\t\t\t\t<CardCode>".$user_data_BT['user_code']."</CardCode>\n";
						$XML_content.="\t\t\t\t\t<NumAtCard>".$order_id."</NumAtCard>\n";
						$XML_content.="\t\t\t\t\t<ShipToCode>".trim($adressnameST)."</ShipToCode>\n";
						$XML_content.="\t\t\t\t\t<PayToCode>".$adressnameBT."</PayToCode>\n";
						$XML_content.="\t\t\t\t\t<U_OB1CODELANGUE>".$codelangue."</U_OB1CODELANGUE>\n";
						$XML_content.="\t\t\t\t\t<U_OB1TYPETOTAL>".$U_OB1TYPETOTAL."</U_OB1TYPETOTAL>\n";
						$XML_content.="\t\t\t\t\t<U_OB1REMARKPACK>Paiement : ".$payment_name." || Livraison : ".$shipping_name."</U_OB1REMARKPACK>\n";
						$XML_content.="\t\t\t\t\t<PaymentGroupCode>".$payment_octg."</PaymentGroupCode>\n";
						$XML_content.="\t\t\t\t\t<U_OB1TRANSP>".$shipping_octg."</U_OB1TRANSP>\n";
						/* if($order_data['order_payment'] != 0) {
							//REDUCTION SAP
							$XML_content.="\t\t\t\t\t<DocTotal>".round($order_data['order_total'],2)."</DocTotal>\n";
						} */
					$XML_content.="\t\t\t\t</row>\n";
				$XML_content.="\t\t\t</Documents>\n";
				
				$XML_content.="\t\t\t<Document_Lines>\n";
				/*******************************ORDER ITEM QUERY****************************/
				//Récupération des produits de la commande (X Lignes)
				$order_items_request = $db_sap->query('SELECT * 
										FROM h8q2p_virtuemart_order_items
										WHERE virtuemart_order_id = '.$order_id);
				while($order_items_data = $order_items_request->fetch()) {
					$XML_content.="\t\t\t\t<row>\n";
						$XML_content.="\t\t\t\t\t<ItemCode>".$order_items_data['order_item_sku']."</ItemCode>\n";
						$XML_content.="\t\t\t\t\t<Quantity>".$order_items_data['product_quantity']."</Quantity>\n";
						$XML_content.="\t\t\t\t\t<UnitPrice>".round($order_items_data['product_item_price'],2)."</UnitPrice>\n";
						if($discount_value > 0) {
							$XML_content.="\t\t\t\t\t<DiscountPercent>".$discount_value."</DiscountPercent>\n";
						}
					$XML_content.="\t\t\t\t</row>\n";
				}
				$XML_content.="\t\t\t</Document_Lines>\n";

				if($order_data['order_shipment'] > 0){
					$XML_content.="\t\t\t<DocumentsAdditionalExpenses>\n";
						$XML_content.="\t\t\t\t<row>\n";
							$XML_content.="\t\t\t\t\t<ExpenseCode>".$ExpenseCode."</ExpenseCode>\n";
							$XML_content.="\t\t\t\t\t<LineTotal>".round($order_data['order_shipment'],2)."</LineTotal>\n";
						$XML_content.="\t\t\t\t</row>\n";
					$XML_content.="\t\t\t</DocumentsAdditionalExpenses>\n";
				}

		$XML_content.="\t\t\t</BO>\n\t\t</BOM>\n\t</COMMANDE>\n</OB>\n"; 

		$XML_file = new SimpleXMLElement($XML_content);
		$XML_file->asXML(dirname(__FILE__).'/SAP/send/'.$order_id.'.xml');

	
	}

}
?>

