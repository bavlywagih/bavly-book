@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ mix('css/indexPage.css') }}">
    <link rel="stylesheet" href="{{ mix('css/post.css') }}">
@endsection

@section('title', 'الرئيسية')

@section('content')

@auth

<div class="fb-post-container">

    <form id="postForm" enctype="multipart/form-data">
        @csrf
        <div class="card p-3 mb-4 shadow-sm" style="max-width: 600px; margin: auto;">
            <div class="d-flex mb-3">
            <img src="{{ $user->currentProfilePhoto ? asset('storage/' . $user->currentProfilePhoto->path) : asset('image/default-user-photo.png') }}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            <textarea class="form-control ms-2" rows="2" placeholder="What's on your mind?" name="body" id="body"></textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <label class="btn btn-sm btn-light border">
                        📷
                        <input type="file" name="images[]" id="images" multiple hidden>
                    </label>
                </div>
                <button class="btn btn-primary btn-sm w-100 m-2" type="submit">post now !</button>
            </div>

            <div id="postError" class="text-danger mt-2" style="font-size: 14px;"></div>
        </div>
    </form>
</div>

<div class="fb-post-container" id="post-container">
    @foreach($posts as $post)
        <div class="fb-post">
            <div class="fb-post-header">
                <img src="{{ $post->user->currentProfilePhoto ? asset('storage/' . $post->user->currentProfilePhoto->path) : asset('image/default-user-photo.png') }}"
                     class="profile-img shadow" style="width:32px;height:32px;border-radius:50%;margin-right:8px; object-fit: cover;">
                <strong>{{ $post->user->first_name }} {{ $post->user->last_name }}</strong>
                <span class="timestamp">{{ $post->created_at->diffForHumans() }}</span>
            </div>
            <div class="fb-post-body">
                <p>{{ $post->body }}</p>
                @if($post->images->count())
                    <div class="fb-post-images">
                        @foreach($post->images as $image)
                            <img src="{{ asset('storage/' . $image->path) }}"
                                 onclick="openCarousel({{ $post->id }}, {{ $loop->index }})">
                        @endforeach
                    </div>
                @endif
                <hr>
                <div class="d-flex align-items-center justify-content-between">

                    @php
                    $userLoved = $post->loves->contains('user_id', auth()->id());
                    @endphp
                    <div>
                        <button class="love-btn" onclick="toggleLove({{ $post->id }}, this)">
                            <i id="love-icon-{{ $post->id }}"class="{{ $userLoved ? 'fa-solid' : 'fa-regular' }} fa-heart fa-regular fa-heart "style="{{ $userLoved ? 'color:#ff0000;' : 'color:black;' }}"></i>
                        </button>
                        
                        <span id="love-count-{{ $post->id }}" class="love-count" onclick="showLoveList({{ $post->id }})" style="cursor:pointer;">
                            {{ $post->loves->count() }}
                        </span>
                    </div>

                    <span>👁️ {{ $post->view_count }}</span>

                </div>


            </div>
        </div>
    @endforeach
</div>

<div class="modal fade" id="loveListModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">المعجبون بالمنشور</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="loveListBody">
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="postImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark text-white">
      <div class="modal-body p-0">
        <div id="postCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner" id="postCarouselInner"></div>
          <button class="carousel-control-prev" type="button" data-bs-target="#postCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#postCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function showLoveList(postId) {
    const post = posts.find(p => p.id === postId);
    if (!post || !post.loves || post.loves.length === 0) {
        document.getElementById('loveListBody').innerHTML = '<p>لا يوجد معجبون حتى الآن.</p>';
    } else {
        const html = post.loves.map(love => `
            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                <img src="${love.user.current_profile_photo ? '/storage/' + love.user.current_profile_photo.path : '{{ asset('image/default-user-photo.png') }}'}" style="width: 32px; height: 32px; border-radius: 50%; margin-right: 10px;">
                <span>${love.user.first_name} ${love.user.last_name}</span>
            </div>
        `).join('');
        document.getElementById('loveListBody').innerHTML = html;
    }

    new bootstrap.Modal(document.getElementById('loveListModal')).show();
}
</script>


