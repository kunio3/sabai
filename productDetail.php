<?php
    //共通変数・関数ファイルの読み込み
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('「「商品詳細ページ「「');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogStart();


    //ログイン認証
    require('auth.php');

    //================================
    // 画面処理
    //================================
    // 商品IDのGETパラメータを取得
    $p_id = (!empty($_GET['p_id']))? $_GET['p_id']: '';
    //DBから商品データを取得
    $viewData = getProductOne($p_id);
    debug('商品情報の中身:'.print_r($viewData,true));
    //ユーザーID
    $u_id = $_SESSION['user_id'];
    debug('ユーザーID:'.$u_id);
    //ユーザー情報
    $userData = getUser($u_id);
    debug('ユーザー情報の中身:'.print_r($userData,true));

    //カラー情報の取得
    $colorList = [
        1 => 'レッド',
        2 => 'ピンク',
        3 => 'ブルー',
        4 => 'オレンジ',
        5 => 'ブラック',
        6 => 'ピンク'
    ];
    $color = $viewData['color'];

    //サイズ情報の取得
    $sizeList = [
        1 => 'S',
        2 => 'M',
        3 => 'L',
        4 => 'XL'
    ];
    $size = $viewData['size'];

    //パラメータに不正な値が入っているかチェックq
    if(empty($viewData)){
        error_log('指定ページに不正な値が入りました');
        //トップページへ遷移
        header("Location:index.php");
    }

    debug('取得した商品情報:'.print_r($viewData,true));

    //post送信されていた場合
    if(!empty($_POST)){
        debug('POST送信があります');
        debug('POST送信の中身:'.print_r($_POST, true));

    //ログイン認証
    require('auth.php');

    //例外処理
    try {
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'INSERT INTO shopping_cart(user_id, product_id, created_at) VALUES(:u_id, :p_id, :date)';
        $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id'], ':date'=> date('Y-m-d H:i:s'));
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            debug('クエリ成功');
            $_SESSION['msg_success'] = SUC03;
            debug('ショッピングカート画面に遷移します');
            //カート画面へ
            header("Location:cart.php");
        } else {
            debug('クエリ失敗');
        }
    } catch (Exception $e) {
        error_log('エラー発生:'.$e->getMessage());
    }

    }
    debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
?>
<!-- ヘッド読み込み -->
 <?php require('head.php'); ?>

    <body class="page-productDetail page-1column">

        <!-- ヘッダー読み込み -->
        <?php require('header.php'); ?>

        <main id="main" class="site-width">

            <section id="content">
                <div class="product-left">
                    <div class="product-img-container">
                        <img src="<?php echo sanitize($viewData['pic1']); ?>" alt="画像1:<?php echo sanitize($viewData['name']); ?>">
                    </div>
                    <div class="product-img-sub">
                        <img src="<?php echo sanitize($viewData['pic2']); ?>" alt="画像2:<?php echo sanitize($viewData['name']); ?>">
                        <img src="<?php echo sanitize($viewData['pic3']); ?>" alt="画像3:<?php echo sanitize($viewData['name']); ?>">
                    </div>
                </div>
                <div class="product-right">
                    <h1 class="product-name"><?php echo sanitize($viewData['name']); ?></h1>
                    <div class="price">
                        ￥<?php echo sanitize(number_format($viewData['price'])); ?><span class="tax">（税込）</span>
                    </div>
                    <div class="color">
                        <h2 class="title">カラー</h2>
                        <span><?php echo sanitize($colorList[$color]); ?></span>
                    </div>
                    <div class="size">
                        <h2 class="title">サイズ</h2>
                        <span><?php echo sanitize($sizeList[$size]); ?></span>
                    </div>
                    <div class="cart">
                        <form action="" method="POST">
                                <button type="submit" name="submit" class="btn btn-primary">カートに入れる</button>
                                <span class="like">
                                    <i class="fa-heart js-like-click <?php if(!empty(isLike($_SESSION['user_id'],$viewData['id']))){echo 'fa-solid active';} else {echo 'fa-regular';} ?>" data-productId="<?php echo sanitize($viewData['id']); ?>"></i>
                                </span>
                            <?php if(isset($userData['id']) && (int)($userData['user'] === 100)): ?>
                                <button type="button" onclick="location.href='admin.php?p_id=<?php echo sanitize($viewData['id']); ?>'" class="btn btn-edit">商品を編集する</button>
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="product-detail">
                        <h2 class="title">アイテム説明</h2>
                    <p>
                        <?php echo sanitize($viewData['detail']); ?>
                    </p>
                </div>
                </div>
            </section>

        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php'); ?>