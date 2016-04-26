<?php
class Do_Tags extends Do_Abstract
{
    protected $props = array(
        'tag_id' => array(
            'int',
            'min,1',
            Comm_ArgChecker::OPT_NO_DEFAULT, 
            Comm_ArgChecker::RIGHT,
        ),
        'tag' => array(
            'string',
            'width_min,1;width_max,14',
            Comm_ArgChecker::OPT_NO_DEFAULT, 
            Comm_ArgChecker::RIGHT,
        ), 
        'weight' => '',
        'class' => '',
        //--  上行---
        'tags' => '', //上行的tags, 要创建的一组标签，用半角逗号隔开每个标签的长度不可超过7个汉字，14个半角字符 
    );
    
    public function setTags($tags = '')
    {
        if ($this->getDataObjectMode() == "input")
        {
            if (empty($tags))
            {           
                throw new Comm_DataObject_Exception('the tags is null');
            }
            else
            {
                $arrayTags = explode(",", $tags);
                foreach ($arrayTags as $key => $tag)
                {
                    if (mb_strwidth($tag, 'utf-8') > 14)
                    {
                        throw new Comm_DataObject_Exception('the tag is too long' . $tag);
                    }

                    if (empty($tag))
                    {
                        unset($arrayTags[$key]);
                    }
                }

                if (count($arrayTags) > 0)
                {
                    $tags = implode(",", $arrayTags);
                }
                else
                {
                    throw new Comm_DataObject_Exception('the tags is null');
                }
            }
        }
        
        $this->setData("tags", $tags);
    }
}