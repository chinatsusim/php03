<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>絵本検索サービス
    </title>
    <script src="js/jquery-2.1.3.min.js"></script>
    <script src="js/masonry.pkgd.min.js"></script>
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
<div id="start" style="display:none;"><img src="img/logo.png" alt=""></div>

<header style="display:none;">
    <h1><a href="./"><img src="img/logo.png" alt=""></a></h1>
</header>

<main style="display:none;">
    <div id="search-result"><?php include 'search.php'; ?></div>
    <div id="favorite-list" style="display:none;"><?php include 'favorite-list.php'; ?></div>
</main>

<div id="popup" style="display:none;">
    お気に入りに追加しました！
</div>
<div id="popup-delete" style="display:none;">
    削除しました！
</div>

<!-- ローディングアニメーション -->
<div id="loading">
  <div class="spinner"></div>
</div>

<!-- フッター -->
<footer style="display:none;">
    <ul class="menu">
        <a href="javascript:void(0);" id="m01"><li><span class="material-symbols-outlined">search</span><br>SEARCH</li></a>
        <a href="javascript:void(0);" id="m02"><li><span class="material-symbols-outlined">favorite</span><br>FAVORITE</li></a>
    </ul>
</footer>

<script>
function start(){
    $('#start').fadeIn(800, function() {
        setTimeout(function() {
            $('#start').fadeOut(800, function() {
                $('header').fadeIn(400);
                $('main').fadeIn(400,function(){
                    $('#books').masonry({
                        itemSelector: '.bookinfo',
                        columnWidth: 185,
                        gutter: 10,
                        percentPosition: false
                    });
                });
                $('footer').fadeIn(400);
            });
        }, 1000);
    });
}

// $(document).ready(function() {
//     let mukou = false;

//     $(document).ready(function() {
//         mukou = true;
//     })

//     function yukou(){
//         if(!mukou){
//             start();
//         }
//     }
//     yukou();
// });


//ローディングアニメーション用の関数
function showLoading() {
    $(document).ready(function() {
        if ($("#loading").is(":visible")) {
        return;
        }
        $("#loading").fadeIn(600).fadeOut(500);
    });
}

//お気に入りリスト表示用の関数
function favoriteRead(){
    $("#favorite-list").load("favorite-list.php", function() {
        $("#favorite-list").show();
    });
}

// 非同期でフォーム送信を実行する（お気に入り追加）
$(".add").on("click",function(e){
    e.preventDefault();

    let form = $(this).closest('.bookinfo');
    
    let sendData = {
        title: form.find('input[name="title"]').val(),
        thumbnail: form.find('input[name="thumbnail"]').val(),
        authors: form.find('input[name="authors"]').val(),
        publisher: form.find('input[name="publisher"]').val(),
        publishedDate: form.find('input[name="publishedDate"]').val(),
        description: form.find('input[name="description"]').val(),
        isbn: form.find('input[name="isbn"]').val()
        }

    $.ajax({
        type: "POST",
        url: "favorite.php",
        data: sendData,
        dataType: "json",
        encode: true,
    })
    
    .done(function(response) {
        $("#popup").fadeIn(800).fadeOut(800);
        setTimeout(function() {
            showLoading();
        },800);
    })
    
    .fail(function(xhr, status, error) {
        alert("エラーです！");
    })
});


// 非同期でフォーム送信を実行する（削除）
$(document).on("click", ".delete", function(e){
    e.preventDefault();
    let form = $(this).closest('.bookinfo_f');
    
    let sendData = {
        isbn: form.find('input[name="isbn"]').val()
    };

    $.ajax({
        type: "POST",
        url: "delete.php",
        data: sendData,
        dataType: "json",
        encode: true,
    })

    .done(function(response) {
        $("#popup-delete").fadeIn(800).fadeOut(800);
        setTimeout(function() {
            showLoading();
        },800);
        setTimeout(function() {
            favoriteRead();
        },1400);
    })

    .fail(function(xhr, status, error) {
        alert("エラーです！");
    })

});

// メニューの切り替え
$("#m02").on("click",function(e){
    e.preventDefault();
    showLoading();
    $("#search-result").hide();
    $("#favorite-list").load("favorite-list.php", function() {
        $(this).show();
    });
});

$("#m01").on("click",function(e){
    e.preventDefault();
    showLoading();
    $("#favorite-list").hide();
    $("#search-result").show();
});

$(document).on("click", ".review", function(e){
    e.preventDefault();
    $(this).closest('.bookinfo_f').find(".review_popup").fadeIn(500);
});

$(document).on("click", ".close-btn", function(e) {
    e.preventDefault();
    $(this).closest('.review_popup').fadeOut(1000);
});

// 非同期でフォーム送信を実行する（レビューの追加）
$(document).on("submit", ".review_popup form", function(e) {
    e.preventDefault();
    
    let form = $(this).closest('.review_popup').find('form');
    
    let sendData = {
        isbn: form.find('input[name="isbn"]').val(),
        rate: form.find('input[name="rate"]:checked').val(),
        memo: form.find('textarea[name="memo"]').val()
    }

    console.log(sendData);
    
    $.ajax({
        type: "POST",
        url: "update.php",
        data: sendData,
        dataType: "json",
        encode: true
    })

    .done(function(response) {
        //1個め
        $(".pop-container").text("登録しました！");
        //2個め
        setTimeout(function() {
            $(".review_popup").fadeOut(500);
        },500);
        setTimeout(function() {
                showLoading();
        },800);
        setTimeout(function() {
            favoriteRead();
        },1400);
    })

    .fail(function(xhr, status, error) {
        console.log(error);
        alert("エラーです！" + xhr + status + error);
    })
});


</script>

<!-- </div> -->
</body>
</html>