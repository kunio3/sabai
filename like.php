<?php
    //共通関数・関数ファイルの読み込み
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('「「お気に入りページ「「');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogStart();

    //================================
    // 画面処理
    //================================
    //ユーザーID
    $u_id = $_SESSION['user_id'];
    debug('ユーザーID:'.$u_id);
    //ユーザー情報
    $userData = getUser($u_id);
    debug('ユーザー情報の中身:'.print_r($userData,true));
    //お気に入り情報
    $likeData = getMyLike($u_id);
    debug('お気に入り情報の中身:'.print_r($likeData,true));

debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

?>
<!-- ヘッド読み込み -->
<?php require('head.php'); ?>

<body class="page-like page-2column page-logined">

    <!-- ヘッダー読み込み -->
    <?php require('header.php'); ?>

    <main id="main" class="site-width">

    <!-- ヘッダーページの読み込み -->
    <?php require('pageTitle.php'); ?>

    <div class="page-container">
        <!-- サイドバーの読み込み -->
        <?php require('sidebar.php'); ?>
        <section id="content">
            <h2 class="title" style="padding:15px;">お気に入り商品一覧</h2>
            <div class="panel-list">
                <?php if(!empty($likeData)): ?> 
                    <?php foreach($likeData as $key => $val): ?>
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
                <?php else: ?>
                    <p style="padding:15px">お気に入りはまだありません。</p>
                <?php endif; ?>
            </div>

        </section>
    </div>

    </main>

<!-- フッター読み込み -->
<?php require('footer.php'); ?>