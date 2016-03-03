<?php

namespace RssCleaner;

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class FeedsList
{

	/** @var array  */
	private $feeds = [];

	public function add($alias, Feed $feed)
	{
		$this->feeds[$alias] = $feed;
	}

	public function hasByName(string $alias) : bool
	{
		return array_key_exists($alias, $this->feeds);
	}

	public function getByName(string $alias) : Feed
	{
		return $this->feeds[$alias];
	}

}
