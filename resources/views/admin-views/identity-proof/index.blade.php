@extends('layouts.admin.app')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-uppercase">Add New Identity Proof</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.identity-proof.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="lang_code">Language</label>
                        <select name="lang_code" id="lang_code" class="form-control">
                            <option value="en">English</option>
                            {{-- <option value="fr">French</option> --}}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name_en">Identity Proof Name (English)</label>
                        <input type="text" name="name_en" id="name_en" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="image">Icon</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>

                    <!-- Image Preview -->
                    <div class="form-group">
                        <img id="imagePreview" src="#" alt="Preview" class="img-thumbnail d-none"
                            style="max-width: 200px;">
                    </div>

                    <button type="submit" class="btn btn-primary">Add Identity Proof</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('image').addEventListener('change', function(event) {
            let reader = new FileReader();
            reader.onload = function() {
                let output = document.getElementById('imagePreview');
                output.src = reader.result;
                output.classList.remove('d-none'); // Show the image preview
            };
            reader.readAsDataURL(event.target.files[0]);
        });
    </script>
@endsection
