<?php
/* @title 分页工具类 @Author quanjunw @Date 2014-4-9下午2:11:36 @Copyright copyright(2014) weibo.com all rights reserved */
class Tools_Pagination
{

    public $totalNum;

    public $pageNum;

    public $page;

    public $url;

    public $pageData;

    public $bp;

    public $sql;

    public $db;

    public $whereData;

    /** 分页工具
     *
     * @param [type] $pageNum
     *            每页条目数
     * @param [type] $page
     *            当前页码数
     * @param [type] $url
     *            页码前面的url
     * @param [type] $totalNum
     *            sql语句
     * @param string $bp
     *            条件字段(数组格式) */
    public function __construct($pageNum, $page, $url, $sql = '', $whereData = '', $total = '')
    {
        $this->db = Comm_Db::connect('school');
        $this->pageNum = $pageNum;
        $this->page = $page;
        $this->url = $url;
        $this->sql = $sql;
        $this->whereData = $whereData;
        $this->totalNum = $totalNum = empty($sql) && empty($whereData) ? $total : self::totalNum();
        
        if (substr($this->url, 0, 1) == '?')
        {
            $this->url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . $this->url;
        }
        
        if ($totalNum === '')
        {
            $totalNum = 0;
        }
        
        $this->totalNum = $totalNum;
        $this->_initPageData();
    }

    private function _initPageData()
    {
        $pageCount = 1;
        
        if ($this->totalNum)
        {
            if ($this->totalNum < $this->pageNum)
            {
                $pageCount = 1;
            }
            else
            {
                if ($this->totalNum % $this->pageNum)
                {
                    $pageCount = (int) ($this->totalNum / $this->pageNum) + 1;
                }
                else
                {
                    $pageCount = $this->totalNum / $this->pageNum;
                }
            }
        }
        
        if ($this->page <= 1)
        {
            $this->page = 1;
            $this->pageData['firstpage'] = $_SERVER['REQUEST_URI'] . '#';
            $this->pageData['previouspage'] = $_SERVER['REQUEST_URI'] . '#';
        }
        else
        {
            $this->pageData['firstpage'] = $this->url . '1';
            $this->pageData['previouspage'] = $this->url . ($this->page - 1);
        }
        
        if (($this->page >= $pageCount) || ($pageCount == 0))
        {
            $this->page = $pageCount;
            $this->pageData['nextpage'] = $_SERVER['REQUEST_URI'] . '#';
            $this->pageData['lastpage'] = $_SERVER['REQUEST_URI'] . '#';
        }
        else
        {
            $this->pageData['nextpage'] = $this->url . ($this->page + 1);
            $this->pageData['lastpage'] = $this->url . $pageCount;
        }
        
        $this->pageData['totalpage'] = $pageCount;
        $this->pageData['page'] = $this->page;
        /* 算法写的有问题 $this->pageData['from'] = ($this->page - 1) * $this->pageNum + 1; */
        $this->pageData['from'] = ($this->page - 1) * $this->pageNum;
        
        if ($this->totalNum == 0)
        {
            $this->pageData['from'] = 0;
        }
        
        if ($this->page * $this->pageNum > $this->totalNum)
        {
            $this->pageData['to'] = $this->totalNum;
        }
        else
        {
            $this->pageData['to'] = ($this->page) * $this->pageNum;
        }
        
        $this->pageData['totalnum'] = $this->totalNum;
        $this->pageData['pageNum'] = $this->pageNum;
        $this->pageData['pageurl'] = $this->url;
        $this->pageData['bp'] = ((empty($this->sql) && empty($this->whereData)) ? '' : self::getPageData());
        
        //去除多余数据
        unset($this->bp, $this->sql, $this->db, $this->whereData);
        
        return $this->pageData;
    }

    public function getPageData()
    {
        if (empty($this->whereData))
        {
            $this->sql = $this->sql . ' LIMIT ' . $this->pageData['from'] . ',' . $this->pageNum;
            return $this->db->fetchAll($this->sql);
        }
        else
        {
            $this->sql = $this->sql . ' LIMIT ' . $this->pageData['from'] . ',' . $this->pageNum;
            
            return $this->db->fetchAll($this->sql, $this->whereData);
        }
    }

    public function totalNum()
    {
        if ($this->whereData)
        {
            return count($this->db->fetchAll($this->sql, $this->whereData));
        }
        else
        {
            return count($this->db->fetchAll($this->sql));
        }
    }

    public function getPageListV4($listNum = 7, $omiMark = "...")
    {
        $pageList = array();
        $begin = $last = array();
        $rimNum = floor($listNum / 2) + 1;
        
        if (($this->page > $rimNum && $this->pageData['totalpage'] > $listNum) && ($this->pageData['totalpage'] - $this->page > $rimNum))
        {
            // 两头的...都存在时
            $begin[] = array(
                
                "num" => 1,
                "url" => $this->url . "1"
            );
            $begin[] = array(
                
                "num" => $omiMark,
                "url" => ""
            );
            $last[] = array(
                
                "num" => $omiMark,
                "url" => ""
            );
            $last[] = array(
                
                "num" => $this->pageData['totalpage'],
                "url" => $this->url . $this->pageData['totalpage']
            );
            
            $firstPage = $this->page - $rimNum + 2;
            $endPage = $this->page + $rimNum - 2;
        }
        elseif ($this->page > $rimNum && $this->pageData['totalpage'] > $listNum)
        {
            // 只有开头的...时
            $begin[] = array(
                
                "num" => 1,
                "url" => $this->url . "1"
            );
            $begin[] = array(
                
                "num" => $omiMark,
                "url" => ""
            );
            
            $firstPage = $this->pageData['totalpage'] - $listNum + 2;
            $endPage = $this->pageData['totalpage'];
        }
        elseif ($this->pageData['totalpage'] - $this->page > $rimNum && $this->pageData['totalpage'] > $listNum)
        {
            // 只有结尾的...时
            $last[] = array(
                
                "num" => $omiMark,
                "url" => ""
            );
            $last[] = array(
                
                "num" => $this->pageData['totalpage'],
                "url" => $this->url . $this->pageData['totalpage']
            );
            
            $firstPage = 1;
            $endPage = $listNum - 1;
        }
        else
        {
            // 没有...时
            $firstPage = 1;
            $endPage = $this->pageData['totalpage'];
        }
        
        for ($i = $firstPage; $i <= $endPage; $i ++)
        {
            $pageList[$i]['num'] = $i;
            $pageList[$i]['url'] = $this->url . $i;
        }
        
        $pageList = array_merge($begin, $pageList, $last);
        
        return $pageList;
    }
}