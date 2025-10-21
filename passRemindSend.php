<?php
    //共通関数・関数ファイルの読み込み
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('「「パスワード再発行メール送信ページ「「');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogStart();

    //ログイン認証はなし

    //================================
    // 画面処理
    //================================
        //post送信されていた場合
        if(!empty($_POST)){
            debug('POST送信があります。');
            debug('POST送信の中身:'.print_r($_POST,true));
            //変数にPOST情報を代入
            $email = $_POST['email'];
            
            //未入力チェック
            validRequired($email,'email');
            //emailの形式チェック
            validEmail($email,'email');
            //emailの最大文字数チェック
            validMaxLen($email,'email');

            if(empty($err_msg)){
                debug('バリデーションOKです。');
                //例外処理
                try {
                    //DBへ接続
                    $dbh = dbConnect();
                    //SQL文作成
                    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
                    $data = array(':email'=>$email);
                    //クエリ実行
                    $stmt = queryPost($dbh,$sql,$data);
                    //クエリ結果の値を取得
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    debug('クエリ結果の中身:'.print_r($result,true));

                    //EmailがDBに登録されている場合
                    if($stmt && array_shift($result)){
                        debug('クエリ成功。');
                        debug('DB登録があります。');

                        $_SESSION['msg_success'] = SUC05;

                        //認証キー生成
                        $auth_key = makeRandKey();

                        //メールを送信
                        $from = 'info@Sabai.com';
                        $to = $email;
                        $subject = '【パスワード再発行認証メール】｜Sabai（サバイ）';
                        $comment = <<<EOT
                        本メールアドレス宛にパスワード再発行のご依頼がありました。
                        下記のURLから認証キーをご入力いただくとパスワードが再発行されます。

                        パスワード再発行認証キー入力ページ：http://localhost:8888/sabai/passRemindRecieve.php
                        認証キー：{$auth_key}
                        ※認証キーの有効期限は30分となります

                        ///////////////////////////////////////////////////////////////////////////
                        Sabai（サバイ）カスタマーセンター
                        Email: info@Sabai.com
                        ///////////////////////////////////////////////////////////////////////////
                        EOT;
                                sendMail($from,$to,$subject,$comment);

                                //認証に必要な情報をセッションへ保存
                                $_SESSION['auth_key'] = $auth_key;
                                $_SESSION['auth_email'] = $email;
                                //現在時刻より30分後のUNIXタイムスタンプを入れる
                                $_SESSION['auth_key_limit'] = time() + (60*30);

                                //認証キー入力ページへ
                                header("Location:passRemindRecieve.php");
                    }else{
                        debug('DBに登録のないメールアドレスが入力されました');
                        $err_msg['common'] = MSG07;
                    }

                } catch (Exception $e){
                    error_log('エラー発生:'.$e->getMEssage());
                    $err_msg['common'] = MSG07;
                }

            }

    }

?>
<!-- ヘッド読み込み -->
<?php require('head.php'); ?>

    <body class="page-signup page-1column">

        <!-- ヘッダー読み込み -->
        <?php require('header.php'); ?>

        <main id="main" class="site-width">

            <section id="content">
                <div class="form-container">
                    <form action="" method="POST" class="form">
                        <h2 class="title">パスワードを忘れた方</h2>
                        <p class="area-text">登録メールアドレスを入力してください。<br>パスワード再発行用の認証メールを送信します。</p>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        </div>
                        <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                            <input type="email" name="email" value="<?php echo getFormData('email'); ?>" placeholder="メールアドレス">
                        </label>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
                        </div>
                        <div class="btn-container">
                            <input type="submit" class="btn btn-mid" value="認証メールを送信する">
                        </div>
                    </form>
                </div>
            </section>

        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php');
