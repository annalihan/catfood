
<?php
/**
 * 图书归还相关接口
 *
 */
class Aj_Borrow_ReturnController extends AbstractController
{
	const FAILURE = -1;
    const NOT_LOGIN = 1;
    const MUST_CHECK_SESSION = false;
    public $authorizeType = self::NOT_LOGIN;
	/**
	*还书
	*@param int $book_id 图书ID
	*@param int $detail_call_number 图书detailID
	*@return Json 还书信息
	*/
	private function _returnBook($book_id, $detail_call_number)
	{
		$reader = $_COOKIE['reader'];
		if(isset($reader))
		{
			$data = Model_Catfood_Borrow::returnBook($book_id, $detail_call_number, $reader);
            Tool_Jsout::normal('100000', '', $data);
		}
		else
            Tool_Jsout::normal('100000', '', '');
	}

	public function run()
	{
        $book_id = Comm_Context::param('book_id','1');
        $detail_call_number = Comm_Context::param('detail_call_number','1');
		$this->_returnBook($book_id, $detail_call_number);
	}
}