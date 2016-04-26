
<?php
/**
 * 图书借阅相关接口
 *
 */
class Aj_Borrow_BorrowController extends AbstractController
{
    const NOT_LOGIN = 1;
    const MUST_CHECK_SESSION = false;
    public $authorizeType = self::NOT_LOGIN;
	const FAILURE = -1;
	/**
	*借书
	*@param int $book_id 图书ID
	*@param int $detail_call_number 图书detailID
	*@return Json 借阅信息
	*/
	private function _borrowBook($book_id, $detail_call_number)
	{
        $reader_id = $_COOKIE['reader'];
        if(isset($reader_id))
		{
			$maxCount = Comm_Context::param('maxCount','3');
			$data = Model_Catfood_Borrow::borrowBook($book_id, $detail_call_number, $reader_id, $maxCount);
			if($data['code'] != self::FAILURE)
				Tool_Jsout::normal('100000', '', $data);
			else
				Tool_Jsout::normal('100000', '', $data);
		}
		else
			Tool_Jsout::normal('100000', '', '');
	}

	public function run()
	{
        $book_id = Comm_Context::param('book_id','1');
        $detail_call_number = Comm_Context::param('detail_call_number','1');
        $this->_borrowBook($book_id, $detail_call_number);
	}
}