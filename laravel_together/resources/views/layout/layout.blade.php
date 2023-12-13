<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  @yield('gantt_link', '') {{-- 12/12 민주 gantt css 개별 링크용--}}
  <link rel="stylesheet" href="/css/common.css">
 
  @yield('link','') {{-- 12/12 11:02 kkh: css 개별 링크용 --}}
  <title>@yield('title', 'Laravel Board')</title>
</head>
<body>
  {{-- 다크모드 --}}
  <div class="dark-light">
        <button type="button" style="background:transparent; border:none; cursor:pointer"><img src="/img/free-icon-moon-7682051.png" style="width: 30px; height: auto;" alt="이미지 설명"></button>
  </div>
   <div class="app">

    <div class="header">
      <a class="header-title">Together</a>
    
     <div class="header-menu">
      @auth
      <a class="btn_signin" href="{{route('user.logout.get')}}">로그아웃</a>
      @endauth
     </div>
     <div class="header-profile">
      
      <button class="icon-Sub"><img src="/img/icon-bell.png" alt=""></button>{{-- <span class="notification-number">3</span> --}}
      <button class="icon-Sub" onclick="toggleModal()"><img src="/img/icon-Subtract.png" alt=""></button>
      <img class="profile-img" src="/img/profile-img.png" alt="">
     </div>
    </div>

    <div class="wrapper">
     <div class="left-side">
      <button>새 프로젝트 생성</button>
      <div class="side-wrapper">
       <div class="side-title">메뉴</div>
       <div class="side-menu">
        <a href="#">
          <img src="/img/dashboard_icon.svg" alt="">
          <span>대시보드</span>
        </a>
        <a href="#">
          <img src="/img/gangchart_icon.svg" alt="">
         <span>간트차트</span>
         {{-- <span class="notification-number updates">3</span> --}}
        </a>
       </div>
      </div>
      <div class="side-wrapper">
       <div class="side-title">개인 프로젝트</div>
       <div class="side-menu">
        <a href="#">
         <div class="project-color-box"></div>
         <span>프로젝트 명1</span>
        </a>
        <a href="#">
          <div class="project-color-box"></div>
          <span>프로젝트 명2</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>프로젝트 명3</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>프로젝트 명4</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>프로젝트 명5</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>프로젝트 명6</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>프로젝트 명7</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>프로젝트 명8</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>프로젝트 명9</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>프로젝트 명10</span>
         </a>
       </div>
      </div>
      <div class="side-wrapper">
       <div class="side-title">팀 프로젝트</div>
       <div class="side-menu">
        <a href="#">
          <div class="project-color-box"></div>
          <span>팀 프로젝트 명1</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>팀 프로젝트 명2</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>팀 프로젝트 명3</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>팀 프로젝트 명4</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>팀 프로젝트 명5</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>팀 프로젝트 명6</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>팀 프로젝트 명7</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>팀 프로젝트 명8</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>팀 프로젝트 명9</span>
         </a>
         <a href="#">
          <div class="project-color-box"></div>
          <span>팀 프로젝트 명10</span>
         </a>
       </div>
      </div>
     </div>
     <div class="main-container">
      <div class="content-wrapper">
        @yield('main')
        @extends('modal.messenger')
    {{-- <div class="overlay-app">
      
    </div> --}}
   </div>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
   <script src="https://cpwebassets.codepen.io/assets/common/browser_support-2c1a3d31dbc6b5746fb7dacdbc81dd613906db219f13147c66864a6c3448246c.js"></script>
   <script src="/js/common.js"></script>
   <script src="/js/ganttchart.js"></script>
</body>
</html>