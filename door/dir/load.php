<?php if(!defined('s7V9pz')) {die();}?><?php
function flr() {
    $arg = func_get_args();
    $t = vc($arg[0]);
    $r = false;
    if ($t == 'new') {
        $n = cnf()['gem'].'/ore/'.$arg[1];
        if (!file_exists($n)) {
            mkdir($n, 0755, true);
            if (isset($arg[2])) {
                file_put_contents($n.'/.htaccess', 'deny from all');
            }
            $r = true;
        }
    } else if ($t == 'suggest') {
        $ch = $rn = $fol = null;
        if (isset($arg[4])) {
            $rn = $arg[4];
        }
        $fl = rn($rn);
        if (isset($arg[1])) {
            $fol = $arg[1].'/';
        }
        $n = cnf()['gem'].'/ore/'.$fol;
        if (isset($arg[2])) {
            $ch = $arg[2];
            $s = $n.$ch.$fl;
        } else {
            $s = $n.$fl;
        }
        if (isset($arg[3])) {
            $s = $s.vc($arg[3]);
        }
        if (!file_exists($s)) {
            return $s;
        } else {
            flr('suggest', $ch, $ex);
        }
    } else if ($t == 'delete') {
        if (isset($arg[1]) && !empty($arg[1]) || $arg[1] === '0') {
            $n = cnf()['gem'].'/ore/'.$arg[1];
            if (isset($arg[2])) {
                $n = $arg[1];
            }
            if (file_exists($n)) {
                if (is_dir($n)) {
                    $dir = opendir($n);
                    while (false !== ($file = readdir($dir))) {
                        if (($file != '.') && ($file != '..')) {
                            $sub = $n . '/' . $file;
                            if (is_dir($sub)) {
                                flr("delete", $sub, 1);
                            } else {
                                unlink($sub);
                            }
                        }
                    }
                    closedir($dir);
                    rmdir($n);
                } else {
                    unlink($n);
                }
                $r = true;
            }}
    } else if ($t == 'rename') {
        if (!empty($arg[1]) || $arg[1] === '0') {
            $o = cnf()['gem'].'/ore/'.$arg[1];
            if (file_exists($o)) {
                if (!empty($arg[2]) || $arg[2] === '0') {
                    $n = cnf()['gem'].'/ore/'.$arg[2];
                    if (!file_exists($n)) {
                        rename($o, $n);
                        $r = true;
                    }
                }
            }
        }
    } else if ($t == 'list') {
        if (!empty($arg[1]) || $arg[1] === '0') {
            $d = cnf()['gem'].'/ore/'.$arg[1];
            if (isset($arg[2]) && $arg[2] == 'brace') {
                $files = glob($d."*", GLOB_BRACE);
            } else if (isset($arg[2]) && !empty($arg[2])) {
                $files = glob($d."*", GLOB_ONLYDIR);
            } else {
                $files = glob($d."*");
            }
            usort($files, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            return $files;
        }
    } else if ($t == 'dimension') {
        $url = $arg[1];
        $headers = array('Range: bytes=0-131072');
        if (isset($arg[2]) && !empty($arg[2])) {
            array_push($headers, 'Referer: ' . $arg[2]);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $data = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        curl_close($ch);

        if ($http_status != 200) {
            echo 'HTTP Status[' . $http_status . '] Errno [' . $curl_errno . ']';
            return [0, 0];
        }

        $image = imagecreatefromstring($data);
        $dims = [imagesx($image), imagesy($image)];
        imagedestroy($image);
        return $dims;
    } else if ($t == 'copy') {
        $src = cnf()['gem'].'/ore/'.$arg[1];
        $dst = cnf()['gem'].'/ore/'.$arg[2];
        if (isset($arg[3])) {
            $src = $arg[1];
            $dst = $arg[2];
        }
        if (file_exists($src)) {
            if (is_dir($src)) {
                $dir = opendir($src);
                if (!file_exists($dst)) {
                    mkdir($dst);
                    while (false !== ($file = readdir($dir))) {
                        if (($file != '.') && ($file != '..')) {
                            if (is_dir($src . '/' . $file)) {
                                flr('copy', $src . '/' . $file, $dst . '/' . $file, 1);
                            } else {
                                copy($src . '/' . $file, $dst . '/' . $file);
                            }
                        }
                    }
                    closedir($dir);
                    $r = true;
                }
            } else {
                copy($src, $dst);
                $r = true;
            }
        }
    } else if ($t == 'resize') {
        $src = cnf()['gem'].'/ore/'.$arg[1];
        $dst = cnf()['gem'].'/ore/'.$arg[1];
        if (file_exists($src)) {
            $width = $arg[3];
            $height = $arg[3];
            $crop = 0;
            if (isset($arg[2]) && $arg[2] !== 0) {
                $dst = cnf()['gem'].'/ore/'.$arg[2];
            }
            if (isset($arg[4]) && $arg[4] !== 0) {
                $height = $arg[4];
            }
            if (isset($arg[5]) && $arg[5] !== 0) {
                $crop = 1;
            }
            if (!empty($width)) {
                $ext = mime_content_type($src);
                if ($ext === 'image/jpeg' || $ext === 'image/png' || $ext === 'image/gif' || $ext === 'image/bmp' || $ext === 'image/x-ms-bmp') {
                    if (list($w, $h) = getimagesize($src)) {
                        $type = strtolower(substr(strrchr($src, "."), 1));

                        if ($crop) {
                            if ($w < $width or $h < $height) {
                                return false; die;
                            }
                            $ratio = max($width/$w, $height/$h);
                            $h = $height / $ratio;
                            $x = ($w - $width / $ratio) / 2;
                            $y = ($w - $width / $ratio) / 2;
                            $w = $width / $ratio;
                        } else {
                            if ($w < $width and $h < $height) {
                                return false; die;
                            }
                            $ratio = min($width/$w, $height/$h);
                            $width = $w * $ratio;
                            $height = $h * $ratio;
                            $x = $y = 0;
                        }
                        if (extension_loaded('imagick') && $ext == 'image/gif') {
                            $path = str_replace('\\', '/', realpath(cnf()['gem']));
                            $file_src = $path.'/ore/'.$arg[1];
                            $file_dst = $dst;
                            $image = new Imagick($file_src);
                            $image = $image->coalesceimages();
                            $original = new \Imagick($file_src);
                            $new = new \Imagick();

                            $i = 0;
                            $frameStep = ceil($original->getNumberImages() / 25);
                            foreach ($original as $frame) {
                                if ($i % $frameStep === 0) {
                                    $delay = $frame->getImageDelay();
                                    $frame->cropImage($w, $h, $x, $y);
                                    $frame->thumbnailImage($width, $height);
                                    $frame->setImagePage($width, $height, 0, 0);
                                    $frame->setImageDelay($delay * $frameStep);
                                    $new->addImage($frame->getImage());
                                }

                                $i++;
                            }
                            file_put_contents($file_dst, $new->getImagesBlob());
                            $new->clear();
                            $new->destroy();
                            $original->clear();
                            $original->destroy();
                        } else {
                            switch ($ext) {
                                case 'image/bmp': $img = imagecreatefromwbmp($src); break;
                                case 'image/gif': $img = imagecreatefromgif($src); break;
                                case 'image/jpeg': $img = imagecreatefromjpeg($src); break;
                                case 'image/png': $img = imagecreatefrompng($src); break;
                                default : return false;
                                }
                                $new = imagecreatetruecolor($width, $height);

                                if ($ext == "image/gif" or $ext == "image/png") {
                                    imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
                                    imagealphablending($new, false);
                                    imagesavealpha($new, true);
                                }
                                imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

                                switch ($ext) {
                                    case 'image/bmp': imagewbmp($new, $dst); break;
                                    case 'image/gif': imagegif($new, $dst); break;
                                    case 'image/jpeg': imagejpeg($new, $dst); break;
                                    case 'image/png': imagepng($new, $dst); break;
                                }
                            }
                            $r = true;
                        }
                    }
                }
            }
        } else if ($t == 'crop') {
            $r = "Requires Silwr Framework Pro Edition";
        } else if ($t == 'compress') {
            $v = cnf()['gem'].'/ore/'.$arg[1];
            if (file_exists($v)) {
                $ext = mime_content_type($v);
                if ($ext === 'image/jpeg' || $ext === 'image/png' || $ext === 'image/gif' || $ext === 'image/bmp') {
                    $info = getimagesize($v);
                    if ($info['mime'] == 'image/jpeg') {
                        $image = imagecreatefromjpeg($v);
                    } else if ($info['mime'] == 'image/gif') {
                        $image = imagecreatefromgif($v);
                    } elseif ($info['mime'] == 'image/png') {
                        $image = imagecreatefrompng($v);
                    }
                    $d = $v;
                    $q = $arg[2];
                    if (isset($arg[3])) {
                        $d = cnf()['gem'].'/ore/'.$arg[2];
                        $q = $arg[3];
                    }
                    imagejpeg($image, $d, $q);
                    $r = true;
                }}
        } else if ($t == 'zip') {
            $f = cnf()['gem'].'/ore/'.$arg[1];
            $z = cnf()['gem'].'/ore/'.$arg[2].'.zip';
            if (file_exists($f) && !file_exists($z)) {
                $rootPath = realpath($f);
                $zip = new ZipArchive();
                $zip->open($z, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                if (is_dir($f)) {
                    $files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($rootPath),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );
                    foreach ($files as $name => $file) {
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();
                            $relativePath = substr($filePath, strlen($rootPath) + 1);
                            $zip->addFile($filePath, $relativePath);
                        }}
                } else {
                    $zip->addFile($f, basename($f));
                    if (isset($arg[3])) {
                        $zip->renameName(basename($f), $arg[3]);
                    }
                }
                $zip->close();
                $r = true;
            }
        } else if ($t == 'download') {
            $f = cnf()['gem'].'/ore/'.$arg[1];
            if (file_exists($f)) {
                if (isset($arg[2]) && !empty($arg[2])) {
                    $filename = $arg[2];
                } else {
                    $filename = basename($f);
                }
                if (!ini_get('output_buffering')) {
                    echo "Output_Buffering Disabled in your server. Kindly enable the output_buffering Directive in your server.";
                    exit;
                }
                ob_clean();
                ob_flush();
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Transfer-Encoding: Binary");
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($f));
                ob_get_clean();
                while (ob_get_level()) {
                    ob_end_clean();
                }
                readfile($f);
            }
        } else if ($t == 'unzip') {
            $f = cnf()['gem'].'/ore/'.$arg[1].'zip';
            $d = cnf()['gem'].'/ore/'.$arg[2];
            if (file_exists($f) && !file_exists($d)) {
                mkdir($d);
                $zip = new ZipArchive;
                if ($zip->open($f) === TRUE) {
                    $zip->extractTo($d);
                    $zip->close();
                    $r = true;
                }
            }
        } else if ($t == 'write') {
            $f = cnf()['gem'].'/ore/'.$arg[1];
            if (!file_exists($f)) {
                $fl = fopen($f, "w");
                fwrite($fl, $arg[2]);
                fclose($fl);
                $r = true;
            }
        } else if ($t == 'overwrite') {
            $f = cnf()['gem'].'/ore/'.$arg[1];
            if (file_exists($f)) {
                $fl = fopen($f, "w");
                fwrite($fl, $arg[2]);
                fclose($fl);
                $r = true;
            }
        } else if ($t == 'edit') {
            $f = cnf()['gem'].'/ore/'.$arg[1];
            if (file_exists($f)) {
                $o = file_get_contents($f);
                $fl = fopen($f, "w");
                fwrite($fl, $o.$arg[2]);
                fclose($fl);
                $r = true;
            }
        } else if ($t == 'size') {
            $bytes = 0;
            $f = cnf()['gem'].'/ore/'.$arg[1];
            if (file_exists($f)) {
                if (is_dir($f)) {
                    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($f));
                    foreach ($iterator as $i) {
                        $bytes += $i->getSize();
                    }
                } else {
                    $bytes = filesize($f);
                }
            }
            if (!isset($arg[2])) {
                $arg[2] = null;
            }
            if ($bytes >= 1099511627776 && $arg[2] === null || $arg[2] === 'tb') {
                $bytes = number_format($bytes / 1099511627776, 2) . ' TB';
            } else if ($bytes >= 1073741824 && $arg[2] === null || $arg[2] === 'gb') {
                $bytes = number_format($bytes / 1073741824, 2) . ' GB';
            } else if ($bytes >= 1048576 && $arg[2] === null || $arg[2] === 'mb') {
                $bytes = number_format($bytes / 1048576, 2) . ' MB';
            } else if ($bytes >= 1024 && $arg[2] === null || $arg[2] === 'kb') {
                $bytes = number_format($bytes / 1024, 2) . ' KB';
            } else if ($bytes > 1) {
                $bytes = $bytes . ' bytes';
            } else if ($bytes == 1) {
                $bytes = $bytes . ' byte';
            } else
            {
                $bytes = '0 bytes';
            }
            $r = $bytes;
        } else if ($t == 'orientation') {
            if (function_exists('exif_read_data')) {
                $exif = @exif_read_data($arg[1]);
                if ($exif && isset($exif['Orientation'])) {
                    $orientation = $exif['Orientation'];
                    if ($orientation != 1) {
                        $info = getimagesize($arg[1]);
                        if ($info['mime'] == 'image/jpeg') {
                            $img = imagecreatefromjpeg($arg[1]);
                        } else if ($info['mime'] == 'image/gif') {
                            $img = imagecreatefromgif($arg[1]);
                        } elseif ($info['mime'] == 'image/png') {
                            $img = imagecreatefrompng($arg[1]);
                        }
                        $deg = 0;
                        switch ($orientation) {
                            case 3:
                                $deg = 180;
                                break;
                            case 6:
                                $deg = 270;
                                break;
                            case 8:
                                $deg = 90;
                                break;
                        }
                        if ($deg) {
                            $img = imagerotate($img, $deg, 0);
                        }
                        imagejpeg($img, $arg[1], 95);
                    }
                }
            }
        } else if ($t == 'upload') {
            set_time_limit(0);
            $f = cnf()['gem'].'/ore/'.$arg[2].'/';
            if (file_exists($f)) {
                $single = $total = 1;
                if (is_array($_FILES[$arg[1]]['name'])) {
                    $total = count($_FILES[$arg[1]]['name']);
                    $single = 0;
                }
                if (isset($arg[5]) && $arg[5] !== 0) {
                    if ($total > $arg[5]) {
                        $total = $arg[5];
                    }
                }
                for ($i = 0; $i < $total; $i++) {
                    $ex = 0;
                    $file = $_FILES[$arg[1]]['name'][$i];
                    $tmpFilePath = $_FILES[$arg[1]]['tmp_name'][$i];
                    if ($single === 1) {
                        $file = $_FILES[$arg[1]]['name'];
                        $tmpFilePath = $_FILES[$arg[1]]['tmp_name'];
                    }
                    if (isset($arg[7]) && !empty($arg[7])) {
                        $ext = $arg[7];
                    } else {
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                    }
                    if ($tmpFilePath != "") {
                        $newFilePath = $f . $file;
                        if (isset($arg[3]) && $arg[3] !== 0) {
                            $newFilePath = $f.$arg[3].$file;
                        }
                        if (isset($arg[6])) {
                            $newFilePath = $f.$arg[3].".".$ext;
                        }
                        if (isset($arg[4]) && $arg[4] !== 0) {
                            $al = explode(",", $arg[4]);
                            if (!in_array(strtolower($ext), $al)) {
                                $ex = 1;
                            }
                        }
                        if (!file_exists($newFilePath) && $ex === 0 || isset($arg[8]) && $ex === 0) {
                            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                                chmod($newFilePath, 0644);
                                flr('orientation', $newFilePath);
                                $r = true;
                            }
                        }
                    }
                }
            }
        }
        return $r;
    }

    ?>