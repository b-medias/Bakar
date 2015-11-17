<?php
/**
* Bakar (http://www.bakar.be)
* @link			http://www.bakar.be
* @copyright	Copyright (c) 2005-2014 Bakar. (http://www.bakar.be)
* @version		3.0
*/
namespace Bakar\Service;

use Bakar\Service\AbstractService;
use Zend\I18n\Translator\Translator;

use ArrayObject;

class TextService extends AbstractService{
	protected $keywords;
	protected $placeholders;
	protected $titles;
	protected $descriptions;
	protected $messages;
	protected $globals;
	protected $texts;
	protected $labels;
	protected $icons;
	protected $compagny;
	protected $profil;
	protected $translator;

	public function setTranslator($translator = NULL){
		if($translator !== NULL){
			$this->translator	=	$translator;
		}
		return $this;
	}
	public function getTranslator(){
		if($this->translator === NULL){
			$translator	=	$this->getServiceManager()->get('translator');	
			$this->setTranslator($translator);
		}
		return $this->translator;
	}
	public function translate($text){
		if($text != NULL && $text != ''){
			$text	=	$this->getTranslator()->translate($text);
		}
		
		return $text;
	}
		
	public function setCompagny($compagny = NULL){
		if($compagny !== NULL){
			$this->compagny	=	$compagny;
		}
		return $this;
	}
	public function getCompagny(){
		if($this->compagny === NULL){
			$this->setCompagny($this->getArrayObject(array(
				'name'		=>	$this->get('COMPAGNY::NAME', FALSE),
				'logo'		=>	$this->get('COMPAGNY::LOGO', FALSE),
				'slogan'	=>	$this->get('COMPAGNY::SLOGAN', FALSE),
				'phone'		=>	$this->get('COMPAGNY::PHONE', FALSE),
				'fax'		=>	$this->get('COMPAGNY::FAX', FALSE),
				'email'		=>	$this->get('COMPAGNY::EMAIL', FALSE),
				'website'	=>	$this->get('COMPAGNY::WEBSITE', FALSE),
				'tva'		=>	$this->get('COMPAGNY::TVA', FALSE),
				'address'	=>	$this->get('COMPAGNY::STREET', FALSE).' '.$this->get('COMPAGNY::NUMBER', FALSE).' <br />'.
								$this->get('COMPAGNY::POSTALCODE', FALSE).' '.$this->get('COMPAGNY::CITY', FALSE).' '.$this->get('COMPAGNY::COUNTRY', FALSE),
			)));
		}
		return $this->compagny;
	}	
	
	public function setGlobals($globals = NULL){
		if($globals !== NULL){
			$this->globals	=	$globals;
		}
		return $this;
	}
	public function getGlobals(){
		if($this->globals === NULL){
			$this->setGlobals($this->generateGlobals());
		}
		return $this->globals;
	}
	public function setGlobal($index, $global){
		$this->getGlobals()->offsetSet($index, $global);
	}
	public function getGlobal($index){
		$global	=	'';
		if($this->getGlobals()->offsetExists($index)){
			$global	=	$this->getGlobals()->offsetGet($index);
		}
		return $global;
	}
	public function generateGlobals(){
		return	$this->getArrayObject(array(
			/**
			*	GLOABL
			*/
			'PROBLEMS_ENCOUNTERED'	=>	$this->translate('Si vous rencontrez des problèmes, n\'hésitez pas à nous contacter par téléphone ou via la rubrique \'Contact\' de notre site Internet'),
			'REDIRECT'				=>	$this->translate('Une redirection est en cours, veuillez patienter...'),
			'REFRESH'				=>	$this->translate('Actualisation de la page, veuillez patienter...'),
			'THANKU'				=>	$this->translate('Nous vous remercions de l\'intérêt que vous portez à notre site Internet'),
			'SORRY'					=>	$this->translate('Veuillez nous excuser, une erreur interne est survenue lors de l\'envoi des données vers le serveur'),
			'INFORM_TECHNICIANS'	=>	$this->translate('Le problème a été signalé aux techniciens qui s\'occuperont d\'analyser votre requête'),
			'CONTACT_YOU'			=>	$this->translate('Nous prendrons contact avec vous le cas échéant'),
			'RESPOND'				=>	$this->translate('Nous vous répondrons dans les plus brefs délais'),
			'SPAM'					=>	$this->translate('Veuillez vérifier dans vos courriers indésirables ou dans vos spams si le courrier n\'a pas été dévié'),
			'TOKEN'					=>	$this->translate('Ce lien restera actif pendant 24 heures, après quoi votre demande sera annulée et il faudra recommencer la procédure'),
		));
	}
	
