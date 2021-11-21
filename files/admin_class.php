<?php
session_start();
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".$password."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			return 1;
		}else{
			return 2;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	function save_folder(){
		extract($_POST);
		$data = " name ='".$name."' ";
		$data .= ", parent_id ='".$parent_id."' ";
		if(empty($id)){
			$data .= ", user_id ='".$_SESSION['login_id']."' ";
			
			$check = $this->db->query("SELECT * FROM folders where user_id ='".$_SESSION['login_id']."' and name  ='".$name."'")->num_rows;
			if($check > 0){
				return json_encode(array('status'=>2,'msg'=> 'Folder name already exist'));
			}else{
				$save = $this->db->query("INSERT INTO folders set ".$data);
				if($save)
				return json_encode(array('status'=>1));
			}
		}else{
			$check = $this->db->query("SELECT * FROM folders where user_id ='".$_SESSION['login_id']."' and name  ='".$name."' and id !=".$id)->num_rows;
			if($check > 0){
				return json_encode(array('status'=>2,'msg'=> 'Folder name already exist'));
			}else{
				$save = $this->db->query("UPDATE folders set ".$data." where id =".$id);
				if($save)
				return json_encode(array('status'=>1));
			}

		}
	}

	function delete_folder(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM folders where id =".$id);
		if($delete)
			echo 1;
	}
	function delete_file(){
		extract($_POST);
		$path = $this->db->query("SELECT file_path from files where id=".$id)->fetch_array()['file_path'];
		$delete = $this->db->query("DELETE FROM files where id =".$id);
		if($delete){
					unlink('assets/uploads/'.$path);
					return 1;
				}
	}

	function save_files(){
		extract($_POST);
		if(empty($id)){
		if($_FILES['upload']['tmp_name'] != ''){
					$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['upload']['name'];
					$move = move_uploaded_file($_FILES['upload']['tmp_name'],'assets/uploads/'. $fname);
		
					if($move){
						$file = $_FILES['upload']['name'];
						$file = explode('.',$file);
						$chk = $this->db->query("SELECT * FROM files where SUBSTRING_INDEX(name,' ||',1) = '".$file[0]."' and folder_id = '".$folder_id."' and file_type='".$file[1]."' ");
						if($chk->num_rows > 0){
							$file[0] = $file[0] .' ||'.($chk->num_rows);
						}
						$data = " name = '".$file[0]."' ";
						$data .= ", folder_id = '".$folder_id."' ";
						$data .= ", description = '".$description."' ";
						$data .= ", user_id = '".$_SESSION['login_id']."' ";
						$data .= ", file_type = '".$file[1]."' ";
						$data .= ", file_path = '".$fname."' ";
						$data .= ", tags = '".$tags."' ";
						if(isset($is_public) && $is_public == 'on')
						$data .= ", is_public = 1 ";
						else
						$data .= ", is_public = 0 ";

						$save = $this->db->query("INSERT INTO files set ".$data);

						$msg=$_SESSION['login_name']." added new file.";
						$logs = $this->db->query("INSERT INTO logs (`logs`,`file_id`,`user_id`) VALUES ('$msg',(SELECT MAX(id) FROM files),'$_SESSION[login_id]')");
						
						if($save)
						return json_encode(array('status'=>1));
		
					}
		
				}
			}else{
						$data = " description = '".$description."' ";
						if(isset($is_public) && $is_public == 'on'){
							$data .= ", is_public = 1 ";
							$msg=$_SESSION['login_name']." shared file to public.";
						}else{
							$data .= ", is_public = 0 ";
							$msg=$_SESSION['login_name']." made file private.";							
						}

						$save = $this->db->query("UPDATE files set ".$data. " where id=".$id);
						$logs = $this->db->query("INSERT INTO logs (`logs`,`file_id`,`user_id`) VALUES ('$msg','$id','$_SESSION[login_id]')");

						if($save)
						return json_encode(array('status'=>1));
			}

	}
	function file_rename(){
		extract($_POST);

		$file[0] = $name;
		$file[1] = $type;
		// $chk = $this->db->query("SELECT * FROM files where SUBSTRING_INDEX(name,' ||',1) = '".$file[0]."' and folder_id = '".$folder_id."' and file_type='".$file[1]."' and id != ".$id);
		
		$proceed = $_SESSION['login_type'] == '1' || $_SESSION['login_type']=='3' ? true : false;
		if(!$proceed){
			$chk = $this->db->query("SELECT user_id FROM files where id = '$id'")->fetch_array();
			
			if($chk['user_id'] != $_SESSION['login_id']){
				return json_encode(array('status'=>2,'new_name'=>'Cannot rename. You do not owned this file.'));
			}
		}

		$save = $this->db->query("UPDATE files set name = '".$name."' where id=".$id);
		if($save){
			$msg=$_SESSION['login_name']." renamed file.";
			$logs = $this->db->query("INSERT INTO logs (`logs`,`file_id`,`user_id`) VALUES ('$msg','$id','$_SESSION[login_id]')");
			return json_encode(array('status'=>1,'new_name'=>$file[0].'.'.$file[1]));
		}	
		// print_r($chk->fetch_array());
		// if($chk->num_rows > 0){
		// 	$file[0] = $file[0] .' ||'.($chk->num_rows);
		// }

	}
	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		$data .= ", password = '$password' ";
		$data .= ", type = '$type' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function file_remove(){
		extract($_POST);

		$proceed = $_SESSION['login_type'] == '1' || $_SESSION['login_type']=='3' ? true : false;
		if(!$proceed){
			$chk = $this->db->query("SELECT user_id FROM files where id = '$id'")->fetch_array();
			
			if($chk['user_id'] != $_SESSION['login_id']){
				return 2;
			}
		}

		$save = $this->db->query("UPDATE files set file_status =1,deleted_by='$_SESSION[login_id]',deleted_datetime=CURRENT_TIMESTAMP where id = ".$id);
		if($save){
			$msg=$_SESSION['login_name']." moved file to trash.";
			$logs = $this->db->query("INSERT INTO logs (`logs`,`file_id`,`user_id`) VALUES ('$msg','$id','$_SESSION[login_id]')");
			return 1;
		}
	}
	function file_restore(){
		extract($_POST);
		$msg=$_SESSION['login_name']." restored file.";
		$logs = $this->db->query("INSERT INTO logs (`logs`,`file_id`,`user_id`) VALUES ('$msg','$id','$_SESSION[login_id]')");
		$save = $this->db->query("UPDATE files set file_status =0 where id = ".$id);
		if($save){
			return 1;
		}		
	}
	function share_file(){
		extract($_POST);
		$name = strtolower(trim($name));
		$share = $this->db->query("SELECT id,file_sharing.file_id
								   FROM users 
								   LEFT JOIN file_sharing ON file_sharing.share_to_id = users.id AND file_sharing.file_id='$file_id'
								   WHERE LCASE(username)='$name'");
		if($share->num_rows>0){
			$user = $share->fetch_array();
			if($user['file_id'] == null){
				$save = $this->db->query("INSERT INTO file_sharing (`file_id`,share_to_id,share_by)
										VALUES ('$file_id','$user[id]','$_SESSION[login_id]')");
				if($save){
					$msg=$_SESSION['login_name']." shared file to ".$name;
					$logs = $this->db->query("INSERT INTO logs (`logs`,`file_id`,`user_id`) VALUES ('$msg','$file_id','$_SESSION[login_id]')");
					return json_encode(array('status'=>1,'msg'=> 'File successfully share to '.$name));
				}	
			}else{
				return json_encode(array('status'=>2,'msg'=> 'File already shared to '.$name));
			}
		}else{
			return json_encode(array('status'=>2,'msg'=> 'Username does not exist.'));
		}
	}
	function share_remove(){
		extract($_POST);
		$msg=$_SESSION['login_name']." revoked sharing to ".$name;
		
		$save = $this->db->query("DELETE FROM file_sharing WHERE `file_id` = '$file_id' AND share_to_id ='$id'");
		if($save){
			$logs = $this->db->query("INSERT INTO logs (`logs`,`file_id`,`user_id`) VALUES ('$msg','$file_id','$_SESSION[login_id]')");
			return 1;
		}	
	}
}