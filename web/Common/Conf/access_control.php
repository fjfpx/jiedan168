<?

return array('ACCESS_CONTROL'=>array(
        'default'=>array('all'), // Uh Tender
        'details'=>array(
            'Main\Controller\IndexController' => array('all'),
            'Main\Controller\UcController' => array('nologin'),
            'Main\Controller\JutilController' => array('all'),
            'Main\Controller\UserController' => array('login'),
            'Main\Controller\InformationController' => array('login'),
            'Main\Controller\MessageController' => array('login'),
        )
    ));

