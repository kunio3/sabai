<?php
    //共通変数・関数ファイルを読み込む
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('「「退会ページ「「');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogStart();

    //ログイン認証
    require('auth.php');

    //post送信されていた場合
    if(!empty($_POST)){
        debug('POST送信があります。');
    //例外処理
    try {
        //DB接続
        $dbh = dbConnect();
        //SQL文作成
        $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
        $sql2 = 'UPDATE product SET delete_flg = 1 WHERE user_id = :us_id';
        $sql3 = 'UPDATE `like` SET delete_flg = 1 WHERE user_id = :us_id';
        $sql4 = 'UPDATE shopping_cart SET delete_flg = 1 WHERE user_id = :us_id';
        //データ流し込み
        $data = array(':us_id'=> $_SESSION['user_id']);
        //クエリ実行
        $stmt1 = queryPost($dbh, $sql1, $data);
        $stmt2 = queryPost($dbh, $sql2, $data);
        $stmt3 = queryPost($dbh, $sql3, $data);
        $stmt4 = queryPost($dbh, $sql4, $data);

        //クエリ実行成功の場合
        if($stmt1){
            //セッション削除
            session_destroy();
            debug('セッション変数の中身:'.print_r($_SESSION, true));
            debug('トップページへ遷移します。');
            header("Location:index.php");
        }else{
            debug('クエリが失敗しました。');
            $err_msg['common'] = MSG07;
        }

    } catch (Exception $e) {
        error_log('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }

    }
    debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>')
?>
<!-- ヘッド読み込み -->
<?php require('head.php'); ?>

    <body class="page-withdraw page-2column page-logined">

        <!-- ヘッダー読み込み -->
        <?php require('header.php'); ?>

        <main id="main" class="site-width">

        <!-- ヘッダーページの読み込み -->
        <?php require('pageTitle.php'); ?>
        
           <div class="page-container">
                <!-- サイドバー読み込み -->
                <?php require('sidebar.php'); ?>
                <section id="content">
                    <div class="withdraw">
                        <form action="" method="post" class="form">
                            <h2 class="title">退会手続き</h2>
                                <div class="withdraw-text">
                                    
                                    本当に退会してもよろしいですか？
                                </div>
                                <div class="form-group form-group--withdraw">
                                    <div class="btn-container btn-container--withdraw">
                                        <input type="submit" class="btn btn-mid" value="退会する" name="submit">
                                    </div>
                                </div>
                        </form>
                    </div>
                </section>
           </div>

        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php');