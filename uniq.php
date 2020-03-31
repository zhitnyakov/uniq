<?php
if (isset($_GET['debug'])) {
   error_reporting(E_ALL);
   ini_set('display_errors', 1); 
}

class Uniq
{
    static public function image($source, $dest)
    {
        $image = file_get_contents($source);
        $im    = new Imagick();
        $im->readimageblob($image);

        $width  = $im->getImageWidth();
        $height = $im->getImageHeight();

        do {
            $ratio      = rand(0, 1000) / 100;
            $new_width  = floor($width * $ratio);
            $new_height = floor($height * $ratio);
        } while (
            ($new_width < 700 || $new_width > 1000)
            && ($new_height < 700 || $new_height > 1000));

        $im->scaleImage($new_width, $new_height);
        $im->rotateImage('#00000000', rand(-30, 30) / 100);
        $crop_pixels = rand(0, 5);
        $im->cropImage(
            $new_width - abs($crop_pixels), $new_height - abs($crop_pixels),
            0, 0
        );

        $color       = new ImagickPixel();
        $rand_color1 = rand(0, 255);
        $rand_color2 = rand(0, 255);
        $rand_color3 = rand(0, 255);
        $color->setColor("rgb($rand_color1,$rand_color2,$rand_color3)");
        $im->borderImage($color, rand(0, 1), rand(0, 1));

        $im->brightnessContrastImage(rand(-5, 5), rand(-5, 5));

        $image = $im->getimageblob();
        file_put_contents($dest, $image);
    }

    static public function video($source, $dest)
    {
        $noise_types = ['all', 'c0', 'c1', 'c2', 'c3'];
        $noise_flags = ['a', 'p', 't', 'u'];
        $noise       = $noise_types[array_rand($noise_types)];
        $noise_flag  = $noise_flags[array_rand($noise_flags)];
        $noise_value = rand(0, 10);
        $bitrate     = rand(750, 1250);
        $command
                     = "ffmpeg -i {$source} -vf noise={$noise}s={$noise_value}:{$noise}f={$noise_flag} -b:v {$bitrate}K {$dest} >/dev/null";
        shell_exec($command);
    }
}

if ($_FILES) {
    $uniq_files = [];
    $file       = [
        'name'     => $_FILES['file']['name'],
        'type'     => $_FILES['file']['type'],
        'tmp_name' => $_FILES['file']['tmp_name'],
    ];

    $file_parts        = explode('.', $file['name']);
    $file['extension'] = end($file_parts);
    $file['basename']  = str_replace(
        ".{$file['extension']}", '', $file['name']
    );

    $file['source'] = "/tmp/source.{$file['extension']}";
    move_uploaded_file($file['tmp_name'], $file['source']);

    $copies = isset($_POST['copies']) ? $_POST['copies'] : 1;

    for ($i = 1; $i <= $copies; $i++) {
        if (strpos($file['type'], 'image') !== false) {
            $copy_filename
                = "/tmp/{$file['basename']}_uniq_{$i}.{$file['extension']}";
            Uniq::image($file['source'], $copy_filename);
            $uniq_files[] = $copy_filename;
        }

        if (strpos($file['type'], 'video') !== false) {
            $copy_filename
                = "/tmp/{$file['basename']}_uniq_{$i}.{$file['extension']}";
            Uniq::video($file['source'], $copy_filename);
            $uniq_files[] = $copy_filename;
        }
    }

    $zip          = new ZipArchive();
    $zip_filename = "/tmp/uniq_result.zip";
    @unlink($zip_filename);
    $zip->open($zip_filename, ZipArchive::CREATE);

    foreach ($uniq_files as $uniq_file) {
        if (@file_exists($uniq_file)) {
            $zip->addFile($uniq_file, pathinfo($uniq_file)['basename']);
        }
    }
    $zip->close();

    foreach ($uniq_files as $uniq_file) {
        @unlink($uniq_file);
    }

    if (is_file($zip_filename) && is_readable($zip_filename)) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: " . filesize($zip_filename));
        header(
            "Content-Disposition: attachment; filename=\"" . basename(
                $zip_filename
            )
            . "\""
        );
        while (ob_get_level()) {
            ob_end_clean();
        }
        readfile($zip_filename);
    }
}
?>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
        crossorigin="anonymous">
  <title>Уникализация</title>
</head>
<body>
<div class="container"
     style="display: flex; align-items: center; height: 100vh;">
  <div class="col-12 col-lg-6 offset-lg-3">
    <form method="post" enctype="multipart/form-data">
      <div class="custom-file">
        <input type="file" class="custom-file-input form-control-lg"
               id="customFile" name="file">
        <label class="custom-file-label" for="customFile">Выбери креатив</label>
      </div>
      <div class="my-3 col-12 col-lg-6 offset-lg-3">
        <input type="number" class="form-control"
               placeholder="Количество копий" name="copies">
      </div>
      <div class="my-3 text-center">
        <button type="submit" class="btn btn-lg btn-primary">
          Уникализировать😎
        </button>
      </div>
      <div class="mt-5 text-center text-muted text-small"
           style="font-size:10px;">
        Made by
        <a href="https://vk.com/dencpa" target="_blank">Denis Zhitnyakov</a>
        &
        <a href="https://dolphin.ru.com/" tagrte="_blank">Dolphin</a>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script
  src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
  integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
  crossorigin="anonymous"></script>
<script
  src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
  integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
  crossorigin="anonymous"></script>
<style>
  body {
    background-image: radial-gradient(circle farthest-corner at 50.1% 52.3%, rgba(255, 231, 98, 1) 58.2%, rgba(251, 212, 0, 1) 90.1%);
  }
</style>
</body>
</html>