	public function set($index, $text){
		$this->getTexts()->offsetSet($index, $text);
		return $this;
	}
	public function get($index, $decoration = TRUE){
		$text	=	NULL;

		if($this->getTexts()->offsetExists($index)){
			$text	=	$this->getTexts()->offsetGet($index);
			
			$text	=	$decoration	==	TRUE	?	'<p>'.$text.'</p>'	:	$text;
		}
		return $text;
	}
	public function setTexts(ArrayObject $texts = NULL){
		if($texts !== NULL){
			$this->translateexts	=	$texts;
		}
		return $this;
	}
	public function getTexts(){
		if($this->translateexts === NULL){
			$this->setTexts($this->generateTexts());
		}
		return $this->translateexts;
	}
	public function generateTexts(){
		return	$this->getArrayObject(array(
			'test'	=>	'Translator Test OK!',
			
			/**
			*	ADDRESS
			*/
			'Address/index/delete/notFound'					=>	$this->translate('Cette adresse n\'existe pas, n\'existe plus ou a déjà été supprimée'),
			'Address/index/delete/confirm'					=>	$this->translate('Etes-vous sûr de vouloir supprimer cette adresse?'),
			'Address/index/delete/success'					=>	$this->translate('L\'adresse a été supprimée avec succès'),
			'Address/notFound'								=>	$this->translate('Vous ne disposez d\'aucune adresse enregistrée'),
			'Address/index/add/success'						=>	$this->translate('L\'adresse a été ajoutée avec succès'),
			'Address/index/add/error'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Address/user/dontHave'							=>	$this->translate('Vous n\'avez pas d\'adresse enregistrée').'<br />'.
											 					$this->translate('Pour pouvoir effectuer vos commandes, vous devez en ajouter une'),
			
			/**
			*	ADVISE
			*/								 					
			'Advise/index/add/subject'						=>	$this->translate('Un avis a été émis sur le site Internet'),
			'Advise/index/add/success'						=>	$this->translate('Votre avis a bien été enregistré'),
			'Advise/index/add/error'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			
			/**
			*	APPLICATION
			*/
			
			/**
			*	BAKAR
			*/

			/**
			*	CACHE
			*/					
								
			/**
			*	CART
			*/
			'Cart/index/empty/confirm'						=>	$this->translate('Etes-vous sûr de vouloir vider votre panier?'),
			'Cart/index/add/error'							=>	$this->translate('Le produit portant la référence %s n\'existe pas ou n\'existe plus dans notre catalogue'),
			'Cart/index/add/success'						=>	$this->translate('Le produit %s a été ajouté %d X dans votre panier'),
			'Cart/index/delete/confirm'						=>	$this->translate('Etes-vous sûr de vouloir supprimer complètement le produit %s de votre panier?'),		
			'Cart/index/show/empty'							=>	$this->translate('Votre panier ne contient aucun produit'),
			'Cart/index/empty/success'						=>	$this->translate('Votre panier a été vidé avec succès'),
			'Cart/index/empty/error'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Cart/index/emtpy/info'							=>	$this->translate('Votre panier ne contient aucun produit'),
			'Cart/index/remove/error'						=>	$this->translate('Le produit n\'existe pas dans votre panier'),
			'Cart/index/remove/success'						=>	$this->translate('Le produit a été supprimé de votre panier avec succès'),
			'Cart/index/paid/info/address'					=>	$this->translate('Veuillez d\'abord enregistrer votre adresse avant de valider votre paiement'),
			'Cart/index/paid/shipping'						=>	$this->translate('Un email avec le récapitulatif de votre commande vient de vous être envoyé').'<br />'.
																$this->translate('Vos points, réductions, parrainages seront comptabilisés et ajoutés à votre compte une fois la commande livrée et payée').'<br />'.
																$this->getGlobal('THANKU'),
			'Cart/transport/info'							=>	$this->translate('La livraison est gratuite à partir d\'une commande totale de minimum 50&euro;').'<br />'.
										 						$this->translate('La livraison le jour même se fait uniquement pour les 19 communes de Bruxelles'),
			'Cart/discount/info'							=>	$this->translate('Le montant total avec les bons de réduction est de %s').'<br />'.
																$this->translate('Les bons de réduction sont utilisable uniquement si le montant total avec ceux-ci est égal ou supérieur à 30&euro;'),

			/**
			*	CATEGORY
			*/
			
			/**
			*	CONTACT
			*/
			'Contact/index/send/success'					=>	$this->translate('Votre message a été envoyé avec succès'),			
			'Contact/index/send/subject'					=>	$this->translate('Message de %s'),
			'Contact/index/send/error'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			
			/**
			*	TICKET 
			*/ 
			'Ticket/index/dontHave'							=>	$this->translate('Vous ne disposez d\'aucun bon de réduction').'<br />'.
																$this->translate('Utilisez les différentes techniques proposées par Be-Pharma pour en avoir'),
			'Ticket/index/howToUse'							=>	$this->translate('Pour utiliser vos bons de réduction, vous devez les cocher dans votre panier').'<br />'.
																$this->translate('Ils seront alors utilisés pour la commande en cours'),
			'Ticket/proposer/description'					=>	$this->translate('Bon de réduction offert grâce à votre parrainage de %s'),
			
			/**
			*	DISCOUNT
			*/
			'Discount/index/add/global/success'				=>	$this->translate('La réduction a été ajoutée avec succès'),
			'Discount/index/add/global/error'				=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Discount/index/add/ticket/success'				=>	$this->translate('Le ticket de réduction a été ajouté avec succès'),
			'Discount/index/add/ticket/error'				=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Discount/use/ticket/notFound'					=>	$this->translate('Ce ticket n\'existe pas, n\'existe plus ou a déjà été utilisé'),
			'Discount/use/token/notFound'					=>	$this->translate('Ce token n\'existe pas'),
			'Discount/use/ticket/success'					=>	$this->translate('Votre ticket de réduction a été utilisé avec succès'),
			'Discount/code/info'							=>	$this->translate('Si vous avez un code promo').'<br />'.
																$this->translate('Saisissez-le ici'),
			
			
			/**
			*	EMAIL
			*/
			'Email/show/token/notExists'					=>	$this->translate('Cet email n\'existe pas ou n\'existe plus'),
			'Email/index/show/notFound'						=>	$this->translate('Cette visualisation n\'existe pas ou n\'existe plus'),
			'Email/show/key/notValid'						=>	$this->translate('La clé de décryptage n\'est pas valide'),
			'Email/show/receiver/notValid'					=>	$this->translate('L\'adresse email ne correspond pas à l\'adresse email du destinataire du message'),
			'Email/show/success'							=>	$this->translate('Visualisation de l\'email avec succès'),
			'Email/index/send/subject'						=>	$this->translate('Vous avez un nouveau message'),

			/**
			*	GARDE
			*/

			/**
			*	LOG
			*/
						
			/**
			*	MAGAZINE
			*/
			'Magazine/index/rubriques/byEsante'				=>	$this->translate('Ce flux rss est fourni par le site www.e-sante.be').'<br />'.
																$this->translate('Be-Pharma n\'est pas responsable de son contenu et chaque lien présent, vous fera quitter le site Be-Pharma'),
			'Magazine/health'								=>	$this->translate('Santé pratique'),
			'Magazine/diseases'								=>	$this->translate('Maladies'),
			'Magazine/pharmaceuticals'						=>	$this->translate('Médicaments'),
			'Magazine/wellness'								=>	$this->translate('Bien-être'),
			'Magazine/ditetic'								=>	$this->translate('Diététique'),
			'Magazine/recettes'								=>	$this->translate('Recettes'),
			'Magazine/beauty'								=>	$this->translate('Beauté'),
			'Magazine/mom'									=>	$this->translate('Maman'),
			
			/**
			*	NEWSLETTER
			*/
			'Newsletter/index/unsubscribe/confirm/notFound'	=>	$this->translate('Cette confirmation de désinscription de la newsletter est périmée, n\'existe pas ou a déjà été validée'),
			'Newsletter/index/unsubscribe/confirm/error'	=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Newsletter/index/unsubscribe/confirm/success'	=>	$this->translate('Votre adresse email a été supprimée avec succès de notre newsletter').'<br />'.
																$this->translate('Vous ne recevrez plus d\'emails promotionnels de notre site Internet'),
			'Newsletter/index/unsubscribe/subject'			=>	$this->translate('Validation de votre désinscription à notre newsletter'),
			'Newsletter/index/unsubscribe/exists'			=>	$this->translate('L\'adresse email %s est déjà inscrite dans notre système pour la désinscription à la newsletter').'<br />'.
																$this->translate('Le mail de confirmation vient de vous être à nouveau envoyé'),
			'Newsletter/index/unsubscribe/member/notFound'	=>	$this->translate('L\'adresse email %s ne se trouve pas dans notre newsletter').'<br />'.
																$this->translate('Veuillez la vérifier et essayer à nouveau'),
			'Newsletter/index/unsubscribe/error'			=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Newsletter/index/unsubscribe/success'			=>	$this->translate('Un email avec un lien de validation vient de vous être envoyé par mail').'<br />'.
																$this->translate('Veuillez valider ce lien pour vous désinscrire complètement de notre newsletter'),
																
			'Newsletter/index/subscribe/confirm/notFound'	=>	$this->translate('Cette confirmation d\'inscription à la newsletter est périmée, n\'existe pas ou a déjà été validée'),
			'Newsletter/index/subscribe/confirm/error'		=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Newsletter/index/subscribe/confirm/success'	=>	$this->translate('Votre inscription à notre newsletter a été validée avec succès').'<br />'. 
																$this->translate('Vous recevrez dès à présent de nombreux bons de réduction et informations concernant notre site'),
			'Newsletter/index/subscribe/subject'			=>	$this->translate('Validation de votre inscription à la newsletter de Be-Pharma'),
			'Newsletter/index/subscribe/exists'				=>	$this->translate('L\'adresse email %s est déjà inscrite dans notre système pour la newsletter').'<br />'.
																$this->translate('Un nouveau mail vient de vous être envoyé'),
			'Newsletter/index/subscribe/error'				=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Newsletter/index/subscribe/success'			=>	$this->translate('Votre adresse email a bien été enregistré').'<br />'.
																$this->translate('Nous vous avons envoyé un email de confirmation que vous devez valider').'<br />'.
																$this->translate('Après confirmation, nous vous enverrons des réductions, des promotions et les nouveautés Be-Pharma'),
			
			/**
			*	ORDER
			*/
			'Order/user/dontHave'							=>	$this->translate('Vous ne disposez d\'aucune commande archivée').'<br />'.
						 					 					$this->translate('Les commandes que vous effectuez sur Be-Pharma sont automatiquement archivées pour vous'),
			'Order/index/add/error'							=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Order/index/add/success'						=>	$this->translate('La commande a été enregistrée avec succès'),
			'Order/index/delete/notFound'					=>	$this->translate('Cette commande n\'existe pas ou n\'existe plus'),
			'Order/index/delete/confirm'					=>	$this->translate('Etes-vous sûr de vouloir supprimer cette commade de vos archives?'),
			'Order/index/delete/success'					=>	$this->translate('La commande a été supprimée avec succès de vos archives'),
			'Order/index/paid/success'						=>	$this->translate('Le paiement a été effectué avec succès'),
			'Order/index/paid/error'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Order/index/paid/cancel'						=>	$this->translate('La transaction a été annulée'),
			'Order/index/ticket/notFound'					=>	$this->translate('Le ticket de caisse n\'existe pas, n\'existe plus ou a été supprimé'),
			'Order/index/ticket/found'						=>	$this->translate('Le ticket de caisse a été trouvé'),
			'Order/index/show/notFound'						=>	$this->translate('La commande n\'existe pas, n\'existe plus ou a été supprimée'),
			'Order/user/ticket/dontHave'					=>	$this->translate('Vous ne disposez d\'aucun ticket').'<br />'.
																$this->translate('Vos tickets sont automatiquement sauvegardés lors de vos commandes'),
			'Order/compare/notFound'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Order/compare/notEqual'						=>	$this->translate('Une erreur est survenue, la commande a bien été validée sur le serveur PayPal mais elle ne correspond pas à la commande faite sur le site').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Order/compare/success'							=>	$this->translate('La commande a été effectuée avec succès'),
			'Order/subject/signal'							=>	$this->translate('Une commande a été annulée'),
			'Order/cancel/notFound'							=>	$this->translate('Erreur, cette commande n\'existe pas ou n\'existe plus'),
			'Order/index/paid/cancel'						=>	$this->translate('La commande a été annulée, aucune opération n\'a été enregistrée').'<br />'.
																$this->getGlobal('PROBLEMS_ENCOUNTERED').'<br />'.
																$this->getGlobal('THANKU').'<br />',
			'Order/signal/subject'							=>	$this->translate('Une commande a été annulée ou un problème a été rencontré'),
			'Order/success/subject'							=>	$this->translate('Récapitulatif de votre commande'),
	
			/**
			*	PAYPAL
			*/
			'Paypal/redirect'								=>	$this->translate('Veuillez patienter, nous vous redirigeons vers le serveur PayPal'),
			'Paypal/paid/error'								=>	$this->translate('Un problème est survenu au moment du paiement').'<br />'.
																$this->translate('Vous avez annulé le paiement ou un problème dû à PayPal est survenu').'<br />'.
																$this->translate('Le traitement de la commande n\'a pas été effectué').'<br />'.
																$this->translate('Pour plus d\'informations, vous pouvez nous joindre via la rubrique Contact ou par téléphone'),
			'Paypal/paid/success'							=>	$this->translate('Le paiement a été effectué avec succès').'<br />'.
																$this->translate('Un email avec le récapitulatif de votre commande vient de vous être envoyé').'<br />'.
																$this->translate('Vos points, réductions, parrainages ont été comptabilisés et ajoutés à votre compte').'<br />'.
																$this->getGlobal('THANKU'),
						
			/**
			*	PDF
			*/
			
			/**
			*	POINT
			*/
			'Point/index/convert/automatique'				=>	$this->translate('Vos points sont convertis automatiquement en bon(s) de réduction'),	
			'Point/user/dontHave'							=>	$this->translate('Vous ne disposez pas de points pour l\'instant').'<br />'.
						 										$this->translate('Pour en avoir, il faut passer des commandes de minimum 50&euro;'),			
			'Point/index/add/null'							=>	$this->translate('Aucun point ajouté'),
			'Point/index/add/error'							=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Point/index/add/success'						=>	$this->translate('Vos points ont été ajouté avec succès'),
			'Point/convert/description'						=>	$this->translate('Bon(s) de réduction offert grâce à l\'accumulation de points de fidélité'),
			'Point/convert/rest'							=>	$this->translate('Reste des points après conversion en bon(s) de réduction'),
			'Point/email/inform/subject'					=>	$this->translate('Bon(s) de réduction offert grâce à l\'accumulation de points de fidélité sur Be-Pharma'),
			
			/**
			*	PRODUCT
			*/
			'Product/notFound'								=>	$this->translate('Le produit portant la référence %s n\'existe pas ou n\'existe plus'),
			'Product/Index/reference/notFound'				=>	$this->translate('Aucun résultat pour la référence %s').'<br />'.
																$this->translate('La référence a été envoyée à notre service. Si un produit correspond, notre service prendra contact avec vous dans la mesure du possible').'<br />'.
																$this->translate('N\'oubliez pas de vous inscrire pour qu\'on puisse vous recontacter'),											
			/**
			*	PROPOSER
			*/
			'Proposer/user/dontHave'						=>	$this->translate('Vous ne disposez d\'aucun parrainage').'<br />'.
																$this->translate('Invitez votre entourage à s\'inscrire sur Be-Pharma et devenez leur parrain').'<br />'.
																$this->translate('Vous recevrez ensuite 5&euro; de réduction pour la première commande de plus de 50&euro; qu\'ils passeront'),
			'Proposer/user/receive/discount'				=>	$this->translate('Réduction de %s reçue le'),
			'Proposer/user/dontReceive/discount'			=>	$this->translate('Pas encore de réduction pour ce parrainage'),
			'Proposer/index/accomplish/error'				=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Proposer/index/accomplish/success'				=>	$this->translate('Votre parrain vient d\'avoir une réduction grâce à votre achat'),														
			'Proposer/index/add/success/subject'			=>	$this->translate('Vous êtes devenu parrain de %s'),
			'Proposer/index/add/success'					=>	$this->translate('Votre parrain a été averti avec succès'),
			'Proposer/index/add/error'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Proposer/index/accomplish/subject'				=>	$this->translate('Bon de réduction offert grâce à votre parrainage de %s'),	
			

			/**
			*	SEARCH
			*/
			'Search/notFound'								=>	$this->translate('Aucun résultat pour la recherche %s'),
			'Search/notFound/signal'						=>	$this->translate('Aucun résultat pour la recherche %s').'<br />'.
																$this->translate('La demande de recherche a été envoyée à notre service.').
																$this->translate('Si un produit correspond, notre service prendra contact avec vous dans la mesure du possible').'<br />'.
																$this->translate('N\'oubliez pas de vous inscrire pour qu\'on puisse vous recontacter'),
			'Search/signal/subject'							=>	$this->translate('Une recherche infructueuse a été effectuée'),
			
			/**
			*	SHARE
			*/
			'Share/index/share/notSharable'					=>	$this->translate('Ce type de partage n\'est pas permis'),
			'Share/index/share/notExists'					=>	$this->translate('Ce type de partage n\'existe pas ou vous ne disposez pas des autorisations nécessaires pour le partager'),
			'Share/index/share/success'						=>	$this->translate('Le lien de partage a été envoyé avec succès'),
			'Share/index/share/subject/wishlist'			=>	$this->translate('%s a partagé une liste de souhaits avec vous'),
			'Share/index/share/subject/product'				=>	$this->translate('%s a partagé un produit avec vous'),
			'Share/index/share/subject/cart'				=>	$this->translate('%s a partagé un panier avec vous'),
			'Share/index/share/error'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			
			/**
			*	STATISTIQUES
			*/
			
