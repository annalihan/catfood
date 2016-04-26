<?php
class Cache_CommentIndex extends Cache_Abstract
{
    protected $configs = array(
        'cmtindex' => array('%s_1_%s', 259200), // 评论内容长生存期缓存3天
        'minicmtindex' => array('%s_1_%s', 1800), // 评论内容短生存期缓存30分钟
    );
    
    protected $keyPrefix = 'cmtindex';
    protected $cachePool = 'COMMENT';
    
    public function getCommentIndex($mid)
    {
        $key = $this->key("cmtindex", $mid);
        return $this->get($key);
    }
    
    public function getCommentIndexs($mids)
    {
        if (is_array($mids))
        {
            $keys = array();

            foreach ($mids as $mid)
            {
                $keys[] = $this->key("cmtindex", $mid);
            }

            return $this->mget($keys);
        }
        else
        {
            return $this->getCommentIndex($mids);
        }
    }
    
    /**
     *
     * @param $mid
     * @param $list
     * @param $ismini 是否使用短期缓存            
     */
    public function setCommentIndex($mid, $list, $ismini = false)
    {
        if ($ismini)
        {
            $key = $this->key("minicmtindex", $mid);
            $livetime = $this->livetime("minicmtindex");
        }
        else
        {
            $key = $this->key("cmtindex", $mid);
            $livetime = $this->livetime("cmtindex");
        }

        return $this->set($key, $list, $livetime);
    }
    
    public function setCommentIndexs($mids, $ismini = false)
    {
        if (is_array($mids) && !empty($mids))
        {
            $keys = array();
            foreach ($mids as $mid => $list)
            {
                $keys[$this->key("cmtindex", $mid)] = $list;
            }

            if ($ismini)
            {
                $livetime = $this->livetime("minicmtindex");
            }
            else
            {
                $livetime = $this->livetime("cmtindex");
            }

            return $this->mset($keys, $livetime);
        }
        
        return false;
    }
}
