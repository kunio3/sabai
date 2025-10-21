<?php
//=====================================
// ログ
//=====================================
//ログを取る
ini_set('log_errors','on');
//ログの出力ファイルを指定
ini_set('error_log','php.log');

//=====================================
// デバッグ
//=====================================
//デバッグフラグ
$debug_flg = false;
//デバッグログ関数
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}

//=====================================
// セッションの準備
//=====================================
//セッションファイルの置き場を変更する(30日間削除されないため)
session_save_path("/var/tmp/");
//ガーベッジコレクションが削除するセッションの有効期限を設定（30日以上立っているものに対して100分の1の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//セッションIDを新しく生成したものと置き換える
session_regenerate_id();

//=====================================
// 画面表示処理ログ吐き出し関数
//=====================================
function debugLogStart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
    debug('セッションID:'.session_id());
    debug('セッション変数の中身:'.print_r($_SESSION,true));
    debug('現在日時タイムスタンプ:'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
        debug('ログイン期限日時タイムスタンプ:'.($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
}

//=====================================
// 定数
//=====================================
//エラーメッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02','Emailの形式で入力してください');
define('MSG03','パスワード（確認）が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','256文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください');
define('MSG08','そのメールアドレスは既に登録されています');
define('MSG09','メールアドレスまたはパスワードが違います');
define('MSG10','郵便番号の形式が違います');
define('MSG11','電話番号の形式が違います');
define('MSG12','現在のパスワードが正しくありません');
define('MSG13','現在のパスワードと同じです');
define('MSG14','選択必須です');
define('MSG15','認証キーが違います');
define('MSG16','画像必須です');
define('SUC01','パスワードを変更しました');
define('SUC02','商品を登録しました');
define('SUC03','カートに商品を入れました');
define('SUC04','商品を購入しました');
define('SUC05','メールを送信しました');
define('SUC06','商品を編集しました');
define('SUC07','基本情報を変更しました');
define('SUC08','登録が完了しました');
define('SUC09','ログインしました');

//=====================================
// バリデーション関数
//=====================================
    //エラーメッセージ格納用の配列
    $err_msg = array();

    //バリデーション関数（未入力チェック）
    function validRequired($str, $key){
        if(empty($str)){
            global $err_msg;
            $err_msg[$key] = MSG01;
        }
    }
    //バリデーション関数（Email形式チェック）
    function validEmail($str, $key){
        if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
            global $err_msg;
            $err_msg[$key] = MSG02;
        }
    }
    //バリデーション関数（Email重複チェック）
    function validEmailDup($email){
        global $err_msg;
        try {
            //DBに接続
            $dbh = dbConnect();
            //SQL文作成
            $sql = 'SELECT count(*) FROM users WHERE email = :email';
            $data = array(':email' => $email);
            //クエリ実行
            $stmt = queryPost($dbh, $sql, $data);
            //クエリ結果
            //$stmtのデータを連想配列形式で取り出す
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            //array_shift関数で配列の先頭の値を取り出す
            if(!empty(array_shift($result))){
                $err_msg['email'] = MSG08;
            }
        } catch (Exception $e) {
            error_log('エラー発生:' .$e->getMessage());
            $error_log['common'] = MSG07;
        }
    }
    //バリデーション関数（同値チェック）
    function validMatch($str1, $str2, $key){
        if($str1 !== $str2){
            global $err_msg;
            $err_msg[$key] = MSG03;
        }
    }
    //バリデーション関数（最小文字数チェック）
    function validMinLen($str, $key, $min = 6){
        if(mb_strlen($str) < $min){
            global $err_msg;
            $err_msg[$key] = MSG05;
        }
    }
    //バリデーション関数（最大文字数チェック）
    function validMaxLen($str, $key, $max = 256){
        if(mb_strlen($str) > $max){
            global $err_msg;
            $err_msg[$key] = MSG06;
        }
    }
    //バリデーションチェック（固定長チェック）
    function validLength($str, $key, $length = 8){
        if(mb_strlen($str) !== $length){
            global $err_msg;
            $err_msg[$key] = MSG15;
        }
    }
    //バリデーション関数（半角チェック）
    function validHalf($str, $key){
        if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
            global $err_msg;
            $err_msg[$key] = MSG04;
        }
    }
    //バリデーション関数（郵便番号形式チェック）
    function validZip($str, $key){
        if(!preg_match("/^\d{7}$/", $str)){
            global $err_msg;
            $err_msg[$key] = MSG10;
        }
    }
    //バリデーション関数（電話番号形式チェック）
    function validTel($str, $key){
        if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/", $str)){
            global $err_msg;
            $err_msg[$key] = MSG11;
        }
    }
    //バリデーション関数（半角数字チェック）
    function validNumber($str, $key){
        if(!preg_match("/^[0-9]+$/", $str)){
            global $err_msg;
            $err_msg[$key] = MSG04;
        }
    }
    //バリデーション関数（パスワードチェック）
    function validPass($str, $key){
        //半角英数字チェック
        validHalf($str, $key);
        //最大文字数チェック
        validMaxLen($str, $key);
        //最小文字数チェック
        validMinLen($str, $key);
    }
    //バリデーションチェック（セレクトボックス）
    function validSelect($str, $key){
        if(!preg_match("/^[1-9]+$/", $str)){
            global $err_msg;
            $err_msg[$key] = MSG14;
        }
    }
    //バリデーションチェック（商品画像チェック）
    function validPicture($str, $key){
        if(empty($str)){
            global $err_msg;
            $err_msg[$key] = MSG16;
        }
    }
    //エラーメッセージ表示
    function getErrMsg($key){
        global $err_msg;
        if(!empty($err_msg[$key])){
            return $err_msg[$key];
        }
    }

