<?php

/**
 * @author Tinico <zhuting@haowan123.com>
 * 用法：(所有配置项都是可选的)
 * $this->renderPartial('//layouts/common_grid_partial',
 *     array(
 *       'type' => 'navTab', //UI表现形式，有navTab和dialog,div三种，默认为navTab
 *       'divID' => '局部刷新DIV ID',当UI表现形式为div时可以使用
 *       'action' => '', //searchForm、pagerForm表单提交地址，默认为当前页面地址
 *       'method' => 'post', //searchForm、pagerForm表单提交方式，默认为POST
 *       'searchButton' => '', //搜索按钮显示文字，默认为查询
 *       'urlPatch' => array('server'=>$server),//URL附加参数，生成操作按钮时会自动附加到URL上，主要就是add，export按钮使用
 *       'data'   => array(), //数据源，二维数据列表
 *       'column' => array('datakey1'=>'headtext1', 'datakey2'=>'headtext2', ...), //列规则，自动按顺序生成表头和数据列表，键名为数据源字段名，值为列表头显示的文字，如果不定义此项，则自动尝试抽取数据源第一行键名作为列表头
 *       'tips' => array('datakey1'=>'tips1', 'datakey2'=>'tips2', ...),//表头提示信息
 *       'pk'     => 'datakey', //行主键，值为数据源字段名，会自动将每行该字段的值赋给所在行<tr target="pk" rel="{$data['datakey']}">
 *       'pages'   => CPagination, //分页组件，如果不定义则不带分页按钮组件
 *       'buttons'=> array( //在每行最后附加操作按钮（不建议使用此项，推荐使用toolBar按钮组件结合dwz的url变量替换功能），请使用CHtml::link工具按照dwz按钮定义自行生成，button的每一个元素为一个按钮
 *           'view', //支持3个常规按钮别名
 *           'modify',
 *           'del',
 *           CHtml::link(Yii::t('','删除'), Yii::app()->controller->createUrl('del', array('pk'=>'{pk}')), array('class'=>'btnDel', 'target'=>'ajaxTodo', 'title'=>'删除', 'onclick'=>'return this.parentNode.click()', 'encode'=>false )),
 *           CHtml::link(Yii::t('','编辑'), Yii::app()->controller->createUrl('update', array('pk'=>'{pk}')), array('class'=>'btnEdit', 'target'=>'dialog', 'title'=>'编辑', 'width'=>800, 'height'=>480, 'onclick'=>'return this.parentNode.click()', 'encode'=>false ))
 *       ),
 *       'layoutH'=> 110, //搜索面板固定高度，参见dwz释义，如果不定义，默认为138
 *       'checkbox'=>false,//是否在第一列显示复选框，启用此项必须设置PK
 *       'searchPannel' => array( //定义搜索面板中的元素，数组元素键值代表Label（不需要Label的可不设置键值），元素值代表内容，请使用CHtml生成或自行组建HTML代码
 *           $this->renderPartial('//layouts/form_channel_select', null, true),//服务器选择列表，请将第三个参数设置为true以便返回代码碎片
 *           $this->renderPartial('//layouts/form_date_selector', array(
 *               'pickerId'=>array('start_date'=>'','end_date'=>'') //日期选择框，数组定义：'Dom元素name'=>'默认值'，如果默认值为空则自动取session，如果session也没有值则默认为当前日期
 *           ), true),
 *           '角色名：' => CHtml::textField('player_name'),
 *           '角色ID：' => CHtml::textField('player_id')
 *       ),
 *       'orderColumn' => array(), //可排序字段
 *       'toolBar' => array( //需要对表格数据进行操作时推荐使用本项功能，支持7个常规按钮别名，自定义按钮请使用CHtml::link按照dwz规则生成，例子如下
 *           'view',
 *           'add' => array('width'=>500,'height'=>200),//这种方式可以定义对话框具体属性
 *           'modify',
 *           'del',
 *           'delall',
 *           'export',
 *           CHtml::link('<span>' . Yii::t('','添加') . '</span>', Yii::app()->controller->createUrl('add'), array('class'=>'add', 'target'=>'dialog', 'width'=>800, 'height'=>480) ),
 *           CHtml::link('<span>' . Yii::t('','删除') . '</span>', Yii::app()->controller->createUrl('del', array('pk'=>'{pk}')), array('class'=>'delete', 'target'=>'ajaxTodo', 'title'=>Yii::t('','是否确定删除？'), 'warn'=>Yii::t('','请选择一条数据！')) ),
 *       )
 *     )
 * );
 *
 * 注意事项：
 * 0、所有通过别名自动生成的按钮被点击，一定会提交 $_GET['opt'] 到服务端供后台判断用户进行的操作，$_GET['opt']的值即为按钮别名
 * 1、toolBar中定义的四个按钮(view,modify,del,delall)被点击，会提交 $_GET['pk'] 供后台判断操作的数据对象，值为用户在表格中选中行的pk；
 *    其中delall按钮提交的pk数据为一个数组，包含所有勾选行的pk。
 * 2、toolBar中定义的几个按钮(view,add,modify)被点击，默认会打开800*480小窗口，如果不符合需要，可设置属性或完全自行定义
 * 3、toolBar中自定义按钮要使用pk值，请在 href 中使用 {pk} 作为占位符
 * 4、dwz分页功能使用 $_POST['pageNum'] 提交页码，如果要使用CActiveDataProvider，则需要在fetchData之前手动设置符合CDataProvider要求的页码即可
 *    方法一：
 *        if(isset($_POST['pageNum'])) $_GET['model类名_page'] = (int)$_POST['pageNum'];
 *        或
 *        $pagenum = intval(Yii::app()->request->getParam('pageNum', 0)); $_GET['PlayerGamedata_page'] = $pagenum;
 *    方法二（推荐）：
 *        $pagenum = intval(Yii::app()->request->getParam('pageNum', 1));
 *        $pagesize = intval(Yii::app()->request->getParam('numPerPage', 20));
 *        $provider = $model->search();
 *        $provider->pagination->pageSize = $pagesize;
 *        $provider->pagination->currentPage = $pagenum - 1;//注意YII中的页码从0开始
 *  5、buttons中链接可以使用的图标样式包含：a.btnAdd, a.btnDel, a.btnView, a.btnEdit, a.btnSelect, a.btnInfo, a.btnAssign, a.btnLook, a.btnAttach
 *     具体使用方法和 toolBar 一样
 *     如果要自行定义按钮并使用{pk}占位符，请设置按钮点击事件：'onclick'='return this.parentNode.click()'
 *  6.如果opt中设置extra(数组)参数则会附加到url中   
 */

