<?php
    //共通変数・関数ファイルを読み込む
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('「「プロフィール編集ページ「「');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogStart();
    
    //ログイン認証
    require('auth.php');

    //================================
    // 画面処理
    //================================
    //DBからユーザーデータを取得
    $dbFormData = getUser($_SESSION['user_id']);

    debug('取得したユーザー情報:'.print_r($dbFormData, true));

    //post送信されていた場合
    if(!empty($_POST)){
        debug('POST送信があります。');
        debug('POST情報:'.print_r($_POST, true));

        //変数にユーザー情報を代入
        $username = $_POST['username'];
        $gender = $_POST['gender'];
        $zip = (!empty($_POST['zip']))? $_POST['zip']: '';
        $address = $_POST['address'];
        $tel = (!empty($_POST['tel']))? $_POST['tel']: '';
        $email = $_POST['email'];

        //DBの情報と入力情報が異なる場合にバリデーションを行う
        if($dbFormData['username'] !== $username){
            //名前の最大文字数チェック
            validMaxLen($username, 'username');
        }

        if($dbFormData['zip'] !== $zip){
            if($zip !== ''){
                //郵便番号形式チェック
                validZip($zip, 'zip');
                //半角英数字チェック
                validNumber($zip, 'zip');
            }
        }

        if($dbFormData['address'] !== $address){
            //住所の最大文字数チェック
            validMaxLen($address, 'address');
        }

        if($dbFormData['tel'] !== $tel){
            if($tel !== ''){
                //電話番号形式チェック
                validTel($tel, 'tel');
                //半角英数字チェック
                validNumber($tel, 'tel');
            }
        }

        if($dbFormData['email'] !== $email){
            //Emailの未入力チェック
            validRequired($email, 'email');
            if(empty($err_msg['email'])){
                //Emailの形式チェック
                validEmail($email, 'email');
                //Emailの最大文字数チェック
                validMaxLen($email, 'email');
            }
            if(empty($err_msg['email'])){
                validEmailDup($email, 'email');
            }
        }

        if(empty($err_msg)){
            debug('バリデーションOKです');

            //例外処理
            try {
                //DBへ接続
                $dbh = dbConnect();
                //SQL文作成
                $sql = 'UPDATE users SET username = :username, gender = :gender, zip = :zip, `address` = :address, tel = :tel, email = :email WHERE id = :u_id';
                $data = array(':username' => $username,':gender' => $gender,':zip' => $zip,':address' => $address,':tel' => $tel, ':email' => $email,':u_id' => $dbFormData['id']);
                debug('データの中身:'.print_r($data,true));
                //クエリ実行
                $stmt = queryPost($dbh, $sql, $data);

                //クエリ成功の場合
                if($stmt){
                    debug('クエリ成功');
                    debug('クエリ情報:'.print_r($stmt, true));
                    debug('マイページへ遷移します');

                    $_SESSION['msg_success'] = SUC07;
                    debug('セッション変数の中身:'.print_r($_SESSION,true));

                    //マイページへ遷移
                    header("Location:mypage.php");
                }else{
                    debug('クエリ失敗');
                    $err_msg['common'] = MSG07;
                }

            } catch (Exception $e) {
                error_log('エラー発生:' .$e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
    debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
?>
<!-- ヘッド読み込み -->
<?php require('head.php'); ?>

    <body class="page-profEdit page-2column page-logined">

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
                        <h2 class="title">基本情報の変更</h2>
                        <div class="form-group">
                            <div class="area-msg">
                                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                                <?php if(!empty($err_msg['username'])) echo $err_msg['username']; ?>
                            </div>
                            <label>
                                お名前
                                <input type="text" name="username" placeholder="例）山田 太郎" value="<?php echo getFormData('username'); ?>">
                            </label>
                        </div>
                        <div class="form-group">
                            <label style="margin-bottom:15px;">
                                性別
                                <div>
                                    <input type="radio" name="gender" value="男性"<?php if($dbFormData['gender'] === '男性') echo "checked"; ?>>
                                    <span>男性</span>
                                    <input type="radio" name="gender" value="女性"<?php if($dbFormData['gender'] === '女性') echo "checked"; ?>>
                                    <span>女性</span>
                                    <input type="radio" name="gender" value="その他"<?php if($dbFormData['gender'] === 'その他') echo "checked"; ?>>
                                    <span>その他</span>
                                </div>
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="area-msg">
                                <?php if(!empty($err_msg['zip'])) echo $err_msg['zip']; ?>
                            </div>
                            <label>
                                郵便番号
                                <input type="text" name="zip" placeholder="例）3430828" value="<?php echo getFormData('zip'); ?>">
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="area-msg">
                                <?php if(!empty($err_msg['address'])) echo $err_msg['address']; ?>
                            </div>
                            <label>
                                住所
                                <input type="text" name="address" placeholder="例）東京都中央区銀座一丁目" value="<?php echo getFormData('address'); ?>">
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="area-msg">
                                <?php if(!empty($err_msg['tel'])) echo $err_msg['tel']; ?>
                            </div>
                            <label>
                                電話番号
                                <input type="text" name="tel" placeholder="例）07041277158" value="<?php echo getFormData('tel'); ?>">
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="area-msg">
                                <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
                            </div>
                            <label>
                                メールアドレス
                                <input type="email" name="email" placeholder="例）user@gmail.com" value="<?php echo getFormData('email'); ?>">
                            </label>
                        <div>
                        <div class="btn-container">
                            <input type="submit" class="btn btn-mid" value="変更する">
                        </div>
                    </form>
                </div>
                        
                </section>
           </div>

        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php');