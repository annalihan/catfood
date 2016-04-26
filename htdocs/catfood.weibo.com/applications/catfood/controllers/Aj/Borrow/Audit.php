
<?php
/**
 * 图书审核相关接口
 *
 */
class Aj_Borrow_AuditController extends AbstractController
{
	const FAILURE = -1;

	/**
	*审核还书
	*@param int $book_id 图书ID
	*@param int $detail_call_number 图书detailID
	*@return Json 审核信息
	*/
	private function _auditBook($book_id, $detail_call_number)
	{
		$reader = $_SESSION['reader'];
		if(isset($reader))
		{
			$data = Model_Catfood_Borrow::auditBook($book_id, $detail_call_number, $reader);
			Tool_Jsout::normal('100000', '', $data);
		}
		else
			Tool_Jsout::normal('100000', '', '');
	}

	public function run()
	{
		$book_id = Comm_Context::get('book_id');
		$detail_call_number = Comm_Context::get('detail_call_number');
		$this->_auditBook($book_id, $detail_call_number);
	}
}
