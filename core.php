<?php


class Posts 
{

	private $mainLink = 'https://habr.com/ru/all/';
	private	$postLink = 'https://habr.com/ru/post/';
	
	
	function __construct()
	{
		$this->posts = [];
	}
	
	function getContent($pattern, $link, $pregId, $count, $group)
	{
		$file = file_get_contents(trim($link));
		if ($file) {
			preg_match_all($pattern, $file, $out);
			$k = 1;
			foreach ($out[$pregId] as $item){
				$this->posts[$group][] = $item;
				if ($k >= $count)  break;
				$k++;
			}
		}
		return $this->posts[$group];
	}
	
	function parsePage($file, $pattern, $pregId, $count, $group)
	{
		if ($file) {
			preg_match_all($pattern, $file, $out);
			$k = 1;
			foreach ($out[$pregId] as $item){
				$cnt = $item;
				if ($k >= $count)  break;
				$k++;
			}
		}
		return $cnt;
	}
	
	// Получаем список id последних постов с главной
	function getPostsId(){
		
		$pattern = '/a href="(.*?post\/)([0-9]*)\/"/';
		$link = $this->mainLink;
		
		$this->posts['id'] = self::getContent($pattern, $link, 2, 5, 'id');
		
		return $this->posts;
	}
	
	function getFileContent($link)
	{
		$file = file_get_contents($link);
		if ($file){
			return $file;
		}
		return false;
	}
	
	// Получаем информацию со всех постов
	function getAllContents()
	{

		for ($i = 0; $i < count($this->posts['id']); $i++){
		 
			$link = $this->postLink.$this->posts['id'][$i].'/';
			$file = self::getFileContent($link);
			$pattern = "/post__title.*?>(.*)</";
			$this->posts[$i]['id'] = $this->posts['id'][$i];
			$this->posts[$i]['title'] = self::parsePage($file, $pattern, 1, 1, 'title');
			$pattern = '/div class="post__text post__text-html?.*">([\s\S]+?)<\/div>/';
			$cnt = self::preparedDataForDB(self::parsePage($file, $pattern, 1, 1, 'content'));
			$this->posts[$i]['content'] = $cnt;
			$this->posts[$i]['short'] = mb_strimwidth($cnt, 0, 200, "...");
				
		}
		
		return $this->posts;
	}
	
	public function preparedDataForDB($text)
    {
        $text = trim($text);
        $text = ($text);
        $text = htmlspecialchars($text, ENT_QUOTES);
        $text = stripslashes($text);
        return $text;
    }
	


}

function d($a)
{
	echo '<pre>'; 
	print_r($a); 
	echo '</pre>';
	die();
}





function upload($p){
	
$mysqli = new Mysqli('localhost', 'root' ,'', 'test_hobby');	
	for ($i = 0; $i < count($p->posts['id']); $i++){
		
		$post_id = $p->posts[$i]['id'];
		$title = $p->posts[$i]['title'];
		$short = $p->posts[$i]['short'];
		$description = $p->posts[$i]['content'];
		

		if (!$mysqli->query("INSERT INTO posts VALUES(
										NULL,
										'$post_id',
										'$title',
										'$short',
										'$description'
										)")) {
			printf("Сообщение ошибки: %s\n", $mysqli->error);
		}
	}
	return true;
}

function read($page){
	$mysqli = new Mysqli('localhost', 'root' ,'', 'test_hobby');
	$query2 = $mysqli->query("
		SELECT count(*) as total
		FROM posts ");
	$count = mysqli_fetch_object($query2)->total;
	$query = $mysqli->query("
			SELECT id, post_id, title, short 
			FROM posts 
			ORDER BY id DESC 
			LIMIT ".(($page-1)*5)." , 5"
			);
	$i = 0;
	while($row = $query->fetch_assoc()){
		$posts[$i]['id'] = $row['id'];
		$posts[$i]['post_id'] = $row['post_id'];
		$posts[$i]['title'] = $row['title'];
		$posts[$i]['short'] = strip_tags(htmlspecialchars_decode($row['short']));
		$i++;
		
	}

	$posts['allpage'] = ceil($count/5);
	$posts['count'] = $i;
	$posts['status'] = 'ok';
	return $posts;
}




if (isset($_POST["action"]) && ($_POST["action"] == 'parse')) { 

	$p = new Posts;
	$p->getPostsId();
	$p->getAllContents();

	upload($p);

}

if (isset($_POST['page']) && filter_var($_POST['page'], FILTER_VALIDATE_INT)) {
	$page = (int)$_POST["page"];
// if (isset($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)) {
	// $page = (int)$_GET["page"];
}else{
	$page = 1;
}
$posts = read($page);

    $result = array(
    	'page' => $page,
    	'action' => $_POST["action"],
    	'allpage' => $posts['allpage'],
    	'count' => $posts['count'],
		'status' => $posts['status'],
		'posts' => $posts
    ); 

    echo json_encode($result); 


