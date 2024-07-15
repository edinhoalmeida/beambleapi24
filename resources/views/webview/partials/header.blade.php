<!DOCTYPE html>
<html lang="en" class="webview-page">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('webview.html_title') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    {{ vite('resources/css/sass/webview/webview.scss') }}
    <style>
      #feedback-form {
  width: 100%;
  margin: 0 auto;
  padding: 0 10px 0;
  font-family: sans-serif;
}

#feedback-form * {
    box-sizing: border-box;
}

#feedback-form h2{
  text-align: center;
  margin-bottom: 30px;
}

#feedback-form input {
  margin-bottom: 15px;
}

#feedback-form input[type=text],#feedback-form input[type=email] {
  display: block;
  height: 32px;
  padding:10px;
  width: 100%;
  border: none;
  background-color: #f3f3f3;
}

#feedback-form label {
  color: #FFFFFF;
  font-size: 0.8em;
}
#feedback-form textarea {
  width: 100%;
  font-size: 1em;
  height: 60px;
  padding:10px;
}

#feedback-form input[type=checkbox] {
  float: left;
}

#feedback-form input:not(:checked) + #feedback-phone {
  height: 0;
  padding-top: 0;
  padding-bottom: 0;
}

#feedback-form #feedback-phone {
  transition: .3s;
}

#feedback-form button[type=submit] {
  display: block;
  margin: 20px auto 0;
  width: 150px;
  height: 40px;
  border-radius: 17px;
  border: none;
  color: #eee;
  font-weight: 700;
  
  background: #a253e5;
}
      </style>
</head>


