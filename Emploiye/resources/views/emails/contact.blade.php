<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body>

    <h2>Nouveau message depuis le formulaire de contact</h2>

    <hr>

    <p>
        <strong>Nom :</strong>
        {{ $data['name'] }}
    </p>

    <p>
        <strong>Email :</strong>
        {{ $data['email'] }}
    </p>

    <p>
        <strong>Téléphone :</strong>
        {{ $data['telephone'] }}
    </p>

    <p>
        <strong>Sujet :</strong>
        {{ $data['subject'] }}
    </p>

    <p>
        <strong>Message :</strong>
    </p>

    <p>
        {{ $data['message'] }}
    </p>

</body>

</html>