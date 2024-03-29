<?php
    session_start(); 
        if(isset($_SESSION['login']) == false){
        //このページのURLをコピーして他のブラウザで閲覧できないようにする
        header("location:error.html");
    }else{
        $userid = $_SESSION['login']['username'];
    }

    $id = $_GET['ID'];
    $view = $_GET['view'];

    //DB接続
    require_once('../DBInfo.php');
    $dbh = new PDO(DBInfo::DSN,DBInfo::USER,DBInfo::PASSWORD);
    $sql = "SELECT * FROM Memo_tags WHERE id=?";

    $stmt = $dbh->prepare($sql);
    $stmt->execute([$id]);

    foreach($stmt as $row){
        
        //<進捗ステータスのチェック判定>
        if($row['progress'] == 0){
            $selected0 = "selected";
        }else if($row['progress'] == 1){
            $selected1 = "selected";
        }else if($row['progress'] == 2){
            $selected2 = "selected";
        }

    }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <!-- icon -->
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico" />
    <!-- css -->
    <link rel="stylesheet" href="edit_form.css">
    <title>編集</title>
</head>
    <body>
        <!-- ヘッダー読み込み -->
        <?php require_once('../Header/header.php'); ?>  

        <main class="container">

            <div>
                <a href="javascript:history.back()" role="button" id="cancelBtn" class="btn-block btn-sm">
                    <button><i class="fas fa-arrow-left"></i> キャンセル</button>
                </a>
            </div>
            
            <form action="../edit.php" method="post">
                <h3>編集</h3>
                <div>
                    <?php print($row['year']) ?>年
                    <select class="input_style0" name="month_edit" required>
                    <?php
                            for($i=1;$i<=12;$i++){
                                if($i != $row['month']){
                                    print("<option value=\"{$i}\">$i</option>");
                                }else if($i == $row['month']){
                                    print("<option value=\"{$i}\" selected>$i</option>");
                                }
                            }
                    ?>
                    </select>
                    <label for="month_edit">月</label>

                    <select class="input_style0" name="day_edit" required>
                    <?php
                    $finalDay = date( 't' , strtotime($row['year'] . "/" . $row['month'] . "/01"));  
                            for($i=1;$i<=$finalDay;$i++){
                                if($i != $row['day']){
                                    print("<option value=\"{$i}\">$i</option>");
                                }else if($i == $row['day']){
                                    print("<option value=\"{$i}\" selected>$i</option>");
                                }
                            }
                    ?>
                    </select>
                    <label for="day_edit">日</label>
                </div>
                   
                <!-- ページリダイレクトに必要なパラメータ -->
                <input type="hidden" name="userid" value="<?php print($row['userid']) ?>">
                <input type="hidden" name="year" value="<?php print($row['year']) ?>">
                <input type="hidden" name="month" value="<?php print($row['month']) ?>">
                <input type="hidden" name="day" value="<?php print($row['day']) ?>">
                <input type="hidden" name="view" value="<?php print($view) ?>">
                <!-- ページリダイレクトに必要なパラメータ -->

                <input type="hidden" name="id" value="<?php print($id) ?>">

                <div class="row form-group">
                    <div class="col col-md-6">
                        <!-- 開始時刻 -->
                        <label for="start">開始時刻</label>
                        <select id="selectTime1" class="form-control" name="start" required>
                        <?php
                            print("<option value=\"\" disabled selected style=\"display:none;\">選択</option>");
                            for($i=0;$i<=23;$i++){
                                if($i < 10){
                                    $s_time = "0{$i}:00:00";
                                }else{
                                    $s_time = "{$i}:00:00";
                                }
                                    if($s_time == $row['start_time']){
                                        print("<option value=\"{$s_time}\" selected>{$i}:00</option>");
                                    }else{
                                        print("<option value=\"{$s_time}\">{$i}:00</option>");
                                    }
                            }
                        ?>
                        </select>
                    </div>

                    <div class="col col-md-6">
                        <!-- 終了時刻 -->
                        <label for="start">終了時刻</label>
                        <select id="selectTime2" class="form-control" name="end" required>
                        <?php
                            print("<option value=\"\" disabled selected style=\"display:none;\">選択</option>");
                            for($i=0;$i<=24;$i++){
                                if($i < 10){
                                    $time = "0{$i}:00:00";
                                }else{
                                    $time = "{$i}:00:00";
                                }
                                    if($time == $row['end_time']){
                                        print("<option value=\"{$time}\" selected>{$i}:00</option>");
                                    }else{
                                        print("<option value=\"{$time}\">{$i}:00</option>");
                                    }
                            }
                        ?>
                        </select>
                    </div>
                </div>

                <!-- タイトル -->
                <div class="form-group">
                    <label>タイトル</label>
                    <input type="text" class="form-control" id="pre_title" name="title" required value="<?php print($row['title']) ?>">
                </div>


                <!-- メモ -->
                <div class="form-group">
                    <label>メモ</label>
                    <textarea id="EditPreviwe" class="form-control" name="memo" cols="11" rows="4" value=""><?php print($row['memo']) ?></textarea>
                </div>

                <div class="row">
                <!-- カラー -->
                <div id="EditTdColor" class="form-group col col-md-6">
                    <label>カラー</label>
                    <div class="color_radio_wrap">
                        <input type="color" name="color" value="<?= $row['color'] ?>" style="width:100%;height:100%;">
                    </div>
                </div>

                <!-- 進捗ステータス -->
                <div class="form-group col col-md-6">
                    <label>進捗</label>
                    <select id="prog_select" class="form-control" name="progress" required>
                        <option value="0" <?php print($selected0) ?>>未了</option>
                        <option value="1" <?php print($selected1) ?>>完了</option>
                        <option value="2" <?php print($selected2) ?>>キャンセル</option>
                    </select>
                </div>
                </div>

                <div class="row form-group">
                    <div class="col col-md-6 mx-auto">
                        <button type="submit" class="btn btn-primary btn-lg btn-block" id="sendBtn">編集</button>
                    </div>
                </div>
            </form>
        </maim>


        <!-- bootstrap -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <!-- bootstrap -->
    </body>
</html>