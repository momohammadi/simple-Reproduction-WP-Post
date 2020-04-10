<?php
//change true for close access to this file
if(false){
	header("HTTP/1.0 404 Not Found");
	exit(0);
}
//insert wp database info
$servername = "localhost";
$username = "dbuser";
$password = "dbpass";
$tablePrefix = "wp_";
$authorID = 1;

/*
*	@init $id = Post Id You want Dublicate it
*	@init $replacement = number of you want find and replace with +1
*	@init $numberOfPost = number of new post
*	@init $catId = wp category id
* @string $wpUrl = your wordpress url end of "/"
*/
$replace = $replacement = 200;	
$id = '784';
$wpUrl = 'https://example.com/';
$numberOfPost = '1';
$catId = '1';

?>
<html>
<body>
<?		
// change dbname to your wordpress database name
try {
    $conn = new PDO("mysql:host=$servername;dbname=dbname" , $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	

	
	
	$stmt = $conn->prepare("SELECT * FROM ".$tablePrefix."posts WHERE ID=".$id." LIMIT 1");
    $stmt->execute();
    $templatePost = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
	
	$stmt = $conn->prepare("SELECT ID FROM ".$tablePrefix."posts ORDER BY `ID` DESC LIMIT 1");
	$stmt->execute();
    $lastID = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['ID'];

	
   
    //seting of same data
    $dateTime = new DateTime();
	$dateTime->setTimestamp(strtotime($templatePost['post_date'])+3600);
	$post_modified = $post_date = $dateTime->format('Y-m-d H:i:s');
	$dateTime->setTimestamp(strtotime($templatePost['post_date_gmt'])+3600);
	$post_modified_gmt = $post_date_gmt = $dateTime->format('Y-m-d H:i:s');
	$post_status = 'publish';
	$comment_status = $templatePost['comment_status'];
	$ping_status = $templatePost['ping_status'];
	$comment_count = $templatePost['comment_count'];
	$post_type = $templatePost['post_type'];
	$to_ping = $templatePost['to_ping'];
	$pinged = $templatePost['pinged'];
	$post_parent = $templatePost['post_parent'];
	$post_content_filtered = $templatePost['post_content_filtered'];

	
	for($i=1;$i<= $numberOfPost;$i++){
		//setting modified data		
		$replace=$replace+1;
		$lastID = $lastID + 1;
		$post_title = str_replace($replacement,$replace,$templatePost['post_title']);
		$post_content = str_replace($replacement,$replace,$templatePost['post_content']);
		$post_excerpt = str_replace($replacement,$replace,$templatePost['post_excerpt']);
		$guid = $wpUrl."?p=".$lastID;		
		$post_name = preg_replace('/[^\da-z]/i', '-', strtolower($post_title));
		$post_name = preg_replace('/-+/', '-', trim($post_name));
		
		$stmt = $conn->prepare("INSERT INTO `".$tablePrefix."posts` (`post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`,`guid`, `post_type`, `comment_count`) VALUES (:authorID, :post_date, :post_date_gmt, :post_content, :post_title, :post_excerpt, :post_status, :comment_status, :ping_status, :post_name, :to_ping, :pinged, :post_modified, :post_modified_gmt, :post_content_filtered, :post_parent, :guid, :post_type, :comment_count)");
		$stmt->bindParam(':authorID', $authorID);
		$stmt->bindParam(':post_date', $post_date);
		$stmt->bindParam(':post_date_gmt', $post_date_gmt);
		$stmt->bindParam(':post_content', $post_content);
		$stmt->bindParam(':post_title', $post_title);
		$stmt->bindParam(':post_excerpt', $post_excerpt);
		$stmt->bindParam(':post_status', $post_status);
		$stmt->bindParam(':comment_status', $comment_status);
		$stmt->bindParam(':ping_status', $ping_status);
		$stmt->bindParam(':post_name', $post_name);
		$stmt->bindParam(':to_ping', $to_ping);
		$stmt->bindParam(':pinged', $pinged);
		$stmt->bindParam(':post_modified', $post_modified);
		$stmt->bindParam(':post_modified_gmt', $post_modified_gmt);
		$stmt->bindParam(':post_content_filtered', $post_content_filtered);
		$stmt->bindParam(':post_parent', $post_parent);
		$stmt->bindParam(':guid', $guid);
		$stmt->bindParam(':post_type', $post_type);
		$stmt->bindParam(':comment_count', $comment_count);			
		$stmt->execute();
		
		//$stmt = $conn->prepare("INSERT INTO `".$tablePrefix."term_relationships` (`object_id`, `term_taxonomy_id`) VALUES (:post_id, :cat_id)");
		//$stmt->bindParam(':post_id', $lastID);
		//$stmt->bindParam(':cat_id', $catId);
		//$stmt->execute();
		
		echo "</br>";
		echo "New post id ".$lastID." Created";
		echo "</br>";
	}
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
	

?>
</body>
</html>
