<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генератор PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        input[type="file"] {
            margin: 10px 0;
            padding: 10px;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            display: inline-block;
        }

        button {
            margin: 10px;
            padding: 10px 20px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        #preview {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }

        .thumb {
            margin: 5px;
            border: 1px solid #ddd;
            padding: 5px;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .thumb img {
            max-width: 150px;
            height: auto;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <?php include 'navigation.php'; ?>
    <h1>Генератор PDF</h1>

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
                .then(() => window.open("/storage/output.pdf", "_blank"));
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
                        img.src = "/storage/output/" + file;
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