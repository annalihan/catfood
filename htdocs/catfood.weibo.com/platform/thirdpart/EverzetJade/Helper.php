<?php

class Jade_Helper
{
    private static $_jade = null;
    private static $_jadeDir = '';

    public static function parse($content, $indent = 4)
    {
        if (self::$_jade == null)
        {
            self::$_jadeDir = dirname(__FILE__);
            spl_autoload_register(array("self", 'autoload'));

            $parser = new Everzet_Jade_Parser(new Everzet_Jade_Lexer_Lexer($indent));
            $dumper = new Everzet_Jade_Dumper_PHPDumper();
            $dumper->registerVisitor('tag', new Everzet_Jade_Visitor_AutotagsVisitor());
            $dumper->registerFilter('javascript', new Everzet_Jade_Filter_JavaScriptFilter());
            $dumper->registerFilter('cdata', new Everzet_Jade_Filter_CDATAFilter());
            $dumper->registerFilter('php', new Everzet_Jade_Filter_PHPFilter());
            $dumper->registerFilter('style', new Everzet_Jade_Filter_CSSFilter());
            $dumper->registerFilter('less', new Everzet_Jade_Filter_LessFilter());
            self::$_jade = new Everzet_Jade_Jade($parser, $dumper);
        }

        return self::$_jade->render($content);
    }

    public static function autoload($class)
    {
        if (0 === strpos($class, 'Everzet_Jade_'))
        {
            $file = self::$_jadeDir . DS . str_replace('_', DS, substr($class, 13)) . '.php';
            
            if (file_exists($file))
            {
                return include $file;
            }
        }

        return false; 
    } 
}