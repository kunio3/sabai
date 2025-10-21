<?php
    //共通関数・関数ファイルの読み込み
    require('function.php');

    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debug('「「商品登録・編集ページ「「');
    debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
    debugLogStart();

    //ログイン認証
    require('auth.php');
    //=================================
    // 画面処理
    //=================================
    //GETデータを格納
    $p_id = (!empty($_GET['p_id']))? $_GET['p_id']: '';
    debug('商品ID:'.$p_id);
    //DBから商品データを取得
    $dbFormData = (!empty($p_id))? getProduct($_SESSION['user_id'], $p_id): '';
    debug('商品データ:'.print_r($dbFormData,true));
    //新規登録画面か編集画面かを判別するフラグ
    $edit_flg = (!empty($dbFormData))? true : false ;

    //ユーザーID
    $u_id = $_SESSION['user_id'];
    debug('ユーザーID:'.$u_id);
    //ユーザー情報を格納
    $userData = getUser($u_id);
    debug('ユーザーデータ:'.print_r($userData,true));
    //=================================
    // パラメータ改ざんチェック
    //=================================
    //GETパラメータはあるが、改ざんされている場合、正しい商品データが取れないのでマイページへ遷移させる
    if(!empty($p_id) && empty($dbFormData)){
        debug('GETパラメータがありません。マイページへ遷移します。');
        //マイページへ
        header("Location:mypage.php");
    }
    //=================================
    // POST送信時処理
    //=================================
    //POST送信されているかチェック
    if(!empty($_POST)){
        debug('POST送信があります。');
        debug('POST情報:'.print_r($_POST,true));
        debug('FILE情報:'.print_r($_FILES,true));

        //変数にユーザー情報を代入
        $name = $_POST['name'];
        $price = (!empty($_POST['price']))? $_POST['price']: 0;
        $color = $_POST['color'];
        $size = $_POST['size'];
        $pic1 = (!empty($_FILES['pic1']['name']))? uploadImg($_FILES['pic1'], 'pic1'): '';
        $pic1 = (empty($pic1) && !empty($dbFormData['pic1']))? $dbFormData['pic1'] : $pic1;
        $pic2 = (!empty($_FILES['pic2']['name']))? uploadImg($_FILES['pic2'], 'pic2'): '';
        $pic2 = (empty($pic2) && !empty($dbFormData['pic2']))? $dbFormData['pic2'] : $pic2;
        $pic3 = (!empty($_FILES['pic3']['name']))? uploadImg($_FILES['pic3'], 'pic3'): '';
        $pic3 = (empty($pic3) && !empty($dbFormData['pic3']))? $dbFormData['pic3'] : $pic3;
        $detail = $_POST['detail'];

        //バリデーションチェック
        //DBの情報がなく、新規の場合
        if(empty($dbFormData)){
            //未入力チェック(商品名)
            validRequired($name, 'name');
            //最大文字数チェック
            validMaxLen($name, 'name');
            //未入力チェック（金額）
            validRequired($price, 'price');
            //半角数字チェック
            validNumber($price, 'price');
            //セレクトボックスチェック
            validSelect($color, 'color');
            //セレクトボックスチェック
            validSelect($size, 'size');
            //未入力チェック（商品詳細）
            validRequired($detail,'detail');
        }else{
            //DBに情報があり、編集の場合
            if($dbFormData['name'] !== $name){
                //未入力チェック
                validRequired($name, 'name');
                //最大文字数チェック
                validMaxLen($name, 'name');
            }
            if($dbFormData['price'] !== $price){
                //未入力チェック
                validRequired($price, 'price');
                //半角数字チェック
                validNumber($price, 'price');
            }
            if($dbFormData['detail'] !== $detail){
                //最大文字数チェック
                validMaxLen($detail, 'detail', 500);
            }
            if($dbFormData['color'] !== $color){
                //セレクトボックスチェック
                validSelect($color, 'color');
            }
            if($dbFormData['size'] !== $size){
                //セレクトボックスチェック
                validSelect($size, 'size');
            }
        }

        if(empty($err_msg)){
            //バリデーションチェックOKです
            debug('バリデーションOKです');
            //例外処理
            try {
                //DBへ接続
                $dbh = dbConnect();
                if($edit_flg){
                    debug('DB更新です');
                    //SQL文作成
                    $sql = 'UPDATE product SET name = :name, price = :price, color = :color, size = :size, pic1 = :pic1, pic2 = :pic2, pic3 = :pic3, detail = :detail WHERE user_id = :u_id AND id = :p_id';
                    $data = array(':name' => $name,':price' => $price,':color' => $color,':size' => $size,':pic1' => $pic1,':pic2' => $pic2,':pic3' => $pic3,':detail' => $detail,':u_id' => $_SESSION['user_id'],':p_id' => $p_id);
                }else{
                    debug('DB新規登録です');
                    //SQL文作成
                    $sql = 'INSERT INTO product(user_id, name, price, color, size, pic1, pic2, pic3, detail, created_at) VALUES (:u_id, :name, :price, :color, :size, :pic1, :pic2, :pic3, :detail, :date)';
                    $data = array(':u_id' => $_SESSION['user_id'], ':name' => $name, ':price' => $price, ':color' => $color, ':size' => $size, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':detail' => $detail, ':date' => date('Y-m-d H:i:s'));
                }
                debug('SQL:'.$sql);
                debug('流し込みデータ:'.print_r($data, true));

                //クエリ実行
                $stmt = queryPost($dbh, $sql, $data);

                //クエリ成功
                if($stmt){
                    if(!$edit_flg){
                        $_SESSION['msg_success'] = SUC02;
                    }else{
                        $_SESSION['msg_success'] = SUC06;
                    }
                    debug('アイテム一覧に遷移します');
                    header("Location:index.php");
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

    <body class="page-admin page-2column page-logined">

        <!-- ヘッダー読み込み -->
        <?php require('header.php'); ?>

        <main id="main" class="site-width">

        <!-- ヘッダーページ読み込み -->
        <?php require('pageTitle.php'); ?>

           <div class="page-container">
                <!-- サイドバー読み込み -->
                <?php require('sidebar.php'); ?>
                <section id="content">
                    <form action="" method="POST" class="form" enctype="multipart/form-data">
                        <h2 class="title">商品登録</h2>
                        <div class="form-group">
                            <div class="area-msg">
                                <?php echo getErrMsg('common'); ?>
                            </div>
                            <label>
                                商品名
                                <div class="area-msg">
                                    <?php echo getErrMsg('name'); ?>
                                </div>
                                <input type="text" name="name" placeholder="例）タイパンツ" value="<?php echo getFormData('name'); ?>">
                            </label>
                           
                        </div>
                        <div class="form-group">
                            <label>
                                金額
                                <div class="area-msg">
                                    <?php echo getErrMsg('price'); ?>
                                </div>
                                <input type="text" name="price" placeholder="例）1,000" style="width: 150px" value="<?php echo getFormData('price'); ?>">
                                <span class="option">円</span>
                            </label>
                        </div>
                      <div class="form-group">
                            <label>
                                カラー
                                <div class="area-msg">
                                    <?php echo getErrMsg('color'); ?>
                                </div>
                                <select name="color">
                                    <option value="0">選択してください</option>
                                    <option value="1"<?php if((int)getFormData('color') === 1){ echo 'selected'; } ?>>レッド</option>
                                    <option value="2"<?php if((int)getFormData('color') === 2){ echo 'selected'; } ?>>ピンク</option>
                                    <option value="3"<?php if((int)getFormData('color')  === 3){ echo 'selected'; } ?>>ブルー</option>
                                    <option value="4"<?php if((int)getFormData('color')  === 4){ echo 'selected'; } ?>>オレンジ</option>
                                    <option value="5"<?php if((int)getFormData('color')  === 5){ echo 'selected'; } ?>>ブラック</option>
                                </select>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                サイズ
                                <div class="area-msg">
                                    <?php echo getErrMsg('size'); ?>
                                </div>
                                <select name="size">
                                    <option value="0">選択してください</option>
                                    <option value="1"<?php if((int)getFormData('size') === 1){ echo 'selected'; } ?>>S</option>
                                    <option value="2"<?php if((int)getFormData('size') === 2){ echo 'selected'; } ?>>M</option>
                                    <option value="3"<?php if((int)getFormData('size')=== 3){ echo 'selected'; } ?>>L</option>
                                    <option value="4"<?php if((int)getFormData('size') === 4){ echo 'selected'; } ?>>XL</option>
                                </select>
                            </label>
                        </div>
                        <div class="img-container">
                            <label>
                                商品画像①
                                <div class="area-msg">
                                    <?php echo getErrMsg('pic1'); ?>
                                </div>
                                <div class="area-drop">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="314528">
                                    <input type="file" name="pic1" class="input-file">
                                    <img src="<?php echo getFormData('pic1'); ?>" class="img-prev" style="<?php if(empty(getFormData('pic1'))) echo 'display: none';?>">
                                    ドラッグ&ドロップ
                                </div>
                            </label>
                        </div>
                        <div class="img-container">
                            <label>
                                商品画像②
                                <div class="area-msg">
                                    <?php echo getErrMsg('pic2'); ?>
                                </div>
                                <div class="area-drop">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="314528">
                                    <input type="file" name="pic2" class="input-file">
                                    <img src="<?php echo getFormData('pic2'); ?>" class="img-prev" style="<?php if(empty(getFormData('pic2'))) echo 'display: none';?>">
                                    ドラッグ&ドロップ
                                </div>
                            </label>
                        </div>
                        <div class="img-container">
                            <label>
                                商品画像③
                                <div class="area-msg">
                                    <?php echo getErrMsg('pic3'); ?>
                                </div>
                                <div class="area-drop">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="314528">
                                    <input type="file" name="pic3" class="input-file">
                                    <img src="<?php echo getFormData('pic3'); ?>" class="img-prev" style="<?php if(empty(getFormData('pic3'))) echo 'display: none'; ?>">
                                    ドラッグ&ドロップ
                                </div>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                商品詳細
                                <div class="area-msg">
                                    <?php echo getErrMsg('detail'); ?>
                                </div>
                                <textarea name="detail" cols="30" rows="10" style="height: 250px;"><?php echo getFormData('detail'); ?></textarea>
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="btn-container">
                                <?php if($edit_flg): ?>
                                    <input type="submit" class="btn btn-mid" value="編集する">
                                <?php else: ?>
                                    <input type="submit" class="btn btn-mid" value="出品する">
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
                        
                </section>
           </div>

        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php');