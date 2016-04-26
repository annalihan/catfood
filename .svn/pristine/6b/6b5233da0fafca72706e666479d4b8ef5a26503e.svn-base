<?php

class Tools_Page
{

    public $totalNum;

    public $pageRecNum;

    public $pageNum;

    public $url;

    public $pageData;

    public $bp;

    /** 分页工具
     *
     * @param [type] $pageRecNum
     *            每页条目数
     * @param [type] $pageNum
     *            当前页码数
     * @param [type] $url
     *            页码前面的url
     * @param [type] $totalNum
     *            总条目数量
     * @param string $bp
     *            输出数组 */
    public function __construct($pageRecNum, $pageNum, $url, $totalNum, $bp = "0")
    {
        $this->pageRecNum = $pageRecNum;
        $this->pageNum = $pageNum;
        $this->url = $url;
        $this->bp = $bp;
        
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
            if ($this->totalNum < $this->pageRecNum)
            {
                $pageCount = 1;
            }
            else
            {
                if ($this->totalNum % $this->pageRecNum)
                {
                    $pageCount = (int) ($this->totalNum / $this->pageRecNum) + 1;
                }
                else
                {
                    $pageCount = $this->totalNum / $this->pageRecNum;
                }
            }
        }
        
        if ($this->pageNum <= 1)
        {
            $this->pageNum = 1;
            $this->pageData['firstpage'] = $_SERVER['REQUEST_URI'] . '#';
            $this->pageData['previouspage'] = $_SERVER['REQUEST_URI'] . '#';
        }
        else
        {
            $this->pageData['firstpage'] = $this->url . '1';
            $this->pageData['previouspage'] = $this->url . ($this->pageNum - 1);
        }
        
        if (($this->pageNum >= $pageCount) || ($pageCount == 0))
        {
            $this->pageNum = $pageCount;
            $this->pageData['nextpage'] = $_SERVER['REQUEST_URI'] . '#';
            $this->pageData['lastpage'] = $_SERVER['REQUEST_URI'] . '#';
        }
        else
        {
            $this->pageData['nextpage'] = $this->url . ($this->pageNum + 1);
            $this->pageData['lastpage'] = $this->url . $pageCount;
        }
        
        $this->pageData['totalpage'] = $pageCount;
        $this->pageData['pagenum'] = $this->pageNum;
        /* 算法写的有问题 $this->pageData['from'] = ($this->pageNum - 1) * $this->pageRecNum + 1; */
        $this->pageData['from'] = ($this->pageNum - 1) * ($this->pageRecNum + 1);
        
        if ($this->totalNum == 0)
        {
            $this->pageData['from'] = 0;
        }
        
        if ($this->pageNum * $this->pageRecNum > $this->totalNum)
        {
            $this->pageData['to'] = $this->totalNum;
        }
        else
        {
            $this->pageData['to'] = ($this->pageNum) * $this->pageRecNum;
        }
        
        $this->pageData['totalnum'] = $this->totalNum;
        $this->pageData['pageRecNum'] = $this->pageRecNum;
        $this->pageData['pageurl'] = $this->url;
        $this->pageData['bp'] = $this->bp;
        
        //return $this->pageData;
    }

    public function getPageData()
    {
        return $this->pageData;
    }

    public function getPageListV4($listNum = 7, $omiMark = "...")
    {
        $pageList = array();
        $begin = $last = array();
        $rimNum = floor($listNum / 2) + 1;
        
        if (($this->pageNum > $rimNum && $this->pageData['totalpage'] > $listNum) && ($this->pageData['totalpage'] - $this->pageNum > $rimNum))
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
            
            $firstPage = $this->pageNum - $rimNum + 2;
            $endPage = $this->pageNum + $rimNum - 2;
        }
        elseif ($this->pageNum > $rimNum && $this->pageData['totalpage'] > $listNum)
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
        elseif ($this->pageData['totalpage'] - $this->pageNum > $rimNum && $this->pageData['totalpage'] > $listNum)
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