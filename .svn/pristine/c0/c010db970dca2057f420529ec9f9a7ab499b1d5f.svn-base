<?php
/**
 * 用户教育信息操作类
 */
class Dr_School extends Dr_Abstract
{
    /**
     * 取得学校信息
     */
    public static function getSchools($userId)
    {
        $cacheSchool = new Cache_School();
        $viewer = Comm_Context::get("viewer", false); //增加未登录判断
        $isSelf = ($viewer && $viewer->id == $userId);
        if ($isSelf)
        {
            $schools = $cacheSchool->getSchools($userId);
        }
        else
        {
            if ($viewer)
            {
                $relation = Dr_Relation::checkRelation($viewer->id, $userId);
            }
            else
            {
                $relation = Dr_Relation::RELATION_NO;
            }

            if ($relation == Dr_Relation::RELATION_FOLLOWED || $relation == Dr_Relation::RELATION_BILATERAL)
            {
                $schools = $cacheSchool->getSchoolsFollow($userId);
            }
            else
            {
                $schools = $cacheSchool->getSchoolsViewer($userId);
            }
        }

        if (false !== $schools)
        {
            return $schools;   
        }

        $comm = Comm_Weibo_Api_Account::getProfileEducation();
        $comm->uid = $userId;

        try
        {
            $schools = $comm->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        if (!$isSelf)
        {
            if ($relation == Dr_Relation::RELATION_FOLLOWED || $relation == Dr_Relation::RELATION_BILATERAL)
            {
                foreach ($schools as $k => $v)
                {
                    if ($v['visible'] == 0)
                    {
                        //0 尽自己可见 1 我关注的人可见 2 所有人可见
                        unset($schools[$k]);
                    }
                }

                $cacheSchool->setSchoolsFollow($userId, $schools);
            }
            else
            {
                foreach ($schools as $k => $v)
                {
                    if ($v['visible'] == 0 || $v['visible'] == 1)
                    {
                        unset($schools[$k]);
                    }
                }

                $cacheSchool->setSchoolsViewer($userId, $schools);
            }
        }
        else
        {
            $cacheSchool->setSchools($userId, $schools);
        }

        return $schools;
    }

    /**
     * 
     * 按照入学时间降序
     * @param $list
     */
    private static function _descendingSort(Array $list)
    {
        $rtn = array();
        foreach ($list as $k => $v)
        {
            $time = isset($v['year']) ? $v['year'] : $k;
            $rtn[$time] = $v;
        }

        krsort($rtn, SORT_NUMERIC);
        return $rtn;
    }

    /**
     * 只返回大学
     * @param $school
     */
    private static function _getUniversity($school)
    {
        $university = array();

        foreach ($school as $key => $val)
        {
            if (isset($val['type']) && $val['type'] == 1)
            {
                $university[$key] = $val;
            }
        }

        return $university;
    }

    /**
     * 返回最后一个大学
     * @param Do_User $owner
     */
    public static function getLastUniversity($userId)
    {
        $result = false;
        $schools = self::getSchools($userId);

        if (count($schools) > 0)
        {
            $schools = self::_descendingSort($schools);
            $schools = self::_getUniversity($schools);
            $schools && $result = array_shift($schools);
        }

        return $result;
    }
    
    public static function getSchoolCount($userId)
    {
        try
        {
            // 静态缓存
            $userSchoolCount = Comm_Context::get('user_school_count', false);
            if ($userSchoolCount === false)
            {
                $userSchoolCount = count(self::getSchools($userId));
                Comm_Context::set('user_school_count', $userSchoolCount);
            }

            return $userSchoolCount;
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    /**
     * 搜索学校时的即时搜索建议
     * @param string $q     搜索的关键字。必须进行URL_encoding。UTF-8编码 
     * @param int $count    每页返回结果数。默认10 
     * @param int $type     学校类型，默认全部。学校类型，1-大学；2-高中；3-中专技校；4-初中；5-小学 
     * @param string $sid   搜索sid标识,由搜索部分配 
     * @param int $dup      是否显示重复学校。默认0不显示，1显示。 
     */
    public static function getSuggestionsSchools($q, $count, $type, $sid = 't_find', $dup = 0)
    {
        try
        {
            $result = array();
            $request = Comm_Weibo_Api_Search::suggestionsSchools();
            $request->q = $q;
            $request->count = $count;
            $request->type = $type;
            $request->sid = $sid;
            $request->dup = $dup;
            return $request->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    /**
     * 获取学校信息列表
     * @param int $uid
     */
    public static function getSchoolList($uid)
    {
        try
        {
            if (empty($uid))
            {
                return false;
            }

            $request = Comm_Weibo_Api_Account::getProfileEducation();
            $request->uid = $uid;
            $info = $request->getResult();
            $schoolList = array();

            if (!empty($info))
            {
                foreach ($info as $school)
                {
                    $schoolList[$school['id']] = $school;
                }
            }

            return $schoolList;
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    /**
     * 根据ids获取学校信息
     * @param string $ids
     */
    public static function getSchoolInfo($ids)
    {
        try
        {
            if (strlen($ids) == 0)
            {
                return false;
            }

            $request = Comm_Weibo_Api_Account::getSchoolInfo();
            $request->ids = $ids;
            return $request->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    public static function getEntranceYear()
    {
        $years = array();
        for ($i = date('Y'); $i >= 1900; $i--)
        {
            $years[$i] = $i;
        }

        return $years;
    }
    
    /**
     * 批量获取教育信息
     */
    public static function getSchoolListBatch(array $uids)
    {
        if (empty($uids))
        {
            return array();
        }

        try
        {
            $request = Comm_Weibo_Api_Account::educationBatch();
            $request->uids = implode(',', $uids);
            return $request->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 批量获取最后一个大学信息
     */
    public static function getUniversityBatch(array $uids)
    {
        $result = array();
        if (empty($uids))
        {
            return $result;
        }

        $info = self::getSchoolListBatch($uids);
        
        try
        {
            //TODO
            $relation = Dr_Relation::checkFriendshipsExists($uids);
        }
        catch (Comm_Exception_Program $e)
        {
            $relation = array();
        }

        foreach ($info as $k => $v)
        {
            $school = self::_descendingSort($v['education']);
            $school = self::_getUniversity($school);

            foreach ($school as $key => $value)
            {
                if (isset($relation[$v['id']]) && ($relation[$v['id']] == Dr_Relation::RELATION_BILATERAL || $relation[$v['id']] == Dr_Relation::RELATION_FOLLOWED))
                {
                    //过滤仅自己可见的学校信息
                    if ($value['visible'] == 0)
                    { 
                        unset($school[$key]);
                    }
                }
                else
                {
                    //过滤仅自己可见、我的关注人可见的学校信息
                    if ($value['visible'] != 2)
                    { 
                        unset($school[$key]);
                    }
                }
            }

            $school = array_slice($school, 0, 1);
            if (!empty($school))
            {
                $result[$v['id']] = $school[0];
            }
        }

        return $result;
    }
}
