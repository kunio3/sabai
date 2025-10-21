<?php
    //共通関数・関数ファイルの読み込み
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('「「パスワード変更ページ「「');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogStart();
    
    //ログイン認証
    require('auth.php');

    //================================
    // 画面処理
    //================================
    //DBからユーザーデータを取得
    $userData = getUser($_SESSION['user_id']);
    debug('取得したユーザー情報:'.print_r($userData, true));
    
    //post送信されていた場合
    if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST送信の中身:'.print_r($_POST,true));


    //変数にユーザー情報を代入
    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];
    $pass_new_re = $_POST['pass_new_re'];
    
    //未入力チェック
    validRequired($pass_old, 'pass_old');
    validRequired($pass_new, 'pass_new');
    validRequired($pass_new_re, 'pass_new_re');

    if(empty($err_msg)){
        debug('未入力チェックOKです');
        //古いパスワードのチェック
        validPass($pass_old, 'pass_old');
        //新しいパスワードのチェック
        validPass($pass_new, 'pass_new');

        //古いパスワードとDBのパスワードを照合
        if(!password_verify($pass_old, $userData['password'])){
            $err_msg['pass_old'] = MSG12;
        }
        
        //新しいパスワードと古いパスワードが同じかチェック
        if($pass_old === $pass_new){
            $err_msg['pass_new'] = MSG13;
        }
    
        //パスワードとパスワード再入力が合っているかチェック
        validMatch($pass_new, $pass_new_re, 'pass_new_re');

        if(empty($err_msg)){
            debug('バリデーションOKです');

            //例外処理
            try {
                //DBへ接続
                $dbh = dbConnect();
                //SQL文作成
                $sql = 'UPDATE users SET `password` = :pass WHERE id = :u_id';
                $data = array(':pass' => password_hash($pass_new, PASSWORD_DEFAULT), ':u_id' => $_SESSION['user_id']);
                //クエリ実行
                $stmt = queryPost($dbh, $sql, $data);

                //クエリ成功の場合
                if($stmt){
                    debug('クエリ成功');
                    $_SESSION['msg_success'] = SUC01;

                    //メールを送信
                    $username = ($userData['username'])? $userData['username'] : '名無し';
                    $from = 'info@sabai.com';
                    $to = $userData['email'];
                    $subject = 'パスワード変更通知 | Sabai';
                    $comment = <<<EOT
{$username}様
パスワードが変更されました。

=======================================
Sabai（サバイ）カスタマーセンター
URL: http://sabai.com
E-mail: info@sabai.com
=======================================
EOT;
                    sendMail($from, $to, $subject, $comment);
                    
                    //マイページへ遷移
                    header("Location:mypage.php");
                }else{
                    debug('クエリに失敗しました');
                    $err_msg['common'] = MSG07;
                }

            } catch (Exception $e){
                error_log('エラー発生:'.$e->getMessage());
                $err_msg['common'] = MSG07;
            }

        }

    }

    }
    debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
?>
<!-- ヘッド読み込み -->
<?php require('head.php'); ?>

    <body class="page-passEdit page-2column page-logined">

        <!-- ヘッダー読み込み -->
        <?php require('header.php'); ?>

        <main id="main" class="site-width">


        <!-- ヘッダーページの読み込み -->
        <?php require('pageTitle.php'); ?>

           <div class="page-container">
                <!-- サイドバー読み込み -->
                <?php require('sidebar.php'); ?>
                <section id="content">
                    <form action="" method="POST" class="form">
                        <h2 class="title">パスワードの変更</h2>
                        <div class="form-group">
                            <div class="area-msg">
                                <?php echo getErrMsg('common'); ?>
                                <?php echo getErrMsg('pass_old'); ?>
                            </div>
                            <label class="<?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>">
                                現在のパスワード
                                <input type="password" name="pass_old" value="<?php echo getFormData('pass_old'); ?>">
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="area-msg">
                                <?php echo getErrMsg('pass_new'); ?>
                            </div>
                            <label class="<?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>">
                                新しいパスワード
                                <input type="password" name="pass_new" value="<?php echo getFormData('pass_new'); ?>">
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="area-msg">
                                <?php echo getErrMsg('pass_new_re'); ?>
                            </div>
                            <label class="<?php if(!empty($err_msg['pass_new_re'])) echo 'err'; ?>">
                                新しいパスワード（確認用）
                                <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re'); ?>">
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="btn-container">
                                <input type="submit" class="btn btn-mid" value="変更する">
                            </div>
                        </div>
                    </form>
                </div>
                        
                </section>
           </div>

        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php');