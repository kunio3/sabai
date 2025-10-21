<?php
    //================================
    // 画面処理
    //================================
    //ユーザーID
    $u_id = $_SESSION['user_id'];
    debug('ユーザーID'.$u_id);
    //ユーザー情報を取得
    $userData = getUser($u_id);
    debug('ユーザー情報'.print_r($userData,true));
    
    //管理者フラグ
    $admin_flg = false;

    if(isset($userData['id']) && (int)$userData['user'] === 100){
        $admin_flg = true;
    }
?>
<section id="sidebar">
        <a href="index.php">アイテム一覧</a>
        <a href="mypage.php">会員登録情報</a>
        <a href="profEdit.php">基本情報変更</a>
        <a href="passEdit.php">パスワード変更</a>
        <a href="like.php">お気に入り</a>
        <a href="orderHistory.php">注文履歴</a>
    <?php if($admin_flg): ?>
        <a href="admin.php">商品登録</a>
    <?php else: ?>
        <a href="withdraw.php">退会</a>
    <?php endif; ?>
</section>