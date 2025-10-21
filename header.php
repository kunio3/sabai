        <header id="header">
            <div class="site-width">
                <h1>
                    <a href="index.php">Sabai</a>
                </h1>
                <nav id="top-nav">
                    <ul>
                        <?php if(empty($_SESSION['user_id'])): ?>
                            <li><a href="signup.php" class="btn btn-signup">会員登録</a></li>
                            <li><a href="login.php">ログイン</a></li>
                        <?php else: ?>
                            <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a></li>
                            <li><a href="mypage.php" class="btn btn-mypage">マイページ</a></li>
                            <li><a href="logout.php">ログアウト</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </header>