<?php
// if (!file_exists("output")) mkdir("output");
$root = '/var/www/html/storage';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($_FILES["images"]["tmp_name"] as $key => $tmp_name) {
        $name = basename($_FILES["images"]["name"][$key]);
        move_uploaded_file($tmp_name, "$root/output/" . $name);
    }
    exit;
}

if (isset($_GET["list"])) {
    echo json_encode(array_values(array_diff(scandir("$root/output"), [".", "..", ".gitignore"])));
    exit;
}

if (isset($_GET["generate"])) {
    $images = array_values(array_diff(scandir("$root/output"), [".", "..", ".gitignore"]));
    if (empty($images)) exit("Нет изображений");

    $pdf = new Imagick();
    $pdf->setResolution(300, 300);

    $cm = 28;

    $gap = $cm * 3;

    foreach ($images as $index => $img) {
        $page = new Imagick("$root/output/" . $img);
        $page->setImageFormat("pdf");

        $width = $page->getImageWidth();
        $height = $page->getImageHeight();
        $canvas = new Imagick();
        $canvas->newImage($width + $gap, $height + $gap, "white");

        $isEven = ($index + 1) % 2 === 0;
        $xOffset = $isEven ? 0 : $gap; // Меняем местами смещение

        $canvas->compositeImage($page, Imagick::COMPOSITE_OVER, $xOffset, 28);
        $pdf->addImage($canvas);
    }

    $pdf->writeImages("$root/output.pdf", true);
    $pdf->clear();
    $pdf->destroy();
    exit;
}
