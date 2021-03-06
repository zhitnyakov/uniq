# Уникализация креативов

Скачать скрипт [можно здесь](https://github.com/zhitnyakov/uniq/archive/master.zip)
Подробнейшая видео-инструкция [находится здесь](https://youtu.be/brHPHBS5J9c).

## Список команд

- `apt update`
- `apt install apache2`
- `apt install php`
- `apt install libapache2-mod-php`
- `apt install php-imagick`
- `apt install php-zip`
- `apt install ffmpeg`
- `apachectl restart`

## Для тех, кто не разбирается в тех. части

Действуйте по 8-минутной [видео-инструкции](https://youtu.be/brHPHBS5J9c).

## Проблема с уникализацией видео

Решение проблемы [описано здесь](https://youtu.be/YccDgMEp9Nw). 

## Для тех, кто разбирается в тех. части

Лучше тоже следуйте 8-минутной [видео-инструкции](https://youtu.be/brHPHBS5J9c) 🙃

Anyway. Если вы хотите закинуть данный скрипт на уже имеющийся у вас сервер, то на сервере должно быть несколько вещей:

- PHP, интегрированный с веб-сервером. Лучше Apache, так как в случае с ним вам будет понятнее, как решать проблему с большими видео-файлами (решение проблемы указано выше). Nginx - вообще легко, но уже на свой страх/риск и с кристально чистым пониманием, как курить этот стек.
- Расширение PHP [ImageMagick](https://www.php.net/manual/ru/book.imagick.php). В debian-based ОС устанавливается командой `apt install php-imagick`
- Расширение [PHP Zip](https://www.php.net/manual/ru/book.zip.php). В debian-based ОС устанавливается командой `apt install php-zip`
- Не забывайте после установки расширений перезапускать веб-сервер!
- [FFmpeg](https://www.ffmpeg.org/). В debian-based ОС устанавливается командой `apt install ffmpeg`
