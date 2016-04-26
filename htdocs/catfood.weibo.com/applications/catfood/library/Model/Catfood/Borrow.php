<?php
class Model_Catfood_Borrow
{
	const BORROW = 0;
	const RETURNBOOK = 2;
	const AUDIT = 1;
	
	const SUCCESS = 0;
	const FAILURE = -1;

	/**
	*借书
	*@param int $book_id 图书ID
	*@param int $detail_call_number 图书detailID
	*@param string $reader_id 借阅者
	*@return array 借阅信息
	*/
	public static function borrowBook($book_id, $detail_call_number, $reader_id, $maxCount)
	{

        $currentBookCount = self::_getBookCount($book_id);//当前图书库存
        $currentBorrowedBookCount = self::_getCurrentBorrowedCount($reader_id);//当前用户可借阅量

        if($currentBookCount <= 0)
        	return array('code' => self::FAILURE, 'msg' =>'该图书已借完');
        if($currentBorrowedBookCount >= $maxCount)
        	return array('code' => self::FAILURE, 'msg' => '已达最大借书量' );

		if(self::_updateBookCount($book_id, self::BORROW))//更新图书库存
    	{
    		if(self::_insertBorrowRecord($book_id,$detail_call_number,$reader_id))// && self::_updateBorrowRecord($book_id, $detail_call_number, $reader_id,self::BORROW))//新增借阅记录
    			return array('code' => self::SUCCESS, 'msg' =>'借阅成功');
    		else
    			return array('code' => self::FAILURE, 'msg' => 'borrow表更新失败');
    	}
    	else
    		return array('code' => self::FAILURE, 'msg' => 'book表更新失败' );
	}

	/**
	*还书
	*@param int $book_id 图书ID
	*@param int $detail_call_number 图书detailID
	*@param string $reader_id 借阅者
	*@return array 还书信息
	*/
	public static function returnBook($book_id, $detail_call_number, $reader_id)
	{
		if(self::_updateBorrowRecord($book_id, $detail_call_number, $reader_id,self::AUDIT))//先只更新借阅记录，书本处于审核状态
			return array('code' => self::SUCCESS, 'msg' =>'还书成功');
		else
			return array('code' => self::FAILURE, 'msg' =>'更新borrow失败');
	}

	/**
	*审核还书
	*@param int $book_id 图书ID
	*@param int $detail_call_number 图书detailID
	*@param string $reader_id 借阅者
	*@return array 审核信息
	*/
	public static function auditBook($book_id, $detail_call_number, $reader_id)
	{
		if(self::_updateBookCount($book_id, self::RETURNBOOK) && self::_updateBorrowRecord($book_id, self::RETURNBOOK))//更新借阅记录图书状态，更新图书库存
			return array('code' => self::SUCCESS, 'msg' =>'审核成功');
		else
			return array('code' => self::FAILURE, 'msg' =>'更新book失败');
	}

	/**
	*获取图书库存
	*@param int $book_id 图书ID
	*@return int 图书库存
	*/
	private static function _getBookCount($book_id)
	{
		$dbCon = Comm_Db::connect('catfood');
		$sqlCom = "select book_total_num from book where book_id=".$book_id;
		return $dbCon->query($sqlCom)->fetchColumn();
	}

	/**
	*更新图书库存
	*@param int $book_id 图书ID
	*@param int $book_status 更新条件：借/还
	*@return int 更新条目数：1/0
	*/
	private static function _updateBookCount($book_id, $book_status)
	{
		$dbCon = Comm_Db::connect('catfood');
		$count=self::_getBookCount($book_id);
		if($book_status == self::BORROW)
			$count -= 1;
		else if($book_status == self::RETURNBOOK)
			$count += 1;
		
		$sqlCom = "update book set book_total_num=:count where book_id=:id";
		$result = $dbCon->prepare($sqlCom);
		
		return $result->execute(array(':count' => $count, ':id' => $book_id ));
	}

	/**
	*新增借阅记录
	*@param int $book_id 图书ID
	*@param int $detail_call_number 图书detailID
	*@param string $reader_id 借阅者
	*@return int 更新条目数：1/0
	*/
	private static function _insertBorrowRecord($book_id, $detail_call_number, $reader_id)
	{
        $dbCon = Comm_Db::connect('catfood');
        $currentTime=date("y-m-d H:i:s",time());
        $deadLineTime=date("y-m-d H:i:s",(strtotime($currentTime)+3600*24*30*3));
        
        $sqlCom="insert into borrow 
        (reader_id,book_id,borrow_date,borrow_deadline,borrow_status,detail_call_number) 
        values (:reader_id,:book_id,:borrow_date,:borrow_deadline,:borrow_status,:detail_call_number)";
        
        $result = $dbCon->prepare($sqlCom);

        return $result->execute(array(':reader_id' => $reader_id, ':book_id' => $book_id, ':borrow_date' => $currentTime, 
        	':borrow_deadline' => $deadLineTime, ':borrow_status' => self::BORROW, ':detail_call_number' => $detail_call_number));
	}	

	/**
	*更新借阅记录
	*@param int $book_id 图书ID
	*@param int $detail_call_number 图书detailID
	*@param string $reader_id 借阅者
	*@return int 更新条目数：1/0
	*/
	private static function _updateBorrowRecord($book_id, $detail_call_number, $reader_id, $book_status)
	{
        $dbCon = Comm_Db::connect('catfood');
        $sqlCom = "update borrow set borrow_status=:book_status where book_id=:book_id and detail_call_number=:detail_call_number and reader_id=:reader_id order by borrow_id desc limit 1";

        $result = $dbCon->prepare($sqlCom);

        return $result->execute(array(':book_status' => $book_status,':book_id' => $book_id, ':detail_call_number' => $detail_call_number, ':reader_id' => $reader_id));
	}

	/**
	*获取当前借阅者已借图书量
	*@param string $reader_id 借阅者
	*@return int 更新条目数：1/0
	*/
	private static function _getCurrentBorrowedCount($reader_id)
	{
		$dbCon = Comm_Db::connect('catfood');
		$sqlCom = "select count(*) from borrow where borrow_status=0 and reader_id='".$reader_id."'";
		return $dbCon->query($sqlCom)->fetchColumn();
	}
}