<?php
function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

// 1.  DB接続します
require 'connect.php';

//２．データ登録SQL作成
$sql = "SELECT * FROM gs_kadai_02 ORDER BY registDate desc";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

//３．データ表示
// $view="";
if($status==false) {
//execute（SQL実行時にエラーがある場合）
$error = $stmt->errorInfo();
exit("SQL_ERROR:".$error[2]);
}

//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード] //fetchは繰り返しとってくる
//JSONい値を渡す場合に使う
$json = json_encode($values,JSON_UNESCAPED_UNICODE);
?>

<h2>FAVORITE LIST</h2>

<div class="filters">
  <a class="filter-btn" data-rate="1">★1</a>
  <a class="filter-btn" data-rate="2">★2</a>
  <a class="filter-btn" data-rate="3">★3</a>
  <a class="filter-btn" data-rate="4">★4</a>
  <a class="filter-btn" data-rate="5">★5</a>
  <a class="filter-btn" data-rate="all">All</a>
  <a class="filter-btn" data-rate="unread">Unread</a>
</div>

<!-- <div class="sort">
  <button id="sort-asc"><span class="material-symbols-outlined">sort</span>RATE ASC</button>
  <button id="sort-desc"><span class="material-symbols-outlined">sort</span>RATE DESC</button>
</div> -->

<div id="books_f">
<div id="no-result" style="display: none;">No Result</div>
<?php foreach($values as $value){ ?>
    <div class="bookinfo_f" data-rate="<?= $value['rate']; ?>">
        <div><img src="<?=$value["thumbnail"];?>" ?></div>
        <h3><?=$value["title"];?></h3>
        <ul class="bookprofile">
            <li>著者：<?=$value["authors"];?></li>
            <li>出版社：<?=$value["publisher"];?></li>
            <li>出版年：<?=$value["publishedDate"];?></li>
            <li>ISBN：<?=$value["isbn"];?></li>
        </ul>
        <div class="description"><?=$value["description"];?></div>

        <!-- レビューデータがある場合のみ呼び出す -->
        <?php if (!empty($value["rate"])): ?>
            <div class="reviewed">
                <div class="star-rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?php if ($i <= $value["rate"]) echo "filled"; ?>">&#9733;</span>
                    <?php endfor; ?>
                </div>
                <div class="reviewed_comment"><?=$value["memo"];?></div>
            </div>
        <?php endif; ?>

        <div class="review_popup" style="display:none;">
            <a href="javascript:void(0);" class="close-btn"></a>
            <div class="pop-container">
            <form method="POST" action="update.php">
                <h4>面白かった？</h4>
                    <div class="fivestar">
                        <input type="radio" id="rate5-<?=$value["isbn"];?>" value="5" name="rate" <?= $value["rate"] == 5 ? 'checked' : ''; ?>><label for="rate5-<?=$value["isbn"];?>" class="star">★</label> 
                        <input type="radio" id="rate4-<?=$value["isbn"];?>" value="4" name="rate" <?= $value["rate"] == 4 ? 'checked' : ''; ?>><label for="rate4-<?=$value["isbn"];?>" class="star">★</label>
                        <input type="radio" id="rate3-<?=$value["isbn"];?>" value="3" name="rate" <?= !isset($value["rate"]) || $value["rate"] == 3 ? 'checked' : ''; ?>><label for="rate3-<?=$value["isbn"];?>" class="star">★</label>
                        <input type="radio" id="rate2-<?=$value["isbn"];?>" value="2" name="rate" <?= $value["rate"] == 2 ? 'checked' : ''; ?>><label for="rate2-<?=$value["isbn"];?>" class="star">★</label>
                        <input type="radio" id="rate1-<?=$value["isbn"];?>" value="1" name="rate" <?= $value["rate"] == 1 ? 'checked' : ''; ?>><label for="rate1-<?=$value["isbn"];?>" class="star">★</label>
                    </div>
                <h4>お子さんの様子をメモしておこう(任意)</h4>
                    <textarea name="memo" cols="20" rows="5"><?=$value["memo"];?></textarea>
                    <input type="hidden" name="isbn" value="<?=h($value["isbn"])?>">
                <div class="button-wrapper">
                    <button type="submit" class="review_send"><span class="material-symbols-outlined">send</span>send</button>
                </div>
            </form>
            </div>
        </div>

        <div class="d-btn">
            <!-- UPDATE -->
            <button type="button" class="review"><span class="material-symbols-outlined">reviews</span>review</button>
            <!-- <a href="javascript:void(0);" class="review"><span class="material-symbols-outlined">reviews</span>review</a> -->

            <!-- DELETE -->
            <form method="POST">
                <input type="hidden" name="isbn" value="<?= $value['isbn']; ?>">
                <button type="submit" class="delete"><span class="material-symbols-outlined">heart_plus</span>delete</button>
            </form>
        </div>

        <div class="regist-date">登録日：<?=$value["registDate"];?></div>

    </div>
    <?php }?>
 </div>

<!-- 2カラムmasonry適用 -->
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script>
$(document).ready(function() {
    $('#books_f').masonry({
        itemSelector: '.bookinfo_f',
        columnWidth: 185,
        gutter: 10,
        percentPosition: false
    });
});

// フィルター制御
$(document).on("click",".filter-btn",function(e){
    e.preventDefault();
    showLoading();

    $(".filter-btn").css('color','#4b4b4b');
    $(".filter-btn").css('font-weight','normal');
    $(this).css('color','#ffd755');
    $(this).css('font-weight','800');

    let selectRate = $(this).data("rate"); //ボタンのrate数
    let books = $(".bookinfo_f"); //書誌数
    let showCount = 0;

    setTimeout(function() {
        for(let i=0;i<books.length;i++){
            let book = $(books[i]);
            let bookRate = book.data("rate");

            if(selectRate === "all"){
                book.show();
                showCount++;
            }else if(selectRate === "unread"){
                if(bookRate === null || bookRate ===""){
                    book.show();
                    showCount++;
                } else {
                    book.hide();
                }
            }else if(bookRate === selectRate){
                book.show();
                showCount++;
            }else {
                book.hide();
            }
        // no resultの表示実装
        if(showCount===0){
            $("#no-result").show();
        } else {
            $("#no-result").hide();
        }

        }
        $('#books_f').masonry('layout');
    },800);
});

</script>