/******************************************************************************
 * 参数默认值
 *****************************************************************************/
//UI表现形式默认为navTab
if (!isset($type) || !in_array($type, array('navTab', 'dialog', 'div'))) {
    $type = 'navTab';
}
//当UI表现形式为div时要求必须设置divID
if($type == 'div' && empty($divID)) {
    echo Yii::t('','局部UI必须设置divID');
    return;
}
//searchForm、pagerForm表单提交地址，默认为当前页面地址
if (!isset($action)) {
    $action = Yii::app()->request->url;
}
//searchForm、pagerForm表单提交方式，默认为POST
if (!isset($method) || !in_array($method, array('get', 'post'))) {
    $method = 'post';
}
//搜索按钮显示文字，默认为查询
if (!isset($searchButton)) {
    $searchButton = Yii::t('','查询');
}
//搜索按钮点击事件
if (empty($searchButtonEvent)) {
    $searchButtonEvent = null;
}
//URL附加参数
if (!isset($urlPatch) || !is_array($urlPatch)) {
    $urlPatch = array();
}
//表格样式
if (empty($tableclass)) {
    $tableclass = 'table';
}
//layoutH默认138
if (!isset($layoutH) || !is_numeric($layoutH)) {
    $layoutH = $tableclass == 'table' ? 48 : 26;
    if(!empty($searchPannel)) {
        $layoutH += 62;
    }
    if(!empty($toolBar)) {
        $layoutH += 27;
    }
}
//默认不显示复选框
if (!isset($checkbox) || $checkbox != true) {
    $checkbox = false;
}
//初始化$data
if(empty($data)) {
    $data = array();
}
//默认表头为$data键值列
if (empty($column)) {
    $column = isset($data[0]) ? array_keys($data[0]) : array();
}
//排序参数
if(empty($orderField)) {
    $orderField = Yii::app()->request->getParam('orderField');
}
if(empty($orderDirection)) {
    $orderDirection = Yii::app()->request->getParam('orderDirection');
}
//表头提示信息
if(empty($tips)) {
    $tips = array();
}
//分页组件，如果$pages不是CPagination的实例或者记录总数为0，则把$pages当成空值来处理
if (!empty($pages)) {
    if(!($pages instanceof CPagination) || $pages->getItemCount() == 0) {
        $pages = null;
    }
}

