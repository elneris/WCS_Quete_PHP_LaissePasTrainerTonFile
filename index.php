<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Upload" content="UploadFile">
    <title>Laisse pas trainer ton file</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<style>

    .thumbnail {
        position :relative;
        height: 250px;
    }

    .btnn {
        position: absolute;
        bottom:10px;
        right: 10px;
    }
    img {
        max-height:150px;
    }
</style>
</head>
<body>

<header>
    <div class="well">
        <h1>Laisse pas trainer ton file</h1>
    </div>
</header>

<div class="container">

<?php

$fileUploadErrors = [
    0 => "Aucune erreur, OK",
    1 => "La taille du fichier téléchargé excède la valeur de upload_max_filesize, configurée dans le php.ini",
    2 => "La taille du fichier téléchargé excède la valeur de max",
    3 => "Le fichier n'a été que partiellement téléchargé.",
    4 => "Aucun fichier n'a été téléchargé.",
    6 => "Un dossier temporaire est manquant.",
    7 => "Échec de l'écriture du fichier sur le disque.",
    8 => "Une extension PHP a arrêté l'envoi de fichier. PHP ne propose aucun moyen de déterminer quelle extension est en cause. ",
];

$valideExtensions = [
    'jpg',
    'png',
    'gif',
];

$maxSize = 1048576;
$maxWidth = 300;
$maxHeight = 300;
$uploadDir = 'upload/';

if (!empty($_POST['sendFile'])) {
    if (!empty($_FILES['fichier']['name'][0] != '')) {
        for ($i = 0; $i < count($_FILES['fichier']['name']); $i++) {
            $tmpUploadFile = $_FILES['fichier']['tmp_name'][$i];
            $fileExtension = strtolower(strrchr($_FILES['fichier']['name'][$i], '.'));
            if (!in_array(substr($fileExtension, 1), $valideExtensions)) {
                $errors[] = "L'extention <b>($fileExtension)</b> du fichier <b>" . $_FILES['fichier']['name'][$i] . "</b> n'est pas valide !";
            }
            if ($tmpUploadFile != "" and empty($errors)) {
                $savedNames[] = $_FILES['fichier']['name'][$i];
                $uploadFile = $uploadDir . uniqid("image") . $fileExtension;
                if (move_uploaded_file($tmpUploadFile, $uploadFile)) {
                    $newFiles[] = basename($uploadFile);
                    $msgValidations[] = "L'image <b>" . $savedNames[$i] . "</b> à bien été envoyée.<br/>";
                }
            }
            if ($_FILES['fichier']['error'][$i] > 0) {
                $errors[] = "Erreur lors du transfert de " . $_FILES['fichier']['name'][$i] . ".<br/>" . $fileUploadErrors[$_FILES['fichier']['error'][$i]] . ".";
            }
        }
    } else {
        $errors[] = "Vous devez ajouter au minimum 1 image";
    }
}

if (!empty($_POST['idDelete'])) {
    $id = "upload/" . $_POST['idDelete'];
    if ($dossier = opendir('./upload/')) {
        if (file_exists($id)){
            unlink($id);
            $msgValidations[] = "L'image <b>" . $_POST['idDelete'] . "</b> à été supprimée avec succès!";
            closedir ($dossier);
        } else {
            $errors[] = "L'image <b>" . $_POST['idDelete'] . "</b> à déjà été supprimée!";
        }
    }
}

if (!empty($errors)) { ?>

    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Erreur<?= count($errors) > 1 ? "s" : "" ?> !!!</strong>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

<?php }
if (!empty($msgValidations)) { ?>

    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Super !!!</strong>
        <ul>
            <?php foreach ($msgValidations as $msgValidation) : ?>
                <li><?= $msgValidation ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

<?php }
?>

    <div class="well">
        <h3>Envoyer un fichier:</h3>
        <form action="" method="post" enctype="multipart/form-data">

            <input type="hidden" name="MAX_FILE_SIZE" value="1048576"/>
            <input type="hidden" name="sendFile" value="1"/>
            <input type="file" multiple="multiple" name="fichier[]"/><br/>
            <button class="btn btn-success btn-xs" type="submit">Envoyer</button>
            <p><i>1 MO Maximum! jpg, png ou gif seulement.</i></p>
        </form>
    </div>

    <div class="row">
        <?php
        $nb_fichier = 0;
        if ($dossier = opendir('./upload/')) {
            while (false !== ($fichier = readdir($dossier))) {
                if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php') { ?>

                    <div class="col-xs-3">
                        <div class="thumbnail">
                            <img src="upload/<?= $fichier ?>" alt="Image">
                            <div class="caption">
                                <h4><?= $fichier ?></h4>
                                <form action="" method="POST">
                                    <input type="hidden" name="idDelete" value="<?= $fichier ?>" />
                                    <button type="submit" class="btn btn-danger btnn btn-xs" role="button">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php }
            }
            closedir ($dossier);
        }
        ?>
    </div>


<footer>

</footer>

</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>
</html>