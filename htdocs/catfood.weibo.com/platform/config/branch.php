<?php
//小流量配置
return array(
    'enabled' => false,
    'white' => array(
        'viewer' => array(
            'normal' => array(
                //1763952531 => true,
            ),
            'mod' => array(
                //array('div' => 10, 'value' => 1),
            ),
            'attr' => array(),
        ),
        'owner' => array(
            'normal' => array(),
            'mod' => array(),
            'attr' => array(),
        ),
        'ip' => array(
            'area' => array(),
            'list' => array(),
        ),
    ),
    'black' => array(
        'viewer' => array(
            'normal' => array(),
            'mod' => array(),
            'attr' => array(),
        ),
        'owner' => array(
            'normal' => array(),
            'mod' => array(),
            'attr' => array(),
        ),
        'ip' => array(
            'area' => array(),
            'list' => array(),
        ),
    ),
);