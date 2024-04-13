<?php
//1. POSTデータ取得
$isbn = $_POST["isbn"];
$title = $_POST["title"];
$authors = $_POST["authors"];
$publisher = $_POST["publisher"];
$publishedDate = $_POST["publishedDate"];
$description = $_POST["description"];
$thumbnail = $_POST["thumbnail"];
$uid = '123456789999';

//2. DB接続します
require 'connect.php';

//３．データ登録SQL作成
$sql = "INSERT INTO gs_kadai_02(isbn,title,authors,publisher,publishedDate,description,thumbnail,uid,registDate)VALUES(:isbn,:title,:authors,:publisher,:publishedDate,:description,:thumbnail,:uid,sysdate());";
$stmt = $pdo->prepare($sql);

$stmt->bindValue(':isbn',$isbn,PDO::PARAM_STR);
$stmt->bindValue(':title',$title,PDO::PARAM_STR);
$stmt->bindValue(':authors',$authors,PDO::PARAM_STR);
$stmt->bindValue(':publisher',$publisher,PDO::PARAM_STR);
$stmt->bindValue(':publishedDate',$publishedDate,PDO::PARAM_STR);
$stmt->bindValue(':description',$description,PDO::PARAM_STR); 
$stmt->bindValue(':thumbnail',$thumbnail,PDO::PARAM_STR); 
$stmt->bindValue(':uid',$uid,PDO::PARAM_INT); 

$status = $stmt->execute();

//４．データ登録処理後
if($status==false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
}else{

  // header("Location: example.php");
  echo json_encode(array("success" => true));
  exit();
}
?>
