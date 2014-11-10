<?
$db_name = "oc_activity";

$tit_doc = "JDC Audit_Log";
$admin_notify = 0;
$admin_mail = "";
$saveurl = ""; //file save directory
$saveurlimg = ""; //file save directory
$table_width = "98%"; //table width size
$image_width = "680"; //이미지 view
$num_per_page = 12;
$page_per_block = 10;
$notify_new_article = 1;//최근게시물 one days

/*
//로그인 체크
if(!trim($_SESSION[UID]) OR !trim($_SESSION[UNM])) {
	echo "<script language='javascript'>";
	echo "history.back();";
	echo "alert('로그인을 하셔야만 이용하실 수 있습니다.')";
	echo "</script>";
	exit;
}
*/
?>