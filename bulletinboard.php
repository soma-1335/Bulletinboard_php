<!DOCTYPE html>
<html lang ="ja">
<head>
    <meta charset="UTF-8">
    <title>m5-1</title>
    <style>
        .flex{
            display: flex;
        }

        .form{
            padding-right: 50px;
        }
    </style>
</head>

<body>
<?php 

    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    

    //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS snstest"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "time DATETIME,"
    . "password char(32)"
    .");";
    $stmt = $pdo->query($sql);
    $flag = 0;
    $name;
    $comment;


    //編集フォームに入力された
    if(isset($_POST['change']) && $_POST['password']){
        $password = $_POST['password'];
        $change = $_POST['change'];
        //テーブルからパスワードを取得
        $sql = 'SELECT * FROM snstest WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt ->bindParam(':id', $change, PDO::PARAM_STR);
        $stmt ->execute();
        $results = $stmt->fetch();
        
        if($results['password'] == $password){
            $flag = 1;
            $name = $results['name'];
            $comment = $results['comment'];
        }
    }

    //コメントを追記
    if(isset($_POST['name']) && isset($_POST['comment']) && isset($_POST['password'])){
        $name = $_POST['name'];
        $comment = $_POST['comment'];
        $password = $_POST['password'];
        $time = date("Y-m-d H:i:s");


        //編集モードの条件分岐
        if(empty($_POST["hidden"]))
        {
            //テーブルに値を追加
            $sql = "INSERT INTO snstest (name,comment,time,password) VALUES (:name, :comment, :time, :password)";
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt-> bindParam(':time', $time , PDO::PARAM_STR); 
            $stmt -> bindParam(':password',$password, PDO::PARAM_STR);
            $stmt -> execute();
        }
        else
        {
            //テーブルの値を編集
            $index = $_POST['hidden'];

            $sql = 'UPDATE snstest SET name=:name,comment=:comment WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':id', $index, PDO::PARAM_INT);
            
            $stmt->execute();
        }
    }


    if(isset($_POST['delete']) && isset($_POST['password'])){
        $delete = $_POST['delete'];
        $password = $_POST['password'];

        //テーブルからパスワードを取得
        $sql = 'SELECT * FROM snstest WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt ->bindParam(':id', $delete, PDO::PARAM_INT);
        $stmt ->execute();
        
        $results = $stmt->fetch();

        if($password == $results['password']){
            $sql = 'DELETE FROM snstest WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    
?>




<div class="flex">
    <div class="form">  
        <h2>入力フォーム</h2>
        <form method="post">
        <?php if($flag == 1){?>
            <input type="text" name="name" value=<?php echo $name?>><br>
            <input type="text" name="comment" value=<?php echo $comment?>><br>
            <input type="password" name="password" placeholder="パスワード"><br>
            <input type="hidden" name="hidden" value=<?php echo $_POST['change']?>>
        <?php }else{?>
            <input type="text" name="name" placeholder="名前"><br>
            <input type="text" name="comment" placeholder="コメント"><br>
            <input type="password" name="password" placeholder="パスワード"><br>
        <?php }?>
            <input type="submit" value="送信">
        </form>
    </div>

    <div class="form">  
        <h2>削除フォーム</h2>
        <form method="post">
            <input type="text" name="delete" placeholder="番号"><br>
            <input type="password" name="password" placeholder="パスワード"><br>
            <input type="submit" value="送信">
        </form>
    </div>

    <div class="form">  
        <h2>編集フォーム</h2>
        <form method="post">
            <input type="text" name="change" placeholder="番号"><br>
            <input type="password" name="password" placeholder="パスワード"><br>
            <input type="submit" value="送信">
        </form>
    </div>
</div>

<br>
<hr>
<br>

    <?php 
    //テーブルの内容を表示
    $sql = "SELECT * FROM snstest";
    $stmt = $pdo->query($sql);
    $results = $stmt -> fetchAll();
    foreach($results as $rows) {
        echo $rows['id'].' : '.$rows['name'].' : '.$rows['comment'].' : '.$rows['time'].'<br>';
    }   
    ?>

</body>
</html>