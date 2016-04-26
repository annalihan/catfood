<?php
/**
 * 读取图书列表
 *author @tingting33
 */
class Dr_Booklist extends Dr_Abstract{
//    获取全部图书列表
    public static function getBooklist($option){
        $db = Comm_Db::connect('catfood');
        $borrow_tb = 'borrow';
        $book_detail = 'book_detail';
        $book_table = 'book';
        $sql = "SET NAMES utf8";
        $db->query($sql);
        $reader_id = $_COOKIE['reader'];
        $data = array();
        $data['bookList'] = array();

        if($reader_id)
        {
            $sql = "SELECT * FROM ".$borrow_tb." WHERE `reader_id` = ?";
            $borrow = $db->fetchAll($sql, array($reader_id));
            if ($option['book_type'])
            {
                //指定类别
                $sql = "SELECT * FROM ".$book_table." WHERE `book_category_id` = ? ORDER BY `book_id` LIMIT ".($option['page']-1)*$option['pagesize'].",".$option['pagesize'];
                $book = $db->fetchAll($sql, array($option['book_type']));
            }
            else
            {
                $sql = "SELECT * FROM ".$book_table." ORDER BY `book_id` LIMIT ".($option['page']-1)*$option['pagesize'].",".$option['pagesize'];
                $book = $db->fetchAll($sql, array($option['book_type']));
            }

            if ($option['key'])
            {
                //有搜索
                $sql = "SELECT * FROM ".$book_table." WHERE `book_name` LIKE ? OR `book_press` LIKE ? OR `book_author` LIKE ? ORDER BY `book_id` LIMIT ".($option['page']-1)*$option['pagesize'].",".$option['pagesize'];
                $book = $db->fetchAll($sql, array("%" . $option['key'] . "%" , "%" . $option['key'] . "%", "%" . $option['key'] . "%"));
            }

            foreach ($book as $allbook)
            {
                $bookList = array(
                    'book_id' => $allbook['book_id'],
                    'book_name' => $allbook['book_name'],
                    'book_img' => $allbook['book_img'],
                    'book_press' => $allbook['book_press'],
                    'book_author' => $allbook['book_author'],
                    'book_quadrant' => $allbook['book_quadrant_id'],
                    'book_category' => $allbook['book_category_id']
                );
                $sql = "SELECT * FROM ".$book_detail." WHERE `book_id` = ?";
                $detail = $db->fetchAll($sql, array($allbook['book_id']));
                $bookList['detail_call_number'] = $detail[0]['detail_call_number'];
                if($borrow)
                {
                    foreach($borrow as $borrowbook)
                    {
                        if($borrowbook['book_id'] == $allbook['book_id'])//借过的书：借/审/还/缺
                        {
                            if($borrowbook['borrow_status'] == 1)//已经还，未审核通过，显示审核
                                $bookList['book_status'] = '1';
                            else if(!$allbook['book_total_num'])//缺
                                $bookList['book_status'] = '3';
                            else if($borrowbook['borrow_status'] == 0)//在借未还，显示还
                                $bookList['book_status'] = '2';
                            else if($borrowbook['borrow_status'] == 2)//已经还，已经审核，显示借书
                                $bookList['book_status'] = '0';
                            break;
                        }
                        else//还没借过的书：借/缺
                        {
                            if(!$allbook['book_total_num'])
                                $bookList['book_status'] = '3';
                            else
                                $bookList['book_status'] = '0';
                        }
                    }
                }
                else
                {
                    if(!$allbook['book_total_num'])
                        $bookList['book_status'] = '3';
                    else
                        $bookList['book_status'] = '0';
                }
                $data['bookList'][] = $bookList;
            }
            return $data;
        }
        else//用户未登录
        {
            if($option['book_type'])
            {
                //指定类别
                $sql = "SELECT * FROM ".$book_table." WHERE `book_category_id` = ? ORDER BY `book_id` LIMIT ".($option['page']-1)*$option['pagesize'].",".$option['pagesize'];
                $book = $db->fetchAll($sql,array($option['book_type']));
            }
            else
            {
                $sql = "SELECT * FROM ".$book_table." ORDER BY `book_id` LIMIT ".($option['page']-1)*$option['pagesize'].",".$option['pagesize'];
                $book = $db->fetchAll($sql,array($option['book_type']));
            }

            if ($option['key'])
            {
                //有搜索
                $sql = "SELECT * FROM ".$book_table." WHERE `book_name` LIKE ? OR `book_press` LIKE ? OR `book_author` LIKE ? ORDER BY `book_id` LIMIT ".($option['page']-1)*$option['pagesize'].",".$option['pagesize'];
                $book = $db->fetchAll($sql, array("%" . $option['key'] . "%" , "%" . $option['key'] . "%", "%" . $option['key'] . "%"));
            }
            foreach($book as $allbook)
            {
                $bookList = array(
                    'book_id' => $allbook['book_id'],
                    'book_name' => $allbook['book_name'],
                    'book_img' => $allbook['book_img'],
                    'book_press' => $allbook['book_press'],
                    'book_author' => $allbook['book_author'],
                    'book_quadrant' => $allbook['book_quadrant_id'],
                    'book_category' => $allbook['book_category_id']
                );
                if(!$allbook['book_total_num'])
                    $bookList['book_status'] = '3';
                else
                    $bookList['book_status'] = '0';
                $data['bookList'][] = $bookList;
            }
            return $data;
        }
    }
}