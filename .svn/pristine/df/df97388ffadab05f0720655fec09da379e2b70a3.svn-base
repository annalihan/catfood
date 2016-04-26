<?php
class Tool_Analyze_UserList
{
    /**
     * 判断列表中是否有过滤用户
     * @param array $userList
     * @param int $prePage 每页数量
     * @param int $totalNumber
     */
    public static function hasFilteredUsers($userList, $prePage, $totalNumber, $currentPage = '')
    {
        $filterFlag = false;

        if (is_array($userList) && $totalNumber > 0)
        {
            $cntList = count($userList);
            if ($prePage >= $totalNumber)
            {
                //只有一页的情况
                if ($totalNumber > $cntList)
                {
                    $filterFlag = true;
                }
            }
            else
            {
                $totalPage = ceil($totalNumber / $prePage); //总页数
                if ($prePage > $cntList && $totalPage != $currentPage)
                {
                    $filterFlag = true;
                }
                else
                {
                    if (($totalNumber % $prePage) > $cntList && $totalPage == $currentPage)
                    {
                        //最后一页
                        $filterFlag = true;
                    }
                }
            }
        }
        
        return $filterFlag;
    }
}