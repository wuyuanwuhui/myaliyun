<?php
//参数设置
$platformID = isset($platformID) ? $platformID : 'platform';
$serverID = isset($serverID) ? $serverID : 'server';
$allPlatform = empty($allPlatform) ? false : true;
$allServer = empty($allServer) ? false : true;
$hideServer = empty($hideServer) ? false : true;
$hidePlatform = empty($hidePlatform) ? false : true;
$rand = rand();//随机数，作为本控件的ID
$function = 'onChange' . $rand;//渠道-服务器联动函数名
$platform_domid = $platformID.$rand;//渠道选择框ID
$server_domid = $serverID.$rand;//服务器选择框ID
//准备渠道列表相关数据
if(!$hidePlatform) {
    $platformValue = isset($cid) ? intval($cid) : Yii::app()->request->getParam($platformID, Yii::app()->session['select_platform']);
    Yii::app()->session['select_platform'] = $platformValue;
    //获取渠道数据
    //$platformData[0] = _('请选择渠道');
    if ($allPlatform) {
        $platformData[-1] = _('全部渠道');
    }
    $platformData += CHtml::listData(Yii::app()->ENV->BindPlatforms, 'platformid', 'platform.platform');
}
//准备服务器列表相关数据
if(!$hideServer) {
    $serverValue = isset($sid) ? intval($sid) : Yii::app()->request->getParam($serverID, Yii::app()->session['select_server']);
    Yii::app()->session['select_server'] = $serverValue;
    //按渠道绑定关系对服务器分组
    $serverData = array();
    $addedServer = array();
    foreach (Yii::app()->ENV->getBindRelation('server_name', true) as $item) { //Yii::app()->ENV->BindRelation
        $serverData[$item['platformid']][$item['sid']] = $item->lsgs->gsdb->server_name;//按渠道分组服务器
        if(!in_array($item->lsgs->gsdb->gsid, $addedServer)) {
            $serverData[-1][$item['sid']] = $item->lsgs->gsdb->server_name;//选择全渠道时列出所有服务器，相同服务器只列出一次
            $addedServer[] = $item->lsgs->gsdb->gsid;
        }
    }
}
//下面开始输出///////////////////////////////////////////////////////////////////
?>
<script type="text/javascript">
//切换游戏时刷新渠道和服务器，作为全局队列事件
if(!$.isArray(window.ChangeENV)) {
    window.ChangeENV = [];
}
</script>
<?php if(!$hidePlatform) {//输出渠道列表和相关事件 ?>
    <script type="text/javascript">
    window.ChangeENV['<?php echo $function; ?>'] = function(data){
        window.<?php echo $function; ?>_server = data.server;
        //var html = '<option value="0"><?php echo _('请选择渠道') ?></option>';
        var html = '';
        <?php if ($allPlatform) { ?>
            html = '<option value="-1"><?php echo _('全部渠道') ?></option>';
        <?php } ?>
        for( var i in data.platform) {
            html += '<option value="' + i + '">' + data.platform[i] + '</option>';
        }
        $('#<?php echo $platform_domid ?>').html(html);
        $('#<?php echo $server_domid ?>').html('<option value="0"><?php echo _('请选择服务器') ?></option>');
    };
    </script>
    <?php
    echo CHtml::dropDownList($platformID, $platformValue, $platformData, array('id'=>$platform_domid));
} ?>
<?php if(!$hideServer) {//输出服务器列表和相关事件 ?>
    <script type="text/javascript">
    //服务器列表
    window.<?php echo $function; ?>_server = <?php echo json_encode($serverData) ?>;
    /**
     * [渠道-服务器]联动
     */
    function <?php echo $function; ?>(force_platform) {
        //是否支持选择全服
        var html = '';
        <?php if ($allServer) { ?>
            html += '<option value="-1"><?php echo _('全部服务器') ?></option>';
        <?php }else { ?>
             html += '<option value="0"><?php echo _('请选择服务器') ?></option>'
        <?php }?>
        var platform_id = (typeof(force_platform)=="undefined") ? document.getElementById('<?php echo $platform_domid ?>').value : force_platform;
        for (var k in window.<?php echo $function; ?>_server[platform_id]) {
            html += '<option value="' + k + '">' + window.<?php echo $function; ?>_server[platform_id][k] + '</option>';
        }
        $('#<?php echo $server_domid ?>').html(html);
    }
    </script>
    <?php
    echo CHtml::dropDownList($serverID, 0, array(_('请选择服务器')), array('id'=>$server_domid));
    //绑定渠道列表改变事件
    if(!$hidePlatform) {
        echo CHtml::script("\$('#{$platform_domid}').change(function(){{$function}();});{$function}();");
    } else {//渠道被隐藏以后还需要处理服务器列表显示
        echo CHtml::script("{$function}(-1);");
        ?>
        <script type="text/javascript">
        window.ChangeENV['<?php echo $function; ?>'] = function(data){
            window.<?php echo $function; ?>_server = data.server;
            <?php echo "{$function}(-1);"; ?>
        };
        </script>
        <?php
    }
    //设置服务器默认选中项
    if (!empty($serverValue)) {
        echo CHtml::script("\$('#{$server_domid}').val({$serverValue})");
    }
}
?>
