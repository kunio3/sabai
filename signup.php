<?php
    //共通関数・関数ファイルを読み込み
    require('function.php');

    //post送信されていた場合
    if(!empty($_POST)){

    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    //未入力チェック
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');

    if(empty($err_msg)){

    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    //email重複チェック
    validEmailDup($email);

    //パスワードの半角英数字チェック
    validHalf($pass, 'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');

    //パスワード（再入力）の最大文字数チェック
    validMaxLen($pass_re, 'pass_re');
    //パスワード（再入力）の最小文字数チェック
    validMinLen($pass_re, 'pass_re');

    if(empty($err_msg)){

    //パスワードとパスワード再入力があっているかチェック
    validMatch($pass, $pass_re, 'pass_re');

    if(empty($err_msg)){

        //例外処理
        try {
        //DBに接続
        $dbh = dbConnect();
        //SQL文の作成
        $sql = 'INSERT INTO users(email,password,login_time,created_at) VALUES(:email,:pass,:login_time,:created_at)';
        $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                      ':login_time' => date('Y-m-d H:i:s'),
                      ':created_at' => date('y-m-d H:i:s'));
        //クエリ実行
       $stmt = queryPost($dbh, $sql, $data);

        //クエリ成功の場合
        if($stmt){
            //ログイン有効期限
            $sesLimit = 60*60;//1時間に設定
            //最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time();
            //ログイン有効期限
            $_SESSION['login_limit'] = $sesLimit;
            //ユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();
            //セッションメッセージ
            $_SESSION['msg_success'] = SUC08;

            debug('セッション変数の中身:'.print_r($_SESSION,true));

             //マイページへ遷移
            header("Location:mypage.php");
        }

       

        } catch (Exception $e) {
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

        <!-- ヘッダー読み込み-->
        <?php require('header.php'); ?>

        <main id="main" class="site-width">

            <section id="content">
                <div class="form-container">
                    <form action="" method="POST" class="form">
                        <h2 class="title">新規会員登録</h2>
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
                        <div class="area-msg">
                            <?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>
                        </div>
                        <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
                            <input type="password" name="pass_re" placeholder="パスワード（確認）" value="<?php echo getFormData('pass_re'); ?>">
                        </label>
                        <div class="btn-container">
                            <input type="submit" class="btn btn-mid" value="登録する">
                        </div>
                    </form>
                </div>
            </section>
            
        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php'); ?>