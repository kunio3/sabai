<?php
//================================
// ログイン認証・自動ログアウト
//================================
// ログインしている場合
if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです。');

    //現在日時が最終ログイン日時＋有効期限を超えていた場合
    //現在時効であるtime()の方が進んでいるとタイムスタンプが大きくなるため、true
    if($_SESSION['login_date'] + $_SESSION['login_limit'] < time()){

    // セッションを削除（ログアウトする）
    session_destroy();
    //ログインページへ
    header("Location:login.php");

    }else{
        debug('ログイン有効期限内です。');
        //最終ログイン日時を現在日時に更新
        $_SESSION['login_date'] = time();

        //有効期限内のときに自動的にログイン画面に遷移する
        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            debug('マイページへ遷移します。');
            //マイページへ
            header("Location:mypage.php");
        }
    }

}else{
    debug('未ログインユーザーです。');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        header("Location:login.php");
    }
}
    








?>