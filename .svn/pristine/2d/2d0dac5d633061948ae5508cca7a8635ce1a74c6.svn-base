<?php
/**
 * 所有图书列表页
 *author @tingting33
 */
class Aj_Booklist_BooklistController extends AbstractController
{
    const NOT_LOGIN = 1;
    const MUST_CHECK_SESSION = false;
    public $authorizeType = self::NOT_LOGIN;
    public function booklist()
    {
        $model = new Model_Catfood_Allbooklist();
        $book_type = Comm_Context::param('book_type','');
        $page = Comm_Context::param('page','1');
        $size = Comm_Context::param('size','40');
        $key = Comm_Context::param('key','');
        $get = array(
            'book_type' => $book_type,
            'page' =>$page,
            'pagesize' =>$size,
            'key' => $key
        );
        $data = $model ->getBooklist($get);
        Tool_Jsout::normal('100000', '', $data);

    }
    public function run()
    {
        $this->booklist();
    }
}