  {{-- <header class="fb-header">
    <div class="logo"><strong>facebook</strong></div>
    <input type="text" placeholder="Search Facebook">
    <div class="icons">
      <span>🏠</span>
      <span>👥</span>
      <span>🔔</span>
      <span>💬</span>
      <span>👤</span>
    </div>
  </header> --}}
   
@auth

  <header class="fb-header">
    <div class="logo"><a href="{{ route('home') }}" class="text-white text-decoration-none" >Bavlybook</a></div>

    <div class="search-box">
      <input type="text" placeholder="Search bavlybook">
    </div>

    <div class="icons">
        <span title="Home"><a href="{{ route('home') }}">🏠</a></span>
        <span title="Friends">👥</span>
        <span title="Notifications">🔔</span>
        <span title="Messages">💬</span>
            <img src="{{ auth()->user()->currentProfilePhoto ? Storage::url(auth()->user()->currentProfilePhoto->path) : asset('image/default-user-photo.png') }}" id="profile-icon" alt="Profile Photo" style="width: 40px; height: 40px;object-fit: cover; border-radius: 3px; border: 3px solid white; background-position: center; cursor: pointer;">




      <div class="dropdown" id="profile-dropdown">
        <a href="{{ route('profile.show') }}">My Profile</a>
        <a href="#">Settings</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="b-f-logout" type="submit">Logout</button>
        </form>
      </div>
    </div>
  </header>

  <script>
    const profileIcon = document.getElementById('profile-icon');
    const dropdown = document.getElementById('profile-dropdown');

    profileIcon.addEventListener('click', (e) => {
      dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
      e.stopPropagation();  
    });

    document.addEventListener('click', () => {
      dropdown.style.display = 'none';
    });
  </script>

@endauth  
  @guest
  <header class="fb-header">
      <div class="inner-header @auth w-nav-100 @endauth">
          <a href="{{ route('home') }}"  class="a-link-mame"><h1 class="fb-header-logo" >Bavlybook</h1></a>
        <form method="POST" action="{{ route('login') }}" class="login-grid">
            @csrf
            <label class="email-label" for="email or phone">Email or Phone</label>
            <input class="email-input" type="text" name="login">
            <label class="pass-label" for="password">Password</label>
            <input class="pass-input" type="password" name="password">
            <span class="Forgotten-item"><a href="#">Forgotten account?</a></span>
            <button  type="submit">Log In</button>
        </form>

        @endauth
    </div>
</header>