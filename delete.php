<?php
//1. POSTデータ取得
$isbn = $_POST["isbn"];

//2. DB接続します
include 'connect.php';

//３．データ登録SQL作成
$sql = "DELETE FROM gs_kadai_02 WHERE isbn=:isbn";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
$status = $stmt->execute();

//４．データ登録処理後
if($status==false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
}else{
echo json_encode(array("success" => true));
exit();
}
?>
