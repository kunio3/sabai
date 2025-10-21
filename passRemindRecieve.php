<?php
    //共通変数・関数ファイルの読み込み
    require('function.php');

    //ログイン認証はなし

    //SESSIONに認証キーがあるか確認、なければリダイレクト
    if(empty($_SESSION['auth_key'])){
        header("Location:passRemindSend.php");//認証キー送信ページへ
    }
    //================================
    // 画面処理
    //================================
    //post送信されていた場合
    if(!empty($_POST)){
        debug('POST送信があります。');
        debug('POST送信の中身：'.print_r($_POST,true));

        //変数に認証キーを代入
        $auth_key = $_POST['token'];

        //未入力チェック
        validRequired($auth_key,'token');

        if(empty($err_msg)){
            debug('未入力チェックOKです。');
            //固定調チェック
            validLength($auth_key,'token');
            //半角チェック
            validHalf($auth_key,'token');

            if(empty($err_msg)){
                debug('バリデーションチェックOKです.');

                if($auth_key !== $_SESSION['auth_key']){
                    $err_msg['common'] = MSG15;
                }
                if(time() > $_SESSION['auth_key_limit']){
                    $err_msg['common'] = MSG15;
                }

                if(empty($err_msg)){
                    debug('認証OKです。');
            
                    //パスワード生成
                    $pass = makeRandKey();

                    //例外処理
                    try {
                        //DBへ接続
                        $dbh = dbConnect();
                        //SQL文作成
                        $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
                        $data = array(':pass' => password_hash($pass, PASSWORD_DEFAULT), ':email' => $_SESSION['auth_email']);
                        //クエリ実行
                        $stmt = queryPost($dbh,$sql,$data);

                        //クエリ成功の場合
                        if($stmt){
                            debug('クエリ成功');

                        //メール送信
                        $from = 'info@sabai.com';
                        $to = $_SESSION['auth_email'];
                        $subject = '【パスワード再発行完了メール】｜Sabai（サバイ）';
                        $comment = <<<EOT
                        本メールアドレス宛にパスワードの再発行をいたしました。
                        下記のURLから再発行パスワードをご入力いただき、ログインしてください。

                        ログインページ：http://localhost:8888/sabai/login.php
                        再発行パスワード：{$pass}
                        ※ログイン後、パスワードのご変更をお願いいたします。

                        ///////////////////////////////////////////////////////////////////////////
                        Sabai（サバイ）カスタマーセンター
                        Email: info@Sabai.com
                        ///////////////////////////////////////////////////////////////////////////
                        EOT;
                            sendMail($from,$to,$subject,$comment);

                            //セッション削除
                            session_unset();
                            $_SESSION['msg_success'] = SUC05;

                            debug('セッション変数の中身:'.print_r($_SESSION,true));

                            //セッションを今すぐ保存する
                            session_write_close(); 
                            //ログインページへ
                            header("Location:login.php");

                        }else{
                            debug('クエリに失敗しました。');
                            $err_msg['common'] = MSG07;
                        }

                    } catch (Exception $e){
                        error_log('エラー発生:'.$e->getMessage());
                        $err_msg['common'] = MSG07;
                    }

                }

            }

        }

    }

?>
<!-- ヘッド読み込み -->
<?php require('head.php'); ?>

    <body class="page-signup page-1column">

        <p id="js-show-msg" style="display: none" class="msg-slide">
            <?php echo getSessionFlash('msg_success'); ?>
        </p>

        <!-- ヘッダー読み込み -->
        <?php require('header.php'); ?>

        <main id="main" class="site-width">

            <section id="content">
                <div class="form-container">
                    <form action="" method="POST" class="form">
                        <h2 class="title">認証キーの入力</h2>
                        <p class="area-text">メール内に記載されている「認証キー」を入力してください。</p>
                        <div class="area-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
                        <label class="<?php if(!empty($err_msg['token'])) echo 'err'; ?>">
                            <input type="text" name="token" placeholder="認証キー">
                        </label>
                        <div class="btn-container">
                            <input type="submit" class="btn btn-mid" value="認証する">
                        </div>
                    </form>
                </div>
            </section>

        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php');