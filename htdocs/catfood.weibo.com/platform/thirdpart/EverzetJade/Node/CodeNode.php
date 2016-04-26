<?php


/*
 * This file is part of the Jade.php.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Code Node. 
 */
class Everzet_Jade_Node_CodeNode extends Everzet_Jade_Node_Node
{
    protected $code;
    protected $buffering = false;
    protected $block;

    /**
     * Initialize code node. 
     * 
     * @param   string  $code       code string
     * @param   boolean $buffering  turn on buffering
     * @param   integer $line       source line
     */
    public function __construct($code, $buffering = false, $line = 0, $unescaped = true)
    {
        parent::__construct($line);

        $this->buffering = $buffering;
        $this->code = $this->_setCode($code, $unescaped);
    }

    private function _setCode($code, $unescaped)
    {
        $code = trim($code);

        //判断是否为字段或者字符串
        if ($unescaped == false)
        {
            if (isset($this->_escapeTokens) == false)
            {
                $this->_escapeTokens = array(
                    T_OPEN_TAG => 1,
                    T_CLOSE_TAG => 1,
                    T_OBJECT_CAST => 1,
                    T_OBJECT_OPERATOR => 1,
                    T_FUNC_C => 1,
                    T_WHITESPACE => 1,
                    T_VARIABLE => 1,
                    T_VAR => 1,
                    T_STRING_VARNAME => 1,
                    T_STRING_CAST => 1,
                    T_STRING => 1,
                    T_NUM_STRING => 1,
                    T_LNUMBER => 1,
                    T_INT_CAST => 1,
                    T_FUNCTION => 1,
                    T_DOUBLE_CAST => 1,
                    T_DOLLAR_OPEN_CURLY_BRACES => 1,
                    T_DNUMBER => 1,
                    T_CONSTANT_ENCAPSED_STRING => 1,
                    T_CHARACTER => 1,
                    T_BOOL_CAST => 1,
                    T_BAD_CHARACTER => 1,
                );
            }

            $canEscape = true;
            $tokens = token_get_all("<?{$code}?>");
            foreach ($tokens as $token)
            {
                if (isset($this->_escapeTokens[$token[0]]) === false)
                {
                    $canEscape = false;
                    break;
                }
            }

            //TODO url里的encode
            return $canEscape ? "htmlentities(" . $code . ", ENT_QUOTES, 'UTF-8')" : $code;
        }

        return $code;
    }

    /**
     * Return code string. 
     * 
     * @return  string
     */
    public function getCode()
    {
        return $this->code; 
    }

    /**
     * Return true if code buffered. 
     * 
     * @return  boolean
     */
    public function isBuffered()
    {
        return $this->buffering;
    }

    /**
     * Set block node. 
     * 
     * @param   Everzet_Jade_Node_BlockNode   $node   child node
     */
    public function setBlock(Everzet_Jade_Node_BlockNode $node)
    {
        $this->block = $node;
    }

    /**
     * Return block node. 
     * 
     * @return  Everzet_Jade_Node_BlockNode
     */
    public function getBlock()
    {
        return $this->block;
    }
}
