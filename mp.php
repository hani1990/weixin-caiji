<?php
//biz 是把数字base64之后的结果，可以一直循环遍历
function get_mp_biz(){
	$n = file_get_contents('./n');
	$n++;
	file_put_contents( './n', $n);
	return base64_encode($n);
}
?>