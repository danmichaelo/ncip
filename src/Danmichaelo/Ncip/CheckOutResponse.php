<?php namespace Danmichaelo\Ncip;
/*
 * (c) Dan Michael O. Heggø (2013)
 *
 * Basic Ncip library. This class currently only implements
 * a small subset of the NCIP services.
 *
 * Example response:
 *
 *	  <ns1:NCIPMessage xmlns:ns1="http://www.niso.org/2008/ncip">
 *	     <ns1:CheckOutItemResponse>
 *	        <ns1:ItemId>
 *	           <ns1:AgencyId>k</ns1:AgencyId>
 *	           <ns1:ItemIdentifierValue>13k040189</ns1:ItemIdentifierValue>
 *	        </ns1:ItemId>
 *	        <ns1:UserId>
 *	           <ns1:AgencyId>k</ns1:AgencyId>
 *	           <ns1:UserIdentifierValue>xxxxxxxxxx</ns1:UserIdentifierValue>
 *	        </ns1:UserId>
 *	        <ns1:DateDue>2013-09-21T18:54:39.718+02:00</ns1:DateDue>
 *	        <ns1:ItemOptionalFields>
 *	           <ns1:BibliographicDescription>
 *	              <ns1:Author>DuCharme, Bob</ns1:Author>
 *	              <ns1:BibliographicRecordId>
 *	                 <ns1:BibliographicRecordIdentifier>11447981x</ns1:BibliographicRecordIdentifier>
 *	                 <ns1:BibliographicRecordIdentifierCode>Accession Number</ns1:BibliographicRecordIdentifierCode>
 *	              </ns1:BibliographicRecordId>
 *	              <ns1:Edition/>
 *	              <ns1:Pagination>XIII, 235 s., ill.</ns1:Pagination>
 *	              <ns1:PublicationDate>2011</ns1:PublicationDate>
 *	              <ns1:Publisher>O'Reilly</ns1:Publisher>
 *	              <ns1:Title>Learning SPARQL : querying and updating with SPARQL 1.1</ns1:Title>
 *	              <ns1:Language>eng</ns1:Language>
 *	              <ns1:MediumType>Book</ns1:MediumType>
 *	           </ns1:BibliographicDescription>
 *	        </ns1:ItemOptionalFields>
 *	        <ns1:Ext>
 *	           <ns1:UserOptionalFields>
 *	              <ns1:UserLanguage>eng</ns1:UserLanguage>
 *	           </ns1:UserOptionalFields>
 *	        </ns1:Ext>
 *	     </ns1:CheckOutItemResponse>
 *	  </ns1:NCIPMessage>
 */

use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;

class CheckOutResponse extends Response {

	public $lang = 'eng';
	public $itemId;
	public $userId;
	public $itemAgencyId;
	public $userAgencyId;
	public $success;
	public $dateDue;
	public $error;
	public $errorDetails;

	protected $args = array('success', 'lang');
	protected $successArgs = array('userId', 'itemId', 'userAgencyId', 'itemAgencyId', 'dateDue');
	protected $failureArgs = array('error');

	protected $template = '
 		  <ns1:NCIPMessage xmlns:ns1="http://www.niso.org/2008/ncip">
 		     <ns1:CheckOutItemResponse>{{main}}
 		      <ns1:Ext>
 		         <ns1:UserOptionalFields>
 		            <ns1:UserLanguage>{{language}}</ns1:UserLanguage>
 		         </ns1:UserOptionalFields>
 		      </ns1:Ext>
 		     </ns1:CheckOutItemResponse>
 		  </ns1:NCIPMessage>';

