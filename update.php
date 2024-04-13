<?php
//1. POSTデータ取得
$isbn = $_POST["isbn"];
$rate = $_POST["rate"];
$memo = $_POST["memo"];

//2. DB接続します
require "connect.php";

//３．データ登録SQL作成
$sql = "UPDATE gs_kadai_02 SET rate=:rate,memo=:memo WHERE isbn=:isbn";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
$stmt->bindValue(':memo', $memo, PDO::PARAM_STR);
$stmt->bindValue(':rate', $rate, PDO::PARAM_INT);
$status = $stmt->execute();

//４．データ登録処理後
if($status==false){
    $error = $stmt->errorInfo();
    exit("SQL_ERROR:".$error[2]);
}else{
    echo json_encode(array("success" => true));
    exit();
}
?>