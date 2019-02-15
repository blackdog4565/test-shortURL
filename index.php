<?php
  require_once($_SERVER['DOCUMENT_ROOT']."/header/header.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/connection.php");

  class shortURL{
    public $idURL; # ID ссылки
    public $fullURL; # полная ссылка
    public $shortURL; # сокращенная ссылка
    public $timeURL; # время жизни ссылки

    function __construct($idURL, $fullURL, $shortURL, $timeURL, $dateURL){ # создание экземпляра короткой ссылки
      $this -> idURL = $idURL;
      $this -> fullURL = $fullURL;
      $this -> shortURL = $shortURL;
      $this -> timeURL = $timeURL;
      $this -> dateURL = $dateURL;
      
    }
    /*function __construct($idURL, $fullURL, $shortURL, $timeURL){ # создание экземпляра короткой ссылки
      $this -> idURL = $idURL;
      $this -> fullURL = $fullURL;
      $this -> shortURL = $shortURL;
      $this -> timeURL = $timeURL;
    }
*/
    public function createShortURL(){ # создание ссылки в бд
      $GLOBALS['db']->query("insert into `shorturl`(`full`, `short`, `time`, time_create) values ('".$this -> fullURL."','".$this -> shortURL."','".$this -> timeURL."','".$this -> dateURL."')");
    } 

    public function editShortURL(){ # редактирование
      $GLOBALS['db']->query("update `shorturl` SET `full` = '".$this -> fullURL."', `short` = '".$this -> shortURL."', `time` = '".$this -> timeURL."' WHERE `shorturl`.`id` = ".$this -> idURL."");
    } 

    public function deleteShortURL(){ # удаление
      $GLOBALS['db']->query("delete from `shorturl` where `shorturl`.`id` = ".$this -> idURL."") or die("Ошибка " . mysqli_error($GLOBALS['db']));
    }     
  }
  function showOrNot ($text,$dbURL_short){
  	$fromDB = $GLOBALS['db']->query("select * from `shorturl` where `short` = '".$dbURL_short."'");
		$timeFromDB = $fromDB->fetch_object(); # создание объекта с информацией о времени жизни ссылки и о времени, когда она была создана

		if($fromDB->num_rows > 0){
			$timeCreateURL = DateTime::createFromFormat("Y-m-d H:i:s",$timeFromDB->time_create); # выделяем время создания
			$timeCreateURL_h = DateTime::createFromFormat("i",$timeFromDB->time_create);
			$date = new DateTime(); # текущее время
			
			$diff = $date->diff($timeCreateURL); # интервал времени с момента создания ссылки
								
			#echo $diff->h * 60 + $diff->i . " | ". $timeFromDB->time*60;
			#echo $timeCreateURL_h;
			if($timeFromDB->time > 0){ # если у ссылки есть время жизни, то проверяем
				if($diff->h * 60 + $diff->i > $timeFromDB->time*60){
		    	echo "$text";
		    }
	 		}
		}

  }
?>
<form class="form-short-url flex" action="" method="post"> 
  <p class="form-p">Введите ссылку, которую хотите сократить.</p>
  <input class="form-url" type="url" placeholder="http://example.com" autocomplete="off" name="url" required>
  <input class="form-sub" type="submit" name="submit" value="Сократить">
