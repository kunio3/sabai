<?php
    //共通変数・関数ファイルを読み込む
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('「「アイテム一覧ページ「「');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogStart();

    //================================
    // 画面処理
    //================================
    //カレントページのGETパラメータを取得
    $currentPageNum = (!empty($_GET['p']))? $_GET['p'] : 1; //デフォルトは1ページ目
    //ソート順
    $sort = (!empty($_GET['sort']))? $_GET['sort'] : '';
    //カラー
    $color = (!empty($_GET['color']))? $_GET['color'] : '';
    //サイズ
    $size = (!empty($_GET['size']))? $_GET['size'] : '';
    //パラメータに不正な値が入っているかチェック
    if(!is_int((int)$currentPageNum)){
        error_log('指定ページに不正な値が入りました');
        header("Location:index.php");
    }
    //表示件数
    $listSpan = 20;
    //現在の表示レコード先頭を算出
    $currentMinNum = (($currentPageNum - 1) * $listSpan);
    //DBから商品データを取得
    $dbProductData = getProductList($color, $size, $sort, $currentMinNum);
    /*
        $dbProductData = [
            'total'=> $stmt->rowCount(),
            'total_page' => ceil($rst['total']/$span),
            'data' => Array
                (
                    [0] => Array
                        (
                            [id]=>1
                            [name]=>""
                            [price]=>"
                            [pic1]=>""
                            [create_date]=>""
                        )
                    [1] => Array
                        (
                            [id]=>2
                            [name]=>""
                            [price]=>"
                            [pic1]=>""
                            [create_date]=>""
                        )
                    [2] => Array
                        (
                            [id]=>3
                            [name]=>""
                            [price]=>"
                            [pic1]=>""
                            [create_date]=>""
                        )
                    [3] => Array
                        (
                            [id]=>4
                            [name]=>""
                            [price]=>"
                            [pic1]=>""
                            [create_date]=>""
                        )
                    [4] => Array
                        (
                            [id]=>5
                            [name]=>""
                            [price]=>"
                            [pic1]=>""
                            [create_date]=>""
                        )
                )
        ]
    */
    debug('現在のページ:'.$currentPageNum);
    debug('商品データの中身:'.print_r($dbProductData,true));
    
    debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
?>
<!-- ヘッド読み込み -->
<?php require('head.php'); ?>

    <body class="page-productList page-2column">

        <p id="js-show-msg" style="display:none" class="msg-slide">
            <?php echo getSessionFlash('msg_success'); ?>
        </p>

        <!-- ヘッダー読み込み -->
        <?php require('header.php'); ?>

        <main id="main" class="site-width">

           <div class="page-header">
                <h1 class="page-title">アイテム一覧</h1>
           </div>

           <div class="page-container">

                <!-- サイドバー -->
                <section id="sidebar">
                    <form>
                        <section class="price">
                            <h2 class="title">表示順</h2>
                            <div class="select-box">
                                <span class="icn-select"></span>
                                <select name="sort">
                                    <option value="0">選択してください</option>
                                    <option value="1">金額が安い順</option>
                                    <option value="2">金額が高い順</option>
                                </select>
                            </div>
                        </section>
                        <section class="color">
                            <h2 class="title">カラー</h2>
                            <label><input type="radio" name="color" value="1">レッド</label>
                            <label><input type="radio" name="color" value="2">ピンク</label>
                            <label><input type="radio" name="color" value="3">ブルー</label>
                            <label><input type="radio" name="color" value="4">オレンジ</label>
                            <label><input type="radio" name="color" value="5">ブラック</label>
                        </section>
                        <section class="size">
                            <h2 class="title">サイズ</h2>
                            <label><input type="radio" name="size" value="1">S</label>
                            <label><input type="radio" name="size" value="2">M</label>
                            <label><input type="radio" name="size" value="3">L</label>
                            <label><input type="radio" name="size" value="4">XL</label>
                        </section>
                        <input type="submit" value="検索" class="btn btn-search">
                    </form>
                </section>

                <section id="content">

                    <div class="search-title">
                        <div class="search-left">
                            <span class="total-num"><?php echo sanitize($dbProductData['total']); ?></span>件の対象商品が見つかりました
                        </div>
                    </div>
                    <div class="panel-list">
                        <?php foreach($dbProductData['data'] as $key => $val): ?>
                        <a href="productDetail.php?p_id=<?php echo $val['id']; ?>" class="panel">
                            <div class="panel-head">
                                <img src="<?php echo sanitize($val['pic1']); ?>" alt="<?php echo sanitize($val['name']); ?>">
                            </div>
                            <div class="panel-body">
                                <p class="panel-title"><?php echo sanitize($val['name']); ?></p>
                                <p class="panel-price">￥<?php echo sanitize(number_format($val['price'])); ?></p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>

                    <?php pagenation($currentPageNum,$dbProductData['total_page']); ?>
                  
                </section>
           </div>

        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php');