/******************************************************************************
 * 输出搜索面板SearchPannel
 *****************************************************************************/
if(empty($searchPannel)) {
    echo '<div class="pageHeader" style="display:none;"><div class="searchBar">';
    $searchPannel = array();
} else {
    echo '<div class="pageHeader"><div class="searchBar">';
}
//局部刷新支持
if($type=='div') {
    echo CHtml::beginForm($action, $method, array ('id'=>'pagerForm', 'onsubmit'=>"return {$type}Search(this,'{$divID}');", 'encode' => false ));
} else {
    echo CHtml::beginForm($action, $method, array ('id'=>'pagerForm', 'onsubmit'=>"return {$type}Search(this);" ));
}
//分页信息
echo CHtml::hiddenField('pageNum',        Yii::app()->request->getParam('pageNum',1));
echo CHtml::hiddenField('numPerPage',     empty($pages) ? Yii::app()->params['pageSize'] : $pages->getPageSize());
echo CHtml::hiddenField('orderField',     $orderField);
echo CHtml::hiddenField('orderDirection', $orderDirection);
//输出搜索面板HTML控件
echo '<table class="searchContent"><tr>';
foreach($searchPannel as $key => $val) {
    echo '<td>';
    if (!empty($key) && !is_numeric($key)) {
        echo $key.'：';
    }
    echo $val;
    echo '</td>';
}
echo '</tr></table>';
echo '<div class="subBar"><ul><li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="'.$searchButtonEvent.'">'.$searchButton.'</button></div></div></li></ul></div>';
echo CHtml::endForm();
echo '</div></div>';

/*内容区域开始*/
echo '<div class="pageContent">';

/******************************************************************************
 * 输出ToolBar工具栏
 *****************************************************************************/
if(!empty($toolBar)) {
    echo '<div class="panelBar"><ul class="toolBar">';
    foreach($toolBar as $name => $attributes) {
        if(!is_array($attributes)) {
            $name = $attributes;
            $attributes = array();
        }
        //设置按钮文字
        if(!empty($attributes['text'])) {
            $text = $attributes['text'];
            unset($attributes['text']);
        } else {
            $text = null;
        }        
        //改变OPT操作
        if(!empty($attributes['opt'])) {
            $opt = $attributes['opt'];
            //附加数组参数
            $extra = !empty($attributes['extra']) ? $attributes['extra'] : array();
            unset($attributes['opt']);
            unset($attributes['extra']);
        } else {
            $opt = null;
            $extra = array();
        }
        
        switch($name) {
            case '|':
                echo '<li class="line">line</li>';
                break;
            case 'add':
                echo '<li>';
                echo CHtml::link(
                    '<span>' . (empty($text) ? Yii::t('','添加') : $text) . '</span>',
                    Yii::app()->controller->createUrl($this->action->id, array('opt'=>empty($opt) ? 'add' : $opt) + $extra + $urlPatch),
                    $attributes + array('class'=>'add', 'target'=>'dialog', 'width'=>800, 'height'=>480, 'mask'=>true)
                );
                echo '</li>';
                break;
            case 'del':
                echo '<li>';
                echo CHtml::link(
                    '<span>' . (empty($text) ? Yii::t('','删除') : $text) . '</span>',
                    Yii::app()->controller->createUrl($this->action->id, array('opt'=>empty($opt) ? 'del' : $opt, 'pk'=>'{pk}') + $extra + $urlPatch),
                    $attributes + array('class'=>'delete', 'target'=>'ajaxTodo', 'title'=>Yii::t('','是否确定删除？'), 'warn'=>Yii::t('','请选中要删除的数据！'))
                );
                echo '</li>';
                break;
            case 'delall':
                echo '<li>';
                echo CHtml::link(
                    '<span>' . (empty($text) ? Yii::t('','批量删除') : $text) . '</span>',
                    Yii::app()->controller->createUrl($this->action->id, array('opt'=>empty($opt) ? 'delall' : $opt) + $extra + $urlPatch),
                    $attributes + array('class'=>'delete', 'target'=>'selectedTodo', 'targetType'=>$type, 'rel'=>'pk[]', 'title'=>Yii::t('','是否确定批量删除？'), 'warn'=>Yii::t('','请选中要批量删除的数据！'))
                );
                echo '</li>';
                break;
            case 'export':
                echo '<li>';
                echo CHtml::link(
                    '<span>' . (empty($text) ? Yii::t('','导出EXCEL') : $text) . '</span>',
                    Yii::app()->controller->createUrl($this->action->id, array('opt'=>empty($opt) ? 'export' : $opt)  + $extra + $urlPatch),
                    $attributes + array('class'=>'icon', 'target'=>'dwzExport', 'targettype'=>$type, 'title'=>Yii::t('','确定要导出这些记录吗？'))
                );
                echo '</li>';
                break;
            case 'chart':
                echo '<li>';
                echo CHtml::link(
                    '<span>' . (empty($text) ? Yii::t('','打开图表') : $text) . '</span>',
                    Yii::app()->controller->createUrl($this->action->id, array('opt'=>empty($opt) ? 'chart' : $opt) + $extra + $urlPatch),
                    $attributes + array('class'=>'icon', 'onclick'=>'return openChart(this)', 'mask'=>true)
                );
                echo '</li>';
                break;
            case 'modify':
                echo '<li>';
                echo CHtml::link(
                    '<span>' . (empty($text) ? Yii::t('','修改') : $text) . '</span>',
                    Yii::app()->controller->createUrl($this->action->id, array('opt'=>empty($opt) ? 'modify' : $opt, 'pk'=>'{pk}') + $extra + $urlPatch),
                    $attributes + array('class'=>'edit', 'target'=>'dialog', 'warn'=>Yii::t('','请选中要修改数据！'), 'width'=>800, 'height'=>480, 'mask'=>true)
                );
                echo '</li>';
                break;
            case 'view':
                echo '<li>';
                echo CHtml::link(
                    '<span>' . (empty($text) ? Yii::t('','查看') : $text) . '</span>',
                    Yii::app()->controller->createUrl($this->action->id, array('opt'=>empty($opt) ? 'view' : $opt, 'pk'=>'{pk}') + $extra + $urlPatch),
                    $attributes + array('class'=>'icon', 'target'=>'dialog', 'warn'=>Yii::t('','请选择中要查看的数据！'), 'width'=>800, 'height'=>480, 'mask'=>true)
                );
                echo '</li>';
                break;
            default:
                echo '<li>'.$name.'</li>';
                break;
        }
    }
    echo '</ul></div>';
}

