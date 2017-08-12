<?php
require_once("./mp.php");
$biz = get_mp_biz();
$url = "https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz={$biz}&scene=124#wechat_redirect";
echo '<meta http-equiv=refresh content="3;url='.$url.'">';