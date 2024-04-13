<?php
require_once('db.php');
try {
    $pdo = new PDO($server_info, $db_id, $db_pw);
    // return $pdo;

} catch (PDOException $e) {
    exit('DB Connection Error:'. $e->getMessage());
}


//SQLエラー
function sql_error($stmt)
{
//execute（SQL実行時にエラーがある場合）
$error = $stmt->errorInfo();
exit('SQLError:'. $error[2]);
}

?>