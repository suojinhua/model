<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/29 0029
 * Time: 16:11
 */

namespace suojinhua\model;

//1.写一个Model方法
//2.通过它里面的方法来调用Base控制器来操作数据库
class Model
{
    //1.设置一个静态变量
    //2.自动引入数据库配置文件后会调用静态变量把配置项存储到变量里  在调用Base的时候传进去
    public static $config;
    //1.写一个静态__callStatic方法
    //2.调用该类没有的静态方法时 会触发该方法 来运行里面的代码 调用静态方法parseAction 来调用Base
    public static function __callStatic($name, $arguments)
    {
        //1.调用静态方法 parseAction 并且把$name传进去
        //2.调用Base 并且把$name当作方法
        return self::parseAction($name, $arguments);
    }
    //1.写一个__call方法
    //2.调用该类没有的方法时 会触发该方法 来运行里面的代码
    // 3.调用静态方法parseAction 来调用Base
    public function __call($name, $arguments)
    {
        //1.调用静态方法 parseAction 并且把$name传进去
        //2.调用Base 并且把$name当作方法
        return self::parseAction($name, $arguments);
    }
    //1.写一个静态方法parseAction
    //2.调用他的时候 会调用Base类 把表名和数据库的配置项传进去 $name是方法名
    private static function parseAction($name, $arguments){
        //1.获得谁调用该类的类名
        //2.类名就是表名要把他截取出来传进Base控制器里
        $table=get_called_class();
        //1.截取获得的类名
        //2.因为获取的类名 是带有命名空间的 所以要把命名空间去掉只留类名也就是表名
        $table=strtolower(ltrim(strrchr($table,'\\'),'\\'));
        //1.调用Base控制器 并且把表名和数据库的配置项传进去
        //2.通过Base来实现想要的功能
        return call_user_func_array([new Base($table,self::$config),$name],$arguments);
    }
    //1.写一个静态方法setConfig
    //2.自动移入database.php后会在里面调用该静态方法 来把里面的数据库配置项传进去
    public static function setConfig($config){
        //1.调用静态属性$config来获得传进来的数据库配置项
        //2.传到Base控制器里连接数据库用的到
        self::$config=$config;
    }
}