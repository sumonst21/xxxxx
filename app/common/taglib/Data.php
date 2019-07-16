<?php
/**
 * Data标签库驱动
 * @category   Extend
 * @package  Extend
 * @subpackage  Driver.Taglib
 * @author    liu21st <liu21st@gmail.com>
 */
namespace app\common\taglib;
use think\template\TagLib;
class Data extends TagLib{
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'article'   => array('attr'=>'var,name','close'=>0,'alias'=>'one',"level"=>3),
        'list'      => array('attr'=>'var,name','close'=>1,"level"=>3),
        'loop'      => array('attr'=>'var,name','close'=>1,"level"=>3),
    );
    function parseDataVal($tag){
        $_data=array();
        foreach ($tag as $_key => $_val) {
            if($_key{0}=='_'){ $_key=substr($_key,1);}
            if ($_val == "request") {
                $_data[] = "'{$_key}'=>input('{$_key}')";
            } elseif ($_val{0} == "$") {
                $_data[] = "'{$_key}'=>{$_val}";
            } elseif ($_val{0} == ":") {
                $_data[] = "'{$_key}'=>".substr($_val,1);
            } else {
                $_data[] = "'{$_key}'=>'{$_val}'";
            }
        }
        return "array(" . join(",", $_data) . ")";
    }
    public function tagList($tag,$content='') {
        $name = $tag['name'] ? $tag['name'] : Request()->controller();unset($tag['name']);
        $function = !empty($tag['function']) ? $tag['function'] : 'GetList';unset($tag['function']);
        $var = !empty($tag['var']) ? $tag['var'] : 'var';unset($tag['var']);
        $data = $this->parseDataVal($tag);

        $display = "<?php \$model=model('$name');\$map = {$data};";
        $display .=" \${$var}=\$model->{$function}(\$map);";
        $display .= '?>'.$content."<?php unset(\$model);unset(\$map);?>";
        unset($_data);unset($pagesize);unset($limit);unset($name);unset($var);unset($order);
        return $display;
    }
    public function tagLoop($tag,$content='') {
        $name = $tag['name'] ? $tag['name'] : Request()->controller();unset($tag['name']);
        $function = !empty($tag['function']) ? $tag['function'] : 'GetList';unset($tag['function']);
        $var = !empty($tag['var']) ? $tag['var'] : 'var';unset($tag['var']);
        $data = $this->parseDataVal($tag);
        $display = "<?php \$model=model('{$name}');\$map = {$data};";
        $display .=" \$__RESULT__=\$model->{$function}(\$map);?>";
        $display .= "{volist name=\"__RESULT__.list\" id=\"{$var}\"}";
        $display .= $content."{/volist}<?php unset(\$model);unset(\$map);?>";
        unset($_data);unset($pagesize);unset($limit);unset($name);unset($var);unset($order);
        return $display;
    }
    public function tagArticle($tag,$content='') {
        $name = $tag['name'] ? $tag['name'] : Request()->controller();unset($tag['name']);
        $function = !empty($tag['function']) ? $tag['function'] : 'GetView';unset($tag['function']);
        $var = !empty($tag['var']) ? $tag['var'] : 'var';unset($tag['var']);
        $data = $this->parseDataVal($tag);
        $display = "<?php \$model=model('{$name}');\$map = {$data};";
        $display .=" \${$var}=\$model->{$function}(\$map);?>";
        $display .= $content."<?php unset(\$model);unset(\$map);?>";
        unset($_data);unset($name);unset($var);
        return $display;
    }
}