<script>
function toggleLove(postId, btn) {
    btn.classList.add('clicked');
    setTimeout(() => {
        btn.classList.remove('clicked');
    }, 400);

    fetch(`/posts/${postId}/love`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById(`love-count-${postId}`).textContent = data.count;
        const icon = document.getElementById(`love-icon-${postId}`);
        if (data.loved) {
            icon.classList.remove('fa-regular');
            icon.classList.add('fa-solid');
            icon.style.cssText = "color: #ff0000 !important;";
        } else {
            icon.classList.remove('fa-solid');
            icon.classList.add('fa-regular');
            icon.style.color = '';
        }

        const post = posts.find(p => p.id === postId);
        if (post) {
            if (data.loved) {
                post.loves.push({
                    user_id: {{ auth()->id() }},
                    user: {
                        first_name: '{{ auth()->user()->first_name }}',
                        last_name: '{{ auth()->user()->last_name }}',
                        current_profile_photo: {!! auth()->user()->currentProfilePhoto ? json_encode([
                            'path' => auth()->user()->currentProfilePhoto->path
                        ]) : 'null' !!}
                    }
                });
            } else {
                post.loves = post.loves.filter(love => love.user_id !== {{ auth()->id() }});
            }
        }
    });
}



</script>

<script>
    const posts = @json($posts);

    function openCarousel(postId, startIndex = 0) {
        const post = posts.find(p => p.id === postId);
        if (!post || !post.images.length) return;

        const inner = document.getElementById('postCarouselInner');
        inner.innerHTML = '';

        post.images.forEach((img, idx) => {
            inner.innerHTML += `
                <div class="carousel-item ${idx === startIndex ? 'active' : ''}">
                    <img src="/storage/${img.path}" class="d-block w-100" style="max-height: 80vh; object-fit: contain;">
                </div>
            `;
        });

        new bootstrap.Modal(document.getElementById('postImageModal')).show();
    }

    document.getElementById('postForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const errorDiv = document.getElementById('postError');
    errorDiv.innerHTML = ''; 

    fetch("{{ route('posts.store') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            errorDiv.innerText = data.error;
        } else {
            form.reset(); 
            prependPost(data); 
        }
    })
    .catch(error => {
        console.error(error);
        errorDiv.innerText = 'حدث خطأ أثناء النشر';
    });
});
    let skip = 10;
    let loading = false;

    window.addEventListener('scroll', () => {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            loadMorePosts();
        }
    });

    function loadMorePosts() {
        if (loading) return;
        loading = true;

        fetch(`/load-posts?skip=${skip}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(post => {
                    renderPost(post);
                    posts.push(post); 
                });
                skip += data.length;
                loading = false;
            });
    }

    function renderPost(post, prepend = false) {
        const container = document.getElementById('post-container');

        const imgSrc = post.user.current_profile_photo
            ? `/storage/${post.user.current_profile_photo.path}`
            : `{{ asset('image/default-user-photo.png') }}`;

const postHtml = `
    <div class="fb-post">
        <div class="fb-post-header">
            <img src="${imgSrc}" class="profile-img" style="width:32px;height:32px;border-radius:50%;margin-right:8px;">
            <strong>${post.user.first_name} ${post.user.last_name}</strong>
            <span class="timestamp">${post.created_at_diff}</span>
        </div>
        <div class="fb-post-body">
            <p>${post.body || ''}</p>
            ${post.images.length ? `
            <div class="fb-post-images">
                ${post.images.map((img, idx) => `
                    <img src="/storage/${img.path}" onclick="openCarousel(${post.id}, ${idx})">
                `).join('')}
            </div>` : ''}
        </div>

        <div class="fb-post-actions mt-2">


            <div class="d-flex align-items-center justify-content-between mt-2">
                <div>
                    <button class="love-btn" onclick="toggleLove(${post.id}, this)">
                        <i id="love-icon-${post.id}"
                        class="${post.user_loved ? 'fa-solid' : 'fa-regular'} fa-heart"
                        style="${post.user_loved ? 'color:#ff0000 !important;' : 'color:black;'}"></i>
                    </button>
                    <span id="love-count-${post.id}" class="love-count" style="cursor:pointer;" onclick="showLoveList(${post.id})">
                        ${post.loves.length}
                    </span>
                </div>
                <span>👁️ ${post.view_count}</span>
            </div>

        </div>
    </div>
`;


        if (prepend) {
            container.insertAdjacentHTML('afterbegin', postHtml);
        } else {
            container.insertAdjacentHTML('beforeend', postHtml);
        }
    }
</script>
<script>
function prependPost(post) {
    const container = document.getElementById('post-container');

    const profileImage = post.user.current_profile_photo
        ? `/storage/${post.user.current_profile_photo.path}`
        : "/images/default-user-photo.png";

    const imagesHTML = post.images.map((img, idx) => `
        <img src="/storage/${img.path}" onclick="openCarousel(${post.id}, ${idx})">
    `).join('');
    post.view_count = post.view_count ?? 0;
    const postHTML = `
        <div class="fb-post">
            <div class="fb-post-header">
                <img src="${profileImage}" alt="Profile" class="profile-pic shadow" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 5px; object-fit:cover;">
                <strong>${post.user.first_name} ${post.user.last_name}</strong>
                <span class="timestamp">الآن</span>
            </div>
            <div class="fb-post-body">
                <p>${post.body ?? ''}</p>
                ${imagesHTML ? `<div class="fb-post-images">${imagesHTML}</div>` : ''}
                <hr>
                <div class="fb-post-actions mt-2">


                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <div>
                            <button class="love-btn" onclick="toggleLove(${post.id}, this)">
                                <i id="love-icon-${post.id}"
                                class="${post.user_loved ? 'fa-solid' : 'fa-regular'} fa-heart"
                                style="${post.user_loved ? 'color:#ff0000 !important;' : 'color:black;'}">
                                </i>
                            </button>
                            <span id="love-count-${post.id}" class="love-count" style="cursor:pointer;" onclick="showLoveList(${post.id})">
                                ${post.loves.length}
                            </span>
                        </div>
                        <span>👁️ ${post.view_count}</span>
                    </div>

                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('afterbegin', postHTML);
    posts.unshift(post); 
}

</script>

@else
   
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <main id="fb-main">
        <div class="fb-container">
            <section class="fb-connect">
                <span>Facebook helps you connect and share with the people in your life.</span>
                <img src="{{ asset('image/welcome_page.png') }}" alt="">
            </section>
            <section class="fb-form">
                <span class="frm-header">Create an account</span><br>
                <span class="frm-spn1">It's free and always will be.</span>
                <form class="sign-up" method="POST" action="{{ route('register') }}">
                    @csrf
                      
                    <input class="frm-textbox textbox-width" type="text" name="first_name" placeholder="First name">
                    <input class="frm-textbox textbox-width"type="text" name="last_name" placeholder="Surname">
                    <input class="frm-textbox"type="text" name="login" placeholder="Mobile number or email address"><br>
                    <input class="frm-textbox" type="password" name="password" placeholder="New password"><br>
                    
                    <div class="form-div">
                        <span class="birthday">Birthday</span><br>
                        <select aria-label="Day" name="birthday_day" id="day" title="Day" class="_5dba">
                            <option value="0">Day</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5" selected="1">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>
                        </select>
                        <select aria-label="Month" name="birthday_month" id="month" title="Month" class="_5dba">
                            <option value="0">Month</option><option value="1">Jan</option><option value="2">Feb</option><option value="3">Mar</option><option value="4">Apr</option><option value="5">May</option><option value="6">Jun</option><option value="7">Jul</option><option value="8" selected="1">Aug</option><option value="9">Sept</option><option value="10">Oct</option><option value="11">Nov</option><option value="12">Dec</option>
                        </select>
                        <select aria-label="Year" name="birthday_year" id="year" title="Year" class="select-last-child">
                            <option value="0">Year</option><option value="2018">2018</option><option value="2017">2017</option><option value="2016">2016</option><option value="2015">2015</option><option value="2014">2014</option><option value="2013">2013</option><option value="2012">2012</option><option value="2011">2011</option><option value="2010">2010</option><option value="2009">2009</option><option value="2008">2008</option><option value="2007">2007</option><option value="2006">2006</option><option value="2005">2005</option><option value="2004">2004</option><option value="2003">2003</option><option value="2002">2002</option><option value="2001">2001</option><option value="2000">2000</option><option value="1999">1999</option><option value="1998">1998</option><option value="1997">1997</option><option value="1996">1996</option><option value="1995">1995</option><option value="1994">1994</option><option value="1993" selected="1">1993</option><option value="1992">1992</option><option value="1991">1991</option><option value="1990">1990</option><option value="1989">1989</option><option value="1988">1988</option><option value="1987">1987</option><option value="1986">1986</option><option value="1985">1985</option><option value="1984">1984</option><option value="1983">1983</option><option value="1982">1982</option><option value="1981">1981</option><option value="1980">1980</option><option value="1979">1979</option><option value="1978">1978</option><option value="1977">1977</option><option value="1976">1976</option><option value="1975">1975</option><option value="1974">1974</option><option value="1973">1973</option><option value="1972">1972</option><option value="1971">1971</option><option value="1970">1970</option><option value="1969">1969</option><option value="1968">1968</option><option value="1967">1967</option><option value="1966">1966</option><option value="1965">1965</option><option value="1964">1964</option><option value="1963">1963</option><option value="1962">1962</option><option value="1961">1961</option><option value="1960">1960</option><option value="1959">1959</option><option value="1958">1958</option><option value="1957">1957</option><option value="1956">1956</option><option value="1955">1955</option><option value="1954">1954</option><option value="1953">1953</option><option value="1952">1952</option><option value="1951">1951</option><option value="1950">1950</option><option value="1949">1949</option><option value="1948">1948</option><option value="1947">1947</option><option value="1946">1946</option><option value="1945">1945</option><option value="1944">1944</option><option value="1943">1943</option><option value="1942">1942</option><option value="1941">1941</option><option value="1940">1940</option><option value="1939">1939</option><option value="1938">1938</option><option value="1937">1937</option><option value="1936">1936</option><option value="1935">1935</option><option value="1934">1934</option><option value="1933">1933</option><option value="1932">1932</option><option value="1931">1931</option><option value="1930">1930</option><option value="1929">1929</option><option value="1928">1928</option><option value="1927">1927</option><option value="1926">1926</option><option value="1925">1925</option><option value="1924">1924</option><option value="1923">1923</option><option value="1922">1922</option><option value="1921">1921</option><option value="1920">1920</option><option value="1919">1919</option><option value="1918">1918</option><option value="1917">1917</option><option value="1916">1916</option><option value="1915">1915</option><option value="1914">1914</option><option value="1913">1913</option><option value="1912">1912</option><option value="1911">1911</option><option value="1910">1910</option><option value="1909">1909</option><option value="1908">1908</option><option value="1907">1907</option><option value="1906">1906</option><option value="1905">1905</option>
                        </select>
                        <a class="why-link" title="We ask for your birthday to give you the right experience for your age ." href="#">Why do I need to provide my date of birth?</a><br>
                        <div class="frm-radio">
                            <input type="radio" name="gender" value="female" required><label>Female</label>
                            <input type="radio" name="gender" value="male"><label>Male</label>
                        </div>
                        <p>By clicking Sign Up, you agree to our <a href="#">Terms, Data Policy</a> and <a href="#">Cookie Policy</a>. You may receive SMS notifications from us and can opt out at any time.</p>
                        <button type="submit">Sgin Up</button><br>
                        <span class="form-last-child"><a href="#">Create a Page</a> for a celebrity, band or business.</span>
                    </div>
            </form>
            </section>
        </div>
    </main>

    <footer class="fb-footer">
        <div class="div-footer">
            <ul class="ul1">
                <li>English (UK)</li><li><a href="#">العربية</a></li><li><a href="#">Français (France)</a></li><li><a href="#">Italiano</a></li><li><a href="#">Deutsch</a></li><li><a href="#">Русский</a></li><li><a href="#">Español</a></li><li><a href="#">Bahasa Indonesia</a></li><li><a href="#">Türkçe</a></li><li><a href="#">Português (Brasil)</a></li><li><a href="#">हिन्दी</a></li>
            </ul>
            <div class="ho-rule"></div>
            <ul class="ul2">
                <li><a href="#">Sign Up</a></li><li><a href="#">Log In</a></li><li><a href="#">Messenger</a></li><li><a href="#">Facebook Lite</a></li><li><a href="#">Mobile</a></li><li><a href="#">Find Friends</a></li><li><a href="#">People</a></li><li><a href="#">Pages</a></li><li><a href="#">Video interests</a></li><li><a href="#">Places</a></li><li><a href="#">Games</a></li><li><a href="#">Locations</a></li><li><a href="#">Marketplace</a></li><li><a href="#">Groups</a></li><li><a href="#">Instagram</a></li><li><a href="#">Local</a></li><li><a href="#">About</a></li><li><a href="#">Create ad</a></li><li><a href="#">Create Page</a></li><li><a href="#">Developers</a></li><li><a href="#">Careers</a></li><li><a href="#">Privacy</a></li><li><a href="#">Cookies</a></li><li><a href="#">AdChoices</a></li><li><a href="#">Terms</a></li><li><a href="#">Help</a></li><li><a href="#">Settings</a></li><li><a href="#">Activity log</a></li>
            </ul>
            <p>Facebook © 2025</p>
        </div>
    </footer>
@endauth

@endsection

{{-- 

<div class="main">
  <aside class="fb-sidebar">
    <ul>
      <li>📃 News Feed</li>
      <li>👨‍👩‍👧‍👦 Groups</li>
      <li>🏪 Marketplace</li>
      <li>🎉 Events</li>
      <li>🕓 Memories</li>
    </ul>
  </aside>
  
  <section class="fb-feed">
    <div class="post-creator">
      <textarea id="postText" rows="3" placeholder="What's on your mind?"></textarea>
      <button onclick="createPost()">Post</button>
    </div>
    
    <div id="posts"></div>
  </section>
  
  <aside class="fb-widgets">
    <div class="widget-box">
      <p><strong>Sponsored</strong></p>
      <p>Ad content here...</p>
    </div>
  </aside>
</div>

<section class="fb-profile">
  <div class="cover-photo"></div>
  <div class="profile-info">
    <img src="https://via.placeholder.com/80" alt="Profile Picture">
    <h2>User Name</h2>
  </div>
</section> --}}

