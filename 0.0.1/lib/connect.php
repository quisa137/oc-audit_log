<?
class dbConn {
	var $dbhost="localhost";
	var $dbuser,$dbpasswd,$db,$connect;

	function dbConn($db="oc_owncloud",$dbuser="root",$dbpasswd="dpffmdktkf") {
		$this->dbuser=$dbuser;
		$this->dbpasswd=$dbpasswd;
		$this->db=$db;

	$this->connect=mysql_connect($this->dbhost,$this->dbuser,$this->dbpasswd);
	//mysql_query('set names utf8');//--------------------------------------------------------mysql 환경 euc, 웹페이지는 utf8일경우 사용
	mysql_select_db($this->db,$this->connect);
	}
	function setResult($que) {
		$result['result']=mysql_query($que,$this->connect);
		$result['cnt']=@mysql_affected_rows();
		return $result;
	}
	function removeQuot($str) {
		$str=str_replace("\"","",$str);
		$str=str_replace("'","",$str);
		return trim($str);
	}
	function addSlash($str) {
		$str=trim($str);
		$str=addslashes($str);
		return trim($str);
	}
	function stripSlash($str) {
		$str=stripslashes($str);
		return trim($str);
	}

	function alertTour($ment,$url,$parent="",$opt="") {
		echo "<script>alert(\"$ment\");".$parent."location.href='$url';".$opt."</script>"; exit;
	}
	function metaTour($url) {
		echo "<meta http-equiv=refresh content='0;url=$url'>"; exit;
	}

	function historyBack($ment) {
		echo "<script>alert(\"$ment\"); history.back();</script>"; exit;
	}
	function dbSelect($table,$where="",$field="*") {
		$q="select $field from $table $where";
		$re=$this->setResult($q); //echo "<br>".$q."<br>";
		return $re;
	}
	function dbSelect1($table,$where="",$field="*") {
		$q1="select $field from $table $where";
		$result=mysql_query($q1,$this->connect);
		//$re1=$this->setResult1($q1); //echo "<br>".$q."<br>";
		return $result;
	}
	function dbInsert($table,$arr) { //scalar 배열 $arr
		for ($i=0;$i<count($arr);$i++) {
			if ($i==0) $arrVal="'".$arr[$i]."'";
			else $arrVal.=",'".$arr[$i]."'";
		}
		$q="insert into $table values($arrVal)";
		$re=$this->setResult($q); //return $q;
		return $re;
	}
	function dbUpdate($table,$arr,$where="") { //연관배열 $arr
		$i=0;
		while (list($key,$val)=each($arr)) {
			if ($i==0) $arrVal=$key."='".$val."'";
			else $arrVal.=",".$key."='".$val."'";
			$i++;
		}
		$q="update $table set $arrVal $where"; //echo $q;
		$this->setResult($q);
	}
	function dbDelete($table,$where) {
		$q="delete from $table $where";
		$this->setResult($q);
	}
}
?>