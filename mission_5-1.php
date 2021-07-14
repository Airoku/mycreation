<!DOCTYPE html>
<html>
    <head>
	    <meta charset="utf-8">
	    <title>mission_5-1</title>
	    <link rel="stylesheet" href="mission_3-1.css">
    </head>
    <body>
        <?php
            require_once("openPDO.php");
            $date=date("Y/m/d H:i:s");
            /* データを配列に代入 */
            
            /* 開くファイルの指定 */
            
            $missPassDel="";
            /* 新規投稿　編集フォーム */
            if(isset($_POST["btn-submit"])){
                $name=$_POST["name"];
                $comment=$_POST["comment"];
                $password=$_POST["password"];
                /* データがはいっているかの確認 */
                if($name==null || $comment==null || $password==null){
                    echo "入力は必須です やり直してください";
                }
                /* データを編集する */
                elseif($_POST["data"]){
                    $id=$_POST["data"];
                    echo $id;
                    $sql = 'UPDATE board SET name=:name,comment=:comment,password=:password WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                    $stmt->execute();
                }
                /* 新規投稿 */
                else{
                    /* データの挿入*/
                    $insert = $pdo->prepare("INSERT INTO board (name, comment,password) VALUES (:name, :comment, :password)");
                    /* PDOStatement に対して実行 */
                    $insert->bindParam(':name', $name, PDO::PARAM_STR);
                    $insert->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $insert->bindParam(':password', $password, PDO::PARAM_STR);
                    /* 挿入するデータ*/
                    /* 実行 */ 
                    $insert->execute();
                }
            }
            /* 削除フォーム */
            elseif(isset($_POST["btn-delete"])){
                $delPassword=$_POST["del_password"];
                $id = $_POST["del-num"];
                $sql = 'SELECT id FROM board';
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $results=$stmt->fetchAll();
                $i=0;
                $delConfirm =0;
                foreach($results as $result){
                    if($result[0]==$id){
                        $i++;
                    }       
                }
                if($id==null || $delPassword==null){
                    $delConfirm++;
                }
                elseif($i==1){
                    $sql = 'DELETE FROM board WHERE id=:id AND password=:password';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':password', $delPassword, PDO::PARAM_STR);
                    $stmt->execute();
                }
                else{
                    $delConfirm += 2;
                }
            }
            /* 編集データの取得 */
            if(isset($_POST["btn-edit"]) && $_POST["number"]!=null && $_POST["edit_password"]!=null){
                $number=$_POST["number"];
                $editPassword=$_POST["edit_password"];
                /* データの選択 */
                $sql = 'SELECT * FROM board WHERE id=:id AND password=:password';
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $number, PDO::PARAM_INT);
                $stmt->bindParam(':password', $editPassword, PDO::PARAM_STR);
                $stmt->execute();
                
                /* すべての結果を配列で返す 配列は左から順番またはカラム名による連想配列*/
                $results = $stmt->fetchAll();
                
                /*データベースのデータを表示する*/
                foreach ($results as $result){
                    $editName = $result[1];
                    $editComment = $result[2];
                    $editNumber = $result[0];
                }
            }
        ?>
        <!-- 以下表示フォーム -->
        <form class="contact-form" method="post" action="">
            <h1>簡易掲示板</h1>
            <div class="submit-form">
                <h2>送信フォーム</h2>
                <p>氏名</p>
                <input type="text" name="name" value="<?php if(isset($_POST["btn-edit"])){echo $editName;} ?>">
                <p>コメント</p>
                <textarea type="text" name="comment"><?php if(isset($_POST["btn-edit"])){echo $editComment;} ?></textarea>
                <input type="hidden" name="data" value="<?php if(isset($_POST["btn-edit"])){echo $editNumber;} ?>">
                <?php 
                    if(isset($_POST["btn-edit"])){
                        echo "<p>パスワードを再設定</p>";
                    }else{
                        echo "<p>パスワードを設定</p>";
                    }
                ?>
                <input type="password" name="password">
                <input type="submit" name="btn-submit">
            </div>
            <div class="delete-form">
                <h2>削除フォーム</h2>
                <?php if($delConfirm==1): ?>
                        <p style="color:red;">入力を確認してください</p>
                    <?php elseif($delConfirm==2):?>
                        <p style="color:red;">正しいIDを入力してください</p>
                    <?php endif ?>
                <p>削除したいデータ番号</p>
                <input type="number" name="del-num">
                <p>削除するにはパスワードを入力してください</p>
                <?php 
                    if($missPassDel!=null){
                        echo $missPassDel."<br>";
                        $missPassDel=0;
                    }
                ?>
                <input type="password" name="del_password">
                <input type="submit" name="btn-delete" value="削除">
            </div>
            <div class="edit-form">
                <h2>編集フォーム</h2>
                <p>編集したいデータ番号</p>
                <input type="text" name="number">
                <p>編集するにはパスワードを入力してください</p>
                <input type="password" name="edit_password">
                <input type="submit" name="btn-edit" value="編集">
            </div>
        </form>
        <!-- データを表示 -->
        <?php     
            /* データの選択 */
            $sql = 'SELECT * FROM board';
            $stmt = $pdo->query($sql);
            /* すべての結果を配列で返す 配列は左から順番またはカラム名による連想配列*/
            $results = $stmt->fetchAll();
            /*データベースのデータを表示する*/
            foreach ($results as $result){   
                echo $result[0].',';
                echo $result[1].',';
                echo $result[2].',';
                echo $result[3].'<br>';
                echo "<hr>";
            }
        ?>
    </body>
</html>
           