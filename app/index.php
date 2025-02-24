<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генератор PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 30px;
        }

        #preview {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .thumb {
            margin: 5px;
            border: 1px solid #ddd;
            padding: 5px;
        }

        .thumb img {
            max-width: 150px;
            height: auto;
        }

        button,
        input[type="file"] {
            margin: 10px;
            padding: 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1>Генератор PDF с отступами</h1>

    <input type="file" id="fileInput" multiple accept="image/*">
    <button onclick="uploadFiles()">Загрузить</button>
    <button onclick="generatePDF()">Создать PDF</button>
    <button onclick="clearUploads()">Очистить</button>

    <h3>Предварительный просмотр</h3>
    <div id="preview"></div>

    <script>
        function uploadFiles() {
            let files = document.getElementById('fileInput').files;
            if (files.length === 0) return alert("Выберите файлы!");

            let formData = new FormData();
            for (let file of files) {
                formData.append("images[]", file);
            }

            fetch("process.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.text())
                .then(() => location.reload());
        }

        function generatePDF() {
            fetch("process.php?generate=1")
                .then(() => window.open("output.pdf", "_blank"));
        }

        function clearUploads() {
            fetch("delete.php")
                .then(() => location.reload());
        }

        window.onload = () => {
            fetch("process.php?list=1")
                .then(res => res.json())
                .then(files => {
                    let preview = document.getElementById("preview");
                    preview.innerHTML = "";
                    files.forEach(file => {
                        let img = document.createElement("img");
                        img.src = "uploads/" + file;
                        let div = document.createElement("div");
                        div.className = "thumb";
                        div.appendChild(img);
                        preview.appendChild(div);
                    });
                });
        };
    </script>
</body>

</html>