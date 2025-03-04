<?php

// Конфигурация путей
$source_dir = $_POST['source_dir'] ?? '';
$output_dir = $_POST['output_dir'] ?? '';

// Проверка наличия путей
if (empty($source_dir) || empty($output_dir)) {
    header("Location: form.php?message=Ошибка: Укажите исходную и выходную директории.");
    exit;
}

// Создание выходной директории, если ее нет
if (!is_dir($output_dir)) {
    if (!mkdir($output_dir, 0777, true)) {
        header("Location: form.php?message=Ошибка: Не удалось создать выходную директорию.");
        exit;
    }
}

$image_counter = 1;
function getChapterNumber($string)
{
    // Используем регулярное выражение для поиска номера главы
    if (preg_match('/Глава\s+(\d+)/u', $string, $matches)) {
        return (int)$matches[1]; // Возвращаем номер главы как целое число
    }
    return 0; // Если номер главы не найден, возвращаем 0
}

function process_directory($dir, &$counter, $output_dir)
{

    $files = scandir($dir);

    // Сортируем массив на основе номера главы
    uasort($files, function ($a, $b) {
        $chapterA = getChapterNumber($a);
        $chapterB = getChapterNumber($b);
        return $chapterA <=> $chapterB; // Используем spaceship operator для сравнения
    });

    if ($files === false) {
        return; // Пропускаем директорию, если не удалось прочитать содержимое
    }

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filepath = $dir . '/' . $file;

        if (is_dir($filepath)) {
            process_directory($filepath, $counter, $output_dir); // Рекурсивный вызов для поддиректорий
        } elseif (is_file($filepath)) {
            $new_filename = sprintf("%04d_%s", $counter, $file); // Добавляем префикс с нумерацией
            $new_filepath = $output_dir . '/' . $new_filename;
            if (rename($filepath, $new_filepath)) {
                $counter++;
            } else {
                error_log("Не удалось переместить файл: " . $filepath . " в " . $new_filepath);
            }
        }
    }

    // После обработки всех файлов в директории, пробуем ее удалить
    if ($dir != $_POST['source_dir']) {   //  <---- ДОБАВЛЕНА ПРОВЕРКА!!!
        if (rmdir($dir)) {
            error_log("Директория успешно удалена: " . $dir);
        } else {
            error_log("Не удалось удалить директорию: " . $dir);
        }
    }
}

process_directory($source_dir, $image_counter, $output_dir);

header("Location: form.php?message=Успешно перемещено " . ($image_counter - 1) . " изображений.");
exit;
