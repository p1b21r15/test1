<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
<body>
    <?php
   
    $form_editnum = 0;
    $form_name = null;
    $form_coment = null;
    
    
    $edit_num = $_POST["edit_num"];
    $pass_post = $_POST["pass_post"];
    $name = $_POST["name"];
    $coment = $_POST["coment"];
    $delete = $_POST["delete"];
    $edit = $_POST["edit"];    
        
// DB接続設定
    $dsn = 'mysql:dbname=データベース名;host=サーバー名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
//テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS tbtest"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "coment TEXT,"
    . "created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,"
    . "updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,"
    . "id_pass char(8)"
    .");";
    $stmt = $pdo->query($sql);
    
    
        
    
    //ボタンを押す前後で分岐
        $value = $_POST["submit"];
        
        //押されたボタンで条件分岐
        switch( $value ){
            
            //「送信」が押された場合
            case "insrt": 
                
                if($edit_num == 0){
                    
                    if($name != null && $coment != null && $pass_post != null){
                        
                //テーブルにレコード追加（インサート文）
                        $sql = $pdo -> prepare("INSERT INTO tbtest (name, coment, id_pass) VALUES (:name, :coment, :id_pass)");
                        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                        $sql -> bindParam(':coment', $coment, PDO::PARAM_STR);
                        $sql -> bindParam(':id_pass', $pass_post, PDO::PARAM_STR);
                        $sql -> execute();
                        
                    }elseif($name != null && $coment == null && $pass_post != null){
                   
                        echo "$name さん、コメントを入力してください<br>";
                        
                    }elseif($name == null && $pass_post != null){

                        echo "氏名を入力してください<br>";
                        
                    }elseif($pass_post == null){
                    
                        echo "新規パスワードを入力してください<br>";
                        
                    }
                    
                }elseif($edit_num != 0){
                
                    if($name != null && $coment != null){
                        
                //テーブルの更新
                        $sql = 'UPDATE tbtest SET name=:name,coment=:coment WHERE id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':coment', $coment, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $edit_num, PDO::PARAM_INT);
                        $stmt->execute();
                    
                        $form_editnum = 0;
                        
                    }elseif($name != null && $coment == null){
                   
                        echo "$name さん、コメントを入力してください<br>";
                        
                    }elseif($name == null){

                        echo "氏名を入力してください<br>";
                        
                    }
                    
                }
                
                
                
            //レコードの表示
            
                $sql = 'SELECT * FROM tbtest';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                $stmt->execute();
                foreach ($results as $row){
                    
            //$rowの中にはテーブルのカラム名が入る
            
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['coment'].',';
                    echo $row['created_at'].',';
                    echo $row['updated_at'].'<br>';
                    echo "<hr>";
                }    
                    
                break;
            
            //「削除」が押された場合    
            case "delete":
                if($delete != null && $pass_post != null){
                //パスワード認証 
                
                    $sql = 'SELECT * FROM tbtest';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    $stmt->execute();
                    foreach($results as $row){
                    
                        if($row['id'] == $delete && $row['id_pass'] == $pass_post){
                
                //テーブルのレコード削除    
                
                        $sql = 'delete from tbtest where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
                        $stmt->execute();
                    
                        }elseif($row['id'] == $delete && $row['id_pass'] != $pass_post){
                        
                            echo "パスワードが違います<br>";
                        
                        }
                    }
                    
                }elseif($delete != null && $pass_post == null){
                    echo "パスワードを入力してください<br>";
                }elseif($delete == null){
                    echo "削除番号を入力してください<br>";
                }
                
                //レコードの表示
            
                $sql = 'SELECT * FROM tbtest';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                $stmt->execute();
                foreach ($results as $row){
                    
            //$rowの中にはテーブルのカラム名が入る
            
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['coment'].',';
                    echo $row['created_at'].',';
                    echo $row['updated_at'].'<br>';
                    echo "<hr>";
                }
                
                break;
                
                        //「編集」が押された場合
            case "edit": 
                if($edit != null && $pass_post != null){
                    
                    //$resultの中にはテーブルのカラム名が入る
                    
                    $sql = 'SELECT * FROM tbtest';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    $stmt->execute();
                    
                    foreach($results as $value){
                            
                            //投稿番号と編集指定番号の比較
                        if($edit == $value['id'] && $pass_post == $value['id_pass']){
                            $form_editnum = $value['id'];
                            $form_name = $value['name'];//フォームの中に編集指定番号の名前代入
                            $form_coment = $value['coment'];//フォームの中に編集指定番号のコメント代入

                        }elseif($edit == $value['id'] && $pass_post != $value['id_pass']){
                            
                            echo "パスワードが違います<br>";
                            
                        }
                    }
                }elseif($edit != null && $pass_post == null){
                    echo "パスワードを入力してください<br>";
                
                }elseif($edit == null){
                    echo "編集するコメント番号を入力してください<br>";
                }
                
                //レコードの表示
            
                $sql = 'SELECT * FROM tbtest';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                $stmt->execute();
                foreach ($results as $row){
                    
            //$rowの中にはテーブルのカラム名が入る
            
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['coment'].',';
                    echo $row['created_at'].',';
                    echo $row['updated_at'].'<br>';
                    echo "<hr>";
                }
                
                break;
            
            //ボタンが押されていない場合
            case null:
                
                //レコードの表示
            
                $sql = 'SELECT * FROM tbtest';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                $stmt->execute();
                foreach ($results as $row){
                    
            //$rowの中にはテーブルのカラム名が入る
            
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['coment'].',';
                    echo $row['created_at'].',';
                    echo $row['updated_at'].'<br>';
                    echo "<hr>";
                }
                
                break;
            
        
        }
   
    if($form_editnum != 0){
        echo "編集モード(指定番号{$form_editnum})<br>";
    
    }else{
        $form_name = null;
        $form_coment = null;
    
    }
    
    ?>
    
    <form action="" method="post">
        <input type="password" min=4 max=8 name="pass_post" placeholder="パスワード（半角4~８文字）">
        <input type="hidden" min=1 name="edit_num" value="<?php echo $form_editnum; ?>">
        <input type="text" name="name" value="<?php if(isset($form_name)){
                                                        echo $form_name; } ?>" placeholder="氏名">
        <input type="text" name="coment" value="<?php if(isset($form_coment)){
                                                        echo $form_coment; } ?>" placeholder="コメント">
        <button type="submit" name="submit" value="insrt">送信</button>

        
        <input type="number" min=1 name="delete" placeholder="削除番号">
        <button type="submit" name="submit" value="delete">削除</button>
        
        
        <input type="number" min=1 name="edit" value="" placeholder="編集番号">
        <button type="submit" name="submit" value="edit">編集</button>
    </form>
    
    
   
    
   
    
</body>
</html>