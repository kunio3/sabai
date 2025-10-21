<?php
    //=====================================
    // 画面処理
    //=====================================
    //ユーザーID
    $u_id = $_SESSION['user_id'];
    debug('ユーザーID:'.$u_id);
    //ユーザー情報の取得
    $userData = getUser($u_id);
    debug('ユーザー情報の中身:'.print_r($userData,true));

    debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<div class="page-header">
    <h1 class="page-title">
    <?php if(!empty($userData['username'])): ?>
        <?php echo sanitize($userData['username']); ?>様の登録情報</h1>
    <?php else: ?>
        <?php echo 'ゲスト様の登録情報'; ?></h1>
    <?php endif; ?>
</div>