@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ mix('css/post.css') }}">
    <link rel="stylesheet" href="{{ mix('css/profile.css') }}">
@endsection

@section('content')
    <div class="cover-photo-container" onclick="showModal('cover')" style="background-image: url('{{ auth()->user()->currentCoverPhoto ? Storage::url(auth()->user()->currentCoverPhoto->path) : "" }}')">
        <div class="cover-edit-btn" onclick="event.stopPropagation(); document.getElementById('cover-input').click()">Change Cover</div>
        <form id="cover-form" enctype="multipart/form-data">
            @csrf
            <input type="file" id="cover-input" name="image" accept="image/*" onchange="uploadImage('cover')">
            <input type="hidden" name="type" value="cover">
        </form>

        {{-- <h2>{{ $user->first_name }} {{ $user->last_name }}</h2> --}}
        
        <div class="profile-photo-container" onclick="event.stopPropagation(); showModal('profile')" style="background-image: url('{{ auth()->user()->currentProfilePhoto ? Storage::url(auth()->user()->currentProfilePhoto->path) : asset('image/default-user-photo.png') }}') ; z-index: 2;">
            <div class="profile-edit-btn" onclick="event.stopPropagation(); document.getElementById('profile-input').click()">Change</div>
            <form id="profile-form" enctype="multipart/form-data">
                @csrf
                <input type="file" id="profile-input" name="image" accept="image/*" onchange="uploadImage('profile')">
                <input type="hidden" name="type" value="profile">
            </form>
        </div>
        <div style="position: absolute; left: 0px;bottom: 0px;right: 0px;height: 300px;background: linear-gradient(rgba(0, 0, 0, 0) 71%, rgba(0, 0, 0, 0.53));z-index: 1;"></div>
        <div class="user-name">{{ $user->first_name }} {{ $user->last_name }}</div>
    </div>

    <div class="container upload-form w-50"><div id="upload-msg" class="alert alert-info d-none"></div></div>

    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white">

                <div class="modal-body p-0">
                    <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" id="carouselInner"></div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>

                        <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>

                    </div>
                </div>

                <div class="modal-footer bg-dark border-top-0 justify-content-between">

                    <div>
                        <button type="button" id="set-current-image-btn" class="btn btn-success me-2">استخدام هذه الصورة</button>
                        <button type="button" id="delete-current-image-btn" class="btn btn-danger">إزالة</button>
                    </div>

                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>

                </div>
            </div>
        </div>
    </div>

    <div class="container ">
        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab">timeline</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab">about</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="friends-tab" data-bs-toggle="tab" data-bs-target="#friends" type="button" role="tab">friends</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="photos-tab" data-bs-toggle="tab" data-bs-target="#photos" type="button" role="tab">photos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos" type="button" role="tab">videos</button>
            </li>
        </ul>

        <div class="tab-content bg-white p-3 border border-top-0 rounded-bottom" id="profileTabsContent">
            <div class="tab-pane fade show active" id="posts" role="tabpanel">
                @foreach(auth()->user()->posts()->latest()->get() as $post)
                    <div class="fb-post">
                        <div class="fb-post-header">
                            <strong>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</strong>
                            <span class="timestamp">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="fb-post-body">
                            <p>{{ $post->body }}</p>

                            @if($post->images && count($post->images))
                                <div class="fb-post-images">
                                    @foreach($post->images as $image)
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="Post Image">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="tab-pane fade" id="about" role="tabpanel">
                <div class="container mt-5" style="display: flex;flex-direction: column;align-items: center;">
                    <div class="d-flex align-items-center mb-3">
                        <h2 class="me-3">{{ $user->first_name }} {{ $user->last_name }}</h2>
                        <button class="btn btn-sm btn-outline-primary" onclick="toggleEditForm()">تحديث البيانات</button>
                    </div>

                    <div id="user-info-view">
                        <p><strong>email:</strong> {{ $user->email }}</p>
                        <p><strong>phone number:</strong> {{ $user->mobile ?? 'غير محدد' }}</p>
                        <p><strong>birthdate:</strong> {{ $user->birthday ?? 'غير محدد' }}</p>
                        <p><strong>gender:</strong> {{ $user->gender == 'male' ? 'ذكر' : ($user->gender == 'female' ? 'أنثى' : 'غير محدد') }}</p>
                    </div>

                    <!-- نموذج التحديث (مخفي في البداية) -->
                    <form id="edit-form" class="d-none" method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        
                        <div class="mb-3">
                            <label class="form-label">الاسم الأول</label>
                            <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">الاسم الأخير</label>
                            <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الموبايل</label>
                            <input type="text" name="mobile" class="form-control" value="{{ $user->mobile }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">تاريخ الميلاد</label>
                            <input type="date" name="birthday" class="form-control" value="{{ $user->birthday }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">النوع</label>
                            <select name="gender" class="form-select">
                                <option value="">اختر النوع</option>
                                <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        <button type="button" class="btn btn-secondary" onclick="toggleEditForm()">إلغاء</button>
                    </form>
                </div>
            </div>
            
            <div class="tab-pane fade" id="friends" role="tabpanel">
                <p>قائمة الأصدقاء.</p>
            </div>
            <div class="tab-pane fade" id="photos" role="tabpanel">
                <p>معرض الصور.</p>
            </div>
            <div class="tab-pane fade" id="videos" role="tabpanel">
                <p>الفيديوهات المرفوعة.</p>
            </div>
        </div>
    </div>
{{-- </div> --}}

    {{-- < --}}