	protected $template_success = '
	 	        <ns1:ItemId>
	 	           <ns1:AgencyId>{{itemAgencyId}}</ns1:AgencyId>
	 	           <ns1:ItemIdentifierValue>{{itemId}}</ns1:ItemIdentifierValue>
	 	        </ns1:ItemId>
	 	        <ns1:UserId>
	 	           <ns1:AgencyId>{{userAgencyId}}</ns1:AgencyId>
	 	           <ns1:UserIdentifierValue>{{userId}}</ns1:UserIdentifierValue>
	 	        </ns1:UserId>
	 	        <ns1:DateDue>{{dateDue}}</ns1:DateDue>';
	 	        // <ns1:ItemOptionalFields>
	 	        //    <ns1:BibliographicDescription>
	 	        //       <ns1:Author>DuCharme, Bob</ns1:Author>
	 	        //       <ns1:BibliographicRecordId>
	 	        //          <ns1:BibliographicRecordIdentifier>11447981x</ns1:BibliographicRecordIdentifier>
	 	        //          <ns1:BibliographicRecordIdentifierCode>Accession Number</ns1:BibliographicRecordIdentifierCode>
	 	        //       </ns1:BibliographicRecordId>
	 	        //       <ns1:Edition/>
	 	        //       <ns1:Pagination>XIII, 235 s., ill.</ns1:Pagination>
	 	        //       <ns1:PublicationDate>2011</ns1:PublicationDate>
	 	        //       <ns1:Publisher>O\'Reilly</ns1:Publisher>
	 	        //       <ns1:Title>Learning SPARQL : querying and updating with SPARQL 1.1</ns1:Title>
	 	        //       <ns1:Language>eng</ns1:Language>
	 	        //       <ns1:MediumType>Book</ns1:MediumType>
	 	        //    </ns1:BibliographicDescription>
	 	        // </ns1:ItemOptionalFields>';

	protected $template_failure = '
 		      <ns1:Problem>
 		         <ns1:ProblemType>{{error}}</ns1:ProblemType>
 		         <ns1:ProblemDetail>{{errorDetails}}</ns1:ProblemDetail>
 		      </ns1:Problem>';

	/**
	 * Create a new Ncip checkout response
	 *
	 * @param  QuiteSimpleXMLElement  $dom
	 * @return void
	 */
	public function __construct(QuiteSimpleXMLElement $dom = null)
	{
		if (is_null($dom)) return;
		parent::__construct($dom->first('/ns1:NCIPMessage/ns1:CheckOutItemResponse'));

		if ($this->success) {
			$this->userId = $this->dom->text('ns1:UserId/ns1:UserIdentifierValue');
			$this->itemId = $this->dom->text('ns1:ItemId/ns1:ItemIdentifierValue');
			$this->userAgencyId = $this->dom->text('ns1:UserId/ns1:AgencyId');
			$this->itemAgencyId = $this->dom->text('ns1:ItemId/ns1:AgencyId');
			$this->dateDue = $this->parseDateTime($this->dom->text('ns1:DateDue'));
			$x = $this->dom->first('ns1:ItemOptionalFields/ns1:BibliographicDescription');
			if ($x) {
				$this->bibliographic = $this->parseBibliographicDescription($x);
			}
		}

	}

	/**
	 * Return a XML representation of the request
	 */
	public function xml()
	{
		$this->validate();

		$s = $this->template;
		$s = str_replace('{{language}}', $this->lang, $s);
		$s = str_replace('{{main}}', $this->success ? $this->template_success : $this->template_failure, $s);
		if ($this->success) {
			$s = str_replace('{{userId}}', $this->userId, $s);
			$s = str_replace('{{itemId}}', $this->itemId, $s);
			$s = str_replace('{{userAgencyId}}', $this->userAgencyId, $s);
			$s = str_replace('{{itemAgencyId}}', $this->itemAgencyId, $s);
			$s = str_replace('{{dateDue}}', $this->formatDateTime($this->dateDue), $s);
		} else {
			$s = str_replace('{{error}}', $this->error, $s);
			$s = str_replace('{{errorDetails}}', $this->errorDetails, $s);
		}
		return $s;
	}

}
