<?php
if (!file_exists("uploads")) mkdir("uploads");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($_FILES["images"]["tmp_name"] as $key => $tmp_name) {
        $name = basename($_FILES["images"]["name"][$key]);
        move_uploaded_file($tmp_name, "uploads/" . $name);
    }
    exit;
}

if (isset($_GET["list"])) {
    echo json_encode(array_values(array_diff(scandir("uploads"), [".", ".."])));
    exit;
}

if (isset($_GET["generate"])) {
    $images = array_values(array_diff(scandir("uploads"), [".", ".."]));
    if (empty($images)) exit("Нет изображений");

    $pdf = new Imagick();
    $pdf->setResolution(300, 300);

    foreach ($images as $index => $img) {
        $page = new Imagick("uploads/" . $img);
        $page->setImageFormat("pdf");

        $width = $page->getImageWidth();
        $height = $page->getImageHeight();
        $canvas = new Imagick();
        $canvas->newImage($width + 28, $height + 28, "white");
        
        $isEven = ($index + 1) % 2 === 0;
        $xOffset = $isEven ? 28 : 0;

        $canvas->compositeImage($page, Imagick::COMPOSITE_OVER, $xOffset, 14);
        $pdf->addImage($canvas);
    }

    $pdf->writeImages("output.pdf", true);
    $pdf->clear();
    $pdf->destroy();
    exit;
}
?>