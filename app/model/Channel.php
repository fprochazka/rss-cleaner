<?php

namespace RssCleaner;

use DOMElement;
use PicoFeed\Syndication\Rss20;

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class Channel extends Rss20
{

	public function addPubDate(DOMElement $xml, $value = 0)
	{
		if ($value instanceof \DateTimeInterface) {
			$value = $value->format('U');
		}

		parent::addPubDate($xml, $value);
	}

}
