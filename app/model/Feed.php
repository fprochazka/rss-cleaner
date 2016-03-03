<?php

namespace RssCleaner;

use PicoFeed;
use PicoFeed\Client\Client;
use PicoFeed\Config\Config;
use PicoFeed\Parser\Item;
use PicoFeed\Reader\Reader;
use PicoFeed\Scraper\Scraper;

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class Feed
{
	/** @var \PicoFeed\Config\Config */
	protected $config;

	/** @var \PicoFeed\Reader\Reader  */
	protected $reader;

	/** @var string */
	protected $url;

	/** @var string */
	private $name;

	public function __construct(string $name, string $url, Config $config)
	{
		$this->name = $name;
		$this->url = $url;
		$this->config = $config;
		$this->reader = new Reader($config);
	}

	public function getUrl() : string
	{
		return $this->url;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function toRss() : Channel
	{
		$sourceFeed = $this->parse($this->reader->download($this->url));
		$rss = $this->sourceFeedToRss($sourceFeed);
		$rss->items = array_filter($rss->items); // remove empty items

		return $rss;
	}

	protected function sourceFeedToRss(PicoFeed\Parser\Feed $sourceFeed) : Channel
	{
		$rss = new Channel();
		$rss->title = $sourceFeed->getTitle();
		$rss->site_url = $sourceFeed->site_url;
		$rss->feed_url = $sourceFeed->feed_url;

		/** @var Item $sourceItem */
		foreach ($sourceFeed->getItems() as $sourceItem) {
			$item = $this->itemToArray($this->processItem($sourceItem));
			$rss->items[] = array_filter($item, function ($v) {
				return $v !== null && $v !== '';
			});
		}

		return $rss;
	}

	protected function processItem(Item $item) : Item
	{
		$grabber = $this->scrapeItem($item);
		if ($grabber->hasRelevantContent()) {
			$item->content = $grabber->getFilteredContent();
		}
		return $item;
	}

	protected function scrapeItem(Item $item) : Scraper
	{
		$grabber = new Scraper($this->config);
		$grabber->setUrl($item->getUrl());
		$grabber->execute();

		return $grabber;
	}

	protected function itemToArray(Item $item) : array
	{
		return [
			'id' => $item->id,
			'url' => $item->getUrl(),
			'title' => $item->getTitle(),
			'updated' => $item->getDate(),
			'content' => $item->getContent(),
			'author' => [
				'name' => $item->getAuthor(),
			],
		];
	}

	protected function parse(Client $resource) : PicoFeed\Parser\Feed
	{
		$parser = $this->reader->getParser(
			$resource->getUrl(),
			$resource->getContent(),
			$resource->getEncoding()
		);

		return $parser->execute();
	}

}
