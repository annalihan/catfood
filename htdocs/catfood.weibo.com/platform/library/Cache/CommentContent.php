<?php
class Cache_CommentContent extends Cache_Abstract
{
    protected $configs = array(
        'cmtcontent' => array('%s_0_%s', 432000), //评论内容长期缓存5天
    );
    protected $keyPrefix = 'cmtcontent';
    protected $cachePool = 'COMMENT';
    
    public function getCommentContent($cid)
    {
        $key = $this->key("cmtcontent", $cid);
        return $this->get($key);
    }
    
    public function getCommentContents($cids)
    {
        if (is_array($cids))
        {
            if (isset($cids['total_number']))
            {
                unset($cids['total_number']);
            }

            if (!empty($cids))
            {
                $keys = array();
                foreach ($cids as $cid => $value)
                {
                    $keys[] = $this->key("cmtcontent", $cid);
                }

                return $this->mget($keys);
            }
            else
            {
                return array();
            }
        }
        else
        {
            return $this->getCommentContent($cids);
        }
    }
    
    public function setCommentContents($cids)
    {
        if (is_array($cids) && !empty($cids))
        {
            $keys = array();
            foreach ($cids as $cid => $content)
            {
                $keys[$this->key("cmtcontent", $cid)] = $content;
            }

            return $this->mset($keys, $this->livetime("cmtcontent"));
        }
    }
    
    public function setCommentContent($cid, $content)
    {
        $key = $this->key("cmtcontent", $cid);
        return $this->set($key, $content, $this->livetime("cmtcontent"));
    }
    
    public function deleteCommentContent($cid)
    {
        $key = $this->key("cmtcontent", $cid);
        return $this->del($key);
    }
}