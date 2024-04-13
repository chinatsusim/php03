<!-- キーワード入力フォーム -->
<form action="index.php" method="POST" class="keyword">
    <input type="text" name="author">
    <button id="submit"><span class="material-symbols-outlined">search</span></button>
</form>

<!-- フォーム処理 -->
    <?php
    if (isset($_POST["author"]) && !empty($_POST["author"])){

        $query = $_POST["author"];

        $maxResults = 6;
        $startIndex = 0;

        $base_url = 'https://www.googleapis.com/books/v1/volumes?q=';
        $url = $base_url.$query.'&maxResults='.$maxResults.'&startIndex='.$startIndex;

        $json = file_get_contents($url);
        $data = json_decode($json);

        $total_count = $data->totalItems;
        $books = $data->items;
        $get_count = count($books);

    } else {
        $total_count = 0;
        $get_count = 0;
        $startMsg = '検索してください。';
        echo '';
    }
    ?>
    <?php if($get_count > 0): ?>
    
    <div class="result">
        <div><b>「<?php echo $query; ?>」に関する絵本</b></div>
        <div>全<?php echo $total_count; ?>件中、<?php echo $get_count; ?>件を表示中</div>   
    </div>

    <div id="books">
        
    <?php 
    foreach($books as $book):
        $title = $book->volumeInfo->title ?? 'データなし';
        $thumbnail = $book->volumeInfo->imageLinks->thumbnail ?? './img/no_image.png';
        $authors = isset($book->volumeInfo->authors) ? implode(',', $book->volumeInfo->authors) : 'データなし';
        $publisher = $book->volumeInfo->publisher ?? 'データなし';
        $publishedDate = $book->volumeInfo->publishedDate ?? 'データなし';
        $description = $book->volumeInfo->description ?? 'データなし';
        $isbn = $book->volumeInfo->industryIdentifiers[0]->identifier ?? 'データなし';

        // いずれかの情報がnullならそのアイテムをスキップ
        if ($title === null || $thumbnail === null || $authors === null) {
            continue;
        }
    ?>
        <div class="bookinfo">
            <div><img src="<?php echo $thumbnail; ?>" ?></div>
            <h3><?php echo $title; ?></h3>
            <ul class="bookprofile">
                <li>著者：<?php echo $authors; ?></li>
                <li>出版社：<?php echo $publisher; ?></li>
                <li>出版年：<?php echo $publishedDate; ?></li>
                <li>ISBN：<?php echo $isbn; ?></li>
            </ul>
            
            <div class="description"><?php echo $description; ?></div>

            <form method="POST">
                <input type="hidden" name="title" value="<?php echo $title; ?>">
                <input type="hidden" name="thumbnail" value="<?php echo $thumbnail; ?>">
                <input type="hidden" name="authors" value="<?php echo $authors; ?>">
                <input type="hidden" name="publisher" value="<?php echo $publisher; ?>">
                <input type="hidden" name="publishedDate" value="<?php echo $publishedDate; ?>">
                <input type="hidden" name="description" value="<?php echo $description; ?>">
                <input type="hidden" name="isbn" value="<?php echo $isbn; ?>">
                <button type="submit" class="add"><span class="material-symbols-outlined">heart_plus</span>favorite</button>
            </form>
        </div>
      <?php endforeach; ?>
    </div>

  <?php else: ?>
    <p>検索してみましょう！</p>
  <?php endif; ?>