/******************************************************************************
 * 输出Grid
 *****************************************************************************/
//局部刷新支持
if($type=='div') {
    echo '<table class="'.$tableclass.'" width="100%" layoutH="' . $layoutH . '" rel="'.$divID.'"   >';
} else {
    echo '<table class="'.$tableclass.'" width="100%" layoutH="' . $layoutH . '">';
}
if (!empty($column)) {
    echo '<thead><tr>';
    //自动输出全选复选框
    if ($checkbox && !empty($pk)) {
        echo '<th width="25" align="center">';
        echo CHtml::checkBox('', false, array('group' => 'pk[]', 'class' => 'checkboxCtrl', 'id'=>false));
        echo '</th>';
    }
    //自动输出带排序功能的表头
    foreach ($column as $field => $header) {
        $htmlOption = array('align'=>'center','style'=>'font-weight:bold;');
        if(isset($tips[$field])) {
            $htmlOption['tips'] = preg_replace('/[\r\n\'\"]/i', '', nl2br($tips[$field]));
            $htmlOption['encode'] = false;
        }
        if(!empty($orderColumn) && is_array($orderColumn) && in_array($field, $orderColumn)) {
            $htmlOption['orderField'] = $field;
            $htmlOption['style'] .= 'color:blue;';
            if($orderField == $field) {
                $htmlOption['class'] = $orderDirection;
            }
        }
        echo CHtml::tag('th', $htmlOption, $header);
    }
    //自动输出操作栏
    if (!empty($buttons)) {
        echo '<th width="'.(60*count($buttons)).'" align="center">' . Yii::t('','操作') . '</th>';
    }
    echo '</tr></thead>';
}
//输出<tbody>
echo '<tbody>';
foreach ($data as $row) {
    //自动输出行主键
    echo empty($pk) ? '<tr>' : "<tr target='pk' rel='" . CHtml::value($row, $pk) . "'>";
    //自动输出复选框
    if ($checkbox && !empty($pk)) {
        echo '<td>';
        echo CHtml::checkBox('pk[]', false, array('value' =>  CHtml::value($row, $pk), 'id'=>false));
        echo '</td>';
    }
    //自动输出各列的值
    foreach ($column as $key => $header) {
        if (is_numeric($key)) {
            echo '<td>' , CHtml::value($row, $header) , '</td>';
        } else {
            echo '<td>' , CHtml::value($row, $key) , '</td>';
        }
    }
    //自动输出操作按钮
    if (!empty($buttons)) {
        echo '<td>';
        foreach($buttons as $name => $attributes) {
            if(!is_array($attributes)) {
                $name = $attributes;
                $attributes = array();
            }
            //设置按钮文字
            if(!empty($attributes['text'])) {
                $text = $attributes['text'];
                unset($attributes['text']);
            } else {
                $text = null;
            }
            //改变OPT操作
            if(!empty($attributes['opt'])) {
                $opt = $attributes['opt'];
                unset($attributes['opt']);
            } else {
                $opt = null;
            }
            
            switch ($name) {
                case 'del':
                    echo CHtml::link(
                        empty($text) ? Yii::t('','删除') : $text,
                        Yii::app()->controller->createUrl($this->action->id, array('opt'=>empty($opt) ? 'del' : $opt, 'pk'=>'{pk}') + $urlPatch),
                        $attributes + array('onclick'=>'return this.parentNode.click()', 'class'=>'btnDel', 'target'=>'ajaxTodo', 'title'=>Yii::t('','是否确定删除？'))
                    );
                    break;
                case 'modify':
                    echo CHtml::link(
                        empty($text) ? Yii::t('','修改') : $text,
                        Yii::app()->controller->createUrl($this->action->id, array('opt'=>empty($opt) ? 'modify' : $opt, 'pk'=>'{pk}') + $urlPatch),
                        $attributes + array('onclick'=>'return this.parentNode.click()', 'class'=>'btnEdit', 'target'=>'dialog', 'title'=>Yii::t('','修改'), 'width'=>800, 'height'=>480, 'mask'=>true)
                    );
                    break;
                case 'view':
                    echo CHtml::link(
                        empty($text) ? Yii::t('','查看') : $text,
                        Yii::app()->controller->createUrl($this->action->id, array('opt'=>empty($opt) ? 'view' : $opt, 'pk'=>'{pk}') + $urlPatch),
                        $attributes + array('onclick'=>'return this.parentNode.click()', 'class'=>'btnLook', 'target'=>'dialog', 'title' => Yii::t('','查看详细信息'), 'width'=>800, 'height'=>480, 'mask'=>true)
                    );
                    break;
                default:
                    echo $name;
                    break;
            }
        }
        echo '</td>';
    }
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

/******************************************************************************
 * 输出分页Pagination
 *****************************************************************************/
$pageBarAttr = array(
   'class' => 'pagination',
   'targetType' => $type,
   'totalCount' => empty($pages) ? count($data) : $pages->getItemCount(),
   'numPerPage' => empty($pages) ? max(count($data), Yii::app()->params['pageSize']) : $pages->getPageSize(),
   'pageNumShown' => 10,
   'currentPage' => Yii::app()->request->getParam('pageNum', 1),
   'encode' => false
);
$pageSizeAttr = array(
   'class' => 'combox',
   'onchange' => "{$type}PageBreak({numPerPage: this.value})",
   'encode' => false
);
//局部刷新支持
if($type=='div') {
   $pageBarAttr['targetType'] = 'dialog';
   $pageBarAttr['rel'] = $divID;
   $pageSizeAttr['onchange'] = "dialogPageBreak({numPerPage: this.value},'{$divID}')";
}
$pageSizeSet = array(20=>20, 30=>30, 50=>50, 100=>100);
//自动隐藏
if(empty($pages)) {
   $pageSizeSet = array($pageBarAttr['numPerPage'] => $pageBarAttr['numPerPage']);
}
echo '<div class="panelBar"><div class="pages"><span>显示</span>';
echo CHtml::dropDownList('', $pageBarAttr['numPerPage'], $pageSizeSet, $pageSizeAttr);
echo '<span>条，共' . $pageBarAttr['totalCount'] . '条</span></div>';
echo CHtml::tag('div', $pageBarAttr);
echo '</div>';

/*内容区域关闭*/
echo '</div>';
//每次页面加载后刷新表头提示信息
if(!empty($tips)) {
    echo CHtml::script('refreshTips();');
}
?>
