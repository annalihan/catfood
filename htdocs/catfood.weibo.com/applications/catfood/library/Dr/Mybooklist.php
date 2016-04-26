<?php
/**
 * 读取我的图书列表
 *author @tingting33
 */
class Dr_Mybooklist extends Dr_Abstract{
    public static function getMybooklist($option){
        $db = Comm_Db::connect('catfood');
        $borrow = 'borrow';
        $book_table = 'book';
        $sql = "SET NAMES utf8";
        $db->query($sql);
        $reader_id = $_COOKIE['reader'];

        $data = array();
        $data['bookList'] = array();
        $sql = "SELECT * FROM ".$book_table;
        $book = $db->fetchAll($sql);

        if($reader_id)
        {
            $sql = "SELECT * FROM ".$borrow." WHERE `reader_id` = ? ORDER BY `book_id` LIMIT ".($option['page']-1)*$option['pagesize'].",".$option['pagesize'];
            $borrow = $db->fetchAll($sql,array($reader_id));
            foreach($book as $allbook)
            {
                foreach($borrow as $borrowbook)
                {
                    if($borrowbook['book_id'] != $allbook['book_id'])
                        continue;
                    if($borrowbook['borrow_status'] == 0)//在借的图书
                    {
                        $bookList = array(
                            'book_name' => $allbook['book_name'],
                            'book_id' => $allbook['book_id'],
                            'book_img' => $allbook['book_img'],
                            'book_press' => $allbook['book_press'],
                            'book_author' => $allbook['book_author'],
                            'book_quadrant' => $allbook['book_quadrant_id'],
                            'book_category' => $allbook['book_category_id'],
                            'borrow_date' => $borrowbook['borrow_date'],
                            'borrow_deadline' => $borrowbook['borrow_deadline'],
                            'detail_call_number' => $borrowbook['detail_call_number'],
                            'bookList' => '2',
                            'book_status' => '2'
                        );
                        $data['bookList'][] = $bookList;
                        break;
                    }
                    else if($borrowbook['borrow_status'] == 1)//已经还，等待审核
                    {
                        $bookList = array(
                            'book_name' => $allbook['book_name'],
                            'book_id' => $allbook['book_id'],
                            'book_img' => $allbook['book_img'],
                            'book_press' => $allbook['book_press'],
                            'book_author' => $allbook['book_author'],
                            'book_quadrant' => $allbook['book_quadrant_id'],
                            'book_category' => $allbook['book_category_id'],
                            'borrow_date' => $borrowbook['borrow_date'],
                            'borrow_deadline' => $borrowbook['borrow_deadline'],
                            'detail_call_number' => $borrowbook['detail_call_number'],
                            'bookList' => '2',
                            'book_status' => '1'
                        );
                        $data['bookList'][] = $bookList;
                        break;
                    }
                }
            }
            return $data;
        }
    }
}