			/**
			*	SUBSCRIBE
			*/
			'Subscribe/success'								=>	$this->translate('Votre demande d\'inscription a été enregistrée avec succès'),
			'Subscribe/error'								=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Subscribe/uses/error'							=>	$this->translate('Ce ticket de confirmation d\'inscription n\'existe pas, n\'existe plus, a expiré ou a déjà été validé'),
			'Subscribe/uses/success'						=>	$this->translate('Votre inscription a été validée avec succès'),

			/**
			*	UNSUBSCRIBE
			*/
			'Unsubscribe/success'							=>	$this->translate('Votre demande de désinscription a été enregistrée avec succès'),
			'Unsubscribe/error'								=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Unsubscribe/uses/error'						=>	$this->translate('Ce ticket de confirmation de désinscription n\'existe pas, n\'existe plus, a expiré ou a déjà été validé'),
			'Unsubscribe/uses/success'						=>	$this->translate('Votre désinscription a été validée avec succès'),				
			
			/**
			*	URGENCY 
			*/

			/**
			*	USER 
			*/ 
			'User/index/login/error'						=>	$this->translate('L\'adresse email ou le mot de passe est incorrect'),
			'User/index/login/success'						=>	$this->translate('Connexion effectuée avec succès'),
			'User/index/logout/confirm'						=>	$this->translate('Etes-vous sûr de vouloir vous déconnecter ?'),
			'User/index/logout/success'						=>	$this->translate('Déconnexion effectuée avec succès'),
			'User/index/signin/success'						=>	$this->translate('Votre inscription a été réalisée avec succès').'<br />'.
																$this->translate('Un mail de confirmation vient de vous être envoyé').'<br />'.
																$this->translate('Veuillez cliquer sur le lien qui se trouve dans le mail pour valider votre inscription'),
			'User/index/signin/exists'						=>	$this->translate('Un ticket d\'inscription existe déjà pour l\'adresse email %s').'<br />'.
																$this->translate('Un nouveau mail de validation vient de vous être envoyé à la même adresse'),
			'User/index/signin/subject'						=>	$this->translate('Validation de votre inscription sur Be-Pharma'),													
			'User/index/signin/confirm/notFound'			=>	$this->translate('Erreur, cette validation d\'inscription est périmée, n\'existe pas ou a déjà été validée').'<br />'.
																$this->translate('Veuillez recommencer votre inscription. Merci'),
			'User/index/signin/error'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'User/index/signin/confirm/error'				=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'User/index/signin/confirm/success'				=>	$this->translate('Votre inscription a été validée avec succès. Vous pouvez dès à présent vous connecter et profiter pleinement de nos services').'<br />'.
																$this->translate('Nous vous souhaitons un agréable moment sur notre site Internet'),
			'User/index/edit/success'						=>	$this->translate('Votre profil a été modifié avec succès').'<br />'.
																$this->getGlobal('REFRESH'),
			'User/index/edit/email/success'					=>	$this->translate('Veuillez noter que vous avez modifié votre adresse email').'<br />'.
																$this->translate('Un mail de confirmation vient de vous être envoyé').'<br />'.
																$this->translate('Veuillez cliquer sur le lien qui se trouve dans le mail pour valider ce changement'),			
			'User/index/edit/confirm/success'				=>	$this->translate('Votre profil a été modifié avec succès').'<br />'.
																$this->translate('Vous recevrez maintenant les notifications et autres informations sur votre nouvelle adresse email').'<br />'.
																$this->translate('Vous devez également vous connecter en utilisant votre nouvelle adresse email'),			
			'User/index/edit/email/exists'					=>	$this->translate('Un ticket pour le changement d\'adresse email existe déjà pour l\'adresse email "%s"').'<br />'.
																$this->translate('Un nouveau mail de validation vient de vous être envoyé à la même adresse'),
			'User/index/edit/email/subject'					=>	$this->translate('Validation du changement de votre adresse email'),
			'User/index/edit/confirm/error'					=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),															
			'User/index/edit/email/error'					=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'User/index/edit/error'							=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'User/index/edit/unvalided'						=>	$this->translate('Un utilisateur a validé son inscription ou son changement d\'adresse email avant votre confirmation').'<br />'.
																$this->translate('L\'adresse email correspond à celle que vous voulez utiliser').'<br />'.
																$this->translate('Veuillez modifier à nouveau votre profil ou prendre contact pour de plus amples informations'),
			'User/index/signin/error'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'User/index/signout/confirm/notFound'			=>	$this->translate('Cette demande de désinscription est périmée, n\'existe pas ou n\'existe plus').'<br />'.
																$this->translate('Veuillez recommencer la procédure de désinscription depuis le début'),
			'User/index/signout/confirm/error'				=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'User/index/signout/confirm/success'			=>	$this->translate('Votre désinscription a été confirmée avec succès'),
																$this->translate('Nous espérons vous revoir très prochainement sur notre site Internet').'<br />'.
																$this->translate('Nous restons à votre disposition pour pouvoir améliorer nos services et vous satisfaire davantage').'<br />'.
																$this->translate('Sachez que vous êtes toujours le bienvenu chez Be-Pharma'),
			'User/index/signout/subject'					=>	$this->translate('Validation de votre désinscription de Be-Pharma'),
			'User/index/signout/exists'						=>	$this->translate('Une demande de désinscription existe déjà pour le compte %s').'<br />'.
																$this->translate('Un mail vient à nouveau de vous être envoyé'),
			'User/index/signout/error'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'User/index/signout/success'					=>	$this->translate('Votre demande de désinscription a été enregistrée avec succès').'<br />'.
																$this->translate('Nous vous avons envoyé un mail pour valider votre désinscription').'<br />'.
																$this->translate('Veuillez cliquer sur le lien présent dans le mail pour valider votre désinscription'),

			'User/password/edit/success'					=>	$this->translate('Votre demande de changement de mot de passe a été enregistrée avec succès').'<br />'.
																$this->translate('Un email avec un lien de validation vient de vous être envoyé'),
			'User/password/edit/error'						=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'User/password/edit/exists'						=>	$this->translate('Un ticket pour le changement de mot de passe existe déjà pour l\'adresse email %s').'<br />'.
																$this->translate('Un nouveau mail de validation vient de vous être envoyé à la même adresse'),
			'User/password/edit/subject'					=>	$this->translate('Validation du changement de votre mot de passe'),
			'User/password/edit/confirm/success'			=>	$this->translate('Votre mot de passe a été modifié avec succès').'<br />'.
																$this->translate('Veuillez vous reconnecter avec vos nouveaux identifiants'),
			'User/password/edit/confirm/error'				=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),	
			'User/password/lost/subject'					=>	$this->translate('Récupération de votre mot de passe'),
			'User/password/lost/exists'						=>	$this->translate('Un ticket pour la récupération de votre mot de passe existe déjà pour l\'adresse email %s').'<br />'.
																$this->translate('Un nouveau mail de récupération vient de vous être envoyé à la même adresse'),
			'User/password/lost/success'					=>	$this->translate('Votre demande de récupération a été enregistrée avec succès').'<br />'.
																$this->translate('Un email avec la procédure à suivre vient de vous être envoyé'),
			'User/password/change/token/notFound'			=>	$this->translate('Ce ticket de récupération de mot de passe n\'existe pas, n\'existe plus ou a déjà été utilisé').'<br />'.
																$this->translate('Veuillez recommencer la procédure'),
			'User/password/change/key/notEqual'				=>	$this->translate('La clé de décryptage ne correspond pas à celle que vous avez reçu par email'),
			'User/password/change/email/notEqual'			=>	$this->translate('L\'adresse email ne correspond pas à l\'adresse email de la demande du ticket'),
			'User/password/change/success'					=>	$this->translate('Votre mot de passe a été modifié avec succès'),												
			
			/**
			*	WISHLIST
			*/
			'Wishlist/index/show/notFound'					=>	$this->translate('Cette liste de souhaits n\'existe pas ou n\'existe plus'),
			'Wishlist/index/unconvert/notFound'				=>	$this->translate('Cette liste de souhaits n\'existe pas ou n\'existe plus'),
			'Wishlist/index/unconvert/confirm'				=>	$this->translate('Etes-vous sûr de vouloir ajouter tous les produits de cette liste de souhaits à votre panier ?'),
			'Wishlist/index/unconvert/success'				=>	$this->translate('Votre liste de souhaits a été ajoutée à votre panier avec succès'),											
			'Wishlist/index/convert/confirm'				=>	$this->translate('Etes-vous sûr de vouloir convertir votre panier en liste de souhaits ?'),
			'Wishlist/index/convert/success'				=>	$this->translate('Votre panier a été converti en liste de souhaits avec succès').'<br />'.
																$this->translate('Vous pouvez à tout moment transférer tout ou une partie des produits de votre liste de souhaits vers votre panier').'<br />'.
																$this->translate('Vous pouvez également partager votre liste de souhaits avec vos contacts').'<br />'.
																$this->translate('Pour gérer vos listes de souhaits, rendez-vous sur votre compte'),
			'Wishlist/index/convert/error'					=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Wishlist/index/plus/notFound'					=>	$this->translate('Le produit portant la référence %s n\'existe pas ou n\'existe plus'),
			'Wishlist/index/plus/item/notFound'				=>	$this->translate('Le produit portant la référence %s n\'existe pas ou n\'existe plus dans votre panier'),
			'Wishlist/index/plus/success'					=>	$this->translate('Le produit portant la référence %s a été augmenté d\'une unité'),
			'Wishlist/index/min/notFound'					=>	$this->translate('Le produit portant la référence %s n\'existe pas ou n\'existe plus'),
			'Wishlist/index/min/item/notFound'				=>	$this->translate('Le produit portant la référence %s n\'existe pas ou n\'existe plus dans votre panier'),
			'Wishlist/index/min/success'					=>	$this->translate('Le produit portant la référence %s a été diminué d\'une unité'),
			'Wishlist/index/replace/notFound'				=>	$this->translate('Le produit portant la référence %s n\'existe pas ou n\'existe plus'),
			'Wishlist/index/replace/item/notFound'			=>	$this->translate('Le produit portant la référence %s n\'existe pas ou n\'existe plus dans votre panier'),
			'Wishlist/index/replace/success'				=>	$this->translate('La quantité du produit portant la référence %s a été modifiée avec succès'),
			'Wishlist/index/remove/notFound'				=>	$this->translate('Le produit portant la référence %s n\'existe pas ou n\'existe plus'),
			'Wishlist/index/remove/confirm'					=>	$this->translate('Etes-vous sûr de vouloir supprimer le produit %s de votre liste de souhaits %s ?'),
			'Wishlist/index/remove/item/notFound'			=>	$this->translate('Le produit portant la référence %s n\'existe pas ou n\'existe plus dans votre panier'),
			'Wishlist/index/remove/success'					=>	$this->translate('Le produit a été supprimé avec succès de votre liste de souhaits'),
			'Wishlist/index/delete/notFound'				=>	$this->translate('Cette liste de souhaits n\'existe pas, n\'existe plus ou a déjà été supprimée'),
			'Wishlist/index/delete/confirm'					=>	$this->translate('Etes-vous sûr de vouloir supprimer votre liste de souhaits %s ?'),
			'Wishlist/index/delete/success'					=>	$this->translate('Votre liste de souhaits a été supprimée avec succès'),
			'Wishlist/index/empty/confirm'					=>	$this->translate('Etes-vous sûr de vouloir vider votre liste de souhaits?'),
			'Wishlist/index/empty/notFound'					=>	$this->translate('Cette liste de souhaits n\'existe pas, n\'existe plus ou a déjà été supprimée'),
			'Wishlist/index/empty/success'					=>	$this->translate('Votre liste de souhaits a été vidée avec succès'),
			'Wishlist/index/empty/error'					=>	$this->getGlobal('SORRY').'<br />'.
																$this->getGlobal('INFORM_TECHNICIANS').'<br />'.
																$this->getGlobal('RESPOND').'<br />'.
																$this->getGlobal('THANKU'),
			'Wishlist/user/dontHave'						=>	$this->translate('Vous ne disposez d\'aucune liste de souhaits').'<br />'.
																$this->translate('Pour en créer une, il suffit de cliquer sur le bouton %s présent dans l\'affichage de votre panier'),
			'Wishlist/user/inWishlist'						=>	$this->translate('Vous êtes dans votre liste de souhaits, ces produits ne se trouvent pas dans votre panier').'<br />'.
					 											$this->translate('Pour les ajouter à votre panier, cliquez sur le boutton %s'), 
			
			/**
			*	REDIRECTOR
			*/
			'Redirect/infos'								=>	$this->translate('Une erreur Internet s\'est produite').'<br />'.
																$this->getGlobal('REDIRECT'),
			
			/**
			*	OPTIONS 
			*/ 
			'Options/gender/male'							=>	$this->translate('Masculin'),
			'Options/gender/female'							=>	$this->translate('Féminin'),
			'Options/gender/other'							=>	$this->translate('Autre'),

			/**
			*	URI
			*	self::INVALID => "Invalid type given. String expected",
			*	self::NOT_URI => "The input does not appear to be a valid Uri",
			*/
			'URI::INVALID'									=>	$this->translate('Invalid type given. String expected'),
			'URI::NOT_URI'									=>	$this->translate('The input does not appear to be a valid Uri'),
			
			/**
			*	STRINGLENGTH
			*	self::INVALID   => "Invalid type given. String expected",
			*	self::TOO_SHORT => "The input is less than %min% characters long",
			*	self::TOO_LONG  => "The input is more than %max% characters long",
			*/
			'STRINGLENGTH::INVALID'							=>	$this->translate('La valeur entrée n\'est pas un type valide (chaînes de caractères attendues)'),
			'STRINGLENGTH::TOO_SHORT'						=>	$this->translate('La valeur entrée est trop courte').'<br />',
																$this->translate('Minimum %min% caractères'),
			'STRINGLENGTH::TOO_LONG'						=>	$this->translate('La valeur entrée est trop longue').'<br />'.
																$this->translate('Maximum %max% caractères').'<br />',
			
			/**
			*	STEP
			*	self::INVALID => "Invalid value given. Scalar expected",
			*	self::NOT_STEP => "The input is not a valid step"
			*/			
			'STEP::INVALID'									=>	$this->translate('Invalid value given. Scalar expected'),
			'STEP::NOT_STEP'								=>	$this->translate('The input is not a valid step'),
			
			/**
			*	REGEX
			*	self::INVALID   => "Invalid type given. String, integer or float expected",
			*	self::NOT_MATCH => "The input does not match against pattern '%pattern%'",
			*	self::ERROROUS  => "There was an internal error while using the pattern '%pattern%'",
			*/
			'REGEX::INVALID'								=>	$this->translate('Invalid type given. String, integer or float expected'),
			'REGEX::NOT_MATCH'								=>	$this->translate('The input does not match against pattern "%pattern%"'),
			'REGEX::ERROROUS'								=>	$this->translate('There was an internal error while using the pattern "%pattern%"'),
			
			/**
			*	NOTEMPTY
			*	self::IS_EMPTY => "Value is required and can't be empty",
			*	self::INVALID  => "Invalid type given. String, integer, float, boolean or array expected",
			*/			
			'NOTEMPTY::IS_EMPTY'							=>	$this->translate('La valeur du champ ne peut pas être vide'),
			'NOTEMPTY::INVALID'								=>	$this->translate('Invalid type given. String, integer, float, boolean or array expected'),
						
			/**				
			*	LESSTHAN
			*	self::NOT_LESS           => "The input is not less than '%max%'",
			*	self::NOT_LESS_INCLUSIVE => "The input is not less or equal than '%max%'"
			*/
			'LESSTHAN::NOT_LESS'							=>	$this->translate('The input is not less than "%max%"'),
			'LESSTHAN::NOT_LESS_INCLUSIVE'					=>	$this->translate('The input is not less or equal than "%max%"'),
						
			/**
			*	ISINSTANCEOF
			*	self::NOT_INSTANCE_OF => "The input is not an instance of '%className%'",
			*/
			'ISINSTANCEOF::NOT_INSTANCE_OF'					=>	$this->translate('The input is not an instance of "%className%"'),
						
			/**
			*	ISBN
			*	self::INVALID => "Invalid type given. String or integer expected",
			*	self::NO_ISBN => "The input is not a valid ISBN number",
			*/
			'ISBN::INVALID'									=>	$this->translate('Invalid type given. String or integer expected'),
			'ISBN::NO_ISBN'									=>	$this->translate('The input is not a valid ISBN number'),
						
			/**			
			*	IP
			*	self::INVALID        => 'Invalid type given. String expected',
			*	self::NOT_IP_ADDRESS => "The input does not appear to be a valid IP address",
			*/
			'IP::INVALID'									=>	$this->translate('Invalid type given. String expected'),
			'IP::NOT_IP_ADDRESS'							=>	$this->translate('The input does not appear to be a valid IP address'),
			
			/**
			*	IN ARRAY
			*	self::NOT_IN_ARRAY => 'The input was not found in the haystack',
			*/
			'IN_ARRAY::NOT_IN_ARRAY'						=>	$this->translate('The input was not found in the haystack'),
			
			/**
			*	IDENTICAL
			*	self::NOT_SAME      => "The two given tokens do not match",
			*	self::MISSING_TOKEN => 'No token was provided to match against',
			*/
			'IDENTICAL::NOT_SAME'							=>	$this->translate('Les valeurs doivent être strictement identiques'),
			'IDENTICAL::MISSING_TOKEN'						=>	$this->translate('No token was provided to match against'),
			
			/**			
			*	IBAN
			*	self::NOTSUPPORTED     => "Unknown country within the IBAN",
			*	self::SEPANOTSUPPORTED => "Countries outside the Single Euro Payments Area (SEPA) are not supported",
			*	self::FALSEFORMAT      => "The input has a false IBAN format",
			*	self::CHECKFAILED      => "The input has failed the IBAN check",	
			*/
			'IBAN::NOTSUPPORTED'							=>	$this->translate('Unknown country within the IBAN'),
			'IBAN::SEPANOTSUPPORTED'						=>	$this->translate('Countries outside the Single Euro Payments Area (SEPA) are not supported'),
			'IBAN::FALSEFORMAT'								=>	$this->translate('The input has a false IBAN format'),
			'IBAN::CHECKFAILED'								=>	$this->translate('The input has failed the IBAN check'),
			
			/**
			*	HOSTNAME
			*	self::CANNOT_DECODE_PUNYCODE  => "The input appears to be a DNS hostname but the given punycode notation cannot be decoded",
			*	self::INVALID                 => "Invalid type given. String expected",
			*	self::INVALID_DASH            => "The input appears to be a DNS hostname but contains a dash in an invalid position",
			*	self::INVALID_HOSTNAME        => "The input does not match the expected structure for a DNS hostname",
			*	self::INVALID_HOSTNAME_SCHEMA => "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'",
			*	self::INVALID_LOCAL_NAME      => "The input does not appear to be a valid local network name",
			*	self::INVALID_URI             => "The input does not appear to be a valid URI hostname",
			*	self::IP_ADDRESS_NOT_ALLOWED  => "The input appears to be an IP address, but IP addresses are not allowed",
			*	self::LOCAL_NAME_NOT_ALLOWED  => "The input appears to be a local network name but local network names are not allowed",
			*	self::UNDECIPHERABLE_TLD      => "The input appears to be a DNS hostname but cannot extract TLD part",
			*	self::UNKNOWN_TLD             => "The input appears to be a DNS hostname but cannot match TLD against known list",
			*/
			'HOSTNAME::CANNOT_DECODE_PUNYCODE'  			=>	$this->translate('The input appears to be a DNS hostname but the given punycode notation cannot be decoded'),
			'HOSTNAME::INVALID'                 			=> 	$this->translate('Invalid type given. String expected'),
			'HOSTNAME::INVALID_DASH'            			=> 	$this->translate('The input appears to be a DNS hostname but contains a dash in an invalid position'),
			'HOSTNAME::INVALID_HOSTNAME'        			=> 	$this->translate('The input does not match the expected structure for a DNS hostname'),
			'HOSTNAME::INVALID_HOSTNAME_SCHEMA' 			=> 	$this->translate('The input appears to be a DNS hostname but cannot match against hostname schema for TLD "%tld%"'),
			'HOSTNAME::INVALID_LOCAL_NAME'      			=>	$this->translate('The input does not appear to be a valid local network name'),
			'HOSTNAME::INVALID_URI'             			=>	$this->translate('The input does not appear to be a valid URI hostname'),
			'HOSTNAME::IP_ADDRESS_NOT_ALLOWED'  			=>	$this->translate('The input appears to be an IP address, but IP addresses are not allowed'),
			'HOSTNAME::LOCAL_NAME_NOT_ALLOWED'  			=>	$this->translate('L\'entrée semble être un nom de réseau local').'<br />'.
																$this->translate('Les noms de réseau local ne sont pas autorisés'),
			'HOSTNAME::UNDECIPHERABLE_TLD'      			=>	$this->translate('The input appears to be a DNS hostname but cannot extract TLD part'),
			'HOSTNAME::UNKNOWN_TLD'             			=>	$this->translate('L\'entrée semble être un nom d\'hôte DNS non valide'),
			
			/**
			*	HEX
			*	self::INVALID => "Invalid type given. String expected",
			*	self::NOT_HEX => "The input contains non-hexadecimal characters",
			*/
			'HEX::INVALID'             						=>	$this->translate('Invalid type given. String expected"'),
			'HEX::NOT_HEX'             						=>	$this->translate('The input contains non-hexadecimal characters'),			

			/**
			*	GREATERTHAN
			*	self::NOT_GREATER => "The input is not greater than '%min%'",
			*	self::NOT_GREATER_INCLUSIVE => "The input is not greater or equal than '%min%'"
			*/
			'GREATERTHAN::NOT_GREATER'          			=>	$this->translate('The input is not greater than "%min%"'),
			'GREATERTHAN::NOT_GREATER_INCLUSIVE'           	=>	$this->translate('The input is not greater or equal than "%min%"'),
			
			/**
			*	EXPLODE
			*	self::INVALID => "Invalid type given",
			*/
			'EXPLODE::INVALID'             					=>	$this->translate('Invalid type given'),

			/**
			*	EMAILADDRESS
			*	self::INVALID            => "Invalid type given. String expected",
			*	self::INVALID_FORMAT     => "The input is not a valid email address. Use the basic format local-part@hostname",
			*	self::INVALID_HOSTNAME   => "'%hostname%' is not a valid hostname for the email address",
			*	self::INVALID_MX_RECORD  => "'%hostname%' does not appear to have any valid MX or A records for the email address",
			*	self::INVALID_SEGMENT    => "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network",
			*	self::DOT_ATOM           => "'%localPart%' can not be matched against dot-atom format",
			*	self::QUOTED_STRING      => "'%localPart%' can not be matched against quoted-string format",
			*	self::INVALID_LOCAL_PART => "'%localPart%' is not a valid local part for the email address",
			*	self::LENGTH_EXCEEDED    => "The input exceeds the allowed length",
			*/
			'EMAILADDRESS::INVALID'             			=>	$this->translate('La valeur entrée n\'est pas un type valide (chaînes de caractères attendues)'),
			'EMAILADDRESS::INVALID_FORMAT'             		=>	$this->translate('La valeur du champ n\'est pas une adresse e-mail valide'),
			'EMAILADDRESS::INVALID_HOSTNAME'            	=>	$this->translate('Le nom d\'hôte %hostname% n\'est pas un nom d\'hôte valide pour une adresse e-mail'),
			'EMAILADDRESS::INVALID_MX_RECORD'           	=>	$this->translate('L\'hôte %hostname% ne semble pas avoir de MX valide ou enregistrement de l\'adresse e-mail'),
			'EMAILADDRESS::INVALID_SEGMENT'             	=>	$this->translate('L\'hôte %hostname% n\'est pas dans un segment de réseau routable'),
			'EMAILADDRESS::DOT_ATOM'             			=>	$this->translate('La partie locale %locale Part% ne peut pas être comparé avec le format dot-atom'),
			'EMAILADDRESS::QUOTED_STRING'             		=>	$this->translate('La partie locale %locale Part% ne peut pas être comparé avec le format QUOTED STRING'),
			'EMAILADDRESS::INVALID_LOCAL_PART'          	=>	$this->translate('La partie locale %locale Part% n\'est pas valide'),
			'EMAILADDRESS::LENGTH_EXCEEDED'             	=>	$this->translate('L\'adresse e-mail dépasse la longueur autorisée'),

			/**
			*	DIGITS
			*	self::NOT_DIGITS   => "The input must contain only digits",
			*	self::STRING_EMPTY => "The input is an empty string",
			*	self::INVALID      => "Invalid type given. String, integer or float expected",
			*/
			'DIGITS::NOT_DIGITS'             				=>	$this->translate('The input must contain only digits'),
			'DIGITS::STRING_EMPTY'        	     			=>	$this->translate('The input is an empty string'),
			'DIGITS::INVALID'             					=>	$this->translate('Invalid type given. String, integer or float expected'),
			
			/**
			*	DATESTEP
			*	self::NOT_STEP     => "The input is not a valid step"
			*/
			'DATESTEP::NOT_STEP'             				=>	$this->translate('The input is not a valid step'),

			/**
			*	DATE
			*	self::INVALID        => "Invalid type given. String, integer, array or DateTime expected",
			*	self::INVALID_DATE   => "The input does not appear to be a valid date",
			*	self::FALSEFORMAT    => "The input does not fit the date format '%format%'",
			*/
			'DATE::INVALID'        							=>	$this->translate('La valeur entrée n\'est pas un type valide (chaînes de caractères attendues)'),
			'DATE::INVALID_DATE'   							=>	$this->translate('La date n\'est pas une date valide'),
			'DATE::FALSEFORMAT'    							=>	$this->translate('Le format de la date n\'est pas correcte, veuillez utiliser le format %format%'),

			/**
			*	CSRF
			*	self::NOT_SAME => "The form submitted did not originate from the expected site",
			*/
			'CSRF::NOT_SAME' 								=>	$this->translate('Le formulaire utilisé a probablement expiré'),

			/**
			*	CREDITCARD
			*	self::CHECKSUM       => "The input seems to contain an invalid checksum",
			*	self::CONTENT        => "The input must contain only digits",
			*	self::INVALID        => "Invalid type given. String expected",
			*	self::LENGTH         => "The input contains an invalid amount of digits",
			*	self::PREFIX         => "The input is not from an allowed institute",
			*	self::SERVICE        => "The input seems to be an invalid credit card number",
			*	self::SERVICEFAILURE => "An exception has been raised while validating the input",
			*/
			'CREDITCARD::CHECKSUM'       					=>	$this->translate('The input seems to contain an invalid checksum'),
			'CREDITCARD::CONTENT'        					=>	$this->translate('The input must contain only digits'),
			'CREDITCARD::INVALID'        					=>	$this->translate('Invalid type given. String expected'),
			'CREDITCARD::LENGTH'         					=>	$this->translate('The input contains an invalid amount of digits'),
			'CREDITCARD::PREFIX'         					=>	$this->translate('The input is not from an allowed institute'),
			'CREDITCARD::SERVICE'        					=>	$this->translate('The input seems to be an invalid credit card number'),
			'CREDITCARD::SERVICEFAILURE' 					=>	$this->translate('An exception has been raised while validating the input'),

			/**
			*	CALLBACK
			*	self::INVALID_VALUE    => "The input is not valid",
			*	self::INVALID_CALLBACK => "An exception has been raised within the callback",
			*/
			'CALLBACK::INVALID_VALUE'    					=>	$this->translate('The input is not valid'),
			'CALLBACK::INVALID_CALLBACK' 					=>	$this->translate('An exception has been raised within the callback'),

			/**
			*	BETWEEN
			*	self::NOT_BETWEEN        => "The input is not between '%min%' and '%max%', inclusively",
			*	self::NOT_BETWEEN_STRICT => "The input is not strictly between '%min%' and '%max%'"
			*/
			'BETWEEN::NOT_BETWEEN'        					=>	$this->translate('The input is not between "%min%" and "%max%", inclusively'),
			'BETWEEN::NOT_BETWEEN_STRICT' 					=>	$this->translate('The input is not strictly between "%min%" and "%max%"'),

			/**
			*	BARCODE
			*	self::FAILED         => "The input failed checksum validation",
			*	self::INVALID_CHARS  => "The input contains invalid characters",
			*	self::INVALID_LENGTH => "The input should have a length of %length% characters",
			*	self::INVALID        => "Invalid type given. String expected",			
			*/
			'BARCODE::FAILED'         						=>	$this->translate('The input failed checksum validation'),
			'BARCODE::INVALID_CHARS'  						=>	$this->translate('The input contains invalid characters'),
			'BARCODE::INVALID_LENGTH' 						=>	$this->translate('The input should have a length of %length% characters'),
			'BARCODE::INVALID'        						=>	$this->translate('Invalid type given. String expected'),
			
			/**
			*	COMPAGNY
			*/
			'COMPAGNY::NAME'								=>	$this->translate('Be-Pharma (Pharmacie de la Couronne)'),
			'COMPAGNY::LOGO'								=>	$this->translate('Be-Pharma'),
			'COMPAGNY::SLOGAN'								=>	$this->translate('Bruxelles Pharmacie'),
			'COMPAGNY::PHONE'								=>	$this->translate('02/648.32.71'),
			'COMPAGNY::FAX'									=>	$this->translate('02/648.32.71'),
			'COMPAGNY::EMAIL'								=>	$this->translate('info@be-pharma.be'),
			'COMPAGNY::WEBSITE'								=>	$this->translate('http://www.be-pharma.be'),
			'COMPAGNY::TVA'									=>	$this->translate('BE0842 272 873'),
			'COMPAGNY::STREET'								=>	$this->translate('Avenue de la Couronne'),
			'COMPAGNY::NUMBER'								=>	$this->translate('384'),
			'COMPAGNY::POSTALCODE'							=>	$this->translate('1050'),
			'COMPAGNY::CITY'								=>	$this->translate('Bruxelles').' ('.$this->translate(Ixelles).')',
			'COMPAGNY::COUNTRY'								=>	$this->translate('Belgique'),
			
			/**
			*	ERROR
			*/
			'ERROR::LABEL'									=>	$this->translate('Erreur pour le champ (%s)'),
			'ERROR::INTERNAL'								=>	$this->translate('Une erreur interne s\'est produite'),
			
			/**
			*	INFO
			*/
			'INFO::LABEL'									=>	$this->translate('Information pour le champ %s'),
			
						
			
			'FALSE_EXTENSION'								=>	$this->translate('L\'extention %fileExtension% du fichier %value% n\'est pas autorisée').'<br />'.
																$this->translate('Les extentions autorisées sont %extension%'),
						
			'TOO_BIG'										=>	$this->translate('Le poids du fichier %value% est de %size%').'<br />'.
																$this->translate('Le poids maximum du fichier ne peut pas dépasser %max%'),
			
			/**
			*	COUNT
			*/
			'TOO_MANY'										=>	$this->translate('Vous tentez de charger %totalFiles% fichier%plurielFiles%').'<br />'.
																$this->translate('Maximum %max% fichier%plurielMax% autorisé%plurielMax%'), 
			'TOO_FEW'										=>	$this->translate('Vous avez chargé que %totalFiles% fichier%plurielCount%').'<br />'.
																$this->translate('Minimum %max% fichier%plurielMax% attendus'),
			
			/**
			*	PHONENUMBER
			*/
			'PHONENUMBER::INVALID'							=>	$this->translate('La valeur entrée n\'est pas un type valide (nombres attendues)'),
			'PHONENUMBER::UNSUPPORTED'						=>	$this->translate('La zone du numéro de téléphone ne correspond pas à un pays supporté par le système'),
			'PHONENUMBER::NO_MATCH'							=>	$this->translate('Le format du numéro de téléphone n\'est pas supporté par le système'),
			
			/**
			*	RECORD
			*/
			'RECORD_EXISTS::ERROR_NO_RECORD_FOUND'			=>	$this->translate('L\'entrée %value% n\'est pas présente dans notre système'),
			'NO_RECORD_EXISTS::ERROR_RECORD_FOUND'			=>	$this->translate('L\'entrée %value% est déjà présente dans notre système'),
			
			/**
			*	SPACE
			*/
			'SPACE'											=>	$this->translate('L\'entrée ne peut pas contenir d\'espace'),
			
			/**
			*	FLOAT
			*/
			'FLOAT::INVALID'								=>	$this->translate('Le type de donnée n\'est pas permis pour un float'),
			'FLOAT::NOT_FLOAT'								=>	$this->translate('La valeur %value% n\'est pas un nombre ou nombre à virgule'),
			
			/**
			* POSTCODE
			*/
			'POSTCODE::INVALID'								=>	$this->translate('Invalid type given. String or integer expected'),
			'POSTCODE::NO_MATCH'							=>	$this->translate('The input does not appear to be a postal code'),
			'POSTCODE::SERVICE'								=>	$this->translate('The input does not appear to be a postal code'),
			'POSTCODE::SERVICEFAILURE'						=>	$this->translate('An exception has been raised while validating the input'),
			
			'UNKNOWN'										=>	$this->translate('Une erreur inconnue s\'est produite, le service technique a été averti'),								
		));
	}
	
	public function setProfil($profil = NULL){
		if($this->profil !== NULL){
			$this->profil = $profil;
		}
		return $this;
	}
	public function getProfil(){
		if($this->profil === NULL){
			$this->setProfil($this->generateProfil());
		}
		return $this->profil;
	}
	public function generateProfil(){
		return	$this->getArrayObject(array(
			'profil'	=>	$this->translate('Gérez ici votre profil et vos adresses de livraison pour des commandes simples, efficaces et rapides'),
			'have'		=>	$this->translate('Vous disposez actuellement de %s'),
			'cart'		=>	$this->translate('Découvrez les produits que vous avez actuellement dans votre caddie. Ajoutez, supprimez et partagez des produits'),
			'point'		=>	$this->translate('Lors de vos achats, Be-Pharma vous offre des points qui vous permettront de bénéficier de réductions très avantageuses'),
			'proposer'	=>	$this->translate('Lorsque vous parrainez des amis, ceux-ci vous rapportent dès leur première commande, une réduction de 5&euro; cumulable par tranche d\'achat de 50&euro;'),
			'discount'	=>	$this->translate('Gérez ici vos bons de réduction que vous avez obtenu grâce aux nombreuses possibilités offertes par Be-Pharma'),
			'order'		=>	$this->translate('Vous retrouverez ici toutes vos anciennes commandes et avec un simple clic, vous pourrez les remettre dans votre panier'),
			'ticket'	=>	$this->translate('Vous retrouverez ici tous vos anciens tickets de livraison, vous évitant ainsi de devoir les chercher en cas de besoin'),
			'wishlist'	=>	$this->translate('Prenez votre temps sur Be-Pharma, convertissez votre caddie en liste de souhaits et vice versa pour pouvoir faire le paiement plus tard'),
			'address'	=>	$this->translate('Gérez ici vos adresses de livraison, ajoutez votre adresse de bureau ou tout autre lieu pour une livraison rapide quelque soit votre emplacement'),
			'welcome'	=>	$this->translate('Bienvenue %s, gérez votre compte ici'),
		));
	}


	public function setLabels($labels = NULL){
		if($labels !== NULL){
			$this->labels	=	$labels;
		}
		return $this;
	}
	public function getLabels(){
		if($this->labels === NULL){
			$this->setLabels($this->generateLabels());
		}
		return $this->labels;
	}
	public function setLabel($index, $label){
		$this->getLabels()->offsetSet($index, $label);
		return $this;
	}
	public function getLabel($index){
		$label	=	NULL;
		if($this->getLabels()->offsetExists($index)){
			$label	=	$this->getLabels()->offsetGet($index);
		}
		return $label;
	}
	public function generateLabels(){
		return	$this	->getArrayObject(array(
			'Yes'						=>	$this->translate('Oui'),
			'No'						=>	$this->translate('Non'),
			'Send'						=>	$this->translate('Envoyer'),
			'Add'						=>	$this->translate('Ajouter'),
			'Change'					=>	$this->translate('Changer'),
			'Show'						=>	$this->translate('Voir'),
			'Edit'						=>	$this->translate('Modifier'),
			'Delete'					=>	$this->translate('Supprimer'),
			'Minus'						=>	$this->translate('Moins'),
			'Plus'						=>	$this->translate('Plus'),
			'Subscribe'					=>	$this->translate('M\'inscrire'),
			'Unsubscribe'				=>	$this->translate('Me désinscrire'),
			'Connexion'					=>	$this->translate('Connexion'),
			'PasswordEdit'				=>	$this->translate('Modifier mon mot de passe'),
			'PasswordLost'				=>	$this->translate('Mot de passe perdu?'),
			'AddressDelete'				=>	$this->translate('Supprimer cette adresse'),
			'AddressAdd'				=>	$this->translate('Ajouter une adresse'),
			'AddToCart'					=>	$this->translate('Ajouter à mon panier'),
			'Share'						=>	$this->translate('Partager'),
			'Create'					=>	$this->translate('Créé le'),
			'Order'						=>	$this->translate('Commande'),
			'Product'					=>	$this->translate('Produit'),
			'Quantity'					=>	$this->translate('Quantité'),
			'PriceUnit'					=>	$this->translate('Prix unitaire'),
			'PriceTotal'				=>	$this->translate('Prix total'),
			'PriceWithDiscount'			=>	$this->translate('Prix avec promotion'),
			'Actions'					=>	$this->translate('Actions'),
			'CNK'						=>	$this->translate('CNK'),
			'Search'					=>	$this->translate('Rechercher'),
			'Discount'					=>	$this->translate('Promotion'),
			'Next'						=>	$this->translate('Suivant'),
			'Previous'					=>	$this->translate('Précédent'),
			'Download'					=>	$this->translate('Télécharger'),		
			'Ticket'					=>	$this->translate('Ticket'),
			'ScoreForOrder'				=>	$this->translate('Point pour la commande'),
			'Score'						=>	$this->translate('Point%s'),
			'Box'						=>	$this->translate('Boîte'),
			'SayPlus'					=>	$this->translate('En savoir plus...'),
			'Transport'					=>	$this->translate('Transport'),
			'EmptyMyCart'				=>	$this->translate('Vider mon panier'),
			'ConvertMyCartToWishlist'	=>	$this->translate('Convertir mon panier en liste de souhaits'),		
			'ValidYourDiscount'			=>	$this->translate('Valider votre bon de réduction'),
			'TransportFree'				=>	$this->translate('Transport gratuit'),
			'TotalNoDiscount'			=>	$this->translate('Total hors promotions'),
			'TotalWithDiscount'			=>	$this->translate('Total avec promotions'),
			'Paid'						=>	$this->translate('Payer'),
			'MyWishlist'				=>	$this->translate('Ma liste de souhaits'),
			'MyWishlists'				=>	$this->translate('Mes listes de souhaits'),
			'MyProfil'					=>	$this->translate('Mon profil'),
			'MyCart'					=>	$this->translate('Mon panier'),
			'MyPoints'					=>	$this->translate('Mes points'),
			'MyProposers'				=>	$this->translate('Mes parrainages'),
			'MyDiscounts'				=>	$this->translate('Mes bons de réduction'),
			'MyOrders'					=>	$this->translate('Mes commandes'),
			'MyTickets'					=>	$this->translate('Mes tickets'),
			'MyAddresses'				=>	$this->translate('Mes adresses'),
			'MyPassword'				=>	$this->translate('Mon mot de passe'),
			'ProfilEdit'				=>	$this->translate('Modifier mon profil'),
			'WelcomeX'					=>	$this->translate('Bienvenue %s'),
			
			
							
			'name'						=>	$this->translate('Votre nom'),
			'email'						=>	$this->translate('Votre adresse email'),
			'subject'					=>	$this->translate('Sujet de votre message'),
			'phone'						=>	$this->translate('Votre numéro de téléphone'),
			'message'					=>	$this->translate('Votre message'),
			'csrf'						=>	$this->translate('Csrf'),
			'why'						=>	$this->translate('Pouvez-vous nous donner quelques explications'),
			'facility'					=>	$this->translate('Que pensez-vous de l\'utilisation du site?'),
			'ergonomie'					=>	$this->translate('Que pensez-vous de l\'ergonomie du site?'),
			'service'					=>	$this->translate('Que pensez-vous du service'),
			'design'					=>	$this->translate('Que pensez-vous du design'),
			'speed'						=>	$this->translate('Que pensez-vous de la vitesse'),
			'remarks'					=>	$this->translate('Vos remarques et suggestions'),
			'postCode'					=>	$this->translate('Votre code postal'),
			'street'					=>	$this->translate('Votre rue'),
			'streetNumber'				=>	$this->translate('Votre numéro de porte'),
			'box'						=>	$this->translate('Votre boîte'),
			'city'						=>	$this->translate('Votre ville / commune'),
			'receiver'					=>	$this->translate('L\'adresse email du destinataire'),
			'emailProposer'				=>	$this->translate('L\'adresse email de votre parrain'),
			'key'						=>	$this->translate('Clé de décryptage'),
			'newPassword'				=>	$this->translate('Votre nouveau mot de passe'),
			'confirmPassword'			=>	$this->translate('Confirmation du mot de passe'),
			'codeSecret'				=>	$this->translate('Code secret / Code de validation'),
			'birthday'					=>	$this->translate('Votre date de naissance'),
			'gender'					=>	$this->translate('Votre sexe'),
			'weight'					=>	$this->translate('Votre poids en Kg'),
			'size'						=>	$this->translate('Votre taille en Cm'),
			'password'					=>	$this->translate('Votre mot de passe'),
		));
	}
	
	public function setPlaceholders($placeholders = NULL){
		if($placeholders !== NULL){
			$this->placeholders	=	$placeholders;
		}
		return $this;
	}
	public function getPlaceholders(){
		if($this->placeholders === NULL){
			$this->setPlaceholders($this->generatePlaceholders());
		}
		return $this->placeholders;
	}
	public function setPlaceholder($index, $placeholder){
		$this->getPlaceholders()->offsetSet($index, $placeholder);
		return $this;
	}
	public function getPlaceholder($index){
		$placeholder	=	NULL;
		
		if($this->getPlaceholders()->offsetExists($index)){
			$placeholder	=	$this->getPlaceholders()->offsetGet($index);
		}
		
		return	$placeholder;
	}
	public function generatePlaceholders(){
		return	$this	->getArrayObject(array(
			'name'				=>	$this->translate('Tapez ici votre nom...'),
			'email'				=>	$this->translate('Tapez ici votre adresse email...'),
			'subject'			=>	$this->translate('Tapez ici le sujet de votre message...'),
			'phone'				=>	$this->translate('Tapez ici votre numéro de téléphone...'),
			'message'			=>	$this->translate('Tapez ici votre message...'),
			'why'				=>	$this->translate('Tapez ici les raisons...'),
			'facility'			=>	$this->translate('Une note pour la facilité d\'utilisation...'),
			'ergonomie'			=>	$this->translate('Une note pour l\'ergonomie...'),
			'service'			=>	$this->translate('Une note pour le service...'),
			'design'			=>	$this->translate('Une note pour le design...'),
			'speed'				=>	$this->translate('Une note pour la vitesse...'),
			'remarks'			=>	$this->translate('Tapez ici vos remarques et suggestions...'),
			'postCode'			=>	$this->translate('Tapez ici votre code postal...'),
			'street'			=>	$this->translate('Tapez ici le nom de votre rue...'),
			'streetNumber'		=>	$this->translate('Tapez ici votre numéro de porte...'),
			'box'				=>	$this->translate('Tapez ici votre boîte, si nécessaire...'),
			'city'				=>	$this->translate('Tapez ici le nom de la commune ou de la ville...'),
			'receiver'			=>	$this->translate('Tapez ici l\'adresse email du destinataire...'),
			'shareShortMessage'	=>	$this->translate('Tapez ici un bref message pour ce partage...'),
			'emailProposer'		=>	$this->translate('Tapez ici l\'adresse email de votre parrain...'),
			'key'				=>	$this->translate('Tapez ici la clé de décryptage que vous avez reçu par email'),
			'newPassword'		=>	$this->translate('Tapez ici votre nouveau mot de passe...'),
			'confirmPassword'	=>	$this->translate('Tapez ici la confirmation de votre nouveau mot de passe...'),
			'codeSecret'		=>	$this->translate('Tapez ici le code secret que vous avez reçu par email...'),
			'birthday'			=>	$this->translate('Tapez ici votre date de naissance au format jj/mm/aaaa'),
			'gender'			=>	$this->translate('Votre sexe'),
			'weight'			=>	$this->translate('Tapez ici votre poids en Kg...'),
			'size'				=>	$this->translate('Tapez ici votre taille en Cm...'),
			'password'			=>	$this->translate('Tapez ici votre mot de passe...'),
		));
	}
		
	public function setKeyword($index, $keyword){
		$this->getKeywords()->offsetSet($index, $keyword);
		return $this;
	}
	public function getKeyword($index){
		$keyword	=	NULL;
		if($this->getKeywords()->offsetExists($index)){
			$keyword	=	$this->getKeywords()->offsetGet($index);
		}
		return $keyword;
	}
	public function setKeywords(ArrayObject $keywords = NULL){
		if($keywords !== NULL){
			$this->keywords	=	$keywords;
		}
		return	$this;
	}
	public function getKeywords(){
		if($this->keywords === NULL){
			$this->setKeywords($this->generateKeywords());
		}
		return $this->keywords;
	}
	public function generateKeywords(){
		return	$this->getArrayObject(array(		
			/**
			*	CONTACT 
			*/
			'Contact/Index/index'			=>	$this->translate('contact').','.
												$this->translate('contacts').','.
												$this->translate('message').','.
												$this->translate('messages').','.
												$this->translate('problème').','.
												$this->translate('problèmes').','.
												$this->translate('bug').','.
												$this->translate('bugs').','.
												$this->translate('envoi').','.
												$this->translate('envois').','.
												$this->translate('demande').','.
												$this->translate('demandes').','.
												$this->translate('interrogation').','.
												$this->translate('interrogations').','.
												$this->translate('suggestion').','.
												$this->translate('suggestions').','.
												$this->translate('question').','.
												$this->translate('questions').','.
												$this->translate('aide').','.
												$this->translate('aides').','.
												$this->translate('soutien').','.
												$this->translate('soutiens').','.
												$this->translate('support').','.
												$this->translate('supports').','.
												$this->translate('technique').','.
												$this->translate('techniques').','.
												$this->translate('besoin d\'aide').','.
												$this->translate('je ne trouve pas').','.
												$this->translate('pouvez-vous m\'aider').',',												
			'Contact/Index/send'			=>	$this->translate('contact').','.
												$this->translate('contacts').','.
												$this->translate('message').','.
												$this->translate('messages').','.
												$this->translate('problème').','.
												$this->translate('problèmes').','.
												$this->translate('bug').','.
												$this->translate('bugs').','.
												$this->translate('envoi').','.
												$this->translate('envois').','.
												$this->translate('demande').','.
												$this->translate('demandes').','.
												$this->translate('interrogation').','.
												$this->translate('interrogations').','.
												$this->translate('suggestion').','.
												$this->translate('suggestions').','.
												$this->translate('question').','.
												$this->translate('questions').','.
												$this->translate('aide').','.
												$this->translate('aides').','.
												$this->translate('soutien').','.
												$this->translate('soutiens').','.
												$this->translate('support').','.
												$this->translate('supports').','.
												$this->translate('technique').','.
												$this->translate('techniques').','.
												$this->translate('besoin d\'aide').','.
												$this->translate('je ne trouve pas').','.
												$this->translate('pouvez-vous m\'aider').',',
		));
	}
	
	public function setTitle($index, $title){
		$this->getTitles()->offsetSet($index, $title);
		return $this;
	}
	public function getTitle($index){
		$title	=	NULL;
		if($this->getTitles()->offsetExists($index)){
			$title	=	$this->getTitles()->offsetGet($index);
		}
		return $title;
	}
	public function setTitles(ArrayObject $titles = NULL){
		if($titles !== NULL){
			$this->titles	=	$titles;
		}
		return $this;
	}
	public function getTitles(){
		if($this->titles === NULL){
			$this->setTitles($this->generateTitles());
		}
		return $this->titles;
	}
	public function generateTitles(){
		return	$this->getArrayObject(array(
			/**
			*	ADDRESS
			*/
			'Address/Index/index'			=>	$this->translate('Ajouter une adresse'),
			'Address/Index/add'				=>	$this->translate('Ajouter une adresse'),
			'Address/Index/delete'			=>	$this->translate('Supprimer une adresse'),
			'Address/Index/prior'			=>	$this->translate('Définir une adresse comme prioritaire'),
			
			/**
			*	ADVISE
			*/
			'Advise/Index/index'			=>	$this->translate('Avis'),
			'Advise/Index/add'				=>	$this->translate('Donnez-nous votre avis'),			
			
			/**
			*	APPLICATION
			*/ 
			'Application/Index/index'		=>	$this->translate('Bienvenue'),
			'Application/Index/cgv'			=>	$this->translate('Conditions générales de vente'),
			'Application/Index/who'			=>	$this->translate('Qui sommes-nous?'),
			
			/**
			*	CART 
			*/
			'Cart/Index/paid'				=>	$this->translate('Paiement'),
			
			
			/**
			*	CATEGORY
			*/
			'Category/Index/index'			=>	$this->translate('Catégorie %s'),
			'Category/Index/categories'		=>	$this->translate('Catégories'),

			/**
			*	CONTACT
			*/
			'Contact/Index/index'			=>	$this->translate('Contactez-nous'),
			'Contact/Index/send'			=>	$this->translate('Contactez-nous'),			

			/**
			*	TICKET
			*/
			'Ticket/Index/index'			=>	$this->translate('Mes tickets de réduction'),
			'Ticket/Index/ticket'			=>	$this->translate('Mes tickets de réduction'),
			
			/**
			*	DISCOUNT
			*/
			'Discount/Index/index'			=>	$this->translate('Mes tickets de réduction'),
			'Discount/Index/ticket'			=>	$this->translate('Mes tickets de réduction'),
			
			/**
			*	EMAIL
			*/
			'Email/Index/show'				=>	$this->translate('Aide à la visualisation des emails'),

			/**
			*	GARDE
			*/
			'Garde/Index/index'				=>	$this->translate('Trouvez votre pharmacie de garde la plus proche'),
			
			/**
			*	MAGAZINE
			*/
			'Magazine/Index/index'			=>	$this->translate('Bienvenue dans le magazine de la santé'),
			'Magazine/Index/rubriques'		=>	$this->translate('Rubrique %s'),

			/**
			*	NEWSLETTER
			*/
			'Newsletter/Index/index'		=>	$this->translate('Newsletter'),
			'Newsletter/Index/subscribe'	=>	$this->translate('Inscrivez-vous à notre newsletter'),
			'Newsletter/Index/unsubscribe'	=>	$this->translate('Désinscription à la newsletter'),
			
			/**
			*	ORDER
			*/
			'Order/Index/index'				=>	$this->translate('Mes commandes'),
			'Order/Index/delete'			=>	$this->translate('Supprimer une commande'),
			'Order/Index/ticket'			=>	$this->translate('Mon ticket'),
			'Order/Index/download'			=>	$this->translate('Télécharger un ticket'),
			'Order/Index/tickets'			=>	$this->translate('Mes tickets'),
			
			/**
			*	PDF 
			*/ 
			
			/**
			*	PAYPAL
			*/
			
			/**
			*	POINT
			*/
			'Point/Index/index'				=>	$this->translate('Mes points'),
			
			/**
			*	PRODUCT
			*/
			'Product/Index/index'			=>	$this->translate('Produit %s'),
			'Product/Index/search'			=>	$this->translate('Recherche %s'),
			
			/**
			*	PROPOSER 
			*/
			'Proposer/Index/index'			=>	$this->translate('Mes parrainages'),			
			
			/**
			*	SEARCH
			*/
			'Search/Index/products'			=>	$this->translate('Recherche %s'),
			'Search/Index/city'				=>	$this->translate('Recherche %s'),
			'Search/Index/street'			=>	$this->translate('Recherche %s'),
			
			
			/**
			*	SHARE
			*/
			'Share/Index/index'				=>	$this->translate('Partager'),
			'Share/Index/show'				=>	$this->translate('Partager'),
		
			/**
			*	URGENCY 
			*/
			'Urgency/Index/index'			=>	$this->translate('Numéros d\'urgence'),
			
			/**
			*	USER 
			*/ 
			'User/Index/index'				=>	$this->translate('Mon compte'),
			'User/Index/profil'				=>	$this->translate('Gérez votre profil'),	
			'User/Index/signin'				=>	$this->translate('Inscrivez-vous en 15 secondes'),
			'User/Index/edit'				=>	$this->translate('Modifiez votre profil'),
			'User/Index/signout'			=>	$this->translate('Désinscription'),
			'User/Index/logout'				=>	$this->translate('Confirmez votre déconnexion'),
			'User/Index/login'				=>	$this->translate('Connectez-vous pour accéder à tous nos services'),
			'User/Password/index'			=>	$this->translate('Mon mot de passe'),
			'User/Password/edit'			=>	$this->translate('Modifiez votre mot de passe'),
			'User/Password/lost'			=>	$this->translate('Mot de passe perdu?'),
			'User/Password/change'			=>	$this->translate('Changement de votre mot de passe'),
			
			/**
			*	WISHLIST
			*/
			'Wishlist/Index/unconvert'		=>	$this->translate('Convertissez votre liste de souhaits en panier'),
			'Wishlist/Index/convert'		=>	$this->translate('Convertissez votre panier en liste de souhaits'),
			'Wishlist/Index/index'			=>	$this->translate('Mes listes de souhaits'),
			'Wishlist/Index/plus'			=>	$this->translate('Plus'),
			'Wishlist/Index/min'			=>	$this->translate('Moins'),
			'Wishlist/Index/replace'		=>	$this->translate('Remplacer'),
			'Wishlist/Index/show'			=>	$this->translate('Ma liste de souhaits %s'),
			'Wishlist/Index/delete'			=>	$this->translate('Supprimer la liste de souhaits %s'),
			'Wishlist/Index/remove'			=>	$this->translate('Supprimer un produit de la liste de souhaits'),
					
			/**
			*	CART
			*/			
			'Cart/Index/show'				=>	$this->translate('Mon panier'),
			'Cart/Index/add'				=>	$this->translate(''),
			'Cart/Index/empty'				=>	$this->translate(''),
			'Cart/Index/min'				=>	$this->translate(''),
			'Cart/Index/plus'				=>	$this->translate(''),
			'Cart/Index/delete'				=>	$this->translate(''),
			'Cart/Index/replace'			=>	$this->translate(''),
			'Cart/Index/success'			=>	$this->translate(''),
			'Cart/Index/error'				=>	$this->translate(''),
			'Cart/Index/cancel'				=>	$this->translate(''),
		));
	}
	
	public function setDescription($index, $description){
		$this->getDescriptions()->offsetSet($index, $description);
		return $this;
	}
	public function getDescription($index){
		$description	=	NULL;
		if($this->getDescriptions()->offsetExists($index)){
			$description	=	$this->getDescriptions()->offsetGet($index);
		}
		return $description;
	}
	public function setDescriptions(ArrayObject $descriptions = NULL){
		if($descriptions !== NULL){
			$this->descriptions	=	$descriptions;
		}
		return $this;
	}
	public function getDescriptions(){
		if($this->descriptions === NULL){
			$this->setDescriptions($this->generateDescriptions());
		}
		return $this->descriptions;
	}
	public function generateDescriptions(){
		return	$this->getArrayObject(array(
			/**
			*	ADDRESS
			*/
			'Address/Index/index'			=>	$this->translate('Ajouter une adresse'),
			'Address/Index/add'				=>	$this->translate('Ajouter une adresse'),
			'Address/Index/delete'			=>	$this->translate('Supprimer une adresse'),
			'Address/Index/prior'			=>	$this->translate('Définir une adresse comme prioritaire'),
			
			/**
			*	ADVISE
			*/
			'Advise/Index/index'			=>	$this->translate('Avis'),
			'Advise/Index/add'				=>	$this->translate('Donnez-nous votre avis'),			
			
			/**
			*	APPLICATION
			*/ 
			'Application/Index/index'		=>	$this->translate('Bienvenue'),
			'Application/Index/cgv'			=>	$this->translate('Conditions générales de vente'),
			'Application/Index/who'			=>	$this->translate('Qui sommes-nous?'),
			
			/**
			*	CART 
			*/
			'Cart/Index/paid'				=>	$this->translate('Paiement'),
			
			
			/**
			*	CATEGORY
			*/
			'Category/Index/index'			=>	$this->translate('Catégorie %s'),
			'Category/Index/categories'		=>	$this->translate('Catégories'),

			/**
			*	CONTACT
			*/
			'Contact/Index/index'			=>	$this->translate('Contactez-nous'),
			'Contact/Index/send'			=>	$this->translate('Contactez-nous'),			

			/**
			*	TICKET
			*/
			'Ticket/Index/index'			=>	$this->translate('Mes tickets de réduction'),
			'Ticket/Index/ticket'			=>	$this->translate('Mes tickets de réduction'),
			
			/**
			*	DISCOUNT
			*/
			'Discount/Index/index'			=>	$this->translate('Mes tickets de réduction'),
			'Discount/Index/ticket'			=>	$this->translate('Mes tickets de réduction'),
			
			/**
			*	EMAIL
			*/
			'Email/Index/show'				=>	$this->translate('Aide à la visualisation des emails'),

			/**
			*	GARDE
			*/
			'Garde/Index/index'				=>	$this->translate('Trouvez votre pharmacie de garde la plus proche.').
												$this->translate('Il vous suffit d\'indiquer votre position et la tranche horaire pour avoir la liste des pharmacies de garde avec l\'adresse, le numéro de téléphone, etc.'),
			
			/**
			*	MAGAZINE
			*/
			'Magazine/Index/index'			=>	$this->translate('Bienvenue dans le magazine de la santé'),
			'Magazine/Index/rubriques'		=>	$this->translate('Rubrique %s'),

			/**
			*	NEWSLETTER
			*/
			'Newsletter/Index/index'		=>	$this->translate('Newsletter'),
			'Newsletter/Index/subscribe'	=>	$this->translate('Inscrivez-vous à notre newsletter'),
			'Newsletter/Index/unsubscribe'	=>	$this->translate('Désinscription à la newsletter'),
			
			/**
			*	ORDER
			*/
			'Order/Index/index'				=>	$this->translate('Mes commandes'),
			'Order/Index/delete'			=>	$this->translate('Supprimer une commande'),
			'Order/Index/ticket'			=>	$this->translate('Mon ticket'),
			'Order/Index/download'			=>	$this->translate('Télécharger un ticket'),
			'Order/Index/tickets'			=>	$this->translate('Mes tickets'),
			
			/**
			*	PDF 
			*/ 
			
			/**
			*	PAYPAL
			*/
			
			/**
			*	POINT
			*/
			'Point/Index/index'				=>	$this->translate('Mes points'),
			
			/**
			*	PRODUCT
			*/
			'Product/Index/index'			=>	$this->translate('Produit %s'),
			'Product/Index/search'			=>	$this->translate('Recherche %s'),
			
			/**
			*	PROPOSER 
			*/
			'Proposer/Index/index'			=>	$this->translate('Mes parrainages'),			
			
			/**
			*	SEARCH
			*/
			'Search/Index/products'			=>	$this->translate('Recherche %s'),
			'Search/Index/city'				=>	$this->translate('Recherche %s'),
			'Search/Index/street'			=>	$this->translate('Recherche %s'),
			
			
			/**
			*	SHARE
			*/
			'Share/Index/index'				=>	$this->translate('Partager'),
			'Share/Index/show'				=>	$this->translate('Partager'),
		
			/**
			*	URGENCY 
			*/
			'Urgency/Index/index'			=>	$this->translate('Cette page regroupe une liste non exhaustive des numéros d\'urgence, ainsi que des liens vers leur site internet.').
												$this->translate('Gardez cette page dans vos favoris pour qu\'elle soit à votre portée. Elle pourrait vous servir un jour.'),
			
			/**
			*	USER 
			*/ 
			'User/Index/index'				=>	$this->translate('Gérez et/ou modifiez tout ce qui vous concerne sur cette page.').
												$this->translate('Ajoutez des adresses pour faciliter vos livraisons et complétez votre profil.'),
			'User/Index/profil'				=>	$this->translate('Gérez et/ou modifiez tout ce qui vous concerne sur cette page.').
												$this->translate('Ajoutez des adresses pour faciliter vos livraisons et complétez votre profil.'),
			'User/Index/signin'				=>	$this->translate('Gérez et/ou modifiez tout ce qui vous concerne sur cette page.').
												$this->translate('Ajoutez des adresses pour faciliter vos livraisons et complétez votre profil.'),
			'User/Index/edit'				=>	$this->translate('Gérez et/ou modifiez tout ce qui vous concerne sur cette page.').
												$this->translate('Ajoutez des adresses pour faciliter vos livraisons et complétez votre profil.'),
			'User/Index/signout'			=>	$this->translate('Gérez et/ou modifiez tout ce qui vous concerne sur cette page.').
												$this->translate('Ajoutez des adresses pour faciliter vos livraisons et complétez votre profil.'),
			'User/Index/logout'				=>	$this->translate('Gérez et/ou modifiez tout ce qui vous concerne sur cette page.').
												$this->translate('Ajoutez des adresses pour faciliter vos livraisons et complétez votre profil.'),
			'User/Index/login'				=>	$this->translate('Gérez et/ou modifiez tout ce qui vous concerne sur cette page.').
												$this->translate('Ajoutez des adresses pour faciliter vos livraisons et complétez votre profil.'),
			'User/Password/index'			=>	$this->translate('Gérez et/ou modifiez tout ce qui vous concerne sur cette page.').
												$this->translate('Ajoutez des adresses pour faciliter vos livraisons et complétez votre profil.'),
			'User/Password/edit'			=>	$this->translate('Gérez et/ou modifiez tout ce qui vous concerne sur cette page.').
												$this->translate('Ajoutez des adresses pour faciliter vos livraisons et complétez votre profil.'),
			'User/Password/lost'			=>	$this->translate('Gérez et/ou modifiez tout ce qui vous concerne sur cette page.').
												$this->translate('Ajoutez des adresses pour faciliter vos livraisons et complétez votre profil.'),
			'User/Password/change'			=>	$this->translate('Gérez et/ou modifiez tout ce qui vous concerne sur cette page.').
												$this->translate('Ajoutez des adresses pour faciliter vos livraisons et complétez votre profil.'),
			
			/**
			*	WISHLIST
			*/
			'Wishlist/Index/unconvert'		=>	$this->translate('Convertissez votre liste de souhaits en panier'),
			'Wishlist/Index/convert'		=>	$this->translate('Convertissez votre panier en liste de souhaits'),
			'Wishlist/Index/index'			=>	$this->translate('Mes listes de souhaits'),
			'Wishlist/Index/plus'			=>	$this->translate('Plus'),
			'Wishlist/Index/min'			=>	$this->translate('Moins'),
			'Wishlist/Index/replace'		=>	$this->translate('Remplacer'),
			'Wishlist/Index/show'			=>	$this->translate('Ma liste de souhaits %s'),
			'Wishlist/Index/delete'			=>	$this->translate('Supprimer la liste de souhaits %s'),
			'Wishlist/Index/remove'			=>	$this->translate('Supprimer un produit de la liste de souhaits'),
					
			/**
			*	CART
			*/			
			'Cart/Index/show'				=>	$this->translate('Mon panier'),
			'Cart/Index/add'				=>	$this->translate(''),
			'Cart/Index/empty'				=>	$this->translate(''),
			'Cart/Index/min'				=>	$this->translate(''),
			'Cart/Index/plus'				=>	$this->translate(''),
			'Cart/Index/delete'				=>	$this->translate(''),
			'Cart/Index/replace'			=>	$this->translate(''),
			'Cart/Index/success'			=>	$this->translate(''),
			'Cart/Index/error'				=>	$this->translate(''),
			'Cart/Index/cancel'				=>	$this->translate(''),
		));
	}
	
	public function setIcons($icons = NULL){
		if($icons !== NULL){
			$this->icons	=	$icons;
		}
		return $this;
	}
	public function getIcons(){
		if($this->icons === NULL){
			$this->setIcons($this->generateIcons());
		}
		return $this->icons;
	}
	public function setIcon($index, $icon){
		$this->getIcons()->offsetSet($index, $icon);
		return $this;
	}
	public function getIcon($index){	
		$icon	=	NULL;
		if($this->getIcons()->offsetExists($index)){
			$icon	=	$this->getIcons()->offsetGet($index);
		}
		return $icon;
	}
	public function generateIcons(){
		$size	=	'fa-3x';
		
		return	$this->getArrayObject(array(
			/**
			*	ADDRESS 
			*/ 
			'Address/Index/index'			=>	'<span class="fa fa-plus '.$size.'"></span><span class="fa fa-map-marker '.$size.'"></span>',
			'Address/Index/add'				=>	'<span class="fa fa-plus '.$size.'"></span> <span class="fa fa-map-marker '.$size.'"></span>',
			'Address/Index/delete'			=>	'<span class="fa fa-trash '.$size.'"></span> <span class="fa fa-map-marker '.$size.'"></span>',
			'Address/Index/prior'			=>	'<span class="fa fa-arrow-up '.$size.'"></span> <span class="fa fa-map-marker '.$size.'"></span>',
			
			/**
			*	ADVISE
			*/
			'Advise/Index/index'			=>	'<span class="fa fa-star-half-o '.$size.'"></span>',
			'Advise/Index/add'				=>	'<span class="fa fa-star-half-o '.$size.'"></span>',
			
			/**
			*	APPLICATION
			*/
			'Application/Index/index'		=>	'<span class="fa fa-home '.$size.'"></span>',
			'Application/Index/cgv'			=>	'<span class="fa fa-gavel '.$size.'"></span>',
			'Application/Index/who'			=>	'<span class="fa fa-question '.$size.'"></span>',
			
			/**
			*	CATEGORY
			*/
			'Category/Index/index'			=>	'<span class="fa fa-th '.$size.'"></span>',
			'Category/Index/categories'		=>	'<span class="fa fa-th '.$size.'"></span>',
			
			/**
			*	CONTACT
			*/
			'Contact/Index/index'			=>	'<span class="fa fa-phone '.$size.'"></span>',
			'Contact/Index/send'			=>	'<span class="fa fa-phone '.$size.'"></span>',
			
			/**
			*	TICKET
			*/
			'Ticket/Index/index'			=>	'<span class="fa fa-tags '.$size.'"></span>',
			'Ticket/Index/ticket'			=>	'<span class="fa fa-tags '.$size.'"></span>',
			
			/**
			*	DISCOUNT 
			*/
			'Discount/Index/index'			=>	'<span class="fa fa-tags '.$size.'"></span>',
			'Discount/Index/ticket'			=>	'<span class="fa fa-tags '.$size.'"></span>',
			
			/**
			*	EMAIL
			*/
			'Email/Index/show'				=>	'<span class="fa fa-envelope '.$size.'"></span>',
			
			/**
			*	GARDE
			*/
			'Garde/Index/index'				=>	'<span class="fa fa-shield '.$size.'"></span>',
			
			/**
			*	MAGAZINE
			*/
			'Magazine/Index/index'			=>	'<span class="fa fa-heart '.$size.'"></span>',
			'Magazine/Index/rubriques'		=>	'<span class="fa fa-newspaper-o '.$sier.'"></span>',

			/**
			*	NEWSLETTER
			*/
			'Newsletter/Index/index'		=>	'<span class="fa fa-bullhorn '.$size.'"></span>',
			'Newsletter/Index/subscribe'	=>	'<span class="fa fa-bullhorn '.$size.'"></span>',
			'Newsletter/Index/unsubscribe'	=>	'<span class="fa fa-bullhorn '.$size.'"></span>',
			
			/**
			*	ORDER
			*/
			'Order/Index/index'				=>	'<span class="fa fa-archive '.$size.'"></span>',
			'Order/Index/delete'			=>	'<span class="fa fa-trash '.$size.'"></span> <span class="fa fa-archive '.$size.'"></span>',
			'Order/Index/ticket'			=>	'<span class="fa fa-file-text-o '.$size.'"></span>',
			'Order/Index/download'			=>	'<span class="fa fa-download '.$size.'"></span> <span class="fa fa-file-text-o '.$size.'"></span>',
			'Order/Index/tickets'			=>	'<span class="fa fa-file-text-o '.$size.'"></span>',
			
			/**
			*	PDF 
			*/ 
			
			/**
			*	PAYPAL
			*/
			
			/**
			*	POINT
			*/
			'Point/Index/index'				=>	'<span class="fa fa-star '.$size.'"></span>',
			
			/**
			*	PRODUCT
			*/
			'Product/Index/index'			=>	'<span class="fa fa-tag '.$size.'"></span>',
			'Product/Index/search'			=>	'<span class="fa fa-tag '.$size.'"></span>',
			
			/**
			*	PROPOSER
			*/
			'Proposer/Index/index'			=>	'<span class="fa fa-chains '.$size.'"></span>',
			
			/**
			* 	SEARCH
			*/
			'Search/Index/products'			=>	'<span class="fa fa-search '.$size.'"></span>',
			'Search/Index/city'				=>	'<span class="fa fa-search '.$size.'"></span>',
			'Search/Index/street'			=>	'<span class="fa fa-search '.$size.'"></span>',
			
			/**
			*	SHARE
			*/
			'Share/Index/index'				=>	'<span class="fa fa-share '.$size.'"></span> <span class="fa fa-users '.$size.'"></span>',
			'Share/Index/show'				=>	'<span class="fa fa-eye '.$size.'"></span>',
			
			/**
			*	URGENCY 
			*/ 
			'Urgency/Index/index'			=>	'<span class="fa fa-ambulance '.$size.'"></span>',
			
			/**
			*	USER 
			*/ 
			'User/Index/index'				=>	'<span class="fa fa-user '.$size.'"></span>',
			'User/Index/profil'				=>	'<span class="fa fa-user '.$size.'"></span>',
			'User/Index/signin'				=>	'<span class="fa fa-pencil-square-o '.$size.'"></span>',
			'User/Index/edit'				=>	'<span class="fa fa-edit '.$size.'"></span>',
			'User/Index/signout'			=>	'<span class="fa fa-user '.$size.'"></span> <span class="fa fa-trash '.$size.'"></span>',
			'User/Index/logout'				=>	'<span class="fa fa-sign-out '.$size.'"></span>',
			'User/Index/login'				=>	'<span class="fa fa-sign-in '.$size.'"></span>',
			'User/Password/index'			=>	'<span class="fa fa-lock '.$size.'"></span>',
			'User/Password/edit'			=>	'<span class="fa fa-lock '.$size.'"></span>',
			'User/Password/lost'			=>	'<span class="fa fa-question '.$size.'"></span> <span class="fa fa-lock '.$size.'"></span>',
			'User/Password/change'			=>	'<span class="fa fa-refresh '.$size.'"></span> <span class="fa fa-lock '.$size.'"></span>',
			
			/**
			*	WISHLIST
			*/
			'Wishlist/Index/unconvert'		=>	'<span class="fa fa-share '.$size.'"></span> <span class="fa fa-shopping-cart '.$size.'"></span>',
			'Wishlist/Index/convert'		=>	'<span class="fa fa-share '.$size.'"></span> <span class="fa fa-magic '.$size.'"></span>',
			'Wishlist/Index/index'			=>	'<span class="fa fa-magic '.$size.'"></span>',
			'Wishlist/Index/plus'			=>	'<span class="fa fa-plus '.$size.'"></span>',
			'Wishlist/Index/min'			=>	'<span class="fa fa-min '.$size.'"></span>',
			'Wishlist/Index/replace'		=>	'<span class="fa fa-rotate-right '.$size.'"></span>',
			'Wishlist/Index/show'			=>	'<span class="fa eye-open '.$size.'"></span> <span class="fa fa-magic '.$size.'"></span>',
			'Wishlist/Index/delete'			=>	'<span class="fa fa-trash '.$size.'"></span> <span class="fa fa-magic '.$size.'"></span>',
			'Wishlist/Index/remove'			=>	'<span class="fa fa-trash '.$size.'"></span> <span class="fa fa-magic '.$size.'"></span>',		
		));
	}	
}