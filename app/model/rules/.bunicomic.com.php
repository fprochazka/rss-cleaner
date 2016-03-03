<?php

return array(
    'grabber' => array(
        '%.*\\/hapi-buni\\/.*%' => array(
            'test_url' => 'http://www.webtoons.com/en/comedy/hapi-buni/ep-65/viewer?title_no=362&episode_no=65',
            'body' => array(
                '//div[@id="_imageList"]',
            ),
            'strip' => array(
            ),
        ),
        '%.*%' => array(
            'test_url' => 'http://www.bunicomic.com/comic/buni-623/',
            'body' => array(
                '//div[@id="comic"]',
            ),
            'strip' => array(
            ),
        ),
    ),
);