<script>
    function toggleEditForm() {
        document.getElementById('edit-form').classList.toggle('d-none');
        document.getElementById('user-info-view').classList.toggle('d-none');
    }
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. اقرأ التاب من URL
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');

    if (tab) {
        const trigger = document.querySelector(`button[data-bs-target="#${tab}"]`);
        if (trigger) {
            // فعّل التاب المحدد
            new bootstrap.Tab(trigger).show();
        }
    }

    // 2. عند التنقل بين التابات، عدّل عنوان الصفحة بدون إعادة تحميل
    const tabButtons = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (event) {
            const selectedTab = event.target.getAttribute('data-bs-target').substring(1);
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.set('tab', selectedTab);
            window.history.replaceState(null, '', newUrl);
        });
    });
});
</script>


    <script>
        let currentImageType = null;
        let currentImageList = [];
        let currentIndex = 0;

        function uploadImage(type) {
            const form = document.getElementById(type + '-form');
            const formData = new FormData(form);
            const msg = document.getElementById('upload-msg');
            msg.classList.add('d-none');

            fetch("{{ route('image.upload') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })

            .then(res => res.json())

            .then(data => {
                if (data.success) {
                    msg.innerText = data.success;
                    msg.classList.remove('d-none');
                    msg.classList.replace('alert-danger', 'alert-info');
                    setTimeout(() => location.reload(), 1000);
                }
                else if (data.errors) {
                    msg.innerText = Object.values(data.errors).join('\n');
                    msg.classList.remove('d-none');
                    msg.classList.replace('alert-info', 'alert-danger');
                }
            })

            .catch(() => {
                msg.innerText = "حدث خطأ أثناء رفع الصورة.";
                msg.classList.remove('d-none');
                msg.classList.replace('alert-info', 'alert-danger');
            });

        }

        function showModal(type) {
            currentImageType = type;

            fetch(`/profile/images/${type}`)

                .then(res => res.json())

                .then(images => {
                    currentImageList = images;
                    currentIndex = 0;
                    const container = document.getElementById('carouselInner');
                    container.innerHTML = '';

                    if (images.length === 0) {
                        container.innerHTML = '<div class="p-5 text-center text-white">لا توجد صور متاحة</div>';
                        return;
                    }

                    const currentImage = type === 'cover'
                        ? parseInt("{{ auth()->user()->currentCoverPhoto?->id }}")
                        : parseInt("{{ auth()->user()->currentProfilePhoto?->id }}");

                        images.forEach((img, index) => {
                            const active = index === 0 ? 'active' : '';
                            const imgUrl = `/storage/${img.path}`;

                            container.innerHTML += `
                                <div class="carousel-item ${active}" data-image-id="${img.id}">
                                    <img src="${imgUrl}" class="d-block w-100 modal-img" alt="Image ${index + 1}">
                                </div>`;
                        });

                    updateActionButtons();
                    new bootstrap.Modal(document.getElementById('imageModal')).show();
                });
        }

        document.getElementById('carouselImages')
            .addEventListener('slid.bs.carousel', function (e) {
                currentIndex = e.to;
                updateActionButtons();
            });

        function updateActionButtons() {
            const selected = currentImageList[currentIndex];
            const currentImage = currentImageType === 'cover'
                ? parseInt("{{ auth()->user()->currentCoverPhoto?->id }}")
                : parseInt("{{ auth()->user()->currentProfilePhoto?->id }}");

            const setBtn = document.getElementById('set-current-image-btn');
            if (selected.id === currentImage) {
                setBtn.style.display = 'none';
            } else {
                setBtn.style.display = 'inline-block';
            }
        }

        document.getElementById('set-current-image-btn')
        .addEventListener('click', function () {
            if (!currentImageList.length) return;
            const selected = currentImageList[currentIndex];

            console.log('Sending image_id:', selected.id);
            console.log('Type:', currentImageType);

            fetch(`/profile/images/set-current`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    image_id: selected.id,
                    type: currentImageType
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                    location.reload();
                } else {
                    alert('فشل في تعيين الصورة');
                }
            });
        });


        document.getElementById('delete-current-image-btn')
            .addEventListener('click', function () {
                if (!currentImageList.length) return;
                const selected = currentImageList[currentIndex];

                if (!confirm('هل أنت متأكد أنك تريد حذف هذه الصورة؟')) return;

                fetch(`/profile/images/delete/${selected.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('فشل في حذف الصورة');
                    }
                });
            });
    </script>
@endsection
