<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>練習用</title>
</head>
    <body>

    <?php
    
    #DB接続設定
    $dsn = '*****';
    $user = '*****';
    $password = '*****';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    #テーブル作る/（条件：作成してなければ）/テーブル名
    #id      　自動で登録されているナンバリング。 データ型/連続で番号を振れる/キーの種類（主キー）
    #name    　名前を入れる。文字列、半角英数で32文字。
    #comment 　コメントを入れる。文字列、長めの文章も入る。
    #date      日付入れる。
    #password　パスワードを入れる。
    $sql = "CREATE TABLE IF NOT EXISTS MPg5"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT NOT NULL,"
    . "date TEXT NOT NULL,"
    . "password TEXT NOT NULL"
    .");";
    
    #変数格納
    #日付を取得
    $date = date("Y/m/d H時i分s秒");
    #名前フォームの内容を＄ｎに格納
    $n = @$_POST["name"];
    #コメントフォームの内容を＄strに格納
    $str = @$_POST["comment"];
    #最後の投稿番号を取得
    $num = @end(file($filename))+1;
    #投稿フォームの編集番号を取得
    $hedit = @$_POST["hiddenedit"];
    #投稿番号格納
    $enum = @$_POST["enum"];
    #新規投稿時のパスワード
    $passinput = @$_POST["passinput"];
    #削除時のパスワード
    $passdelete = @$_POST["passdelete"];
    #編集時のパスワード
    $passedit = @$_POST["passedit"];

    #入力フォーム
    if(!empty($_POST["submit"])){
        #二つのフォームが存在しているか
        if(isset($_POST["name"]) && isset($_POST["comment"])){
            #名前かつコメントに文字が入力されているか
            if($_POST["name"] != "" && $_POST["comment"] != ""){
                #編集番号が添えられていない場合
                if($hedit==""){
                    #tbtest(項目)にデータを入力する準備
                    $sql = $pdo -> prepare("INSERT INTO MPg5 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
                    #PDO::PARAM_STR:SQL CHAR, VARCHAR, または他の文字列データ型を表す。
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                    $name = $n;
                    $comment = $str;
                    $password = $passinput;
                    $sql -> execute();
                }else{
                    #編集番号が添えられている場合
                    #bindParamの引数（:nameなど）はどんな名前のカラムを設定したかで変える必要がある。
                    $id = $hedit; //変更する投稿番号
                    $name = $n;
                    $comment = $str;
                    $password = $passinput;
                    $sql = 'UPDATE MPg5 SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }else{
                echo "すべて入力してください<br>";
            }
        }
    }
    
    #投稿削除
    if(!empty($_POST["delete"])){
        #フォームが存在しているか
        if(isset($_POST["dnum"]) && isset($_POST["passdelete"])){
            #入力されているか
            if($_POST["dnum"] != "" && $_POST["passdelete"] != ""){
                #テーブルからデータを配列に格納
                $sql = 'SELECT * FROM MPg5';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                #投稿番号格納
                $id = $_POST["dnum"];
                foreach ($results as $row){
                    #投稿番号が一致している
                    if($row['id'] == $id){
                        #パスワードが一致している
                        if($row['password'] == $passdelete){
                            #whereは必須
                            $sql = 'delete from MPg5 where id=:id';
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt->execute();
                        }else{
                            echo "パスワードが間違っています。<br>";
                        }
                    }
                }
            }else{
                echo "数字またはパスワードが入力されていません。<br>";
            }
        }   
    }

    #編集選択
    if(!empty($_POST["edit"])){
        #番号とパスワード欄が存在するか
        if(isset($_POST["enum"]) && isset($_POST["passedit"])){
            #番号とパスワードが入力されているか
            if($_POST["enum"] != "" && $_POST["passedit"]!=""){
                #投稿番号格納
                $id = $enum;
                #テーブルからデータを配列に格納
                $sql = 'SELECT * FROM MPg5';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    #投稿番号が一致している
                    if($row['id'] == $id){
                        #パスワードが一致している
                        if($row['password'] == $passedit){
                            $heditnum = $row['id'];
                            $text_name = $row['name'];
                            $text_comment = $row['comment'];
                        }else{
                            echo "パスワードが間違っています。<br>";
                            $hedit = "";
                            $enum = "";
                        }
                    }
                }
            }else{
                echo "数字またはパスワードが入力されていません。";
            }
        }
    }
    
                        $sql = 'SELECT * FROM MPg5';
                        $stmt = $pdo->query($sql);
                        $results = $stmt->fetchAll();
                        #ブラウザ上にテーブルの内容を表示
                        foreach ($results as $row){
                            //$rowの中にはテーブルのカラム名が入る
                            echo $row['id'].':'.$row['date'].'<br>';
                            echo $row['name'].'<br>';
                            echo $row['comment'].'<br>';
                        echo "<hr>";
                        }

    ?>

        <hr>

        新規投稿フォーム
        <form method='POST' action=''>
        <input type="hidden" name="hiddenedit" value='<?php echo @htmlspecialchars($enum); ?>'>
        名前：<input type='text' name='name' placeholder='名前' value='<?php echo @htmlspecialchars($text_name); ?>'><br>
        コメント：<input type='text' name='comment' placeholder='コメント' value='<?php echo @htmlspecialchars($text_comment); ?>' ><br>
        パスワード：<input type='text' name='passinput' placeholder='パスワード' autocomplete="off"><br>
        <input type='submit' name='submit' value='送信'>
        </form>
        
        <br>
        削除番号指定用フォーム
        <form method='POST' action=''>
        <input type='number' name='dnum' min=0 placeholder='投稿番号'><br>
        パスワード：<input type='text' name='passdelete' placeholder='パスワード' autocomplete="off"><br>
        <input type='submit' name='delete' value=削除><br>
        
        <br>
        <form method='POST' action=''>
        編集番号指定用フォーム
        <input type='number' name='enum' min=0 placeholder='投稿番号'><br>
        パスワード：<input type='text' name='passedit' placeholder='パスワード' autocomplete="off"><br>
        <input type='submit' name='edit' value=編集>
        </form>
        
        <hr>

    </body>
</html>