</form>
<?php  
  if(isset($_POST['submit'])){ # по нажатию кнопки процесс создания переходов
  	$symdols = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"; # всевозможные символы для создания короткой ссылки
	  $shortURL = substr(str_shuffle($symdols), 0, 8); # выбираем случайно 8 символов для создаиня уникальной ссылки
	  $url = $_POST['url']; # введенная ссылка пользователем
	  
	  $checkShortURL = $db->query("select * from `shorturl` where `short` = '".$shortURL."'")->num_rows; # запрос на поиск элементов в бд с такой же ссылкой

	  while($checkShortURL >= 1){ # пока будут находиться такие короткие ссылки, будут создаваться еще, для достижение уникальности
	    $shortURL = substr(str_shuffle($symdols), 0, 8);
	    $checkShortURL = $db->query("select * from `shorturl` where `short` = '".$shortURL."'")->num_rows;
	  }

	  $newURL = "http://short-url.com/$shortURL"; # готовая короткая ссылка


    $fh = fopen(".htaccess", "a"); # открываем файл .htaccess
    fwrite($fh, "
    RewriteRule ^$shortURL$ /shortURL/$shortURL.php"); # записываем ссылку на файл в каталоге и её сокращённый вариант
    fclose($fh); # закрываем файл
    $date = date("Y-m-d H:i:s"); # 
    $short = new shortURL(0, $url, $shortURL, 0, $date);
    $short->createShortURL();
    
    $f = fopen("shortURL/$shortURL.php", "w"); # создаем файл с именем короткой ссылки в папку shortURL
    fwrite($f, "<?php 
      require_once(\$_SERVER['DOCUMENT_ROOT'].\"/connection.php\");

      \$fromDB = \$db->query(\"select * from `shorturl` where `short` = '$shortURL'\");
      \$timeFromDB = \$fromDB->fetch_object(); # создание объекта с информацией о времени жизни ссылки и о времени, когда она была создана

      if(\$fromDB->num_rows > 0){
        \$timeCreateURL = DateTime::createFromFormat(\"Y-m-d H:i:s\",\$timeFromDB->time_create); # выделяем время создания
        \$date = new DateTime(); # текущее время

        \$diff = \$date->diff(\$timeCreateURL); # интервал времени с момента создания ссылки
				
				

        if(\$timeFromDB->time == 0){ # если ссылка вечна, то всегда переходим
              header('Location: $url');  
        }
        elseif(\$timeFromDB->time > 0){ # если у ссылки есть время жизни, то проверяем
          if(\$diff->h * 60 + \$diff->i > \$timeFromDB->time*60){ # не закночилась ли ее время жизни
            header('Location: 404.php'); 
          }
          else{
            header('Location: $url');  
          }
        }
      }
      else{
        header('Location: 404.php'); 
      }
    ?>"); # код редиректа, с ссылкой которую ввёл пользователь
    fclose($f);
  }
?>
<div class="allURL">
  <ul class="ul ">
  	<li>
  		<ul class="bar flex">
  			<li class="allURL__li id">
  				ID
  			</li>
  			<li class="allURL__li">
  				Ссылка перехода
  			</li>
  			<li class="allURL__li bar__short">
  				Сокращенная ссылка
  			</li>
  			<li class="allURL__li">
  				Время жизни ссылки
  			</li>
  			<li class="allURL__li bar__date">
  				Дата создания ссылки
  			</li>
  		</ul>
  	</li>
  	<hr class="hr-bot">
    <?php
      $result = $db->query("select * from `shorturl`");
      while($dbURL = $result->fetch_object()){ # пока есть объекты в БД
      	if(strlen($dbURL->full) <= 20){
      		$fullURL_li = $dbURL->full;
      	}
      	else{
	      	$fullURL_li =	substr($dbURL->full, 0, 20) . "...";
      	}
        echo "

          <li>
            <ul class=\"flex ul allURL__ul\"> <!-- все ссылки пользователя -->
              <li class=\"allURL__li id\">
                ".$dbURL->id."
              </li>
              <li class=\"allURL__li\" title=\"$dbURL->full\">
                ".$fullURL_li."
              </li>
              <li class=\"allURL__li short\">
                <a class=\"allURL__a\" href=\"$dbURL->short\">http://short-url.com/".$dbURL->short."</a>
              </li>
              <li class=\"allURL__li time\">
                ".$dbURL->time."
              </li>
              <li class=\"allURL__li\">
                ".$dbURL->time_create."
              </li>
              <li class=\"allURL__li edit-button\"><a class=\"\">Изменить</a></li>
            </ul>
            <div class=\"edit-field\"> 
              <form id=\"$dbURL->id edit\" action=\"\" method=\"post\" class=\"flex form-edit\"> <!-- форма изменения ссылок -->";
								
								$inputP = "<p class=\"form-edit-p\">Ссылка больше не действительна</p>";
                showOrNot($inputP,$dbURL->short);

              	$inputText = "<input class=\"form-edit-sub form-edit-sub_activate\" type=\"submit\" name=\"activate\" value=\"Активировать\">";
                showOrNot($inputText,$dbURL->short);

                echo "<!--hidden для хранения и обработки после отправки данных по изменению -->

                <input type=\"hidden\" name=\"old_short\" value=\"".$dbURL->short."\" required> 
                <input type=\"hidden\" name=\"id\" value=\"".$dbURL->id."\" required>
                <input type=\"hidden\" name=\"full\" value=\"".$dbURL->full."\" required>
                <input type=\"hidden\" name=\"time\" value=\"".$dbURL->time."\" required>
                <input type=\"hidden\" name=\"time_create\" value=\"".$dbURL->time_create."\" required>
                
                <!-- новые значения для ссылки -->

                <input class=\"form-edit-input form-edit-input_url\" type=\"text\" autocomplete=\"off\" name=\"short-url-edit\" value=\"".$dbURL->short."\" maxlength=\"18\">
                <input class=\"form-edit-input form-edit-input_time\" type=\"number\" autocomplete=\"off\" name=\"short-time-edit\" value=\"".$dbURL->time."\">

                <!-- кнопки для принятия решения -->

                <div class=\"flex form-edit__button\">
                  <input class=\"form-edit-sub form-edit-sub_accept\" type=\"submit\" name=\"accept-edit\" value=\"Принять\">
                  
                  <input class=\"form-edit-sub form-edit-sub_delete\" type=\"submit\" name=\"delete\" value=\"Удалить\">
                  
                  <input class=\"form-edit-sub form-edit-sub_cancel\" type=\"button\" name=\"cancel-edit\" value=\"Отменить\">
                </div>
              </form>
            </div>
            <hr class=\"hr-bot\">
          </li>
        ";
      }
    	if(isset($_POST['activate'])){
      	$a = new shortURL($_POST['id'],$_POST['full'],$_POST['short-url-edit'],0,$_POST['time_create']);
        $a->editShortURL();
        echo '<meta http-equiv="refresh" content="0;">';
      }
      if(isset($_POST['accept-edit'])){ # если нажата кнопка принятия изменений новых значений
        $shortURL_old = $_POST['old_short']; # сохраняем старые и новые значения ссылок и времени
        $shortURL_new = $_POST['short-url-edit'];

        $timeOfURL_old = $_POST['time'];
        $timeOfURL_new = $_POST['short-time-edit'];

        $fullURL = $_POST['full'];

         
          
          $check_short = $db->query("select * from `shorturl` where `short` = '".$_POST['short-url-edit']."'")->num_rows;
          if(strlen($shortURL_new) < 3 or strlen($shortURL_new) > 18){
	        	if(strlen($shortURL_new) < 3){
	        		echo "
	        	<div class=\"err flex\">
	        		<p class=\"err__p\">Ссылка <span class=\"err__span\">". $shortURL_new."</span> является слишком коротой (меньше 3 символов)! <br> Введите новую.</p>
	        		<input class=\"err__ok\" type=\"submit\" value=\"ОК\">
	        	</div>";
	        	}
	        	if(strlen($shortURL_new) > 18){
	        		echo "
	        	<div class=\"err flex\">
	        		<p class=\"err__p\">Ссылка <span class=\"err__span\">". $shortURL_new."</span> является слишком большой (больше 18 символов)!<br> Введите новую.</p>
	        		<input class=\"err__ok\" type=\"submit\" value=\"ОК\">
	        	</div>";
	        	}
	        }
	        else{
	        	if($check_short == 1 and $shortURL_old != $shortURL_new){ # если находится такая же ссылка, то выводится предупреждение
	        	echo "
	        	<div class=\"err flex\">
	        		<p>Ссылка <span class=\"err__span\">". $shortURL_new."</span> уже используется! Введите новую.</p>
	        		<input class=\"err__ok\" type=\"submit\" value=\"ОК\">
	        	</div>";
	        	}
		        else{
		          unlink("shortURL/$shortURL_old.php"); # удаляем старый файл редиректа

		          $readFileURL_new = fopen("shortURL/$shortURL_new.php", "w");
		          fwrite($readFileURL_new, "<?php 
					      require_once(\$_SERVER['DOCUMENT_ROOT'].\"/connection.php\");

					      \$fromDB = \$db->query(\"select * from `shorturl` where `short` = '$shortURL_new'\");
					      \$timeFromDB = \$fromDB->fetch_object(); # создание объекта с информацией о времени жизни ссылки и о времени, когда она была создана

					      if(\$fromDB->num_rows > 0){

					        \$timeCreateURL = DateTime::createFromFormat(\"Y-m-d H:i:s\",\$timeFromDB->time_create); # выделяем время создания
					        \$date = new DateTime(); # текущее время

					        \$diff = \$date->diff(\$timeCreateURL); # интервал времени с момента создания ссылки
									
									

					        if(\$timeFromDB->time == 0){ # если ссылка вечна, то всегда переходим
					              header('Location: $fullURL');  
					        }
					        elseif(\$timeFromDB->time > 0){ # если у ссылки есть время жизни, то проверяем
					          if(\$diff->h * 60 + \$diff->i > \$timeFromDB->time*60){ # не закночилась ли ее время жизни
					            header('Location: 404.php'); 
					          }
					          else{
					            header('Location: $fullURL');  
					          }
					        }
					      }
					      else{
					        header('Location: 404.php'); 
					      }
					    ?>");
		          fclose($readFileURL_new);
		          

		          $readFileHtaccess = file_get_contents(".htaccess"); # меняем файл .htaccess
		          $newHtaccess = str_replace("$shortURL_old", "$shortURL_new", "$readFileHtaccess");
		          
		          file_put_contents(".htaccess", "$newHtaccess");

		          $shortFromDB = new shortURL($_POST['id'],$_POST['full'],$_POST['short-url-edit'],$_POST['short-time-edit'],$_POST['time_create']); # создаем объект ссылки для изменения
		  	      $shortFromDB->editShortURL();	
		  	      echo '<meta http-equiv="refresh" content="0;">'; # обновляем страницу
		        }
	        }
	         
        

        
	        
        
       
        
      }
      if(isset($_POST['delete'])){ # если нажата кнопка удаления
        $q = new shortURL($_POST['id'],$_POST['full'],$_POST['short-url-edit'],$_POST['short-time-edit'],$_POST['time_create']);
        $q->deleteShortURL();
        echo '<meta http-equiv="refresh" content="0;">';
      }
      
    ?>  
     
  </ul>
</div>
<?php
  require_once($_SERVER['DOCUMENT_ROOT']."/footer/footer.php");
?>
