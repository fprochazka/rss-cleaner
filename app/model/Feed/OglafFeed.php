<?php

namespace RssCleaner\Feed;

use Atrox\Matcher;
use Nette\Http\Url;
use PicoFeed\Parser\Item;
use PicoFeed\Scraper\Scraper;
use RssCleaner\Feed;

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class OglafFeed extends Feed
{

	protected function processItem(Item $item)
	{
		$grabber = $this->scrapeItem($item->getUrl());

		$item->content = $this->withNextPage($grabber, $item);

		return $item;
	}

	private function withNextPage(Scraper $grabber, Item $firstPageItem)
	{
		$content = NULL;
		if ($grabber->hasRelevantContent()) {
			$content = $grabber->getFilteredContent();
		}

		$matcher = Matcher::single('.//div[@id="nav"]', [
			'nextPage' => './a/div[@id="nx"]/../@href',
			'nextStory' => './a/div[@id="ns"]/../@href',
		])->fromHtml();

		$navigation = $matcher($grabber->getRawContent());

		if ($navigation['nextPage'] !== null && $navigation['nextPage'] !== $navigation['nextStory']) {
			$url = new Url($firstPageItem->getUrl());
			$url->path = $navigation['nextPage'];

			$content .= "\n\n" . $this->withNextPage($this->scrapeItem((string) $url), $firstPageItem);
		}

		return $content;
	}

}
