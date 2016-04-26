<?php
/**
 * 读取我的借阅历史列表
 *author @tingting33
 */
class Dr_Historybooklist extends Dr_Abstract{
    public static function getHistorybooklist($option){
        $db = Comm_Db::connect('catfood');
        $borrow = 'borrow';
        $book = 'book';
        $sql = "SET NAMES utf8";
        $db->query($sql);
        $reader_id = $_COOKIE['reader'];
        $data = array();
        $data['bookList'] = array();
        $sql = "SELECT * FROM ".$book." ORDER BY `book_id` ";
        $book = $db->fetchAll($sql);
        if($reader_id)
        {
            $sql = "SELECT * FROM ".$borrow." WHERE `reader_id` = ? limit ".($option['page']-1)*$option['pagesize'].",".$option['pagesize'];
            $borrow = $db->fetchAll($sql,array($reader_id));
            //var_dump($borrow);
            foreach($book as $allbook)
            {
                foreach($borrow as $borrowbook)
                {
                    if($borrowbook['book_id'] == $allbook['book_id'] && $borrowbook['borrow_status'] == 2)
                    {

                        $valid = 1;
                        foreach ($borrow as $subBorrowbook)//如果用户借阅了以前借阅的图书，该图书的借阅记录不显示在历史借阅中
                        {
                            if($borrowbook['book_id'] == $subBorrowbook['book_id'] && $subBorrowbook['borrow_status'] != 2)
                            {
                                $valid = 0;
                                break;
                            }
                        }
                        if($valid == 0)//用户借阅了曾经借阅的书籍，该书本不显示在历史借阅中
                            break;

                        $bookList = array(
                            'book_name' => $allbook['book_name'],
                            'book_id' => $allbook['book_id'],
                            'book_img' => $allbook['book_img'],
                            'book_press' => $allbook['book_press'],
                            'book_author' => $allbook['book_author'],
                            'book_quadrant' => $allbook['book_quadrant_id'],
                            'book_category' => $allbook['book_category_id'],
                            'detail_call_number' => $borrowbook['detail_call_number']);
                        if(!$allbook['book_total_num'])
                            $bookList['book_status'] = '3';
                        else
                            $bookList['book_status'] = '0';
                        $data['bookList'][] = $bookList;
                        break;
                    }
                }
            }
            return $data;
        }
    }


}