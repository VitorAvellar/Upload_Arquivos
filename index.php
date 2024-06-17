<?php
include("conexao.php");

// Lógica de exclusão
if (isset($_GET['deletar'])) {
    $id = intval($_GET['deletar']);
    $sql_query = $mysqli->query("SELECT * FROM arquivos WHERE id = '$id'") or die($mysqli->error);
    $arquivo = $sql_query->fetch_assoc();

    if (unlink($arquivo['path'])) {
        $deu_Certo = $mysqli->query("DELETE FROM arquivos WHERE id = '$id'") or die($mysqli->error);
        if ($deu_Certo) {
            echo "<p>Arquivo excluído com sucesso</p>";
        } else {
            echo "<p>Erro ao excluir o arquivo do banco de dados</p>";
        }
    } else {
        echo "<p>Erro ao excluir o arquivo do sistema de arquivos</p>";
    }
}

// Lógica de upload
if (isset($_FILES['arquivo']) && isset($_POST['descricao'])) {
    $arquivo = $_FILES['arquivo'];
    $descricao = $_POST['descricao'];

    if ($arquivo['error'])
        die("Falha ao enviar o arquivo");

    $pasta = "arquivos/";
    $nomeDoArquivo = $arquivo['name'];
    $novoNomeDoArquivo = uniqid();
    $extensao = strtolower(pathinfo($nomeDoArquivo, PATHINFO_EXTENSION));

    if ($extensao != 'jpg' && $extensao != 'png' && $extensao != 'mp4'&& $extensao != 'jpeg' && $extensao != 'jfif')
        die("Tipo de arquivo inválido. Coloque apenas arquivos: .JPG, .PNG, .MP4, .JPEG, .JFIF");

    $path = $pasta . $novoNomeDoArquivo . "." . $extensao;

    $deuCerto = move_uploaded_file($arquivo["tmp_name"], $path);
    if ($deuCerto) {
        $stmt = $mysqli->prepare("INSERT INTO arquivos (nome, path, descricao) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nomeDoArquivo, $path, $descricao);
        $stmt->execute() or die($stmt->error);
        echo "<p>Arquivo enviado com sucesso!</p>";
    } else {
        echo "<p>Falha ao enviar o arquivo</p>";
    }
}

// Consulta para buscar todos os arquivos
$sql_query = $mysqli->query("SELECT * FROM arquivos") or die($mysqli->error);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de arquivos</title>
    
</head>
<body>
    <form method="POST" enctype="multipart/form-data" action="">
        <p>
            <label for="arquivo">Selecione o arquivo</label>
            <input id="arquivo" name="arquivo" type="file">
        </p>
        <p>
            <label for="descricao">Descrição do arquivo</label>
            <input id="descricao" name="descricao" type="text">
        </p>
        <button name="upload" type="submit">Upload do arquivo</button>
    </form>

    <h1>Lista de arquivos</h1>
    <table>
        <thead>
            <tr>
                <th>Preview</th>
                <th>Arquivo</th>
                <th>Descrição</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($arquivo = $sql_query->fetch_assoc()) { ?>
                <tr>
                    <td><img height="50" src="<?php echo $arquivo['path']; ?>" alt=""></td>
                    <td><a href="<?php echo $arquivo['path']; ?>"><?php echo $arquivo['nome']; ?></a></td>
                    <td><?php echo $arquivo['descricao']; ?></td>
                    <td><a href="index.php?deletar=<?php echo $arquivo['id']; ?>">Deletar</a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 20px auto;
        }

        form p {
            margin: 0 0 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="file"],
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f9;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        img {
            max-width: 100%;
            height: 150px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            form, table {
                width: 100%;
                margin: 0;
            }
        }
    </style>
</html>
