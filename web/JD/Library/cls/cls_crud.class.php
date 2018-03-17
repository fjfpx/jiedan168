<?php
namespace Library\cls;
/**
 * 自动化数据表操作类
 * @example
 * <code>
 * $db  = cls_crud::factory(array('table'=>'article'));
 * $data = $db->get_block_list(array('category_id' => 3), 9);
 *
 * $data = $db->get_list(array('title', 'id', 'time'), array('category_id'=>$cid), 'time', 1, $_GET['page'], 2, 1);
 * $page_html = $db->page->get_html("?action=list&cid={$cid}", 'ylmf-page');
 *
 * </code>
 */
class cls_crud   {
//    private static $_instance = null;
    /**
     * 数据表名
     * @var string
     */
    private $table = '';
    private $from = '';
    private $link;
    private $query_id;

    /**
     * 分页对象
     * @var array
     */
    public $page = null;
    /**
     * 私有构造函数（单例模式）
     * @param
     */
    public function __construct($config, $database=array())
    {
        $this->connect($database);
        $this->page = new cls_page();
        $this->config($config);
    }

    /**
     * 配置
     * @param array $config     配置变量
     */
    private function config($config)
    {
        if(!empty($config))
        {
            foreach($config as $cf => $val)
            {
                if($cf == 'table'){
                    $this->$cf = C('DB_PREFIX').$val;
                }else{
                    // & from
                    $this->$cf = $val;
                }
            }
        }
    }
    /**
     * 工厂函数
     * @param array $config     配置变量（最基本要配置数据表名）
     * @param array $database   数据库链接参数
     */
    private static function factory($config, $database=array())
    {
        if (null === self::$_instance)
        {
            self::$_instance = new self();
            self::$_instance->connect($database);
            self::$_instance->page = new cls_page();
        }
        self::$_instance->config($config);
        return self::$_instance;
    }
    /**
     * 设置数据表(例如：在操作的过程中需要改变数据表，就可以使用此方法)
     * @param string $table
     */
    public function set_table($table)
    {
        $this->table = $table;
    }
    public function set_from($from){
        $this->from = $from;
    }
    /**
     * 连接数据库+选择数据库...
     * @return void
     */
    private function connect($database = array())
    {
            $this->link = @mysql_connect(C('db_host'),C('db_user'), C('db_pwd') );
            if(!$this->link)
            {
                die(mysql_error());
            }
            else
            {
                if($this->server_info() > '4.1')
                {
                    $charset = str_replace('-', '',strtolower(C('db_charset')) );
                    mysql_query("SET character_set_connection=".$charset.", character_set_results=".$charset.", character_set_client=binary",$this->link);
                }
                if($this->server_info() > '5.0')
                {
                    mysql_query("SET sql_mode=''",$this->link);
                }
                if(!@mysql_select_db(C('db_name'), $this->link))
                {
                    die(mysql_error());
                }
            }
    }
    /**
     * 返回数据库版本...
     * @return string
     */
    private function server_info()
    {
        return mysql_get_server_info();
    }
    /**
     * 向数据库查询sql语句...
     *
     * @deprecated INSERT UPDATE DELETE
     * @param  string $sql
     * @return bool
     */
    public function query($sql)
    {
            $this->query_id = @mysql_query($sql,$this->link);
            if(!$this->query_id)
            {
                die(mysql_error());
            }
            else
            {
                $GLOBALS['QUERY_NUM']++;
                return $this->query_id;
            }
    }
    /**
     * 读取一条记录
     * @param string $id        主键
     * @param string $fields    获取字段
     * @return array
     */
    public function read($id, $fields='*')
    {
        $sql = "SELECT {$fields} FROM `{$this->table}` WHERE `id`='{$id}'";
        $this->query($sql);
        return $this->fetch_one();
    }
    /**
     * 插入一条记录
     * @param array $array  数组
     * @return bool
     */
    public function insert($array)
    {
        $fields = array();
        $values = array();
        foreach($array as $f => $v)
        {
            $fields[] = "`{$f}`";
            $values[] = "'".mysql_real_escape_string($v)."'";
        }
        $fields = implode(',', $fields);
        $values = implode(',', $values);
        $sql = "INSERT INTO `{$this->table}`({$fields}) VALUES({$values})";
        return $this->query($sql);
    }
    /**
     * 更新一条记录
     * @param int   $id     主键
     * @param array $array  数据数组
     */
    public function update($id, $array)
    {
       $values = array();
        foreach($array as $f => $v)
        {
            $values[] = "`{$f}`='".mysql_real_escape_string($v)."'";
        }
        $values = implode(',', $values);
        $sql = "UPDATE `{$this->table}` SET {$values} WHERE id='{$id}' LIMIT 1";
        return $this->query($sql);
    }
    /**
     * 删除一条记录
     * @param int $id   主键
     * @return bool
     */
    public function delete($id)
    {
        $sql = "DELETE FROM `{$this->table}` WHERE id='{$id}' LIMIT 1";
        return $this->query($sql);
    }
    /**
     * 获取分页列表的数据
     * @example
     * <code>
     * 参数：$wheres = array('id'=>23, 'NOT(`name` IS NULL)')
     * ==>   `id`='23' AND NOT(`name` IS NULL)
     * </code>
     * @param string|array  $fields 需要读取的字段（注：如果是字符串，则需要对字段名加上“``”标识）
     * @param array  $wheres    where条件数组，如果下标是数字，则直接加入条件，否则组合成:`{下标}`='{值}'这样的条件。最后用and链接
     * @param string $order     排序字段
     * @param int    $desc      是否是降序
     * @param int    $offset    偏移量
     * @param int    $limit     读取记录数
     * @param int    $return_total  是否返回满足条件的记录总数，默认为0，需要显示分页时可以设置为1.(如果需要获取分页代码，此值须为1)
     * @return array
     */
    public function get_list($fields="*", $wheres=array(), $order='', $desc=1, $page=1, $limit=20, $return_total=0)
    {
        //处理需要读取的字段
        if(is_array($fields) && !empty($fields))
        {
            $fields = '`'.implode('`,`', $fields) . '`';
        }
        //处理where条件
        if($wheres)
        {
            $where = array();
            foreach($wheres as $f => $v)
            {
                if(is_numeric($f))
                {
                    $where[] = $v;
                }
                else
                {
                    $where[] = "`{$f}`='".mysql_real_escape_string($v)."'";
                }
            }
            $where = implode(' AND ', $where);
        }
        else
        {
            $where = '1';
        }
        //处理orderby
        $orderby = '';
        if(!empty($order)){
            $orderby = "ORDER BY {$order} ";
        }
        $this->page->set_page($page, $limit);

        $tablestr = empty($this->table)?$this->from:"`{$this->table}`";
        $sql = "SELECT {$fields} FROM {$tablestr} ";
        if(!empty($where)){
            $sql .= " WHERE {$where} ";
        }
        $sql .= " {$orderby} ";
        if($this->page->offset!=0 || $this->page->limit!=0){
            $sql .= " LIMIT {$this->page->offset}, {$this->page->limit} ";
        }

        //var_dump($this);
        $this->query($sql);
        $data = $this->fetch_all();
        //var_dump($sql);
        if($return_total){
            //返回记录总数（分页）
            $sql = "SELECT count(*) FROM {$tablestr} WHERE {$where}";
            $this->query($sql);
            $total = $this->fetch_one();
            $this->page->set_total(current($total));
        }
        return $data;
    }
    public function get_block_list($wheres, $limit, $fields="*", $order='id'){
        $data = $this->get_list($fields, $wheres, $order, 1, 0, $limit);
        return $data;
    }
    /**
     * 返回单条记录数据...
     * @deprecated   MYSQL_ASSOC==1 MYSQL_NUM==2 MYSQL_BOTH==3
     * @param  int   $result_type
     * @return array
     */
    public function fetch_one($result_type = 1){
        return mysql_fetch_array($this->query_id,$result_type);
    }
    /**
     * 返回多条记录数据..
     * @deprecated    MYSQL_ASSOC==1 MYSQL_NUM==2 MYSQL_BOTH==3
     * @param   int   $result_type
     * @return  array
     */
    public function fetch_all($result_type = 1)   {
        while($row = mysql_fetch_array($this->query_id,$result_type)){
            $row_array[] = $row;
        }
        return $row_array;
    }
}
