<?php
     //共通関数・関数ファイルの読み込み
     require('function.php');

     debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
     debug('「「 Ajax「「');
     debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
     debugLogStart();

    //================================
    // 画面処理
    //================================
     //ユーザーID
     $u_id = $_SESSION['user_id'];
     //データベースからお気に入りのデータを取得
     $likeData = getMyLike($u_id);
     debug('データの中身:'.print_r($likeData,true));
    //POST送信があり、ユーザーIDがあり、ログインしている場合
    if(isset($_POST['productId']) && isset($_SESSION['user_id']) && isLogin()){
        debug('POST送信があります');
        $p_id = $_POST['productId'];
        debug('商品ID:'.$p_id);
        //例外処理
        try {
            //DBへ接続
            $dbh = dbConnect();
            //レコードがあるか検索
            $sql = 'SELECT * FROM `like` WHERE product_id = :p_id AND user_id = :u_id';
            $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id']);
            //クエリ実行
            $stmt = queryPost($dbh, $sql, $data);
            $resultCount = $stmt->rowCount();
            debug($resultCount);
            //レコードが一件でもある場合
            if(!empty($resultCount)){
                //レコードを削除する
                $sql = 'DELETE FROM `like` WHERE product_id = :p_id AND user_id = :u_id';
                $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id']);
                //クエリ実行
                $stmt = queryPost($dbh,$sql,$data);
            }else{
                //レコードを挿入する
                $sql = 'INSERT INTO `like`(user_id, product_id, created_at) VALUES(:u_id, :p_id, :date)';
                $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
                debug('データの中身:'.print_r($data,true));
                //クエリ実行
                $stmt = queryPost($dbh, $sql, $data);

                if($stmt){
                    debug('クエリ成功');
                }else{
                    debug('クエリ失敗');
                }
            }

            
        } catch (Exception $e) {
            error_log('エラー発生:'.$e->getMessage());
        }

    }

debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

?>