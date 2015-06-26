<?php 
	// Connect to your Database 
	require 'database.php';
	
	//save into db
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
	// Select accounts
	//$response = mysql_query("SELECT idpersonnel as record_id, parentid, nom_arabe, prenom_arabe, tel, email, image FROM my_personnel") or die(mysql_error()); 
	
	$sql="SELECT p.idpersonnel as record_id, parentid,  grade as g.intitule, p.nom_arabe, p.prenom_arabe, p.tel, p.email, p.image 
FROM my_personnel p
INNER JOIN my_grade_lang g ON p.idgrade=g.grade_idgrade where g.lang_idlang=1";
	$q = $pdo->prepare($sql);
					
   
	// create some class for your records
	class Account
	{
		public $id = 0;
		public $parent = null;
	
		public $nom_arabe = '';
		public $prenom_arabe = '';
		public $grade = '';
		public $phone = '';
		public $email = '';
		public $image = '';
		
		public function load($record) {
			$this->id = $record['record_id'];
			$this->parent = $record['parentid'];
		
			$this->nom_arabe = $record['nom_arabe'];
			$this->prenom_arabe = $record['prenom_arabe'];
			$this->grade = $record['grade'];
			$this->tel = $record['tel'];
			$this->email = $record['email'];
			$this->image = "demo/images/photos/" . $record['image'];
			$this->href = "showdetails.php?recordid=" . $this->id;
		} 
	}
	
	// create hash and group all children by parentid
	$items = Array();
	foreach($pdo->query($sql) as $record ) 
	{ 
		$account = new Account();
		$account->load($record);
		array_push($items, $account);
	} 

	function encodeURIComponent($str) {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
        return strtr(rawurlencode($str), $revert);
    }
	
	// serialize $rootAccount object including all its children into JSON string  
	$jsonstring = encodeURIComponent(json_encode($items));

	echo $jsonstring;
?>