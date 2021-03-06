<?php
namespace ZainLib;
Class Helper
{
    public static function printr($object, $name = '', $attributes = false, $properties = false, $htmlEntities = true)
    {
        $console = false;
        if (in_array(php_sapi_name(), array('cli'))) {
            $console = true;
        }
        $classHint = '';
        if (($attributes | $properties) && (is_array($object) || is_object($object))) {
            if (is_object($object)) {
                $class = get_class($object);
                if (!$name)
                    $name = $class;
                else
                    $classHint = 'type: ' . $class;
            }
            if (function_exists('getAttributes')) {
                $object = getAttributes($object, $attributes, $properties);
            }
        }
        $bt = debug_backtrace();
        $bp = '';
        $file = $bt[0]['file'];
        $possibleBasePath = __DIR__;
        if (strpos($file, $possibleBasePath) === 0) {
            $bp = $possibleBasePath . '/';
        }
        if (!$bp) {
            $possibleBasePath = dirname(__DIR__);
            if (strpos($file, $possibleBasePath) === 0) {
                $bp = $possibleBasePath . '/';
            }
        }
        $file = str_replace($bp, '', $file);
        $line = $bt[0]['line'];
        $preStart = '<pre>';
        $preEnd = '</pre>';
        //xdebug overloads var_dump with html so ignore that
        if (is_object($object) && function_exists('xdebug_break')){
            $htmlEntities = false;
            $preStart = '';
            $preEnd = '';
        }
        if ($console) {
            print  $file . ' on line ' . $line . " $name is: ";
        }
        else {
            print '<div style="background: #FFFBD6">';
            $nameLine = '';
            if ($name)
                $nameLine = '<b> <span style="font-size:18px;">' . $name . "</span></b> $classHint printr:<br/>";
            print '<span style="font-size:12px;">' . $nameLine . ' ' . $file . ' on line ' . $bt[0]['line'] . '</span>';
            print '<div style="border:1px so lid #000;">';
            print $preStart;
        }
        if ($htmlEntities){
            ob_start();
        }
        if (is_array($object))
            print_r($object);
        else
            var_dump($object);
        if ($htmlEntities){
            $content = ob_get_clean();
            echo htmlentities($content);
        }
        if (!$console) {
            print $preEnd;
            echo '</div></div><hr/>';
        }
    }
}
Class Logger
{
    public static $log = array();
    public static $appendLogToFile = true;
    public static $appendLogFile = '/tmp/zain_log_prepend.txt';
    public static function addLog($content){
        self::$log[] = $content;
        self::appendLogToFile($content);
    }
    public static function appendLogToFile($newContent)
    {
        if (!self::$appendLogToFile){
            return ;
        }
        $content = '';
        if (@file_exists(self::$appendLogFile)){
            $content = file_get_contents(self::$appendLogFile);
        }
        $content =  $content  . $newContent . "\n" ;
        @file_put_contents(self::$appendLogFile,$content);

    }
    public static function dumpContentToFile($content,$varExport=true)
    {
        $dumpFile = dirname(dirname(__FILE__)) . '/dump.txt';
        $dumpContent = $content;
        if ($varExport){
            $dumpContent= var_export($dumpContent,true);
        }
        file_put_contents($dumpFile,$dumpContent);
    }
}