//=====================================
// ログイン認証
//=====================================
function isLogin(){
    if(!empty($_SESSION['login_date'])){
        debug('ログイン済みユーザーです。');

        //現在日時が最終ログイン日時と有効期限を超えていた場合（time()が有効期限のタイムスタンプを超えている）
        if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
            debug('ログイン有効期限オーバーです。');

            //セッションを削除
            session_destroy();
            return false;
        }else{
            debug('ログイン有効期限内です。');
            return true;
        }
    }else{
        debug('未ログインユーザーです。');
        return false;
    }
}
//=====================================
// データベース
//=====================================
   //DB接続関数
   function dbConnect(){
    //DBへの接続準備
    $dsn = 'mysql:dbname=sabai;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        //SQL実行失敗時にエラーコードのみを設定
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        //デフォルトフェッチモードを連想配列形式に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        //バッファードクエリを使うに設定
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    //PDOオブジェクトを生成し、DBに接続
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
    }
    //SQL実行関数
    function queryPost($dbh, $sql, $data){
        //クエリの作成
        $stmt = $dbh->prepare($sql);
        //プレースフォルダに値をセットし、SQL文を実行
        $stmt->execute($data);
        debug('データの中身:'.print_r($stmt, true));
        return $stmt;
    }
    //ユーザー情報の取得
    function getUser($u_id){
        debug('ユーザー情報を取得します。');
        //例外処理
        try {
            //DB接続
            $dbh = dbConnect();
            //SQL文の作成
            $sql = 'SELECT * from users WHERE id = :u_id';
            $data = array(':u_id' => $u_id);
            //SQL文の実行
            $stmt = queryPost($dbh, $sql, $data);

            //クエリ成功の場合
            if($stmt){
                debug('クエリ成功');
            }else{
                debug('クエリ失敗');
            }
        } catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
        //クエリ結果のデータを返却
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    //商品情報の取得
    function getProduct($u_id, $p_id){
        debug('商品情報を取得します。');
        debug('ユーザーID:'.$u_id);
        debug('商品ID:'.$p_id);
        //例外処理
        try {
            //DB接続
            $dbh = dbConnect();
            //SQL文の作成
            $sql = 'SELECT * FROM product WHERE id = :p_id AND user_id = :u_id AND delete_flg = 0';
            $data = array(':p_id' => $p_id, ':u_id' => $u_id);
            //SQL文の実行
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                debug('クエリ成功');
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                debug('クエリ失敗');
                return false;            
            }

        } catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }

    }
    //商品情報を取得します
    function getProductList($color, $size, $sort, $currentMinNum = 1,$span = 20){
        debug('商品情報を取得します');
    //例外処理
    try {
        //DBに接続
        $dbh = dbConnect();
        //件数用のSQL文作成
        $sql = 'SELECT id FROM product';
        //WHERE句の場合分け
        $hasWhere = false;
        //カラーSQL
        if(!empty($color)){
            $sql .= ($hasWhere)? " AND " : " WHERE ";
            $sql .= ' color = '.$color;
            $hasWhere = true;
        }
        //サイズSQL
        if(!empty($size)){
            $sql .= ($hasWhere)? " AND " : " WHERE ";
            $sql .= ' size = '.$size;
            $hasWhere = true;
        }
        //ソートSQL
        if(!empty($sort)){
            debug('ソートの中身:'.print_r($sort,true));
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY price ASC ';
                break;
                case 2:
                    $sql .= ' ORDER BY price DESC ';
                break;
            }
        }
        $data = array();
        //クエリ実行
        $stmt = queryPost($dbh,$sql,$data);
        //総レコード数
        $rst['total'] = $stmt->rowCount();//idのレコード数を返す
        //総ページ数
        $rst['total_page'] = ceil($rst['total']/$span);
        if(!$stmt){
            return false;
        }

        //ページング用のSQL文作成
        $sql = 'SELECT * FROM product';
        //WHERE句の場合分け
        $hasWhere = false;
        //カラーSQL
        if(!empty($color)){
            $sql .= ($hasWhere)? " AND " : " WHERE ";
            $sql .= ' color = '.$color;
            $hasWhere = true;
        }
        //サイズSQL
        if(!empty($size)){
            $sql .= ($hasWhere)? " AND " : " WHERE ";
            $sql .= ' size = '.$size;
            $hasWhere = true;
        }
        //ソートSQL
        if(!empty($sort)){
            debug('ソートの中身:'.print_r($sort,true));
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY price ASC ';
                break;
                case 2:
                    $sql .= ' ORDER BY price DESC ';
                break;
            }
        }
        //OFFSETは何件目からデータを取得するか
        //LIMITで1ページあたりに表示する件数
        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array();
        debug('SQL:'.$sql);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            //クエリ結果のデータを全レコードに格納
            //指定された商品ページにあるデータを取ってくる
            $rst['data'] = $stmt->fetchAll();
            return $rst;
            /*
            $rst = [
                'total'=> $stmt->rowCount(),
                'total_page' => ceil($rst['total']/$span),
                'data' => $stmt->fetchAll(),
            ]
            */
        }else{
            return false;
        }
    } catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }

    }
    //商品情報の取得
    function getProductOne($p_id){
        //例外処理
        try {
            //DBへ接続
            $dbh = dbConnect();
            //SQL文作成
            $sql = 'SELECT * FROM product WHERE id = :p_id AND delete_flg = 0';
            $data = array(':p_id' => $p_id);
            //クエリ実行
            $stmt = queryPost($dbh,$sql,$data);
            //クエリ結果のデータを1レコード返却
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('エラー発生:'.$e->getMessage());
        }
    }
    //カードに入っている商品情報を取得
    function getProductCart($u_id){
        //例外処理
        try {
            //DBに接続
            $dbh = dbConnect();
            //SQL文の作成
            $sql = 'SELECT s.user_id, s.product_id, p.pic1, p.name, p.price FROM shopping_cart AS s LEFT JOIN product AS p ON p.id = s.product_id WHERE s.user_id = :u_id';
            $data = array(':u_id' => $u_id);
            //SQL文の実行
            $stmt = queryPost($dbh,$sql,$data);

            if($stmt){
                debug('クエリ成功');
                return $stmt->fetchAll();
            }else{
                debug('クエリ失敗');
            }
        } catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }
    //商品IDの取得
    function getProductId($u_id){
        //例外処理
        try {
            //DBへ接続
            $dbh = dbConnect();
            //SQL文作成
            $sql = 'SELECT product_id FROM shopping_cart WHERE user_id = :u_id';
            $data = array(':u_id' => $u_id);
            //SQL文の実行
            $stmt = queryPost($dbh,$sql,$data);

            if($stmt){
                debug('クエリ成功');
                $rst =  $stmt->fetchAll(PDO::FETCH_COLUMN);
                debug('クエリ結果の中身:'.print_r($rst,true));
                return $rst;
            }else{
                debug('クエリ失敗');
                return false;
            }

        } catch (Exception $e) {
            error_log('エラー発生:'.$e->getMessage());
        }
    }
    //注文履歴情報を取得
    function getOrderHistory($u_id){
        //例外処理
        try {
            //DBに接続
            $dbh = dbConnect();
            //SQL文の作成
            $sql = 'SELECT o.product_id, p.pic1, p.name, p.price FROM order_history as o LEFT JOIN product as p ON o.product_id = p.id WHERE buy_user = :u_id';
            $data = array(':u_id'=>$u_id);
            //SQL文の実行
            $stmt = queryPost($dbh,$sql,$data);

            if($stmt){
                debug('クエリ成功');
                $rst = $stmt->fetchAll();
                debug('クエリ結果の中身:'.print_r($rst,true));
                return $rst;
            }else{
                debug('クエリ失敗');
            }

        } catch (Exception $e){
            error_log('エラー発生；'.$e->getMessage());
        }
    }
    //お気に入り情報があるか
    function isLike($u_id,$p_id){
        debug('お気に入り情報があるか確認します。');
        debug('ユーザーID:'.$u_id);
        debug('商品ID:'.$p_id);
        //例外処理
        try {
            //DBへ接続
            $dbh = dbConnect();
            //SQL作成
            $sql = 'SELECT * FROM `like` WHERE product_id = :p_id AND user_id = :u_id';
            $data = array(':p_id' => $p_id,':u_id' => $u_id);
            //クエリ実行
            $stmt = queryPost($dbh,$sql,$data);
            
            if($stmt->rowCount()){
                debug('お気に入りがあります。');
                return true;
            }else{
                debug('お気に入りはありません。');
                return false;
            }

        } catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }
    //自分のお気に入り情報を取得
    function getMyLike($u_id){
        //例外処理
        try {
            //DBへ接続
            $dbh = dbConnect();
            //SQL文作成
            $sql = 'SELECT p.id, p.pic1, p.name, p.price FROM product AS p RIGHT JOIN `like` AS l ON p.id = l.product_id WHERE l.user_id = :u_id';
            $data = array(':u_id' => $u_id);
            //クエリ実行
            $stmt = queryPost($dbh,$sql,$data);
            //クエリ結果の全データを返却
            return $stmt->fetchAll();

        } catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }
