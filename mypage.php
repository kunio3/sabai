<?php
    //共通関数・関数ファイルの読み込み
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('「「マイページ「「');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogStart();

    //ログイン認証
    require('auth.php');

    //=====================================
    // 画面処理
    //=====================================
    //ユーザーID
    $u_id = $_SESSION['user_id'];
    debug('ユーザーID:'.$u_id);
    //ユーザー情報の取得
    $userData = getUser($u_id);
    debug('ユーザー情報の中身:'.print_r($userData,true));

    debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<!-- ヘッド読み込み -->
<?php require('head.php'); ?>

    <body class="page-mypage page-2column page-logined">

        <p id="js-show-msg" style="display: none" class="msg-slide">
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
                    <div class="info">
                        <h2 class="title">会員登録情報</h2>
                        <div class="info-list">
                            <div class="info-block">
                                <p class="info-label">お名前</p>
                                <div class="info-item"><?php echo (!empty($userData['username']))? $userData['username']: 'ー'; ?></div>
                            </div>
                            <div class="info-block">
                                <p class="info-label">住所</p>
                                <div class="info-item">
                                    <p><?php echo (!empty($userData['zip']))? $userData['zip']: 'ー'; ?></p>
                                    <p><?php echo (!empty($userData['address']))? $userData['address']: 'ー'; ?></p>
                                </div>
                            </div>
                            <div class="info-block">
                                <p class="info-label">性別</p>
                                <div class="info-item"><?php echo (!empty($userData['gender']))? $userData['gender']: 'ー'; ?></div>
                            </div>
                            <div class="info-block">
                                <p class="info-label">電話番号</p>
                                <div class="info-item"><?php echo (!empty($userData['tel']))? $userData['tel']: 'ー'; ?></div>
                            </div>
                            <div class="info-block">
                                <p class="info-label">メールアドレス</p>
                                <div class="info-item"><?php echo $userData['email']; ?></div>
                            </div>
                        </div>
                    </div>
                </section>
           </div>

        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php');