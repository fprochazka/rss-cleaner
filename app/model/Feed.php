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

	/** @var int */
	private $maxItems;

	public function __construct($name, $url, Config $config, $maxItems = 10)
	{
		$this->name = $name;
		$this->url = $url;
		$this->config = $config;
		$this->reader = new Reader($config);
		$this->maxItems = $maxItems;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return \RssCleaner\Channel
	 */
	public function toRss()
	{
		$sourceFeed = $this->parse($this->reader->download($this->url));
		$rss = $this->sourceFeedToRss($sourceFeed);
		$rss->items = array_filter($rss->items); // remove empty items

		return $rss;
	}

	/**
	 * @param \PicoFeed\Parser\Feed $sourceFeed
	 * @return \RssCleaner\Channel
	 */
	protected function sourceFeedToRss(PicoFeed\Parser\Feed $sourceFeed)
	{
		$rss = new Channel();
		$rss->title = $this->name ?: $sourceFeed->getTitle();
		$rss->site_url = $sourceFeed->site_url;
		$rss->feed_url = $sourceFeed->feed_url;

		/** @var Item $sourceItem */
		foreach ($sourceFeed->getItems() as $sourceItem) {
			$item = $this->itemToArray($this->processItem($sourceItem));
			$rss->items[] = array_filter($item, function ($v) {
				return $v !== null && $v !== '';
			});

			if (count($rss->items) >= $this->maxItems) {
				break; // no more
			}
		}

		return $rss;
	}

	/**
	 * @param \PicoFeed\Parser\Item $item
	 * @return \PicoFeed\Parser\Item
	 */
	protected function processItem(Item $item)
	{
		$grabber = $this->scrapeItem($item->getUrl());
		if ($grabber->hasRelevantContent()) {
			$item->content = $grabber->getFilteredContent();
		}
		return $item;
	}

	/**
	 * @param string $url
	 * @return \PicoFeed\Scraper\Scraper
	 */
	protected function scrapeItem($url)
	{
		$grabber = new Scraper($this->config);
		$grabber->setUrl($url);
		$grabber->execute();

		return $grabber;
	}

	protected function itemToArray(Item $item)
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

	/**
	 * @param \PicoFeed\Client\Client $resource
	 * @return \PicoFeed\Parser\Feed
	 */
	protected function parse(Client $resource)
	{
		$parser = $this->reader->getParser(
			$resource->getUrl(),
			$resource->getContent(),
			$resource->getEncoding()
		);

		return $parser->execute();
	}

}
