<?php
/**
 * 模板处理类
 *     includeTemplate:用于jade模板中include
 * @package Core
 * @author chenjie <chenjie5@staff.sina.com.cn>
 * @version 2013-10-24
 */
class Core_Template
{
    private static $_templateEngine = null;

    public static function getInstance()
    {
        if (self::$_templateEngine == null)
        {
            //创建引擎
            $appPath = Core_Loader::getAppPath(Core_Branch::$branchName);
            self::$_templateEngine = new Core_Template_Yaf($appPath);

            //灰度
            self::$_templateEngine->assign('g_bucket', Core_Branch::$grayVersion);
        }

        return self::$_templateEngine;
    }

    /**
     * include段落
     * 存在输出缓存乱序问题，不建议使用
     * 建议直接在模板里使用include(不能指定相应的pl进行数据准备)
     * @param  string $templateFile 段落的模板路径或者ID,如header,article/detail等
     * @return [type]           [description]
     */
    public static function includeTemplate($templateFile, $sourceFile = false)
    {
        //判断是否存在对应的类
        $paths = explode('/', str_replace('../', '', $templateFile));
        $classNames = array();
        foreach ($paths as $path)
        {
            $classNames[] = ucfirst($path);
        }

        $className = implode('_', $classNames);
        $engine = self::getInstance();

        //如果include的为pagelet，那么直接使用pagelet对象进行渲染(含有数据)
        if (class_exists($className) && is_subclass_of($className, 'Comm_Bigpipe_Pagelet'))
        {
            $pl = new $className();
            $meta = $pl->getMetaData();
            $data = $pl->prepareData();

            $engine->assignValues($meta);
            $engine->assignValues($data);
            $engine->display($pl->getTemplate());
        }
        else
        {
            //绝对路径
            if (is_file($sourceFile))
            {
                $templateFile = dirname($sourceFile) . DS . $templateFile;
            }
            
            $engine->display($templateFile);
        }
    }
}