<?php   
namespace Library\cls;
/**  
 * 分页类(与crud类结合使用)  
 */  
class cls_page   
{   
    public $offset = 0;   
    public $limit = 20;   
    public $total = 0;   
    private $page = 1;   
    private $pagenum = 0;   
    private $style = 'pages';   
    /**  
     * 设置当前页码  
     * @param $page  
     */  
    public function set_page($page = 1, $limit = 20)   
    {   
        //当前页数   
        $this->page = intval($page);   
        $this->limit = intval($limit);   
        if(!$this->page || $this->page < 1)   
        {   
            $this->page = 1;   
        }   
        $this->offset = $this->limit * ($this->page - 1);   
    }   
    /**  
     * 设置总记录数  
     * @param $total  
     */  
    public function set_total($total)   
    {   
        $this->total = intval($total);   
        if(!$this->total || $this->total < 1)   
        {   
            $this->total = 0;   
        }   
        $this->pagenum = ceil($this->total / $this->limit);   
    }   
    /**  
     * 获取分页代码  
     * @param $url  
     * @return string  
     */  
    public function get_html($url, $style='')   
    {   
        $source = '';   
        if($this->page > $this->pagenum)   
        {   
            $this->page = $this->pagenum;   
        }   
        if(!emptyempty($style))   
        {   
            $this->style = $style;   
        }   
        if($this->pagenum > 1)   
        {   
            $source = "<div class='".$this->style."'><a href='{$url}&page=1' mce_href='{$url}&page=1' class='nextprev'>首页 </a>";   
            $next = $this->page + 1;   
            $pre = $this->page - 1;   
            if($this->page > 1)   
            {   
                $source .= "<a href='{$url}&page={$pre}' mce_href='{$url}&page={$pre}'>上一页</a>";   
            }   
            $flag = 0;   
            for($i = $this->page - 5; $i <= $this->page - 1; $i++)   
            {   
                if($i > 0)   
                {   
                    $source .= "<a href='{$url}&page=$i' mce_href='{$url}&page=$i' class='nextprev'> $i </a>";   
                }   
            }   
            $source .= "<span class='current'>".$this->page."</span>";   
            if($this->page < $this->pagenum)   
            {   
                for($i = $this->page + 1; $i <= $this->pagenum; $i++)   
                {   
                    $source .= "<a href='{$url}&page=$i' mce_href='{$url}&page=$i'> $i </a>";   
                    $flag++;   
                    if($flag == 5)   
                    {   
                        break;   
                    }   
                }   
            }   
            if($this->page < $this->pagenum)   
            {   
                $source .= "<a href='{$url}&page={$next}' mce_href='{$url}&page={$next}'>下一页</a>";   
            }   
            $source .= "<a href='\' mce_href='{$url}&page=".$this->pagenum."'>尾页</a></div>";   
        }   
        return $source;   
    }   
}   
?> 
