<?php

namespace RssCleaner;

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class FeedsList
{

	/** @var Feed[]  */
	private $feeds = [];

	public function add($alias, Feed $feed)
	{
		$this->feeds[$alias] = $feed;
	}

	/**
	 * @param string $alias
	 * @return bool
	 */
	public function hasByName($alias)
	{
		return array_key_exists($alias, $this->feeds);
	}

	/**
	 * @param string $alias
	 * @return Feed
	 */
	public function getByName($alias)
	{
		return $this->feeds[$alias];
	}

}
