<?php
return array(
    
    'PdoMysql' => array(
        
        'catfood' => array(
            
            'write' => array(
                'host' => isset($_SERVER['SINASRV_CATFOOD_HOST']) ? $_SERVER['SINASRV_CATFOOD_HOST'] : '10.210.238.190',
                'port' => isset($_SERVER['SINASRV_CATFOOD_PORT']) ? $_SERVER['SINASRV_CATFOOD_PORT'] : 3306 ,
                'name' => isset($_SERVER['SINASRV_CATFOOD_NAME']) ? $_SERVER['SINASRV_CATFOOD_NAME'] : 'catfood',
                'user' => isset($_SERVER['SINASRV_CATFOOD_USER']) ? $_SERVER['SINASRV_CATFOOD_USER'] : 'buting',
                'pass' => isset($_SERVER['SINASRV_CATFOOD_PASS']) ? $_SERVER['SINASRV_CATFOOD_PASS'] : '123456'
//                'host' => $_SERVER['SINASRV_CATFOOD_HOST'],
//                'port' => $_SERVER['SINASRV_CATFOOD_PORT'],
//                'name' => $_SERVER['SINASRV_CATFOOD_NAME'],
//                'user' => $_SERVER['SINASRV_CATFOOD_USER'],
//                'pass' => $_SERVER['SINASRV_CATFOOD_PASS']
//                'pass' => $_SERVER['SINASRV_CATFOOD_PASS']
            ),
            'read' => array(
                'host' => isset($_SERVER['SINASRV_CATFOOD_HOST']) ? $_SERVER['SINASRV_CATFOOD_HOST'] : '10.210.238.190',
                'port' => isset($_SERVER['SINASRV_CATFOOD_PORT']) ? $_SERVER['SINASRV_CATFOOD_PORT'] : 3306 ,
                'name' => isset($_SERVER['SINASRV_CATFOOD_NAME']) ? $_SERVER['SINASRV_CATFOOD_NAME'] : 'catfood',
                'user' => isset($_SERVER['SINASRV_CATFOOD_USER']) ? $_SERVER['SINASRV_CATFOOD_USER'] : 'buting',
                'pass' => isset($_SERVER['SINASRV_CATFOOD_PASS']) ? $_SERVER['SINASRV_CATFOOD_PASS'] : '123456'
//                'host' => $_SERVER['SINASRV_CATFOOD_HOST_R'],
//                'port' => $_SERVER['SINASRV_CATFOOD_PORT_R'],
//                'name' => $_SERVER['SINASRV_CATFOOD_NAME_R'],
//                'user' => $_SERVER['SINASRV_CATFOOD_USER_R'],
//                'pass' => $_SERVER['SINASRV_CATFOOD_PASS_R']
            )
        )
    )
);
