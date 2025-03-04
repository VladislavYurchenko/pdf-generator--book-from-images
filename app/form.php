<!DOCTYPE html>
<html>
<head>
    <title>Перенос изображений</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"],
        button {
            background-color: #5cb85c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover,
        button:hover {
            background-color: #449d44;
        }

        p {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        /* Стили для двухколоночного макета */
        .container {
            display: flex;
            width: 60%; /* Ограничение ширины контента */
            margin: 0 auto; /* Центрирование контента */
        }

        .column {
            width: 50%;
            padding: 10px;
            box-sizing: border-box;
        }

        /* Стили для превью изображений */
        .output-preview {
            margin-top: 30px;
            text-align: center;
            overflow-y: auto;
            border: 1px solid #ddd;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            width: 60%; /* Ограничение ширины контента */
            margin: 0 auto; /* Центрирование контента */
        }

        .output-preview-images {
            height: 800px;
            overflow-y: scroll;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        .output-preview img {
            width: calc(50% - 20px); /* Два изображения в ряд, с учетом margin */
            max-width: calc(50% - 20px);
            margin: 10px; /* Увеличены отступы */
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        /* Стили для списка директорий и файлов */
        .directory-listing {
            margin-top: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .directory-listing h2 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .directory-listing ul {
            list-style-type: none;
            padding: 0;
        }

        .directory-listing li {
            margin-bottom: 5px;
            color: #555;
        }

        /* Стили для очистки потока после двух колонок */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>

<?php include 'navigation.php'; ?>

    <h1>Перенос изображений</h1>

    <div class="container">
        <div class="column">
            <form action="move_files.php" method="post">
                <label for="source_dir">Путь к папке с томами:</label><br>
                <input type="text" id="source_dir" name="source_dir" value="/var/www/html/storage/volumes" style="width:500px"><br><br>

                <label for="output_dir">Путь к выходной папке:</label><br>
                <input type="text" id="output_dir" name="output_dir" value="/var/www/html/storage/output" style="width:500px"><br><br>

                <label for="naming_scheme">Схема именования:</label><br>
                <select id="naming_scheme" name="naming_scheme">
                    <option value="sequential">Последовательная нумерация (0001_filename.jpg)</option>
                    <option value="timestamp">Метка времени (timestamp_filename.jpg)</option>
                    <option value="hash">Хеш (md5hash.jpg)</option>
                    <option value="uuid">UUID (уникальный идентификатор)</option>
                </select><br><br>

                <input type="submit" value="Начать перенос"><br><br>
            </form>

            <form action="" method="post">
                <input type="hidden" name="clear_output" value="true">
                <button type="submit">Очистить выходную папку</button>
            </form>

            <?php
            // Показываем сообщение об успехе или ошибке после обработки
            if (isset($_GET['message'])) {
                echo "<p>" . htmlspecialchars($_GET['message']) . "</p>";
            }

            // Очистка выходной директории
            if (isset($_POST['clear_output']) && $_POST['clear_output'] == 'true') {
                $output_dir = '/var/www/html/storage/output'; // Укажите здесь путь к выходной папке
                $files = glob($output_dir . '/*'); // Получаем список всех файлов в директории

                foreach ($files as $file) {
                    if (is_file($file)) {
                        if (unlink($file)) {
                            echo "<p>Файл " . basename($file) . " удален</p>";
                        }
                        else {
                            echo "<p>Ошибка при удалении файла: " . basename($file) . "</p>";
                        }
                    }
                }

                header("Location: form.php?message=Выходная папка очищена.");
                exit;
            }
            ?>
        </div>

        <div class="column">
            <?php
            // Вывод списка директорий и файлов в папке с томами
            $source_dir = '/var/www/html/storage/volumes';
            $directory_contents = scandir($source_dir);
            function getChapterNumber2($string)
            {
                // Используем регулярное выражение для поиска номера главы
                if (preg_match('/Глава\s+(\d+)/u', $string, $matches)) {
                    return (int)$matches[1]; // Возвращаем номер главы как целое число
                }
                return 0; // Если номер главы не найден, возвращаем 0
            }
        
        
            // Сортируем массив на основе номера главы
            uasort($directory_contents, function ($a, $b) {
                $chapterA = getChapterNumber2($a);
                $chapterB = getChapterNumber2($b);
                return $chapterA <=> $chapterB; // Используем spaceship operator для сравнения
            });
            if ($directory_contents) {
                echo '<div class="directory-listing">';
                echo '<h2>Содержимое папки с томами:</h2>';
                echo '<ul>';
                foreach ($directory_contents as $item) {
                    if ($item != "." && $item != "..") {
                        echo '<li>' . htmlspecialchars($item) . '</li>';
                    }
                }
                echo '</ul>';
                echo '</div>';
            } else {
                echo '<p>Не удалось прочитать содержимое папки с томами.</p>';
            }
            ?>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="output-preview">
        <h2>Превью изображений в выходной папке:</h2>
        <div class="output-preview-images">
        <?php
        // Отображение превью изображений из output
        $output_dir = '/var/www/html/storage/output';
        $image_files = glob($output_dir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE); // Получаем список всех файлов изображений
        natsort($image_files);

        if ($image_files) {
            foreach ($image_files as $image) {
                echo '<img loading="lazy" src="/storage/output/'. basename($image).'" alt="' . basename($image) . '">';
            }
        } else {
            echo '<p>В выходной папке нет изображений.</p>';
        }
        ?>
        </div>
    </div>

</body>
</html>