<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{asset('mail/mail.css')}}">
  <title>Mail</title>
</head>
<body>
  <p>Olá {{ $user->name }}!</p>
  <p></p>
  <p>Para redefinir a senha da sua conta simov, clique <a href="{{env('MAIL_LINK').'?code='.$codigo}}">aqui</a></p>
  <p>Atenciosamente,</p>
  <p>Equipe da coapa</p>
</body>
</html>


