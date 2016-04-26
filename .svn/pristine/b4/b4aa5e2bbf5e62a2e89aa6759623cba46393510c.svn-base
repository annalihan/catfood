<?php 
interface Comm_Queue_Interface
{
    //Queue不单独出现，和Cache一起，暂时去掉configure方法
    //public function configure($config);
    public function pop($key);
    public function push($key, $value);
    //public function multiPop($key, size);
    //public function multiPush($key, $values);
    public function blockPop($keys, $timeout = 1);
}