//=====================================
// メール送信
//=====================================
    function sendMail($from, $to, $subject, $comment){
        if(!empty($to) && @!empty($subject) && !empty($comment)){
        //文字化けしないように設定
        mb_language("Japanese");//現在使っている言語を設定
        mb_internal_encoding("UTF-8");//内部の日本語をどのようにエンコーディングするか
        
        //メールを送信
        $result = mb_send_mail($to, $subject, $comment, "From:".$from);
        //送信結果を判定
        if($result){
            debug('メールを送信しました');
        }else{
            debug('メールの送信に失敗しました');
        }
        }
    }
//=====================================
// その他
//=====================================
    //サニタイズ
    function sanitize($str){
        return htmlspecialchars($str, ENT_QUOTES);
    }
    //フォーム入力保持
    function getFormData($str){
        global $dbFormData;
        //ユーザーデータがある場合
        if(!empty($dbFormData)){
            //フォームのエラーがある場合
            if(!empty($err_msg[$str])){
                //POSTにデータがある場合
                if(isset($_POST[$str])){
                    return $_POST[$str];
                }else{
                //POSTにデータがない場合
                    return $dbFormData[$str];
                }
            }else{
                //POSTにデータがあり、 DB情報と違う場合
                if(isset($_POST[$str]) && $_POST[$str] !== $dbFormData[$str]){
                    return $_POST[$str];
                }else{
                //そもそも変更していない
                    return $dbFormData[$str];
                }
            }      
            //ユーザーデータがない場合
        }else{
            if(isset($_POST[$str])){
            return $_POST[$str];
            }
        }
    }
    //sessionを1回だけ取得できる
    function getSessionFlash($key){
        if(!empty($_SESSION[$key])){
            $data = $_SESSION[$key];
            $_SESSION[$key] = '';
            return $data;
        }
    }
    //画像処理
    function uploadImg($file, $key){
        debug('画像アップロード処理開始');
        debug('FILE情報:'.print_r($file,true));

        if(isset($file['error']) && is_int($file['error'])){//is_intを使って整数であるかを確認する
            try {
            //アップロードファイルのバリデーションチェック
            switch($file['error']){
                case UPLOAD_ERR_OK: //OK
                    break;
                case UPLOAD_ERR_NO_FILE: //ファイルが未選択の場合
                    throw new RuntimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE://php.ini定義の最大サイズを超過した場合
                case UPLOAD_ERR_FORM_SIZE://フォーム定義の最大サイズを超過した場合
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                default://その他の場合
                    throw new RuntimeException('その他のエラーが発生しました');
            }
            //アップロードファイルが画像ファイルかどうかチェック
            //exif_imagetype()は画像形式を判定する関数
            //@をつけることでファイルが画像でないときにWarningではなくfalseを返す
            $type = @exif_imagetype($file['tmp_name']);//返り値は整数
            debug('タイプの中身:'.print_r($type, true));
            //画像ファイルであれば、1,2,3のいずれかの値が返ってくる
            if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)){//調べたい値が配列の中に含まれているか調べる
                throw new RuntimeException('画像形式が未対応です');
            }
            //ハッシュ化することで画像パスの重複を防ぐ
            //image_type_to_extension関数はファイルの拡張子を取得する
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            
            //move_uploaded_file()はサーバーの一時フォルダに保存されたファイルを保存先に移動する
            if(!move_uploaded_file($file['tmp_name'], $path)){
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }
            //誰にでも画像が表示されるように画像のファイルを変更する
            //chmod()はファイルやディレクトリーの権限を変更する関数
            chmod($path, 0644);//0600だと他人には画像が表示できないため

            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス:'.$path);
            return $path;

            } catch (RuntimeException $e) {
                //try内で定義したRuntimeExceptionを拾う
                debug($e->getMessage());
                global $err_msg;
                $err_msg[$key] = $e->getMessage();
            }
        
        }
    }
    //認証キーの生成
    function makeRandKey($length = 8){
        static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; ++$i){
            $str .= $chars[mt_rand(0, 61)];
        }
        return $str;
    }
    //ページネーション
    //$currentPageNum:現在のページ数
    //$totalPageNum:総ページ数
    //$link:検索用GETパラメータリンク
    //$pageColNum: 表示するページ数値
    function pagenation($currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
        //現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンクを4個出す
        if($currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
            $minPageNum = $currentPageNum - 4;
            $maxPageNum = $currentPageNum;
        //現在のページが、総ページ数の1ページ前なら、左にリンク3個、右に1個出す
        }elseif($currentPageNum == ($totalPageNum - 1) && $totalPageNum > $pageColNum){
            $minPageNum = $currentPageNum - 3;
            $maxPageNum = $currentPageNum + 1;
        //現在のページが２の場合は左にリンク1個、右にリンク3個出す
        }elseif($currentPageNum == 2 && $totalPageNum >= $pageColNum){
            $minPageNum = $currentPageNum - 1;
            $maxPageNum = $currentPageNum + 3;
        //現在のページが1の場合は左に何も出さない。右に5個出す
        }elseif($currentPageNum == 1 && $totalPageNum > $pageColNum){
            $minPageNum = $currentPageNum;
            $maxPageNum = 5;
        //総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを1に設定
        }elseif($totalPageNum <= $pageColNum){
            $minPageNum = 1;
            $maxPageNum = $totalPageNum;
        //それ以外は左に2個出す
        }else{
            $minPageNum = $currentPageNum - 2;
            $maxPageNum = $currentPageNum + 2;
        }

        echo '<div class="pagenation">';
            echo '<ul class="pagenation-list">';
                if($currentPageNum != 1){
                    echo '<li class="list-item list-item--left"><a href="?p=1'.$link.'">&lt;</a></li>';
                }
                for($i = $minPageNum; $i <= $maxPageNum; $i++){
                    echo '<li class="list-item"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
                }
                if($currentPageNum != $maxPageNum && $maxPageNum > 1){
                    echo '<li class="list-item list-item--right"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
                }
            echo '</ul>';
        echo '</div>';

    }

?>