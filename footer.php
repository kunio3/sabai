        <footer id="footer">
            <div class="site-width">
                <div class="footer-nav" >
                    <ul>
                        <li><!--<a href="#">利用規約</a>--></li>
                        <li><!--<a href="#">プライバシーポリシー</a>--></li>
                        <li><!--<a href="#">特定商取引法に基づく表示</a>--></li>
                    </ul>
                </div>
             </div>
            ©︎ 2025 Sabai
        </footer>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script>
            //メッセージ表示
            var $jsShowMsg = $('#js-show-msg');
            var msg = $jsShowMsg.text();
            if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
                $jsShowMsg.slideToggle('slow');
                setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 5000);
            }
            //画像ライブプレビュー機能
            var $dropArea = $('.area-drop');
            var $fileInput = $('.input-file');
            $dropArea.on('dragover', function(e){
                e.stopPropagation();//イベントをキャンセル
                e.preventDefault();
                $(this).css('border', '3px #ccc dashed');//.area-dropのDOMを取得
            });
            $dropArea.on('dragleave', function(e){//画像を離したとき
                e.stopPropagation();
                e.preventDefault();
                $(this).css('border', 'none');
            });
            $fileInput.on('change', function(e){//画像の情報が入ったとき
                $dropArea.css('border', 'none');
                var file = this.files[0];//thisは<input type="file">要素で、選択したファイルの情報が入っているオブジェクト
                $img = $(this).siblings('.img-prev');
                //ファイルの読み込み
                fileReader = new FileReader();
                //ファイルの読み込みが完了した後の処理
                fileReader.onload = function(event){
                    $img.attr('src', event.target.result).show();//event.target.resultは読み込んだ画像ファイルのデータ
                };

                //画像ファイルをデータURLに変更
                fileReader.readAsDataURL(file);

            });
            //お気に入り登録・削除
            var $like = $('.js-like-click') || null;
            var likeProductId = $like.data('productid') || null;
            if(likeProductId !== undefined && likeProductId !== null){
                $like.on('click', function(){
                    var $this = $(this);
                    $.ajax({
                        type: "POST",
                        url: "ajaxLike.php",
                        data: { productId: likeProductId}
                    }).done(function(data){
                        console.log('Ajax Success');
                        //クラス属性をつけ外しする
                        //クリックされた時につけるクラス
                        $this.toggleClass('fa-regular fa-solid');
                        $this.toggleClass('active');
                    }).fail(function(msg){
                        console.log('Ajax Error');
                    });
                });
            }else{
                console.log('エラーが発生しました。');
            }
        </script>
    </body>
</html>