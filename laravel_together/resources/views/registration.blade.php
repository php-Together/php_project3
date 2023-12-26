<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="/css/login.css">
  {{-- 부트스트랩 --}}
<link href="https://fonts.googleapis.com/css?family=Nanum+Gothic" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Document</title>
</head>
<body>
  <div class="grid2">

    <form action="{{route('user.registration.post')}}" method="POST" class="form login">
      {{-- @include('layout.errorlayout') --}}
      @forelse ($errors->all() as $val)
      <div id="errorMsg" class="form-text text-danger"> {{$val}}</div>
      @empty
      @endforelse
      <br>
      @csrf
      <div class="form__field">
        <label for="email"><svg class="icon">
            <use xlink:href="#icon-user"></use>
          </svg><span class="hidden">email</span></label>
        <input autocomplete="off" id="email" type="text" name="email" class="form__input" placeholder="email">
      </div>

      <div class="form__field">
        <label for="password"><svg class="icon">
            <use xlink:href="#icon-lock"></use>
          </svg><span class="hidden">Password</span></label>
        <input id="password" type="password" name="password" class="form__input" placeholder="Password">
      </div>

      <div class="form__field">
          <label for="passwordchk"><svg class="icon">
              <use xlink:href="#icon-lock"></use>
            </svg><span class="hidden">Password chk</span></label>
          <input id="passwordchk" type="password" name="passwordchk" class="form__input" placeholder="Password chk">
        </div>

        <div class="form__field">
          <label for="name"><svg class="icon">
              <use xlink:href="#icon-user"></use>
            </svg><span class="hidden">email</span></label>
          <input autocomplete="name" id="name" type="text" name="name" class="form__input" placeholder="username">
        </div>

      <div class="form__field">
        <input type="submit" class="button_line_none" value="Sign Up">
      </div>

    </form>


  </div>
  
    <svg xmlns="http://www.w3.org/2000/svg" class="icons">
      <symbol id="icon-arrow-right" viewBox="0 0 1792 1792">
        <path d="M1600 960q0 54-37 91l-651 651q-39 37-91 37-51 0-90-37l-75-75q-38-38-38-91t38-91l293-293H245q-52 0-84.5-37.5T128 1024V896q0-53 32.5-90.5T245 768h704L656 474q-38-36-38-90t38-90l75-75q38-38 90-38 53 0 91 38l651 651q37 35 37 90z" />
      </symbol>
      <symbol id="icon-lock" viewBox="0 0 1792 1792">
        <path d="M640 768h512V576q0-106-75-181t-181-75-181 75-75 181v192zm832 96v576q0 40-28 68t-68 28H416q-40 0-68-28t-28-68V864q0-40 28-68t68-28h32V576q0-184 132-316t316-132 316 132 132 316v192h32q40 0 68 28t28 68z" />
      </symbol>
      <symbol id="icon-user" viewBox="0 0 1792 1792">
        <path d="M1600 1405q0 120-73 189.5t-194 69.5H459q-121 0-194-69.5T192 1405q0-53 3.5-103.5t14-109T236 1084t43-97.5 62-81 85.5-53.5T538 832q9 0 42 21.5t74.5 48 108 48T896 971t133.5-21.5 108-48 74.5-48 42-21.5q61 0 111.5 20t85.5 53.5 62 81 43 97.5 26.5 108.5 14 109 3.5 103.5zm-320-893q0 159-112.5 271.5T896 896 624.5 783.5 512 512t112.5-271.5T896 128t271.5 112.5T1280 512z" />
      </symbol>
    </svg>
    {{-- 부트스트랩 --}}
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>