<?php
    //共通変数・関数ファイルを読み込み
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('「「注文履歴ページ「「');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogStart();

    //ログイン認証
    require('auth.php');
    //================================
    // 画面処理
    //================================
    //ユーザーIDを格納
    $u_id = $_SESSION['user_id'];
    debug('ユーザーID:'.$u_id);
    //DBから注文履歴情報を取得
    $orderHistory = getOrderHistory($u_id);
    debug('注文履歴情報の中身:'.print_r($orderHistory,true));
    
    debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
?>
<!-- ヘッド読み込み -->
<?php require('head.php'); ?>

    <body class="page-orderHistory page-2column page-logined">

        <p id="js-show-msg" style="display:none" class="msg-slide">
            <?php echo getSessionFlash('msg_success'); ?>
        </p>

        <!-- ヘッダー読み込み -->
        <?php require('header.php'); ?>

        <main id="main" class="site-width">

            <!-- ヘッダーページの読み込み -->
            <?php require('pageTitle.php'); ?>
               
           <div class="page-container">
                <!-- サイドバー読み込み -->
                <?php require('sidebar.php'); ?>
                <section id="content">
                    <h2 class="title" style="padding:15px;">購入した商品一覧</h2>
                        <div class="panel-list">
                            <?php if(!empty($orderHistory)): ?>
                                <?php foreach($orderHistory as $key => $val): ?>
                                    <div class="panel">
                                        <div class="panel-head">
                                            <img src="<?php echo sanitize($val['pic1']); ?>" alt="<?php echo sanitize($val['name']); ?>">
                                        </div>
                                        <div class="panel-body">
                                            <p class="panel-title"><?php echo sanitize($val['name']); ?></p>
                                            <p class="panel-price">￥<?php echo sanitize(number_format($val['price'])); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="area-msg" style="padding:15px;">注文した商品はまだありません。</div>
                            <?php endif; ?>      
                        </div>
                </section>
           </div>

        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php');