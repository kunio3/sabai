<?php
   //共通変数・関数ファイルを読み込み
   require('function.php');

   debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
   debug('「「ショッピングカートページ「「');
   debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
   debugLogStart();

   //ログイン認証
   require('auth.php');
   //================================
   // 画面処理
   //================================
   //ユーザーIDの取得
   $u_id = $_SESSION['user_id'];
   debug('ユーザーID:'.$u_id);
   //商品IDの取得
   $p_ids = getProductId($u_id);
   debug('商品ID:'.print_r($p_ids,true));
   //カートに入っている商品情報を取得
   $viewData = getProductCart($u_id);
   debug('商品情報の中身:'.print_r($viewData,true));
   //カート商品の有無
   $hasData = !empty($viewData);
   //カート商品の合計金額
   $totalPrice = 0;
   foreach($viewData as $key => $val){
    $totalPrice += $val['price'];
    }
    debug('カート商品の合計金額:'.print_r($totalPrice,true));

   if(!empty($_POST)){
        debug('POST送信があります');
        debug('POST送信の中身:'.print_r($_POST,true));
        //例外処理
        try {
            //DBに接続
            $dbh = dbConnect();
            //SQL文の作成
            $sql = 'INSERT INTO order_history(buy_user, product_id, created_at) VALUES(:u_id, :p_id, :date)';
            //商品IDを一つずつセット
            foreach($p_ids as $p_id){
                $data = array(':u_id' => $_SESSION['user_id'],':p_id' => $p_id,':date' => date('Y-m-d H:i:s'));
                debug('データの中身:'.print_r($data, true));
                //SQL文の実行
                $stmt = queryPost($dbh,$sql,$data);
            }
            
            if($stmt){
                debug('クエリ成功');
                //ショッピングカートの中身を空にする
                $sql = 'DELETE FROM shopping_cart WHERE user_id = :u_id';
                $data = array(':u_id' => $_SESSION['user_id']);
                $stmt = queryPost($dbh,$sql,$data);

                debug('クエリ結果の中身:'.print_r($stmt,true));

                //セッション変数の中身
                $_SESSION['msg_success'] = SUC04;

                debug('セッション変数の中身:'.print_r($_SESSION,true));

                //セッションを今すぐ保存する
                session_write_close(); 

                //商品リストページへ遷移
                header("Location:orderHistory.php");
            }else{
                debug('クエリ失敗');
                return false;
            }

        } catch (Exception $e) {
            error_log('エラー発生:'.$e->getMessage());
        }

   }
   
   debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
   
?>
<!-- ヘッド読み込み -->
<?php require('head.php'); ?>

    <body class="page-cart page-1column page-logined">

        <p id="js-show-msg" style="display:none" class="msg-slide">
            <?php echo getSessionFlash('msg_success'); ?>
        </p>

        <!-- ヘッダー読み込み -->
        <?php require('header.php'); ?>

        <main id="main" class="site-width">

           <div class="page-header">
                <h1 class="page-title">ショッピングカート</h1>
           </div>
           <div class="page-container">
                <section id="content">
                    <div class="item-left">
                        <?php if(!empty($viewData)): ?>
                            <?php foreach($viewData as $key => $val): ?>
                            <div class="item">
                                <div class="item-img">
                                    <img src="<?php echo sanitize($val['pic1']); ?>" alt="<?php echo sanitize($val['name']); ?>">
                                </div>
                                <div class="item-detail">
                                    <div class="item-title"><?php echo sanitize($val['name']); ?></div>
                                    <div class="item-price">￥<?php echo sanitize(number_format($val['price'])); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>カートに商品はありません。</p>
                        <?php endif; ?>
                            <div class="item-total">
                                <div class="item-total-title">合計</div>
                                <div class="item-total-price">￥<span><?php echo sanitize(number_format($totalPrice)); ?></span></div>
                            </div>
                    </div>
                    <div class="item-right">
                        <form action="" method="POST">
                            <button type="submit" name="submit" class="btn btn-primary js-disabled-submit">購入する</button>
                        </form>
                    </div>
                </section>
           </div>

        </main>

        <!-- フッター読み込み -->
        <?php require('footer.php'); ?>


        <script>
    //ボタンの非活性化
    $(function(){
        //JSON形式の文字列に変換する
        const hasData = <?php echo json_encode($hasData); ?>;
        //$viewDataがある場合にsubmitボタンが押せるようにする
        //$viewDataがない場合はsubmitボタンを非活性にする
        //Disabledと意味が逆なので！をつける
        if(hasData){
            $('.js-disabled-submit').prop('disabled',false);
        }else{
            $('.js-disabled-submit').prop('disabled',true);
        }
    });
</script>



       