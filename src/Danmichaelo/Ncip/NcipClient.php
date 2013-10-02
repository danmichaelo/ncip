<?php namespace Danmichaelo\Ncip;
/*
 * (c) Dan Michael O. Heggø (2013)
 *
 * Basic Ncip library. This class currently only implements
 * a small subset of the NCIP services.
 */

use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement,
	Danmichaelo\QuiteSimpleXMLElement\InvalidXMLException;

class NcipClient {

	protected $agency_id;
	protected $connector;
	protected $namespaces;

	/**
	 * Create a new Ncip client
	 *
	 * @param  string  $url
	 * @param  array   $options
	 * @return void
	 */
	public function __construct(NcipConnector $connector = null, $options = array())
	{
		$this->agency_id = array_get($options, 'agency_id', Config::get('ncip::agency_id'));
		$this->connector = $connector ?: new NcipConnector;
		$this->namespaces = array_get($options, 'namespaces',
			array('ns1' => 'http://www.niso.org/2008/ncip'));
	}

	protected function parseResponse($xml)
	{
		if (is_null($xml)) {
			return null;
		}
		try {
			$xml = new QuiteSimpleXMLElement($xml);
		} catch (InvalidXMLException $e) {
			throw new InvalidNcipResponseException('Invalid response received from the NCIP service "' . $this->connector->url . '". Did you configure it correctly?');
		}

		$xml->registerXPathNamespaces($this->namespaces);

		return $xml;
	}


	/**
	 * Lookup user information from user id
	 *
	 * @param  string  $user_id
	 * @return UserResponse
	 */
	public function lookupUser($user_id)
	{
		$request = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
		<ns1:NCIPMessage xmlns:ns1="http://www.niso.org/2008/ncip" ns1:version="http://www.niso.org/schemas/ncip/v2_01/ncip_v2_01.xsd">
			<ns1:LookupUser>
				<ns1:UserId>
					<ns1:UserIdentifierValue>' . $user_id . '</ns1:UserIdentifierValue>
				</ns1:UserId>
				<ns1:LoanedItemsDesired/>
				<ns1:RequestedItemsDesired/>
			</ns1:LookupUser>
		</ns1:NCIPMessage>';

		$response = $this->parseResponse($this->connector->post($request));
		return new UserResponse($response);
	}

	/**
	 * Check out an item to a user
	 *
	 * @param  string  $user_id
	 * @param  string  $item_id
	 * @return CheckOutResponse
	 */
	public function checkOutItem($user_id, $item_id)
	{
		$request = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
			<ns1:NCIPMessage xmlns:ns1="http://www.niso.org/2008/ncip" ns1:version="http://www.niso.org/schemas/ncip/v2_01/ncip_v2_01.xsd">
				<ns1:CheckOutItem>
					<ns1:UserId>
						<ns1:UserIdentifierValue>' . $user_id . '</ns1:UserIdentifierValue>
					</ns1:UserId>
					<ns1:ItemId>
					   <ns1:AgencyId>' . $this->agency_id . '</ns1:AgencyId>
					   <ns1:ItemIdentifierValue>' . $item_id . '</ns1:ItemIdentifierValue>
					</ns1:ItemId>
				</ns1:CheckOutItem>
			</ns1:NCIPMessage>';

		$response = $this->parseResponse($this->connector->post($request));
		return new CheckOutResponse($response);
	}

	/**
	 * Check in an item
	 *
	 * @param  string  $item_id
	 * @return array
	 */
	public function checkInItem($item_id)
	{
		$request = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
			<ns1:NCIPMessage xmlns:ns1="http://www.niso.org/2008/ncip" ns1:version="http://www.niso.org/schemas/ncip/v2_01/ncip_v2_01.xsd">
				<ns1:CheckInItem>
					<ns1:ItemId>
					   <ns1:AgencyId>' . $this->agency_id . '</ns1:AgencyId>
					   <ns1:ItemIdentifierValue>' . $item_id . '</ns1:ItemIdentifierValue>
					</ns1:ItemId>
				</ns1:CheckInItem>
			</ns1:NCIPMessage>';

		$response = $this->parseResponse($this->connector->post($request));
		return new CheckInResponse($response);
	}

	/**
	 * Lookup item information from item id
	 *
	 * @param  string  $item_id
	 * @return ItemResponse
	 */
	public function lookupItem($item_id)
	{
		$request = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
			<ns1:NCIPMessage xmlns:ns1="http://www.niso.org/2008/ncip" ns1:version="http://www.niso.org/schemas/ncip/v2_01/ncip_v2_01.xsd">
				<ns1:LookupItem>
					<ns1:ItemId>
					   <ns1:ItemIdentifierType>Accession Number</ns1:ItemIdentifierType>
					   <ns1:ItemIdentifierValue>' . $item_id . '</ns1:ItemIdentifierValue>
					</ns1:ItemId>
				</ns1:LookupItem>
			</ns1:NCIPMessage>';

		$response = $this->parseResponse($this->connector->post($request));
		return new ItemResponse($response);
	}

}
