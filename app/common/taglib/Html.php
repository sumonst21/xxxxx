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
class Html extends TagLib{
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'input'         => array('attr'=>'value,name','close'=>0),
        'select'        => array('attr'=>'value,name,options','close'=>0),
        'checkbox'      => array('attr'=>'value,name,options','close'=>0),
        'radio'         => array('attr'=>'value,name,options','close'=>0),
        'switch'        => array('attr'=>'value,name,options','close'=>0),
        'editor'        => array('attr'=>'value,name','close'=>0),
        'textarea'      => array('attr'=>'value,name','close'=>0),
        'button'        => array('attr'=>'value,name','close'=>0),
        'upload'        => array('attr'=>'value,name','close'=>0),
    );
    protected function parseAttrValue($tag=array()){
        //$ret = [];
        $append='';
        if(empty($tag['id'])){$tag['id']=$tag['name'];}
        if(empty($tag['layui-filter'])){$tag['layui-filter']=$tag['name'];}
        foreach ($tag as $_key => $_val) {
            if ($_val == "request") {
                $append .= $_key.'="{:input(\''.$_key.'\')}" ';
            } elseif ($_val{0} == "$") {
                $append .= $_key.'="{'.$_val.'}" ';
            } elseif ($_val{0} == ":") {
                $append .= $_key.'="{'.$_val.'}" ';
            } else {
                $append .= $_key."=\"{$_val}\" ";
            }
        }
        return $append;
    }
    public function tagInput($tag) {
        $attrs =array(
            'class'=>'layui-input',
            'type'  => 'text',
            'autocomplete'  =>  "off"
        );
        $tag = array_merge($attrs,$tag);
        $append= $this->parseAttrValue($tag);
        return '<input '.$append.' />';
    }
    public function tagUpload($tag) {
        $attrs =array(
            'length'=>'1',
            'type'=>'img',
        );
        $tag = array_merge($attrs,$tag);
        $tag['value'] = str_replace(array('[',']','｛','｝'),array('{','}','{','}'),$tag['value']);
        $append= $this->parseAttrValue($tag);
        return '<div class="fm"><input type="text" class="layui-input" autocomplete="off" placeholder="请选择图片" readonly="readonly"  '.$append.' /><button class="layui-btn" type="button">选择</button></div>';
    }
    public function tagSelect($tag) {
        $tags =array(
            'values'=>'',
            'options'=>'',
            'output'=>'',
            'first'=>'',
            'selected'=>'',
        );
        $tag = array_merge($tags,$tag);

        $options    = $tag['options'];unset($tag['options']);
        $values     = $tag['values'];unset($tag['values']);
        $output     = $tag['output'];unset($tag['output']);
        $first      = $tag['first'];unset($tag['first']);
        $selected   = $tag['selected'];unset($tag['selected']);

        $append= $this->parseAttrValue($tag);
        $parseStr = '<select '.$append.'>';
        if(!empty($first)) {
            $parseStr .= '<option value="" >'.$first.'</option>';
        }
        if(!empty($options)) {
            if(0===strpos($options,':')) {
                $parseStr.='<?php $_options='.substr($options,1).';';
            }else{
                $parseStr.='<?php $_options=$'.$options.';';
            }
            if(!empty($selected)) {
                $this->tpl->parseVar($selected);
                $parseStr   .= '$_selected=  explode(",",'.$selected.');';
            }
            $parseStr   .= 'foreach($_options as $key=>$val) { ?>';
            if(!empty($selected)) {
                $parseStr   .= '<?php        if(!empty('.$selected.') && ('.$selected.' == $key || in_array($key,$_selected))) { ?>';
                $parseStr   .= '<option selected="selected" value="<?php         echo $key ?>"><?php         echo $val ?></option>';
                $parseStr   .= '<?php        }else { ?><option value="<?php      echo $key ?>"><?php         echo $val ?></option>';
                $parseStr   .= '<?php        } ?>';
            }else {
                $parseStr   .= '<option value="<?php         echo $key ?>"><?php         echo $val ?></option>';
            }
            $parseStr   .= '<?php        } ?>';
        }else if(!empty($values)) {
            $parseStr   .= '<?php         for($i=0;$i<count($'.$values.');$i++) { ?>';
            if(!empty($selected)) {
                $parseStr   .= '<?php        if(isset($'.$selected.') && ((is_string($'.$selected.') && $'.$selected.' == $'.$values.'[$i]) || (is_array($'.$selected.') && in_array($'.$values.'[$i],$'.$selected.')))) { ?>';
                $parseStr   .= '<option selected="selected" value="<?php         echo $'.$values.'[$i] ?>"><?php         echo $'.$output.'[$i] ?></option>';
                $parseStr   .= '<?php        }else { ?><option value="<?php      echo $'.$values.'[$i] ?>"><?php         echo $'.$output.'[$i] ?></option>';
                $parseStr   .= '<?php        } ?>';
            }else {
                $parseStr   .= '<option value="<?php         echo $'.$values.'[$i] ?>"><?php         echo $'.$output.'[$i] ?></option>';
            }
            $parseStr   .= '<?php        } ?>';
        }
        $parseStr   .= '</select>';
        return $parseStr;
    }
    public function tagRadio($tag) {
        $tags =array(
            'options'=>'',
            'checked'=>'',
        );
        $tag = array_merge($tags,$tag);
        $options    = $tag['options'];unset($tag['options']);
        $checked   = $tag['checked'];unset($tag['checked']);
        $append= $this->parseAttrValue($tag);

        $parseStr = '';
        if(!empty($options)) {
            if(strpos($options,'$')===0){
                $parseStr.='<?php $_options='.$options.';?>';                
            }elseif(strpos($options,':')===0){
                $parseStr.='<?php $_options='.substr($options,1).';?>';
            }elseif(strpos($options,',')>0) {
                $parseStr.='<?php $_options="'.$options.'";?>';
            }
            $parseStr   .= '<?php if(is_string($_options)) {';
            $parseStr   .= 'if(strpos($_options,"|")>0){';
            $parseStr   .= '    $_options=explode(",",$_options);';
            $parseStr   .= '    $_RESULT=array();';
            $parseStr   .= '    foreach($_options as $__RK=>$__RV){';
            $parseStr   .= '        $TMP=explode("|",$__RV);';
            $parseStr   .= '        $_RESULT[$TMP[0]]=$TMP[1];';
            $parseStr   .= '    }';
            $parseStr   .= '    $_options=$_RESULT;';
            $parseStr   .= '}else{';
            $parseStr   .= '    $_options=explode(",",$_options);';
            $parseStr   .= '}}?>';

            if($checked){
                if(strpos($checked,'$')===0){
                    $parseStr.='<?php $checked='.$checked.';?>';
                }elseif(strpos($checked,':')===0){
                    $parseStr.='<?php $checked='.substr($checked,1).';?>';
                }elseif(strpos($checked,',')>0) {
                    $parseStr.='<?php $checked="'.$checked.'";?>';
                }
            }else{
                $parseStr   .= '<?php $checked=  "";?>';
            }
            $parseStr   .= '<?php foreach($_options as $key=>$val) {';
            $parseStr   .= '    if($checked==$key) {?>';
            $parseStr   .= '        <input type="radio" value="<?php echo $key;?>" title="<?php echo $val;?>" '.$append.' checked="checked">';
            $parseStr   .= '    <?php }else {?>';
            $parseStr   .= '        <input type="radio" value="<?php echo $key;?>" title="<?php echo $val;?>" '.$append.'>';
            $parseStr   .= '    <?php }?>';
            $parseStr   .= '<?php } ?>';
        }
        return $parseStr;
    }
    public function tagCheckbox($tag) {
        $tags =array(
            'options'=>'',
            'output'=>'',
            'first'=>'',
            'checked'=>'',
        );
        $tag = array_merge($tags,$tag);

        $options    = $tag['options'];unset($tag['options']);
        $name    = $tag['name'] ? $tag['name'] : '';
        $checked   = $tag['checked'];unset($tag['checked']);

        $append= $this->parseAttrValue($tag);
        $parseStr = '';
        if(!empty($options)) {
            if(0===strpos($options,':')) {
                $parseStr.='<?php $_options='.substr($options,1).';';
            }else{
                $parseStr.='<?php $_options=$'.$options.';';
            }
            $parseStr   .= '<?php        $_selected=  explode(",",$'.$checked.'); ?>';
            $parseStr   .= 'foreach($_options as $key=>$val) { ?>';
            if(!empty($checked)) {                
                $parseStr   .= '<?php        if(!empty($'.$checked.') && ($'.$checked.' == $key || in_array($key,$_selected))) { ?>';
                $parseStr   .= '<label><input type="checkbox" name="'.$name.'" checked="checked" value="<?php echo $key ?>"><?php         echo $val ?></label>';
                $parseStr   .= '<?php        }else { ?><label><input type="checkbox" name="'.$name.'" checked="checked" value="<?php echo $key ?>"><?php echo $val ?></label>';
                $parseStr   .= '<?php        } ?>';
            }else {
                $parseStr   .= '<label><input type="checkbox" name="'.$name.'" checked="checked" value="<?php echo $key ?>"><?php echo $val ?></label>';
            }
            $parseStr   .= '<?php        } ?>';
        }
        return $parseStr;
    }
    public function tagSwitch($tag) {
        $value = !empty($tag['value']) ? $tag['value'] : '';unset($tag['value']);
        $options = !empty($tag['options']) ? $tag['options'] : '是|否';unset($tag['options']);
        $options = str_replace(array('[',']','｛','｝'),array('{','}','{','}'),$options);
        //$href = !empty($tag['href']) ? $tag['href'] : '';unset($tag['href']);

        $append= $this->parseAttrValue($tag);
        $ckd = '<?php if('.$value.'==1){echo " checked";}?>';
        $parseString = '<input type="checkbox" value="1" lay-skin="switch" lay-text="'.$options.'" '.$ckd.' '.$append.' >';
        return $parseString;
    }
    public function tagTextarea($tag,$content='') {
        $attrs =array(
            'type'=>'',
        );
        $tag = array_merge($attrs,$tag);
        $value = '';
        if(!empty($tag['value'])){
            $value = str_replace(array('[',']','｛','｝'),array('{','}','{','}'),$tag['value']);
            //$this->tpl->parseVar($value);
            unset($tag['value']);
        }
        $append= $this->parseAttrValue($tag);

        return '<textarea class="layui-textarea" '.$append.'>'.$value.'</textarea>';
    }
    public function tagButton($tag) {
        $value = !empty($tag['value']) ? $tag['value'] : '';unset($tag['value']);
        $append= $this->parseAttrValue($tag);
        $parseString = '<button '.$append.' >'.$value.'</button>';
        return $parseString;
    }
}
