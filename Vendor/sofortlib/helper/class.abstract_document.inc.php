<?php
/// \cond
/**
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.3.0  $Id: class.abstract_document.inc.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 * @internal
 *
 */
class PnagAbstractDocument {

	var $shopArticles = array();
	var $pnagArticles = array();

	var $customer = null;
	var $currency = '';

	/**
	 * @deprecated - use $invoice->getAmount(); instead
	 * includes the total amount of all pnag-articles
	 */
	var $total = 0;

	/**
	 * includes the total amount of all shop-articles
	 */
	var $shopTotal = 0;


	/**
	 * deletes all existing Pnag-Articles
	 * @private
	 */
	function _deleteLocalPnagArticles () {
		$this->pnagArticles = array();
	}


	/**
	 * puts the given article into $this->shopArticles
	 * should only be used for the articles from the shopsystem
	 * @todo change VAT according to legislation
	 */
	function setShopArticle($item_id, $product_number = 0, $product_type = '-', $title = '', $description = '', $quantity = 0, $unit_price = '', $tax = '19') {
		array_push($this->shopArticles, new PnagArticle($item_id, $product_number, $product_type, $title, $description, $quantity, $unit_price, $tax));
		$this->shopArticles = $this->_deleteDuplicates($this->shopArticles);
		$this->_calcShopTotal();
		return $this;
	}


	/**
	 * searches in the before given shoparticles for the highest tax and returns it
	 * @return int/float - highest found taxvalue e.g. 0 or 7 or 19...
	 */
	function getHighestShoparticleTax() {
		$highestTax = 0;
		foreach ($this->shopArticles as $shopArticle) {
			if ($shopArticle->getTax() > $highestTax) {
				$highestTax = $shopArticle->getTax();
			}
		}
		return $highestTax;
	}


	/**
	 * puts the given article into $this->pnagArticles
	 * should only be used for the articles from the pnag-response
	 * @todo change VAT according to legislation
	 */
	function setPnagArticle($item_id, $product_number = 0, $product_type = '-', $title = '', $description = '', $quantity = 0, $unit_price = '', $tax = '19') {
		array_push($this->pnagArticles, new PnagArticle($item_id, $product_number, $product_type, $title, $description, $quantity, $unit_price, $tax));
		$this->pnagArticles = $this->_deleteDuplicates($this->pnagArticles);
		$this->_calcPnagTotal();
		return $this;
	}


	/**
	 * delete double-saved Articles with the help of the itemId
	 * @param array $articles with $article-objects
	 * @internal
	 * @private
	 */
	function _deleteDuplicates($articles) {
		$itemIds = array();
		foreach ($articles as $key => $article) {
			if ( in_array ($article->getItemId(), $itemIds) ) {
				unset ( $articles [$key]);
			} else {
				$itemIds[] = $article->getItemId();
			}
		}
		return $articles;
	}


	/**
	 * Set the customer's credentials
	 * @param $name	string
	 * @param $lastname string
	 * @param $firstname string
	 * @param $company string
	 * @param $csID string customer id in shop
	 * @param $vat_id string - customer's VAT ID
	 * @param $shop_id - shop's ID
	 * @param $ID
	 * @param $cIP
	 * @param $street_address string
	 * @param $suburb string
	 * @param $city string
	 * @param $postcode string
	 * @param $state string
	 * @param $country	string
	 * @param $format_id string
	 * @param $telephone string
	 * @param $email_address string
	 */
	function setCustomer($name = '', $lastname = '', $firstname = '', $company = '', $csID = '', $vat_id = '', $shop_id = '', $ID = '', $cIP = '', $street_address = '', $suburb = '', $city = '', $postcode = '', $state = '', $country = '', $format_id = '', $telephone = '', $email_address = '') {
		$this->customer = new PnagCustomer($name, $lastname, $firstname, $company, $csID, $vat_id, $shop_id, $ID, $cIP, $street_address, $suburb, $city, $postcode, $state, $country, $format_id, $telephone, $email_address);
		return $this;
	}


	/**
	 *
	 * Setter for currency
	 * @param $currency string
	 */
	function setCurrency($currency) {
		$this->currency = $currency;
		return $this;
	}


	/**
	 * Calculate the total amount of the pnagarticles
	 * @deprecated because $this->total is deprecated
	 * @private
	 * @return object
	 */
	function _calcPnagTotal() {
		$this->total = 0;
		foreach($this->pnagArticles as $pnagArticle) {
			$this->total += $pnagArticle->unit_price * $pnagArticle->quantity;
		}
		return $this;
	}


	/**
	 * Calculate the total amount of the shoparticles
	 * @private
	 * @return $object
	 */
	function _calcShopTotal() {
		$this->shopTotal = 0;
		foreach($this->shopArticles as $shopArticle) {
			$this->shopTotal += $shopArticle->unit_price * $shopArticle->quantity;
		}
		return $this;
	}


	/**
	 * get the total amount of the shoparticles
	 */
	function getShopTotal() {
		return $this->shopTotal;
	}


