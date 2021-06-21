<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Mail</title>
  <style>
    body{
      font-family: Arial, Helvetica, sans-serif;
    }
  </style>
</head>
<body>
  <p>Olá {{ $user->name }}!</p>
  <p></p>
  <p>Para redefinir a senha da sua conta simov, clique <a href="{{env('MAIL_LINK').$codigo}}">aqui</a></p>
  <p>Atenciosamente,</p>
  <p>Equipe da coapa</p>
</body>
</html>


