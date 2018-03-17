<?
namespace Library\webutils;
/**  
 * 分页类
 */
class pages{
    private $config=array();

    public function __construct($config){
        $this->config = $config;
    }
    public function showpage(){
        $total= (int)$this->config['total'];
        $page= (int)$this->config['page'];
        $epage= (int)$this->config['epage'];
        $pagetype = isset($this->config['pagetype']) ? (int)$this->config['pagetype'] : 0;
        $url_prefix = isset($this->config['url_prefix']) ? $this->config['url_prefix'] : "";
        $url_postfix = isset($this->config['url_postfix']) ? $this->config['url_postfix'] : "";
        $pageparam = isset($this->config['param']) ? $this->config['param']:array();

        $str_param = "";
        $pK = array('op', 't', 'classify','search');
        foreach ($pageparam as $k=>$v){
            if(  in_array($k, $pK) && $v!=''){
                $str_param .="/$k/$v";
            }
        }

        if($total % $epage){
            $page_num=(int)($total / $epage)+1;
        } else {
            $page_num=$total / $epage;
        }
        if($page==""){
            $page=1;
        }

        $action = strstr($_SERVER['REQUEST_URI'],"?");
        $a = explode("&",$action);
        $vs=false;
        foreach($a as $key=>$v){
            if(is_numeric(strpos($v,"type"))) $vs=$v;
        }

        if($vs){
            $action = (is_numeric(strpos($vs,"?")))?$vs:"&".$vs;
        }else{
            $action="";
        }

        $front_url = $url_prefix."/index1.html".$action.$url_postfix;
        $up_url    = $url_prefix."/index".($page-1).".html".$action.$url_postfix;
        $next_url  = $url_prefix."/index".($page+1).".html".$action.$url_postfix;
        $tail_url  = $url_prefix."/index".($page_num).".html".$action.$url_postfix;
        if( $pagetype ){
            $front_url = $url_prefix."/page/1".$str_param.$action.$url_postfix;
            $up_url    = $url_prefix."/page/".($page-1).$str_param.$action.$url_postfix;
            $next_url  = $url_prefix."/page/".($page+1).$str_param.$action.$url_postfix;
            $tail_url  = $url_prefix."/page/".($page_num).$str_param.$action.$url_postfix;
        }

        if ($page!=1 && $page>$page_num){
            header("location:" + $url_prefix +"index{$page_num}.html".$url_postfix);
        }
        if($page_num>1){
            $display = '<div class="paging"><ul class="pagination pagenum">';
            //上一页
            if($page==1){
                $display .= '<li class="disabled"><a href="javascript:;">&laquo;</a></li>';
            }else{
                $display .= "<li class='disabled'><a href='$up_url'>&laquo;</a></li>";
            }
            // left
            if( $page>5 ){
                $display .= "<li><a href='{$front_url}'>1</a></li>";
                $display .= "<li class='disabled'>…</li>";
            }
            // middle
            for($i=$page-($page>5&&$page<=$page_num-5?2:4);$i<=$page+($page<=$page_num-5&&$page>5?2:4);$i++){
                if($i>=1 && $i<=$page_num){
                    if($i==$page){
                        $display .= "<li class='active'><a href='javascript:;'>$i</a></li>";
                    }else{
                        if($pagetype){
                            $display .= "<li><a href='{$url_prefix}/page/{$i}{$str_param}{$action}{$url_postfix}'>$i</a></li>";
                        }else{
                            $display .= "<li><a href='{$url_prefix}/index{$i}.html{$action}{$url_postfix}'>$i</a></li>";
                        }
                    }
                }
            }
            //right
            if( $page<=$page_num-5 ){
                $display .= "<li class='disabled'>…</li>";
                $display .= "<li><a href='{$tail_url}'>".($page_num)."</a></li>";
            }

            //下一页
            if($page==$page_num || $page_num<1){
                $display .= '<li class="disabled"><a href="javascript:;">&raquo;</a></li>';
            }else{
                $display .= "<li><a href='{$next_url}'>&raquo;</a></li>";
            }
            $display .= "</ul></div>";
        }

        return $display;
    }
}



?>

