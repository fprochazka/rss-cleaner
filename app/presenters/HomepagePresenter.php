<?php

namespace App\Presenters;

use Nette;
use Nette\Application\Responses\TextResponse;

class HomepagePresenter extends Nette\Application\UI\Presenter
{

	public function actionDefault()
	{
		$this->sendResponse(new TextResponse('hello world'));
	}

}
