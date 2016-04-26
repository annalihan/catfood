<?php

class Tool_Mail
{
    /**
     * 对邮件地址进行中文的UTF-8编码转化
     * @param unknown_type $address
     * @return string
     */
    private static function _formatMailAddress($address)
    {
        if (preg_match("|<([^<]+)>|", $address, $matches))
        {
            $name = mb_substr($address, 0, mb_strpos($address, '<'));
            $name = trim($name);
            $mail = $matches[1];
            $address = '=?UTF-8?B?' . base64_encode($name) . '?= ' . '<' . $mail . '>';
        }
        
        return $address;
    }
    
    /**
     * 发送html格式的邮件
     * @param string/array $to
     * @param string $subject
     * @param string $body
     * @param string/array $cc
     */
    public static function send($to, $subject, $body, $cc = null)
    {
        $headers[] = 'X-Mailer: PHP';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = 'From: chenjie5@staff.sina.com.cn';

        // 抄送
        if (is_array($cc))
        {
            foreach ($cc as $mail)
            {
                $ccMails[] = self::_formatMailAddress($mail);
            }
            
            $cc = join(", ", $ccMails);
        }

        if ($cc)
        {
            $headers[] = 'Cc: ' . $cc;
        }
        
        // 发送
        if (is_array($to))
        {
            foreach ($to as $mail)
            {
                $toMails[] = self::_formatMailAddress($mail);
            }
            
            $to = join(", ", $toMails);
        }

        // 标题
        $subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
        
        return mail($to, $subject, $body, join("\r\n", $headers));
    }
}
