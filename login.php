
<?php
    //共通変数・関数ファイルの読み込み
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('「「ログインページ「「');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogStart();

    //ログイン認証
    require('auth.php');

    //================================
    // ログイン画面処理
    //================================
    // post送信されていた場合
    if(!empty($_POST)){

    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save']))? true : false;
    
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');

    //パスワードの半角英数字チェック
    validHalf($pass, 'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');

    //未入力チェック
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    
    if(empty($err_msg)){
        debug('バリデーションチェックOKです。');
    
        //例外処理
        try {
            //DBへ接続
            $dbh = dbConnect();
            //SQL文の作成
            $sql = 'SELECT password,id FROM users WHERE email = :email and delete_flg = 0';
            $data = array(':email'=> $email);
            //クエリの実行
            $stmt = queryPost($dbh, $sql, $data);
            //クエリ結果の値を取得
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            debug('クエリ結果の中身:'.print_r($result,true));
            
            //パスワード照合
            //array_shiftで配列の最初の要素を取り出して返す（この場合はpassword）
            if(!empty($result) && password_verify($pass, array_shift($result))){
                debug('パスワードが一致しました。');
                //ログイン有効期限
                $sesLimit = 60*60;//60秒✖︎60分（この場合は1時間で設定)
                //最終ログイン日時を現在日時に
                $_SESSION['login_date'] = time();
                
                //ログイン保持にチェックがある場合
                if($pass_save){
                    debug('ログイン保持にチェックがあります。');
                    //ログイン有効期限を30日にしてセット
                    $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                }else{  
                    debug('ログイン保持にチェックはありません。');
                    //次回からログイン保持しないのでログイン有効期限を１時間後にセット
                    $_SESSION['login_limit'] = $sesLimit;
                }
                    //ユーザーIDを格納
                    $_SESSION['user_id'] = $result['id'];

                    //セッションメッセージを格納
                    $_SESSION['msg_success'] = SUC09;

                    //セッションを今すぐ保存する
                    session_write_close(); 
                    
                    debug('セッション変数の中身:'.print_r($_SESSION,true));
                    debug('マイページへ遷移します。');
                    //マイページ
                    header("Location:mypage.php");
                }else{
                    debug('パスワードが一致しません。');
                    $err_msg['common'] = MSG09;
                }

         } catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
        
    }

    }
    debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
?>
<!-- ヘッド読み込み -->
<?php require('head.php'); ?>

    <body class="page-login page-1column">

        <p id="js-show-msg" style="display: none" class="msg-slide">
            <?php echo getSessionFlash('msg_success'); ?>
        </p>

        <!-- ヘッダー読み込み -->
        <?php require('header.php'); ?>

        <main id="main" class="site-width">

            <section id="content">
                <div class="form-container">
                <form action="" method="POST" class="form">
                        <h2 class="title">ログイン</h2>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
                        </div>
                        <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                            <input type="email" name="email" placeholder="メールアドレス" value="<?php echo getFormData('email'); ?>">
                        </label>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
                        </div>
                        <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                            <input type="password" name="pass" placeholder="パスワード" value="<?php echo getFormData('pass'); ?>">
                        </label>
                        <label>
                            <input type="checkbox" name="pass_save">
                            次回のログインを省略する
                        </label>
                        <p class="area-link">
                            <a href="passRemindSend.php">パスワードを忘れた方（再設定）</a>
                        </p>
                        <div class="btn-container">
                            <input type="submit" class="btn btn-mid" value="ログイン">
                        </div>
                    </form>
                </div>
            </section>
            
        </main>

        <!-- フッター読み込み　-->
         <?php require('footer.php'); ?>
