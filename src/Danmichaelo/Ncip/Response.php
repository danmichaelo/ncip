<?php namespace Danmichaelo\Ncip;

class Response {

	protected $dom;

	public function parseDateTime($datestring) {
		return new \DateTime($datestring);
	}

	public function formatDateTime($datetime) {
		return $datetime->format(\DateTime::ISO8601); // does not include milliseconds
	}

	protected function parseBibliographicDescription($b)
	{
		$a = array(
			'title' => $b->text('ns1:Title'),
			'author' => $b->text('ns1:Author'),
			'publicationDate' => $b->text('ns1:PublicationDate'),
			'publisher' => $b->text('ns1:Publisher'),
			'objektid' => $b->text('ns1:BibliographicRecordId/ns1:BibliographicRecordIdentifier'),
			'edition' => $b->text('ns1:Edition'),
			'pagination' => $b->text('ns1:Pagination'),
			'language' => $b->text('ns1:Language'),
			'mediumType' => $b->text('ns1:MediumType')
		);
		return $a;
	}

}
