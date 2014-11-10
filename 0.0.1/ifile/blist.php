<? session_start();?>
<?
include "../lib/connect.php";
$dbCon = new dbConn();
include "blist_h.php";
?>

<?
function popup_msg($msg) {
   echo("<script language=\"javascript\">
   <!--
   alert('$msg');
   history.back();
   //-->
   </script>");
}

function make_where ($blank_is, $column_list, $word, $ban) {
	global $word_list;

	if($ban) {
		$like = "NOT LIKE";
		$join = "AND";
	}
	else {
		$like = "LIKE";
		$join = "OR";
	}

	$word = stripslashes($word);
	$temp = eregi_replace("(\")(.*)( +)(.*)(\")","\\2[###blank###]\\4",$word);
	$temp = eregi_replace("\(|\)| and | or "," ",$temp);
	//$temp = eregi_replace("\(|\)| and | or "," \\0 ",$temp);
	$temp = trim(eregi_replace(" {2,}"," ",$temp));
	$result[word] = eregi_replace("\(|\)| and | or "," ",$temp);
	$temp = explode(" ",$temp);
	$word_list = $temp;
	for($i=0; $i < sizeof($temp); $i++) {
		if($i) {
			if(eregi("^\)$",$temp[$i-1]) && !eregi("^or$|^and$",$temp[$i])) {
				$temp2[] = $blank_is;
			}
			if(!eregi("^(\(|\)|and|or)$",$temp[$i-1]) && eregi("^\($",$temp[$i])) {
				$temp2[] = $blank_is;
			}
			if(!eregi("^(\(|\)|and|or)$",$temp[$i-1]) && !eregi("^(\(|\)|and|or)$",$temp[$i])) {
				$temp2[] = $blank_is;
			}
		}
		$temp2[] = $temp[$i];
	}
	for($i=0; $i< sizeof($temp2); $i++) {
		if(eregi("^(\(|\)|and|or)$",$temp2[$i])) {
			continue;
		}
		unset($temp);
		$temp .= "(";
		$temp2[$i] = addslashes($temp2[$i]);
		$column_list_array =explode(",",$column_list);
		for($j=0; $j< sizeof($column_list_array); $j++) {
			if($j && $temp && $temp!="(") {
				$temp .= " $join";
			}
			$temp .= " $column_list_array[$j] $like '%$temp2[$i]%'";
		}
		$temp .= ")";
		$temp2[$i] = $temp;
	}
	$temp = implode(" ",$temp2);
	$result[where] = str_replace("[###blank###]"," ",$temp);
	return $result;
}

function PDS_Head() {
	global $PHP_SELF;
	global $title_doc;
	global $part,$page,$spt,$tpt,$sa,$sb,$sc,$sd,$se,$keyword;
?>
	<!DOCTYPE HTML>
	<html lang="ko">
	<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="../lib/bbs.css" type="text/css" rel=stylesheet>
	<script language="JavaScript">
	<!--
	<?
	echo "var v_pgm='". $PHP_SELF. "'\n";
	echo "var v_string='page=".$page. "&spt=".$spt. "&tpt=".$tpt. "&sa=".$sa. "&sb=".$sb. "&sc=".$sc. "&sd=".$sd. "&se=".$se. "&keyword=".$keyword. "'\n";
	echo "var v_string1='part=".$part. "&spt=".$spt. "&tpt=".$tpt. "'\n";
	?>
	function strCheck(str, ch, msg) {
		var s = str.value;
		for(i=0; i<s.length; i++) {
			if(ch.indexOf(s.substring(i,i+1)) == -1) {
				alert(msg + '에 허용할 수 없는 문자가 입력되었습니다');
				str.focus();
				return true;
			}
		}
		return false;
	}
	function numCheck(num, ch, msg) {
		var n = num.value;
        for (i=0; i<n.length; i++) {
			if(ch.indexOf(n.substring(i,i+1)) == -1) {
				alert(msg + '에 허용할 수 없는 문자가 입력되었습니다');
				num.focus();
				return true
			}
		}
		return false
	}
	function both_trim(a) {//양쪽문자열제거
		var search = 0
		while ( a.charAt(search) == " ") {
			search = search + 1
		}
		a = a.substring(search, (a.length))
		search = a.length - 1
		while (a.charAt(search) ==" ") { 
			search = search - 1 
		}
		return a.substring(0, search + 1)         
	}
	function checkAll() {
		var chk = document.forms.checkform;
		if (document.checkform.checkboxAll.checked == true) {
			for (var i=0; i<chk.length;i++) {
				if (chk[i].type == "checkbox" && chk[i].checked == false) {
					chk[i].checked = true;
				}
			}
		}
		else {
			for (var i=0; i<chk.length;i++) {
				if (chk[i].type == "checkbox" && chk[i].checked == true) {
					chk[i].checked = false;
				}
			}
		}
	}

	function popup_image(page,nam,wid,hit){ //스크롤이 있는경우
		var  windo=eval('window.open("'+page+'","'+nam+'","status=no,toolbar=no,resizable=no,scrollbars=yes, menubar=no,width='+wid+',height='+hit+',top=10,left=10")'); 
	}
	function popup_image0(page,nam,wid,hit){ //스크롤이 없는경우
		var  windo=eval('window.open("'+page+'","'+nam+'","status=no,toolbar=no,resizable=no,scrollbars=no, menubar=no,width='+wid+',height='+hit+',top=10,left=10")'); 
	}
	function goRead(num) { window.location.href="?mode=read&number="+num+"&"+v_string;	}
	function goList() { window.location.href="?"+v_string;	}
	function goRefresh() { window.location.href="?";	}
	function goRefreshs() { window.location.href="?"+v_string1;	}
	//-->
	</script>
	</head>
	<body leftmargin="0" topmargin="0" style="background-color:transparent">
<?
}

function PDS_Left_Body() {}

function PDS_Read() {
	global $dbCon;
	global $PHP_SELF,$db_name,$db_name_comment,$db_name_file;
	global $table_width;
	global $part,$page,$spt,$tpt,$sa,$sb,$sc,$sd,$se,$keyword;
    global $reply_yesno_act,$file_download,$image_width,$savedir,$saveurl;
	global $memo_enable,$content_insert;
	global $number,$passwd;//dialog values

	//-----dialog check
	$result = $dbCon->dbSelect1($db_name,"WHERE uid='$number'","userpw,userid");
	$real_userpw = mysql_result($result,0,0);
	$real_userid = mysql_result($result,0,1);

	if(!trim($passwd)) {
		/*
		if(strcmp($_SESSION[UID],$real_userid)) {
			popup_msg("권한이 없습니다."); exit;
		}*/
	} else {
		if(strcmp($real_userpw,$passwd)) {//dialog values
			popup_msg("권한이 없습니다."); exit;
		}
	}

	$str = $dbCon->dbSelect($db_name,"WHERE uid='$number' LIMIT 1");
	mysql_data_seek($str[result],0);
	$Row=mysql_fetch_object($str[result]);

	if($Row->filecnt) {//업로드 파일이 있을경우
		$strf = $dbCon->dbSelect($db_name_file,"WHERE dbnm='$db_name' AND bidx='$number' ORDER BY idx LIMIT $Row->filecnt","idx,filep,filel,filex,filez,filed,fdesc,regdate");
		for($i=0; $i<$strf[cnt]; $i++) {
			mysql_data_seek($strf[result],$i);
			$Rowf=mysql_fetch_object($strf[result]);

			$file_idx[] = $Rowf->idx;
			$file_pig[] = $Rowf->filep;
			$file_log[] = $Rowf->filel;
			$file_ext[] = $Rowf->filex;
			$file_siz[] = $Rowf->filez;
			$file_dwn[] = $Rowf->filed;
			$file_dsc[] = $Rowf->fdesc;
			$file_reg[] = $Rowf->regdate;

			if(!strcmp(strtolower($Rowf->filex),"gif") || !strcmp(strtolower($Rowf->filex),"jpg")) {
				$img_size = getimagesize($savedir.$Rowf->filel);
				if($img_size[0] > $image_width) $width[$i] = $image_width;
				else $width[$i] = $img_size[0];
				$jpegchk[$i] = 1;
			} else $jpegchk[$i] = 0;
		}
	}

	$Row->viscnt++;
	$q_up = "UPDATE $db_name SET viscnt='$Row->viscnt' WHERE uid='$number' LIMIT 1";
	$dbCon->setResult($q_up);
?>

	<div class="bbs_type1_view">
		<div class="subject">
			<strong><?=$Row->subject?></strong>
			<div class="info">
				<ul>
					<?
					if(!$Row->admchk) echo"<li>".$Row->usernm."</li>";
					else echo"<li><img src='../images/logo_txt.gif'></li>";
					?>

					<?
					if($Row->email) echo"<li><a href='mailto:$Row->email'><img src='../images/bbs_icon_mail.gif' border=0 align=absmiddle></a></li>";
					if($Row->homepage) echo"<li><a href='http://$Row->homepage' target='_blank'><img src='../images/bbs_icon_hp.gif' border=0 align=absmiddle></a></li>";
					?>
					<li><?= date("Y.m.d H:i",$Row->moddate)?></li>
					<li>Hit <?=$Row->viscnt?></li>
				</ul>
			</div>
			<?
			if($Row->filecnt) {
				echo "<div class=\"fileinfo\">";
				echo "<ul>";
				for($i=0; $i<$Row->filecnt; $i++) {
					echo "<li><img src='../images/bbs_icon_disk.gif'><span style='color:#1A7580;cursor:hand;' onClick=\"javascript:download_file('$db_name_file','$savedir','$file_idx[$i]');\">$file_pig[$i] (".sprintf("%.1f", $file_siz[$i]/1000)."K)</span></li>";
				}
				echo "</ul>";
				echo "</div>";
			}
			?>
		</div>

		<div class="article" id="view_contents">
		<?=$Row->comment?>
		</div>
	</div><!-- (e)bbs_type1_view -->

	<div class="bbs_btnview">
		<a href="javascript:goList()"><img src='../images/btn_302.gif' border=0 align=absmiddle></a>
		<?
		if($reply_yesno_act && !$Row->mchk && !$Row->nchk) echo " <a href=\"javascript:goReply($Row->uid)\"><img src='../images/btn_305.gif' border=0 align=absmiddle></a>";//게시판공지글일경우 답변버튼삭제
		if(trim($_SESSION[UID]) && !strcmp($_SESSION[UID],$Row->userid)) {
			echo " <a href=\"javascript:goModify($Row->uid)\"><img src='../images/btn_304.gif' border=0 align=absmiddle></a>";
			echo " <a href=\"javascript:delAction($Row->uid)\"><img src='../images/btn_306.gif' border=0 align=absmiddle></a>";
		} else {
			if(!$Row->admchk) {
			echo " <a href=\"javascript:goPwdModify($Row->uid)\"><img src='../images/btn_304.gif' border=0 align=absmiddle></a>";
			echo " <a href=\"javascript:goPwdDelete($Row->uid)\"><img src='../images/btn_306.gif' border=0 align=absmiddle></a>";
			}
		}
		?>
	</div>
	<p></p>

<?
}

function PDS_ListView() {
    global $dbCon;
	global $PHP_SELF,$db_name,$db_name_imgfile,$db_name_comment,$allow_comment;
    global $table_width;
	global $part,$page,$spt,$tpt,$sa,$sb,$sc,$sd,$se,$keyword;
	global $num_per_page,$page_per_block,$notify_new_article,$reply_indent,$notify_admin,$regist_yesno_act;
	global $number;

	if(!$page) $page = 1;
	if(trim($keyword)) {
		$sh_keyfield = "";
		if($sa) $sh_keyfield = $sh_keyfield. ",". $sa;
		if($sb) $sh_keyfield = $sh_keyfield. ",". $sb;
		if($sc) $sh_keyfield = $sh_keyfield. ",". $sc;
		if($sd) $sh_keyfield = $sh_keyfield. ",". $sd;
		if($se) $sh_keyfield = $sh_keyfield. ",". $se;
		if(!$sh_keyfield) {
			$sb = "file";
			$sh_keyfield = ",file";
		}
		$sh_keyfield = substr($sh_keyfield,1);
		$sh_query = make_where("AND", $sh_keyfield, $keyword, 0);//검색처리함수 호출
		$sh_query = "AND ". $sh_query[where];
	}

	$sql_part = "";
	//if(trim($spt)) $sql_part = $sql_part. " AND sprt='$spt'";
	//if(trim($tpt)) $sql_part = $sql_part. " AND tprt='$tpt'";

	if(!eregi("[^[:space:]]+",$keyword)) {
		$str = $dbCon->dbSelect1($db_name, "","COUNT(activity_id)");
	} else {
		$encoded_key = urlencode($keyword);
   		$str = $dbCon->dbSelect1($db_name, "WHERE activity_id>0 $sql_part $sh_query","COUNT(activity_id)");
	}
	$total_record = mysql_result($str,0,0);

	//------------------ print scorp definition
	if(!$total_record) {
   		$first = 1;	$last = 0;
	} else {
   		$first = $num_per_page*($page-1);
   		$last = $num_per_page*$page;
   		$IsNext = $total_record - $last;
   		if($IsNext > 0) $last -= 1;
		else $last = $total_record - 1;
	}
	$total_page = ceil($total_record/$num_per_page);
	$time_limit = 60*60*24*$notify_new_article;
	$article_num = $total_record - $num_per_page*($page-1);

?>

	<table width="<?=$table_width?>" cellspacing="0" cellpadding="0" class="bbs_type1_info">
	<tr><td>Total <?=number_format($total_record)?> , <?=$page?>/<?=$total_page?> page</td></tr>
	</table>

	<table width="<?=$table_width?>" cellspacing="0" cellpadding="0" class="bbs_type1">
		<colgroup>
			<col width="30" />
			<col width="20" />
			<col width="" />
			<col width="80" />
			<col width="80" />
			<col width="120" />
		</colgroup>
		<thead>
			<tr>
				<th>No</th>
				<th>&nbsp;</th>
				<th>파일명</th>
				<th>사용자</th>
				<th>일자</th>
				<th>비고</th>
			</tr>
		</thead>
		<tbody>

<?
	if(!eregi("[^[:space:]]+",$keyword)) $str = $dbCon->dbSelect($db_name, "ORDER BY timestamp DESC LIMIT $first,$num_per_page");
	else $str = $dbCon->dbSelect($db_name, "WHERE activity_id>0 $sql_part $sh_query ORDER BY timestamp DESC LIMIT $first,$num_per_page");

	for($i=0; $i<$str['cnt']; $i++) {
		mysql_data_seek($str['result'],$i);
		$Row=mysql_fetch_object($str['result']);


		echo "<tr ONMOUSEOVER=\"this.style.backgroundColor='#ffffee'\" ONMOUSEOUT=\"this.style.backgroundColor=''\">";

		if($number == $Row->activity_id) echo "<td class=\"no\"><img src='../images/bbs_icon_arrow.gif' border=0></td>";
		else echo "<td class=\"no\">${article_num}</td>";

		//파일종류 check
		if(0) echo "<td class=\"file\"><img src='../images/bbs_icon_clib.gif' width=9 height=14 border=0></td>";
		else echo "<td class=\"file\"></td>";

		echo "<td class=\"subject\" style='word-break:break-all;'>";
		echo "<a href='javascript:goRead($Row->activity_id)' onMouseOver=\"status='read';return true;\" onMouseOut=\"status=''\">";
		echo $Row->file;
		echo "</a>";
		$date_diff = time() -  $Row->timestamp;
		if($date_diff < $time_limit) echo " <img src='../images/bbs_icon_new.gif' border=0>";
		echo "</td>";


		echo "<td class=\"writer\">". $Row->user. "</td>";



	   	echo "<td class=\"date\">". date("y.m.d",$Row->timestamp). "</td>";
		echo "<td class=\"hit\">". $Row->type. "</td>";

		echo "</tr>";
	   	$article_num--;
	}
?>
		</tbody>
	</table>

	<div class="bbs_btnview">
		<div class="btn_list">
		<a href="javascript:goRefresh()"><img src='../images/btn_301.gif' border=0 align=absmiddle></a>
		<?if($regist_yesno_act) echo"<a href=\"javascript:goPost()\"><img src='../images/btn_303.gif' border=0 align=absmiddle></a>";?>
		</div>
		<div class="pager">
		<?
		$total_block = ceil($total_page/$page_per_block);
		$block = ceil($page/$page_per_block);
		$first_page = ($block-1)*$page_per_block;
		$last_page = $block*$page_per_block;
		if($total_block <= $block) { $last_page = $total_page; }

		if($block > 1) {//pre page
			$my_page = $first_page;
			echo "<a class=\"arrow\" href=\"?spt=${spt}&tpt=${tpt}&sa=${sa}&sb=${sb}&sc=${sc}&sd=${sd}&se=${se}&keyword=${keyword}\"><img src='../images/btn_3130.gif' border=0 align=absmiddle></a><a class=\"arrow\" href=\"?page=$my_page&spt=${spt}&tpt=${tpt}&sa=${sa}&sb=${sb}&sc=${sc}&sd=${sd}&se=${se}&keyword=${keyword}\"><img src='../images/btn_313.gif' border=0 align=absmiddle></a> ";
		}
		for($direct_page = $first_page+1; $direct_page <= $last_page; $direct_page++) {//current page
			if($page == $direct_page) {echo "<a class=\"current\">$direct_page</a>"; }
			else { echo "<a href=\"?page=$direct_page&spt=${spt}&tpt=${tpt}&sa=${sa}&sb=${sb}&sc=${sc}&sd=${sd}&se=${se}&keyword=${keyword}\">$direct_page</a>"; }
		}
		if($block < $total_block) {
			$my_page = $last_page+1;
			echo " <a class=\"arrow\" href=\"?page=$my_page&spt=${spt}&tpt=${tpt}&sa=${sa}&sb=${sb}&sc=${sc}&sd=${sd}&se=${se}&keyword=${keyword}\"><img src='../images/btn_314.gif' border=0 align=absmiddle></a><a class=\"arrow\" href=\"?page=$total_page&spt=${spt}&tpt=${tpt}&sa=${sa}&sb=${sb}&sc=${sc}&sd=${sd}&se=${se}&keyword=${keyword}\"><img src='../images/btn_3140.gif' border=0 align=absmiddle></a>";
		}
		?>
		</div>

	</div>

	<div class="wrap_bbssearch">
		<form name="searchword" method="get" action="<?=$PHP_SELF?>">
		<input type=hidden name=part value="<?=$part?>">
		<input type=hidden name=spt value="<?=$spt?>">
		<input type=hidden name=tpt value="<?=$tpt?>">
		<input type="checkbox" name="sa" value="user" <?if($sa) echo"checked";?>>사용자 
		<input type="checkbox" name="sb" value="file" <?if($sb) echo"checked";?>>파일명
		<input type="checkbox" name="sc" value="type" <?if($sc) echo"checked";?>>형태
		<input size="20" maxlength="30" name="keyword" value="<?=$keyword?>" class=forms>
		<input type=image src="../images/btn_310.gif" align=absmiddle>
		</form>
	</div>
<?
}

function PDS_Right_Body() {}

function PDS_End() {
	echo "</body></html>";
}
/*
if(!strcmp($mode, "read")) {
	PDS_Head();
	PDS_Left_Body();
	PDS_Read();
	PDS_Right_Body();
	PDS_End();
}
else {
*/
	PDS_Head();
	PDS_Left_Body();
	PDS_ListView();
	PDS_Right_Body();
	PDS_End();
//}
?>