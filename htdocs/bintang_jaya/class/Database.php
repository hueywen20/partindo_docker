<?php
class Database
{
//Other Useful stuff
 public $record;
 public $error;
 public $affected_rows;
 public $link_id;
 public $query_id; 
 public $query_count = 0;

 
 //Open connection, Use $new_link to force open a second connection
 function connect($new_link = false){
 	$this->link_id = @mysql_connect("db","root","",$new_link)
 	 or die ( '<b>FATAL ERROR:</b> Cannot connect to the database because: ' . mysql_error() ); 
    @mysql_select_db("bitjy", $this->link_id)
     or die ( '<b>FATAL ERROR:</b> Could not select '.$this->database.' because: ' . mysql_error() );

    //Be paranoid
    unset($this->server,$this->user,$this->pass,$this->database);
 }

 //Close connetion
 function close(){
    if(!mysql_close($this->link_id))
    	$this->wtf('Failed to close MySQL connection');
 }

 //Prepare data for query
 function clean($data){
 	if(is_array($data)){
 		if(get_magic_quotes_gpc())
			$data = array_map('stripslashes', $data);
		$data = array_map('mysql_real_escape_string', $data);
 	}else{
		if(get_magic_quotes_gpc())
			$data = stripslashes($data);
		$data = mysql_real_escape_string($data);
 	}
 	return $data;
 }

 //Perform Query - Used mainly as internal class function, unless specifically needed use fetch_one or fetch_all to get results
 function query($sql){
    $this->query_id = mysql_query($sql, $this->link_id) or die(mysql_error() . ' : ' . $sql);
    //$this->query_id = mysql_query($sql, $this->link_id);
    
    if (!$this->query_id) {
        $this->wtf('<b>MySQL Query fail: </b>'.$sql);
        return 0;
    } 
    
    $this->query_count++;
    return $this->query_id;
 }
 
 //Fetch an Array - Used mainly as internal class function, unless specifically needed use fetch_one or fetch_all to get results
 function fetch_array($query_id=-1) {
    // retrieve row
    if ($query_id!=-1)
        $this->query_id = $query_id;
        
	//pull records
    $this->record = @mysql_fetch_assoc($this->query_id);
	
	//Strip slashes throughout array
    if($this->record){
		$this->record = array_map("stripslashes", $this->record);
	}
    
    return $this->record;
 }
 
 //Fetch all rows and put in array
 //gnnya spy gk skln looping gk nulis while lg...
 function fetch_all($sql) {
    $query_id = $this->query($sql);
        
    while ($row = $this->fetch_array($query_id, $sql))
        $array[] = $row;
    
    @mysql_free_result($query_id);
    return $array;
 }

 //Fetch single row
 function fetch_one($sql) {
    $query_id = $this->query($sql);
	if (@mysql_num_rows($query_id) > 0){
		$array = $this->fetch_array($query_id);
		@mysql_free_result($query_id);
	}
    return $array;
 }
 
 /*
 UPDATE ROW
 USAGE:
 $table is the table you wish to update.
 $data is array with key being the field name and value being the new updated information i.e. $data['comment'] = 'Cheese is good.';
 $where is your WHERE statement added to the end of the generated query if needed.
 All values are sanitized
 */
 function update($table, $data, $where = '1') {
	 $q = 'UPDATE `'.$table.'` SET ';

    foreach($data as $key=>$var) {
        if(strtolower($var)=='null')
			$q .= '`$key` = NULL, ';
        elseif(strtolower($var)=='now()')
			$q .= '`'.$key.'` = NOW(), ';
        else
			$q .= '`'.$key.'`=\''.$this->clean($var).'\', ';
    }

    $q = trim($q, ', ') . ' WHERE '.$where.';';

    return $this->query($q);
    
    $this->affected_rows = @mysql_affected_rows();
 }
 
  /*
 INSERT ROW
 USAGE:
 $table is the table you wish to update.
 $data is array with key being the field name and value being the information to be inserted i.e. $data['news_title'] = 'Hello World';
All values are sanitized
 */
 function insert($table, $data) {
	$q = 'INSERT INTO `'.$table.'` ';
    $v = '';
    $f = '';
    
    foreach($data as $key=>$var) {
        $f .= '`'.$key.'`, ';
        if(strtolower($var)=='null')
			$v .= 'NULL, ';
        elseif(strtolower($var)=='now()')
			$v .= 'NOW(), ';
        else
			$v .= '\''.$this->clean($var).'\', ';
    }
    
    $q .= '('. trim($f, ', ') .') VALUES ('. trim($v, ', ') .');';
    //echo $q;
    if($this->query($q)){
        return mysql_insert_id();
    }
    else return false;
    $this->affected_rows = @mysql_affected_rows();
 }

 //Display errors with better information.
 function wtf($msg='') {
    if($this->link_id > 0)
     $this->error = @mysql_error($this->link_id);
    else
     $this->error = @mysql_error();
    
    if(strlen($_SERVER['HTTP_REFERER'])>0)
     $refer = '<tr><td align="right">Referer:</td><td><a href="'.$_SERVER['HTTP_REFERER'].'">'.$_SERVER['HTTP_REFERER'].'</a></td></tr>';
    else
     $refer = '';
     if ($_SERVER['REMOTE_ADDR'] == '174.52.62.252')
     {
    echo'
        <table align="center" border="1" cellspacing="0" style="background:white;color:black;width:80%;">
        <tr><th colspan="2">MySQL Error</th></tr>
        <tr><td align="right" valign="top">Message:</td><td>',$msg,'</td></tr>
        <tr><td align="right" valign="top" nowrap>MySQL Error:</td><td>',$this->error,'</td></tr>
        <tr><td align="right">Script:</td><td><a href="',$_SERVER['REQUEST_URI'],'">',$_SERVER['REQUEST_URI'],'</a></td></tr>
        ',$refer,'
        </table>
        ';
	}
 }

function getlastid($table,$field,$condition=''){
	if (!empty($condition)){
		$condition = ' WHERE '.$condition;
	}
	$dblast = $this->fetch_one("SELECT ".$field." FROM ".$table.$condition." ORDER BY ".$field." DESC");
	$lastid = 1;
	if (sizeof($dblast) > 0){
		$dataq = $dblast[$field];
		$lastid = $dataq+1;
	}
	return $lastid;
}
 
 //Begin Transaction
function beginTransaction(){
   $this->query('START TRANSACTION;');
}

//End Transaction
function endTransaction(){
	global $errmsg;
   //If there is an error with the sql syntax, data will be rollback
   if (@mysql_error() || !empty($errmsg)){
      $this->query('ROLLBACK;');
   }
   else{
      $this->query('COMMIT;');
   }
}
}
?>