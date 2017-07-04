<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/29 0029
 * Time: 16:12
 */

namespace suojinhua\model;
use PDO;
use PDOException;
//1.创建一个操作数据库的控制器
//2.用他里面的方法可以给数据库增删改查
class Base
{
    //1.设置一个属性
    //2.用来当作数据库的表名
    public $table;
    //1.设置一个静态属性
    //2.用来获得PDO对象 设置为静态是为了获得过一次后就会存储起来 不用重复连接
    public static $pdo=NULL;
    //1.设置一个属性
    //2.用来获得操作数据库的时候的WHERE 条件
    public $where='';
    //1.设置一个属性
    //2.用来获得操作数据库后获得的数据
    public $data;
    //1.写一个构造方法
    //2.调用该类的时候就获得表名和连接数据库
    public function __construct($table,$config)
    {
        //1.获得传进来的数据库表名
        //2.操作数据库的时候要用到
        $this->table=$table;
        //1.调用方法
        //2.连接数据库并获得连接数据库时用到的参数
        $this->connect($config);
    }
    //1.写一个连接数据库的方法
    //2.调用该类的时候构造方法会调用他 来完成数据库的连接
    public function connect($config){
        //1.判断静态方法pdo是否为null
        // 2.不是的话说明以连接数据库就不用重复连接了
        if (!is_null(self::$pdo)) return;
        //1.在try里连接数据库
        //2.出现错误的时候会被catch抓到
        try{
            //1.设置一个变量
            //2.获得数据的地址和库名
            $dsn = "mysql:host=" . $config['db_host'] . ";dbname=" . $config['db_name'];
            //1.设置一个变量
            //2.获得用户名
            $user = $config['db_user'];
            //1.设置一个变量
            //2.获取密码
            $password = $config['db_password'];
            //1.调用数据库连接数据库
            //2.操作数据库
            $pdo = new PDO($dsn,$user,$password);
            //设置错误
            //1.把所有错误设置成异常错误
            //2.这样出现错误的话会被catch抓到  来处理
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            //设置字符集
            //1.设置字符集
            //2.设置成和用户端一样的避免出现乱码
            $pdo->query("SET NAMES " . $config['db_charset']);
            //存到静态属性中
            //1.获得pdo对象
            //2.要在别的方法里用到
            self::$pdo = $pdo;
        //1.用catch抓取异常错误
        //2.在下处理
        }catch (PDOException $e){
            //1.输出错误并且终止代码运行
            //2.出现错误后不要运行代码了
            exit($e->getMessage());
        }
    }
    //1.设置一个获取操作数据库的条件的方法
    //2.t通过传进来的参数让where属性获得
    public function where($var){
        //1.获得传进来的参数条件赋给where属性
        //2.操作数据库的时候来调用该属性
        $this->where=" WHERE {$var}";
        //1.返回对象
        //2.用来链式调用
        return $this;
    }
    //1.写一个获得数据库数据的方法
    //2.调用它的时候他会组合sql语句并且传给q方法来完整数据库的操作
    public function get(){
        //1.组合sql语句
        //2.要传给q方法
        $sql="SELECT * FROM {$this->table} {$this->where}";
//        fun($this->where);
        //1.调用q方法并且传参数  来完成数据库的操作
        //2.获得返回的数据 并且返回出去
        return $this->q($sql);
    }
    //1.设置操作数据库的方法
    //2.调用它的时候来通过传进来的sql语句来完成相应的数据路的操作 并返回数据
    public function q($sql){
        //1.在try里操作数据库
        //2.出现错误的时候会被catch抓到
        try{
            //1.执行数据库的操作
            //2.通过传进来的sql语句来完成相应的操作
            //3.并且获得数据
            $res=self::$pdo->query($sql);
            //1.把获得的数据转化为关联数组
            //2.方便操作提取里面的数据
            $data=$res->fetchAll(PDO::FETCH_ASSOC);
            //1.返回数组
            //2.谁调用谁接收
            return $data;
        //1.用catch抓取异常错误
        //2.在下面处理
        }catch (PDOException $e){
            //1.输出错误并且终止代码运行
            //2.出现错误后不要运行代码了
            exit($e->getMessage());
        }

    }
    //1.写一个查找数据库内容的方法
    //2.调用他的时候通过传进来的参数来查找相应的数据
    public function find($sql){
//        fun($sql);
        //1.调用getpri方法 来获得主键名
        //2.通过主键名来查找
        $pri=$this->getpri();
//        fun($pri);
        //1.设置查找条件
        //2.通过传进来的参数当作条件
        $this->where("{$pri}={$sql}");
        //1.组合sql语句
        //2.传进q方法来操作
        $sql="SELECT * FROM {$this->table} {$this->where}";
//        fun($sql);
        //1.调用q方法传进去参数
        //2.完成查找 并获得数据
        $data= $this->q($sql);
//        fun($data);
        //1.通过current函数
        //2.把data的二维数组转化为一维数组
        $data=current($data);
//        fun($data);exit;
        //1.把获得数据赋给data属性
        //2.通过toarr来获得数据
        $this->data=$data;
        //1.返回对象
        //2.链式调用的时候用
        return $this;
    }
    //1.写一个toarr的方法
    //2.调用它的时候来获得数据
    public function toarr(){
        return $this->data;
    }
    //1.写一个获得主键名 的方法
    //2.调用它的时候来获得主键名
    public function getpri(){
        //1.获得表结构
        //2.要来循环出主键名
        $desc=$this->q("DESC {$this->table}");
        //1.设置一个变量
        //2.把循环出来的主键名赋给他
        $pri='';
        //1.循环表结构
        //2.获得主键名
        foreach ($desc as $v){
            //1.加入$v里面的kry等于pri的话说明是主键
            //2.因为主键是PRI
            if ($v['Key']=='PRI'){
                //1.获得主键名
                //2.操作数据库的时候当作条件
                $pri=$v['Field'];
                //1.终止循环
                //2.找到主键后就不用循环了
                break;
            }
        }
        //1.返回主键名
        //2.谁调用谁获得
        return $pri;
    }
    //1.设置一个查找数据库信息的方法
    //2.调用它的时候会查找到相应的数据并且返回数组
    public function findArr($num){
        //1.调用find方法 完成查找
        //2.并且返回duix
        $obi=$this->find($num);
        //1.返回data方法 获得数据
        //2.返回数据 谁调用谁获得
        return $this->data;
    }
    //1.写一个获得数据数量的方法
    //2.调用它的时候可以获得数据库的数量
    public function count($num='*'){
        //1.组合sql语句
        //2.传给q方法来操作
        $sql="SELECT count({$num}) as c FROM {$this->table} {$this->where}";
        //1.调用q方法 并且传进去sql语句
        //2.完成获得数量的操作 并获得数据
        $data=$this->q($sql);
        //1.返回获得的数据的0下标的c
        //2.返回的就是数据库的数量
        return $data[0]['c'];
    }
    //1.写一个操作数据库的e方法
    //2.他用来没有结果集的数据库操作
    public function e($sql){
        //1.在try里操作数据库
        //2.出现错误的时候会被catch抓到
        try{
            //1.通过传进来完成exec的数据库操作
            //2.返回结果
            return self::$pdo->exec($sql);
            //1.用catch抓取异常错误
            //2.在下面处理
        }catch (PDOException $e){
            //1.输出错误并且终止代码运行
            //2.出现错误后不要运行代码了
            exit($e->getMessage());
        }
    }
}