	/**
	 * @deprecated better: check the shop-endprice against the pnag-endprice and set the difference as an article with name "Discount"/"Agio"
	 *
	 * check the shopTotal against the invoiceTotal
	 * if there is more than 1% difference this function returns false
	 * call when ALL article are given to the invoice-class
	 * @param float $totalInShop the total of the shopsystem
	 * @param float $pnagTotal use the given value for comparing ELSE it trys to use the invoice-total
	 *
	 * @return true if check is ok ELSE false if not in range of tolerance
	 */
	function checkShopTotalVsInvoiceTotal ($totalInShop, $pnagTotal = -1){
		if ($pnagTotal == -1) {
			$pnagTotal = $this->getShopTotal();
		}

		if ($totalInShop < $pnagTotal) {
			$percent = $totalInShop/$pnagTotal;
		} else {
			$percent = $pnagTotal/$totalInShop;
		}

		if ($percent < 0.99) {
			return false;
		} else {
			return true;
		}
	}
}


/**
 *
 * Data object that encapsulates user's data
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * $ID$
 *
 */
class PnagCustomer {
	var $name = '';
	var $lastname = '';
	var $firstname = '';
	var $company = '';
	var $csID = '';
	var $vat_id = '';
	var $shop_id = '';
	var $ID = '';
	var $cIP = '';
	var $street_address = '';
	var $suburb = '';
	var $city = '';
	var $postcode = '';
	var $state = '';
	var $country = '';
	var $format_id = '';
	var $telephone = '';
	var $email_address = '';


	/**
	 * Set the customer's credentials
	 * @param $name	string
	 * @param $lastname string
	 * @param $firstname string
	 * @param $company string
	 * @param $csID string customer id in shop
	 * @param $vat_id string - customer's VAT ID
	 * @param $shop_id - shop's ID
	 * @param $ID
	 * @param $cIP
	 * @param $street_address string
	 * @param $suburb string
	 * @param $city string
	 * @param $postcode string
	 * @param $state string
	 * @param $country	string
	 * @param $format_id string
	 * @param $telephone string
	 * @param $email_address string
	 */
	function PnagCustomer($name = '', $lastname = '', $firstname = '', $company = '', $csID = '', $vat_id = '', $shop_id = '', $ID = '', $cIP = '', $street_address = '', $suburb = '', $city = '', $postcode = '', $state = '', $country = '', $format_id = '', $telephone = '', $email_address = '') {
		$this->name = $name;
		$this->lastname = $lastname;
		$this->firstname = $firstname;
		$this->company = $company;
		$this->csID = $csID;
		$this->vat_id = $vat_id;
		$this->shop_id = $shop_id;
		$this->ID = $ID;
		$this->cIP = $cIP;
		$this->street_address = $street_address;
		$this->suburb = $suburb;
		$this->city = $city;
		$this->postcode = $postcode;
		$this->state = $state;
		$this->country = $country;
		$this->format_id = $format_id;
		$this->telephone = $telephone;
		$this->email_address = $email_address;
	}
}


/**
 *
 * Data object that encapsulates article's data
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * $ID$
 *
 */
class PnagArticle {

	var $item_id = '';
	var $product_number = '';
	var $product_type = '';
	var $title = '';
	var $description = '';
	var $quantity = '';
	var $unit_price = '';
	var $tax = '';
	var $differenceFound = 0; //needed for comparison of response-article and shop-article
	var $isDeleted = 0;  //used, if article will be deleted in shopsystem later


	/**
	 * Constructor
	 * @param $item_id int
	 * @param $product_number string
	 * @param $product_type string
	 * @param $title string
	 * @param $description string
	 * @param $quantity int
	 * @param $unit_price float
	 * @param $tax float
	 */
	function PnagArticle($item_id, $product_number, $product_type, $title, $description, $quantity, $unit_price, $tax) {
		$this->item_id = $item_id;
		$this->product_number = $product_number;
		$this->product_type = $product_type;
		$this->title = $title;
		$this->description = $description;
		$this->quantity = $quantity;
		$this->unit_price = $unit_price;
		$this->tax = $tax;
	}


	/**
	 * checks if given params are the same of this article
	 * @return boolean
	 */
	function checkParams ($item_id, $quantity, $unit_price)	{
		if ($this->item_id != $item_id)
			return false;
		if ($this->quantity != $quantity)
			return false;
		if (intval (floatval ($this->unit_price) * 1000) != intval ( floatval ($unit_price) * 1000) )
			return false;

		return true;  //params are equal
	}


	function setDifferenceFound ($value) {
		$this->differenceFound = $value;
	}


	function getDifferenceFound () {
		return $this->differenceFound;
	}


	function getItemId () {
		return $this->item_id;
	}


	function getQuantity () {
		return $this->quantity;
	}


	function setQuantity ($quantity) {
		$this->quantity = $quantity;
	}


	function getUnitPrice () {
		return $this->unit_price;
	}


	function setUnitPrice ($unitPrice) {
		$this->unit_price = $unitPrice;
	}


	function getTitle() {
		return $this->title;
	}


	function getTax() {
		return $this->tax;
	}


	function setTax ($value) {
		$this->tax = $value;
	}


	function setIsDeleted ($value = 1) {
		$this->isDeleted = $value;
	}


	function getIsDeleted () {
		return $this->isDeleted;
	}


	function setProductNumber ($productNumber) {
		$this->product_number = $productNumber;
	}


	function getProductNumber () {
		return $this->product_number;
	}
}
/// \endcond
?>