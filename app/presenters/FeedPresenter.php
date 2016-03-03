<?php

namespace App\Presenters;

use Nette;
use Nette\Application\Responses\TextResponse;
use RssCleaner\FeedsList;

class FeedPresenter extends Nette\Application\UI\Presenter
{

	/** @var FeedsList @inject */
	public $feeds;

	public function actionDefault($feed)
	{
		if (!$this->feeds->hasByName($feed)) {
			$this->error();
		}

		$feed = $this->feeds->getByName($feed);
		$rss = $feed->toRss();

		$rssContent = $rss->execute();
		$this->getHttpResponse()->setContentType('text/xml', 'utf-8');
		$this->sendResponse(new TextResponse($rssContent));
	}

}
