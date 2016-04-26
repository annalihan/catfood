<?php


class Page_Index extends Page_Abstract
{
    public $tpl = 'index/index.phtml';

    public $name = 'index';

    public function prepareData()
    {
        return array();
    }

    public function getPageMetaData()
    {

        $data['g_page_title'] = '首页';
        return $data;

    }
}
