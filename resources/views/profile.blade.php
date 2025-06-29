@extends('layouts.app')

@section('content')

<script>
    console.log("profile:", {!! json_encode(auth()->user()->profilePhotos()->get()) !!});
    console.log("cover:", {!! json_encode(auth()->user()->coverPhotos()->get()) !!});
</script>



<style>
    .cover-photo-container {
        position: relative;
        width: 100%;
        height: 300px;
        background-color: #ccc;
        background-size: cover;
        background-position: center;
    }

    .cover-edit-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        display: none;
    }

    .cover-photo-container:hover .cover-edit-btn {
        display: block;
    }

    .profile-photo-container {
        position: absolute;
        bottom: -75px;
        left: 30px;
        width: 150px;
        height: 150px;
        border: 4px solid white;
        /* border-radius: 50%; */
        background-size: cover;
        background-position: center;
        background-color: #fff;
        overflow: hidden;
        cursor: pointer;
    }

    .profile-edit-btn {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        text-align: center;
        padding: 5px;
        font-size: 14px;
        display: none;
        /* border-bottom-left-radius: 50%;
        border-bottom-right-radius: 50%; */
    }

    .profile-photo-container:hover .profile-edit-btn {
        display: block;
    }

    .upload-form {
        margin-top: 120px;
        padding: 20px;
    }

    input[type="file"] {
        display: none;
    }

    .modal-img {
        max-height: 70vh;
        object-fit: contain;
    }
</style>

<div class="cover-photo-container"onclick="showModal('cover')"style="background-image: url('{{ auth()->user()->currentCoverPhoto() ? Storage::url(auth()->user()->currentCoverPhoto()->path) : 'https://via.placeholder.com/1200x300' }}')">
<div class="cover-edit-btn" onclick="event.stopPropagation(); document.getElementById('cover-input').click()">Change Cover</div>
    <form id="cover-form" enctype="multipart/form-data">
        @csrf
        <input type="file" id="cover-input" name="image" accept="image/*" onchange="uploadImage('cover')">
        <input type="hidden" name="type" value="cover">
    </form>


    <div class="profile-photo-container"onclick="event.stopPropagation(); showModal('profile')" style="background-image: url('{{ auth()->user()->currentProfilePhoto() ? Storage::url(auth()->user()->currentProfilePhoto()->path) : 'https://via.placeholder.com/150' }}')">
<div class="profile-edit-btn" onclick="event.stopPropagation(); document.getElementById('profile-input').click()">Change</div>
        <form id="profile-form" enctype="multipart/form-data">
            @csrf
            <input type="file" id="profile-input" name="image" accept="image/*" onchange="uploadImage('profile')">
            <input type="hidden" name="type" value="profile">
        </form>
    </div>
</div>

<div class="container upload-form">
    <div id="upload-msg" class="alert alert-info d-none"></div>
</div>

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
      <div class="modal-footer bg-dark border-top-0 justify-content-center">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>






<script>
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
            } else if (data.errors) {
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
    fetch(`/profile/images/${type}`)
        .then(res => res.json())
        .then(images => {
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            const container = document.getElementById('carouselInner');
            container.innerHTML = '';

            if (images.length === 0) {
                container.innerHTML = '<div class="p-5 text-center text-white">لا توجد صور متاحة</div>';
            }

            images.forEach((img, index) => {
                const active = index === 0 ? 'active' : '';
                container.innerHTML += `
                    <div class="carousel-item ${active}">
                        <img src="/storage/${img.path}" class="d-block w-100 modal-img" alt="Image ${index + 1}">
                    </div>`;
            });

            modal.show();
        })
        .catch(() => {
            alert("حدث خطأ أثناء تحميل الصور.");
        });
}

</script>
@endsection
